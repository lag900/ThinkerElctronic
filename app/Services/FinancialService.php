<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialService
{
    /**
     * Get real net profit for a specific period
     */
    public function getNetProfit($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? Carbon::today()->startOfDay();
        $endDate = $endDate ?? Carbon::today()->endOfDay();

        // Real profit from completed orders (Revenue - COGS - Delivery)
        // Note: In our system, total_profit is (price - cost) * qty.
        // Net Profit from orders = total_profit - any other order specific costs if not included.
        // User formula: total paid orders - FIFO cost - delivery cost
        
        $completedOrders = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('SUM(total) as revenue'),
                DB::raw('SUM(total_cost) as cogs'),
                DB::raw('SUM(delivery_price) as shipping_cost')
            )
            ->first();

        $grossProfit = ($completedOrders->revenue ?? 0) - ($completedOrders->cogs ?? 0) - ($completedOrders->shipping_cost ?? 0);

        // Sum of expenses in the same period
        $totalExpenses = Expense::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('amount');

        return [
            'revenue' => (float)($completedOrders->revenue ?? 0),
            'cogs' => (float)($completedOrders->cogs ?? 0),
            'shipping_cost' => (float)($completedOrders->shipping_cost ?? 0),
            'expenses' => (float)$totalExpenses,
            'net_profit' => (float)($grossProfit - $totalExpenses)
        ];
    }

    public function getTodayProfit()
    {
        return $this->getNetProfit(Carbon::today()->startOfDay(), Carbon::today()->endOfDay());
    }

    public function getMonthProfit()
    {
        return $this->getNetProfit(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
    }

    /**
     * PENDING PROFIT (orders not completed)
     */
    public function getPendingProfit()
    {
        $pending = Order::whereIn('status', ['pending', 'processing'])
            ->select(
                DB::raw('SUM(total) as revenue'),
                DB::raw('SUM(total_cost) as cogs'),
                DB::raw('SUM(delivery_price) as shipping_cost')
            )
            ->first();

        return (float)(($pending->revenue ?? 0) - ($pending->cogs ?? 0) - ($pending->shipping_cost ?? 0));
    }

    /**
     * EXPECTED REVENUE/PROFIT
     */
    public function getExpectedAnalytics()
    {
        return [
            'pending_revenue' => (float)Order::whereIn('status', ['pending', 'processing'])->sum('total'),
            'pending_profit' => $this->getPendingProfit(),
            'cod_expected' => (float)Order::whereIn('status', ['pending', 'processing'])
                ->where('payment_method', 'cod')
                ->sum('total'),
            'online_paid' => (float)Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])
                ->sum('total'),
        ];
    }

    public function getStatusBreakdown()
    {
        return Order::select('status', 
                DB::raw('count(*) as count'), 
                DB::raw('COALESCE(SUM(total), 0) as total_money'),
                DB::raw('COALESCE(SUM(total_profit), 0) as gross_profit'),
                DB::raw('payment_method')
            )
            ->groupBy('status', 'payment_method')
            ->get();
    }
}
