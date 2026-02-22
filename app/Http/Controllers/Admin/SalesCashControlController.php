<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SalesCashControlController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAccess($request->user());

        [$startDate, $endDate, $filter] = $this->parseDateRange($request);

        $summary = $this->calculateSummary($startDate, $endDate, $filter, $request->user());
        $paymentMethods = $this->getPaymentMethodsSummary($startDate, $endDate);
        $employeeStats = $this->getEmployeePerformance($startDate, $endDate);

        return Inertia::render('Admin/SalesCashControl', [
            'summary' => $summary,
            'employeeStats' => $employeeStats,
            'paymentMethods' => $paymentMethods
        ]);
    }

    private function authorizeAccess($user)
    {
        if (!$user->hasPermission('view_reports') && !$user->hasPermission('view_financial_reports')) {
            abort(403, 'Unauthorized access to reporting protocols.');
        }
    }

    private function parseDateRange(Request $request)
    {
        $filter = $request->query('date_range', 'today');
        $startDate = Carbon::today();
        $endDate = Carbon::today()->endOfDay();

        if ($filter === 'this_week') {
            $startDate = Carbon::now()->startOfWeek();
        } elseif ($filter === 'this_month') {
            $startDate = Carbon::now()->startOfMonth();
        } elseif ($filter === 'custom') {
            $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : Carbon::today();
            $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : Carbon::today()->endOfDay();
        }

        return [$startDate, $endDate, $filter];
    }

    private function calculateSummary($startDate, $endDate, $filter, $user)
    {
        $ordersQuery = Order::whereBetween('created_at', [$startDate, $endDate])->where('status', 'completed');
        
        $totalRevenueSum = (clone $ordersQuery)->sum('total');
        $onlineSalesTotal = (clone $ordersQuery)->where('payment_method', '!=', 'cash')->sum('total');
        $posSalesTotal = (clone $ordersQuery)->where('payment_method', 'cash')->sum('total');

        $salesToday = Order::whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])
                            ->where('status', 'completed')->sum('total');

        $totalExpensesSum = Expense::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->sum('amount');
        
        $canViewProfit = $user->hasPermission('view_profit') && $user->isSuperAdmin();
        $netProfit = $canViewProfit ? ($totalRevenueSum - $totalExpensesSum) : null;

        return [
            'total_sales' => $totalRevenueSum,
            'total_sales_today' => $salesToday,
            'online_sales' => $onlineSalesTotal,
            'pos_sales' => $posSalesTotal,
            'net_profit' => $netProfit,
            'can_view_profit' => $canViewProfit,
            'filter' => $filter,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
        ];
    }

    private function getPaymentMethodsSummary($startDate, $endDate)
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select('payment_method', DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();
    }

    private function getEmployeePerformance($startDate, $endDate)
    {
        $orderStats = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'created_by',
                DB::raw('COUNT(*) as invoices_count'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN total ELSE 0 END) as total_sales'),
                DB::raw('SUM(CASE WHEN status = "completed" AND payment_method = "cash" THEN total ELSE 0 END) as cash_sales'),
                DB::raw('SUM(CASE WHEN status = "completed" AND payment_method != "cash" THEN total ELSE 0 END) as online_sales'),
                DB::raw('SUM(CASE WHEN status = "refunded" THEN total ELSE 0 END) as refunds'),
                DB::raw('SUM(CASE WHEN status = "refunded" AND payment_method = "cash" THEN total ELSE 0 END) as refunded_cash')
            )
            ->groupBy('created_by')
            ->get()
            ->keyBy('created_by');

        $expenseStats = Expense::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->select('user_id', DB::raw('SUM(amount) as total_expenses'))
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $userIds = $orderStats->keys()->merge($expenseStats->keys())->unique();
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        $stats = [];
        foreach ($userIds as $userId) {
            $user = $users->get($userId);
            if (!$user) continue;

            $o = $orderStats->get($userId);
            $e = $expenseStats->get($userId);

            $cashSales = $o ? $o->cash_sales : 0;
            $refundedCash = $o ? $o->refunded_cash : 0;
            $userExp = $e ? $e->total_expenses : 0;

            $stats[] = [
                'id' => $user->id,
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'invoices_count' => $o ? (int)$o->invoices_count : 0,
                'total_sales' => $o ? (float)$o->total_sales : 0,
                'cash_sales' => $cashSales,
                'online_sales' => $o ? (float)$o->online_sales : 0,
                'refunds' => $o ? (float)$o->refunds : 0,
                'expenses' => $userExp,
                'net_cash' => (float)($cashSales - $refundedCash - $userExp),
            ];
        }

        return collect($stats)->sortByDesc('total_sales')->values()->all();
    }

}
