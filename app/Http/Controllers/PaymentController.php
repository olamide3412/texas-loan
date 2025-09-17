<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnums;
use App\Enums\PaymentMethodEnums;
use App\Enums\PaymentStatusEnums;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{


    public function initiatePayment(Order $order)
    {
        try {
            // Validate that the order is eligible for payment
            if ($order->payment_type !== 'instant') {
                return redirect()->back()->with('error', 'This order is not set for instant payment.');
            }

            if ($order->status === 'completed') {
                return redirect()->back()->with('error', 'This order has already been completed.');
            }

            if ($order->remaining_balance <= 0) {
                return redirect()->back()->with('error', 'This order has no outstanding balance.');
            }

            $tx_ref = 'TEXAS_ORDER_' . Str::upper(Str::random(10)) . '_' . $order->id;
            $secretKey = config('services.flutterwave.secret_key');

            // Initiate Flutterwave payment //withOptions(['force_ip_resolve' => 'v4'])->
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://api.flutterwave.com/v3/payments', [
                'tx_ref' => $tx_ref,
                'amount' => $order->total_price, // Use the total order amount
                'currency' => 'NGN',
                'redirect_url' => route('payment.verify', $order->id),
                'customer' => [
                    'email' => $order->client->email,
                    'name' => $order->client->full_name,
                    'phonenumber' => $order->client->phone_number
                ],
                'customizations' => [
                    'title' => 'Texas Loan Order Payment',
                    'description' => 'Payment for Order #' . $order->order_ref,
                    //'logo' => core()->getConfigData('general.design.admin_logo.logo_image'),
                ]
            ]);


            if ($response->successful()) {
                // Store temporary payment record
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $order->total_price,
                    'payment_method' => PaymentMethodEnums::Flutterwave->value,
                    'payment_status' => PaymentStatusEnums::Pending->value,
                    'tx_ref' => $tx_ref,
                ]);

                return redirect($response['data']['link']);
            } else {
                Log::error('Flutterwave API error: ' . $response->body());
                return redirect()->back()->with('error', 'Failed to initiate payment. Please try again.');
            }

        } catch (\Exception $e) {
            Log::error('Payment initiation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    public function flwWebHook(Request $request)
    {
        // Verify webhook signature
        $secretHash = config('services.flutterwave.secret_hash');
        $signature = $request->header('verif-hash');

        if (!$signature || ($signature !== $secretHash)) {
            abort(401);
        }

        $payload = $request->all();
        Log::info('Flutterwave Webhook: ', $payload);

        $txRef = $payload['data']['tx_ref'];

        // Find payment by transaction reference
        $payment = Payment::where('tx_ref', $txRef)->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $order = $payment->order;

        if ($payload['data']['status'] === 'successful') {
            // Verify the transaction
            $verificationResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.flutterwave.secret_key'),
            ])->get('https://api.flutterwave.com/v3/transactions/' . $payload['data']['id'] . '/verify');

            $verificationData = $verificationResponse->json();

            if ($verificationData['data']['status'] === 'successful' &&
                $verificationData['data']['amount'] == $payment->amount &&
                $verificationData['data']['currency'] === 'NGN') {

                // Update payment status
                $payment->update([
                    'payment_status' => PaymentStatusEnums::Success->value,
                    'payment_date' => now(),
                    'flw_ref' => $payload['data']['flw_ref'],
                ]);

                // Update order - mark as fully paid and completed
                $order->update([
                    'amount_paid' => $order->total_price,
                    'remaining_balance' => 0,
                    'status' => 'completed',
                    'given_by' => Auth::id() ?? $order->given_by,
                ]);

                Log::info('Payment verified and processed successfully: ' . $txRef);
            }
        } else {
            // Payment failed
            $payment->update([
                'payment_status' => PaymentStatusEnums::Failed->value,
            ]);

            Log::warning('Payment failed: ' . $txRef);
        }

        return response()->json(['message' => 'Webhook processed'], 200);
    }

    public function verifyPayment(Request $request, Order $order)
    {
        if (!$request->tx_ref) {
            return redirect()->route('order.show', $order->id)
                ->with('error', 'Invalid payment verification.');
        }

        $payment = Payment::where('tx_ref', $request->tx_ref)
                        ->where('order_id', $order->id)
                        ->first();

        if (!$payment) {
            return redirect()->route('order.show', $order->id)
                ->with('error', 'Payment record not found.');
        }

        if ($request->status === 'successful') {
            // Verify the transaction
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.flutterwave.secret_key'),
            ])->get('https://api.flutterwave.com/v3/transactions/' . $request->transaction_id . '/verify');

            $responseData = $response->json();

            if ($responseData['data']['status'] === 'successful' &&
                $responseData['data']['amount'] == $payment->amount &&
                $responseData['data']['currency'] === 'NGN') {

                // Update payment status
                $payment->update([
                    'payment_status' => PaymentStatusEnums::Success->value,
                    'payment_date' => now(),
                    'flw_ref' => $responseData['data']['flw_ref'],
                    'collected_by' => Auth::id()
                ]);

                // Update order - mark as fully paid and completed
                $order->update([
                    'amount_paid' => $order->total_price,
                    'remaining_balance' => 0,
                    'status' => OrderStatusEnums::Paid->value,
                    'given_by' => Auth::id() ?? $order->given_by,
                ]);

                return redirect()->route('order.show', $order->id)
                    ->with('success', 'Payment verified successfully! Order marked as paid.');
            }
        }

        // Payment failed or verification failed
        $payment->update([
            'payment_status' => PaymentStatusEnums::Failed->value,
        ]);

        return redirect()->route('order.show', $order->id)
            ->with('error', 'Payment verification failed. Please try again.');
    }
}
