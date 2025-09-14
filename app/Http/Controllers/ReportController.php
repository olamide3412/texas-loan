<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function paymentReports(Request $request)
    {
        // Build the query with filters
        $query = Payment::with(['order', 'user.staff']) // Include staff relationship
            ->when($request->start_date, function ($q) use ($request) {
                $q->whereDate('payment_date', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($q) use ($request) {
                $q->whereDate('payment_date', '<=', $request->end_date);
            })
            ->when($request->payment_method, function ($q) use ($request) {
                $q->where('payment_method', $request->payment_method);
            })
            ->when($request->payment_status, function ($q) use ($request) {
                $q->where('payment_status', $request->payment_status);
            })
            ->when($request->collected_by, function ($q) use ($request) {
                $q->where('collected_by', $request->collected_by);
            })
            ->orderBy('payment_date', 'desc');

        // Handle CSV export
        if ($request->export === 'csv') {
            return $this->exportToCSV($query->get());
        }

        // Get paginated results
        $payments = $query->paginate(25);

        // Get summary statistics
        $summary = [
            'total_income' => $query->sum('amount'),
            'completed_payments' => $query->clone()->where('payment_status', 'completed')->count(),
            'pending_payments' => $query->clone()->where('payment_status', 'pending')->count(),
            'failed_payments' => $query->clone()->where('payment_status', 'failed')->count(),
        ];

        // Get user collection summary with staff relationship
        $userCollections = User::with(['staff']) // Load staff relationship
            ->withCount(['payments as transaction_count'])
            ->withSum('payments as total_collected', 'amount')
            ->having('transaction_count', '>', 0)
            ->when($request->start_date, function ($q) use ($request) {
                $q->whereHas('payments', function ($q) use ($request) {
                    $q->whereDate('payment_date', '>=', $request->start_date);
                });
            })
            ->when($request->end_date, function ($q) use ($request) {
                $q->whereHas('payments', function ($q) use ($request) {
                    $q->whereDate('payment_date', '<=', $request->end_date);
                });
            })
            ->get()
            ->map(function ($user) {
                $user->average_amount = $user->transaction_count > 0
                    ? $user->total_collected / $user->transaction_count
                    : 0;

                // Get the staff name or use a default
                $user->staff_name = $user->staff
                    ? $user->staff->first_name . ' ' . $user->staff->last_name
                    : 'Unknown Staff';

                return $user;
            });

        return inertia('Auth/Reports/Report', [
            'payments' => $payments,
            'summary' => $summary,
            'userCollections' => $userCollections,
            'filters' => $request->only(['start_date', 'end_date', 'payment_method', 'payment_status', 'collected_by'])
        ]);
    }

    private function exportToCSV($payments)
    {
        $filename = 'payments-report-' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV header
        fputcsv($output, [
            'Date', 'Order Reference', 'Transaction Reference', 'Amount',
            'Payment Method', 'Status', 'Collected By', 'Payment Date'
        ]);

        // CSV data
        foreach ($payments as $payment) {
            $collectorName = 'System';
            if ($payment->collector && $payment->collector->staff) {
                $collectorName = $payment->collector->staff->first_name . ' ' . $payment->collector->staff->last_name;
            }

            fputcsv($output, [
                $payment->payment_date,
                $payment->order->order_ref ?? 'N/A',
                $payment->tx_ref ?? 'N/A',
                $payment->amount,
                $payment->payment_method,
                $payment->payment_status,
                $collectorName,
                $payment->created_at
            ]);
        }

        fclose($output);
        exit;
    }
}
