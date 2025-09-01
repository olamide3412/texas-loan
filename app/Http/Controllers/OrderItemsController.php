<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnums;
use App\Enums\PaymentTypeEnums;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrderItemsController extends Controller
{
   /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $q = $request->get('q', '');

        $orders = Order::latest()
            ->with(['client', 'items.product'])
            ->where(function ($query) use ($q) {
                $query->where('order_ref', 'LIKE', '%' . $q . '%')
                      ->orWhereHas('client', function ($sub) use ($q) {
                          $sub->where('first_name', 'LIKE', '%' . $q . '%')
                              ->orWhere('last_name', 'LIKE', '%' . $q . '%')
                              ->orWhere('phone_number', 'LIKE', '%' . $q . '%');
                      });
            })
            ->paginate(25);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $clients = Client::all();
        $products = Product::all();

        return view('orders.create', compact('clients', 'products'));
    }

    /**
     * Store a new order
     */
    public function store(Request $request)
    {
        $rules = [
            'client_id' => ['required', Rule::exists('clients', 'id')],
            'payment_type' => ['required', Rule::in(array_map(fn($pt) => $pt->value, PaymentTypeEnums::cases()))],
            'repayment_frequency' => ['required', Rule::in(['weekly', 'monthly'])],
            'repayment_term' => ['required', 'integer', 'min:1'],
            'products' => ['required', 'array'],
            'products.*.id' => ['required', Rule::exists('products', 'id')],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
        ];

        $validated = $request->validate($rules);

        // Calculate totals
        $totalPrice = 0;
        foreach ($validated['products'] as $productData) {
            $product = Product::findOrFail($productData['id']);
            $totalPrice += ($product->price * $productData['quantity']);
        }

        // Create order
        $order = Order::create([
            'client_id' => $validated['client_id'],
            'order_ref' => strtoupper(Str::random(10)),
            'total_price' => $totalPrice,
            'remaining_balance' => $totalPrice,
            'amount_paid' => 0,
            'repayment_frequency' => $validated['repayment_frequency'],
            'repayment_term' => $validated['repayment_term'],
            'payment_type' => $validated['payment_type'],
            'status' => OrderStatusEnums::Pending->value,
            'created_by' => Auth::id(),
        ]);

        // Create order items
        foreach ($validated['products'] as $productData) {
            $product = Product::findOrFail($productData['id']);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $productData['quantity'],
                'total_price' => $product->price * $productData['quantity'],
            ]);
        }

        log_new("Order {$order->order_ref} created for client {$order->client->full_name}");

        return redirect()->route('orders.show', $order->id)->with('success', 'Order created successfully!');
    }

    /**
     * Show single order
     */
    public function show(Order $order)
    {
        $order->load(['client', 'items.product']);
        return view('orders.show', compact('order'));
    }

    /**
     * Update order (basic fields only, not items here)
     */
    public function update(Request $request, Order $order)
    {
        $rules = [
            'status' => ['required', Rule::in(array_map(fn($st) => $st->value, OrderStatusEnums::cases()))],
        ];

        $validated = $request->validate($rules);

        $order->update($validated);

        log_new("Order {$order->order_ref} was updated");

        return redirect()->route('orders.show', $order->id)->with('success', 'Order updated successfully!');
    }

    /**
     * Delete order
     */
    public function destroy(Order $order)
    {
        try {
            log_new("Deleting order {$order->order_ref}");
            $order->delete();

            return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('orders.index')->with('error', 'Order cannot be deleted as it is associated with other records.');
            }
            throw $e;
        }
    }
}
