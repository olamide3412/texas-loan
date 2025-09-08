<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnums;
use App\Enums\PaymentTypeEnums;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Client;
use App\Models\EmploymentDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OrderController extends Controller
{

        public function index(Request $request)
    {
         $query = Order::with('items.product', 'client')->latest();

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_ref', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Apply status filter
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Apply payment type filter
        if ($request->has('payment_type') && $request->payment_type !== 'all') {
            $query->where('payment_type', $request->payment_type);
        }

        // Apply date range filter
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(20);

        return inertia('Auth/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'status', 'payment_type', 'date_from', 'date_to'])
        ]);
    }

    public function create(Client $client)
    {
        //dd($client->toArray());
        $products = Product::all();
        return Inertia::render('Auth/Orders/CreateOrder', [
            'client' => $client,
            'products' => $products,
        ]);
    }


    public function store(Request $request)
    {
        //dd($request->toArray());

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'products'  => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'payment_type' => 'required|in:' . implode(',', array_map(fn($type) => $type->value, PaymentTypeEnums::cases())),
            'repayment_frequency' => 'nullable|in:weekly,monthly',
            'repayment_term' => 'nullable|integer|min:1',
            'employment' => 'nullable|array',
            'employment.guarantors' => 'nullable|json',
        ]);

        //dd('Validated');

        DB::beginTransaction();
        try {
            // Calculate total price
            $totalPrice = 0;
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['id']);
                $totalPrice += $product->price * $item['quantity'];
            }

            // Create order
            $order = Order::create([
                'client_id'          => $request->client_id,
                'order_ref'          => strtoupper(Str::random(10)),
                'total_price'        => $totalPrice,
                'remaining_balance'  => $request->payment_type === PaymentTypeEnums::Instant->value ? 0 : $totalPrice,
                'amount_paid'        => $request->payment_type === PaymentTypeEnums::Instant->value ? $totalPrice : 0,
                'payment_type'       => $request->payment_type,
                'repayment_frequency'=> $request->repayment_frequency ?? 'monthly',
                'repayment_term'     => $request->repayment_term ?? 1,
                'status'             => OrderStatusEnums::Pending->value,
                'created_by'         => Auth::id(), // works for staff guard
            ]);

            // Attach products
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['id']);
                OrderItem::create([
                    'order_id'    => $order->id,
                    'product_id'  => $product->id,
                    'quantity'    => $item['quantity'],
                    'price'       => $product->price,
                    'total_price' => $product->price * $item['quantity'],
                ]);
            }

            // Save employment details if provided
            if ($request->has('employment') && $request->payment_type !== PaymentTypeEnums::Instant->value) {
                EmploymentDetail::create([
                    'client_id'      => $request->client_id,
                    'order_id'       => $order->id,
                    'monthly_salary' => $request->employment['monthly_salary'] ?? 0,
                    'department'     => $request->employment['department'] ?? null,
                    'work_number_id' => $request->employment['work_number_id'] ?? null,
                    'employer'       => $request->employment['employer'] ?? null,
                    'address'        => $request->employment['address'] ?? null,
                    'guarantors'     => $request->employment['guarantors'] ?? null, //json_encode($request->employment['guarantors'] ?? []),
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Order created successfully!!!');

            // return response()->json([
            //     'message' => 'Order created successfully',
            //     'order'   => $order->load('items', 'employmentDetail')
            // ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create order',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show a specific order.
     */
    public function show(Order $order)
    {
        $order->load('items.product', 'employmentDetail', 'client');

        return inertia('Auth/Orders/Show', [
            'order' => $order,
        ]);
    }

    public function edit(Order $order)
    {
        $order->load('client', 'payments', 'approvedBy.staff', 'rejectedBy.staff', 'givenBy.staff', 'createdBy.staff');

        //dd($order->toArray());
        return inertia('Auth/Orders/Edit', [
            'order' => $order,
        ]);

    }


    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,rejected',
        ]);


        if ($order->status === OrderStatusEnums::Rejected->value){
            return redirect()->back()->with('error', 'Order status can\'t be updated because it has been rejected');
        }

        // if ($order->status === OrderStatusEnums::Processing->value &&
        //     $request->status !== OrderStatusEnums::Completed->value ){
        //     return redirect()->back()->with('error', 'Order status can\'t be updated because it has been rejected');
        // }

        $updateData = ['status' => $validated['status']];

        // Automatically assign current user based on status
        if ($validated['status'] === 'processing') {
            $updateData['approved_by'] = Auth::id();
        } elseif ($validated['status'] === 'completed') {
            $updateData['given_by'] = Auth::id();
        } elseif ($validated['status'] === 'rejected') {
            $updateData['rejected_by'] = Auth::id();
        }

        $order->update($updateData);

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }

    public function updatePayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0.01|max:' . $order->remaining_balance,
            'payment_method' => 'required|in:cash,transfer,pos',
        ]);

        // Create payment record
        $payment = $order->payments()->create([
            //'order_ref' => $order->order_ref,
            'amount' => $validated['payment_amount'],
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'success',
            'payment_date' => now(),
        ]);

        // Update order payment totals
        $totalPaid = $order->payments()->where('payment_status', 'success')->sum('amount');
        $remainingBalance = max(0, $order->total_price - $totalPaid);

        $order->update([
            'amount_paid' => $totalPaid,
            'remaining_balance' => $remainingBalance,
        ]);

        // Auto-complete order if balance is zero
        if ($remainingBalance === 0 && $order->status === 'processing') {
            $order->update([
                'status' => 'completed',
                'given_by' => Auth::id(),
            ]);
        }

        return redirect()->back()->with('success', 'Payment recorded successfully!');
    }

    public function destroy(Order $order)
    {
        // Delete related records first
        $order->items()->delete();
        if ($order->employment_detail) {
            $order->employment_detail()->delete();
        }

        $order->delete();

        return redirect()->route('order.index')->with('success', 'Order deleted successfully!');
    }

}
