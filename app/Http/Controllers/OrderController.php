<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnums;
use App\Enums\PaymentTypeEnums;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Client;
use App\Models\EmploymentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OrderController extends Controller
{

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
        return response()->json(
            $order->load('items.product', 'employmentDetail', 'client')
        );
    }

    /**
     * List all orders.
     */
    public function index()
    {
        $orders = Order::with('items.product', 'client')->latest()->paginate(20);
        return response()->json($orders);
    }
}
