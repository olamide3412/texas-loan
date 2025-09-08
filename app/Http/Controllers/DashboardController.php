<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnums;
use App\Enums\RoleEnums;
use App\Models\Log;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $users = User::latest()->limit(25)->get();
        $usersCount = User::count();
        $adminsCount = User::where('role', RoleEnums::Administrator->value)->count();
        $superAdminsCount = User::where('role', RoleEnums::SuperAdministrator->value)->count();
        $activeResponseCount = 0; // WhatsAppResponse::where('is_active', true)->count();
        $inactiveResponseCount = 0; //WhatsAppResponse::where('is_active', false)->count();
        $exchangeRateCount = 0; // ExchangeRate::count();

        $logsCount = Log::count();

        //dd($users->toArray());
        return inertia('Auth/Dashboard', [
            'counts' => [
                'users' => $usersCount,
                'admins' => $adminsCount,
                'superAdmins' => $superAdminsCount,
                'logs' => $logsCount,

            ],
            'orderStats' => [
                'pending_orders' => Order::where('status', OrderStatusEnums::Pending->value)->count(),
                'processing_orders' => Order::where('status', OrderStatusEnums::Processing->value)->count(),
                'completed_orders' => Order::where('status', OrderStatusEnums::Completed->value)->count(),
                'rejected_orders' => Order::where('status', OrderStatusEnums::Rejected->value)->count(), // If you have rejected status
                'cancelled_orders' => Order::where('status', OrderStatusEnums::Cancelled->value)->count(),
            ],
            'paymentStats' => [
                'today' => Payment::whereDate('payment_date', Carbon::today())
                         ->where('payment_status', 'success')
                         ->sum('amount'),
                'yesterday' => Payment::whereDate('payment_date', Carbon::yesterday())
                                    ->where('payment_status', 'success')
                                    ->sum('amount'),
                'this_month' => Payment::whereMonth('payment_date', Carbon::now()->month)
                                    ->whereYear('payment_date', Carbon::now()->year)
                                    ->where('payment_status', 'success')
                                    ->sum('amount'),
                'this_year' => Payment::whereYear('payment_date', Carbon::now()->year)
                                    ->where('payment_status', 'success')
                                    ->sum('amount'),
                'total_revenue' => Payment::where('payment_status', 'success')->sum('amount'),
                'successful_payments' => Payment::where('payment_status', 'success')->count(),
                'failed_payments' => Payment::where('payment_status', 'failed')->count(),
            ]
        ]);
    }
}
