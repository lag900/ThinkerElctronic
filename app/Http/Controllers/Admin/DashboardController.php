<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Orders metrics
        $salesToday = Order::whereDate('created_at', $today->toDateString())->where('status', 'completed')->sum('total');
        $ordersTodayCount = Order::whereDate('created_at', $today->toDateString())->count();
        $ordersMonthCount = Order::where('created_at', '>=', $startOfMonth)->count();
        $avgOrderValue = $ordersMonthCount > 0 ? Order::where('created_at', '>=', $startOfMonth)->sum('total') / $ordersMonthCount : 0;

        // Revenue & Profit (Month)
        $revenueMonth = Invoice::where('created_at', '>=', $startOfMonth)->sum('amount_paid');
        $expensesMonth = Expense::where('date', '>=', $startOfMonth)->sum('amount');
        
        // Revenue & Profit (Today)
        $expensesToday = Expense::whereDate('date', $today->toDateString())->sum('amount');
        $costToday = OrderItem::whereHas('order', function($q) use ($today) {
            $q->whereDate('created_at', $today->toDateString())->where('status', 'completed');
        })->sum('cost');
        $netProfitToday = $salesToday - $costToday - $expensesToday;
        
        $cashInHandToday = Invoice::whereDate('updated_at', $today->toDateString())->where('amount_paid', '>', 0)->sum('amount_paid') - $expensesToday;
        $pendingPayments = Invoice::where('status', '!=', 'paid')->sum(DB::raw('total - amount_paid'));

        // Inventory Intelligence
        $stockValueCost = Product::sum(DB::raw('stock_quantity * cost_price'));
        $stockValueSell = Product::sum(DB::raw('stock_quantity * price'));
        $expectedProfit = $stockValueSell - $stockValueCost;
        $lowStockProductsCount = Product::whereColumn('stock_quantity', '<=', 'min_stock_alert')->where('stock_quantity', '>', 0)->count();
        $outOfStockCount = Product::where('stock_quantity', '<=', 0)->count();

        // Customer Intelligence
        $topCustomers = DB::table('orders')
            ->select('customer_id', DB::raw('SUM(total) as total_spent'), DB::raw('COUNT(id) as orders_count'))
            ->whereNotNull('customer_id')
            ->groupBy('customer_id')
            ->orderByDesc('total_spent')
            ->take(5)
            ->get();
            
        foreach($topCustomers as $tc) {
            $tc->details = Customer::find($tc->customer_id);
        }
        
        $mostActiveCustomer = $topCustomers->first() ?? null;
        $customersOweMoneyCount = Invoice::where('status', '!=', 'paid')
            ->whereColumn('amount_paid', '<', 'total')
            ->whereNotNull('customer_id')
            ->distinct('customer_id')
            ->count('customer_id');
        $newCustomersMonthCount = Customer::where('created_at', '>=', $startOfMonth)->count();

        $stats = [
            'total_sales_today' => request()->user()->hasPermission('view_money') ? $salesToday : null,
            'net_profit_today' => request()->user()->hasPermission('view_profit') && request()->user()->isSuperAdmin() ? $netProfitToday : null,
            'monthly_revenue' => request()->user()->hasPermission('view_money') ? $revenueMonth : null,
            'monthly_net_profit' => request()->user()->hasPermission('view_profit') && request()->user()->isSuperAdmin() ? ($revenueMonth - $expensesMonth) : null,
            'total_expenses_month' => request()->user()->hasPermission('view_expenses') ? $expensesMonth : null,
            'cash_in_hand_today' => request()->user()->hasPermission('view_money') ? $cashInHandToday : null,
            'pending_payments' => request()->user()->hasPermission('view_money') ? $pendingPayments : null,
            
            'orders_today' => $ordersTodayCount,
            'orders_month' => $ordersMonthCount,
            'average_order_value' => $avgOrderValue,
            
            'stock_value_cost' => request()->user()->hasPermission('view_money') ? $stockValueCost : null,
            'stock_value_sell' => request()->user()->hasPermission('view_money') ? $stockValueSell : null,
            'expected_inventory_profit' => request()->user()->hasPermission('view_profit') && request()->user()->isSuperAdmin() ? $expectedProfit : null,
            'low_stock_count' => $lowStockProductsCount,
            'out_of_stock_count' => $outOfStockCount,
            
            'customers_owe_money_count' => $customersOweMoneyCount,
            'new_customers_month' => $newCustomersMonthCount,
        ];

        // Charts Payload (Daily data for the last 14 days)
        $chartData = [];
        if (request()->user()->hasPermission('view_analytics')) {
            $last14Days = collect(range(13, 0))->map(function($days) {
                return Carbon::today()->subDays($days)->format('Y-m-d');
            });
            
            $dailySales = Order::where('created_at', '>=', Carbon::now()->subDays(14))
                ->where('status', 'completed')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as amount'))
                ->groupBy('date')->get()->pluck('amount', 'date')->toArray();
                
            $dailyExpenses = Expense::where('date', '>=', Carbon::now()->subDays(14))
                ->select('date', DB::raw('SUM(amount) as amount'))
                ->groupBy('date')->get()->pluck('amount', 'date')->toArray();
                
            $chartData = $last14Days->map(function($date) use ($dailySales, $dailyExpenses) {
                $sales = $dailySales[$date] ?? 0;
                $expenses = $dailyExpenses[$date] ?? 0;
                
                // Estimate cost for profit chart by joining OrderItem and Order. This is intensive, we can approximate profit safely for charting purposes or run specifically.
                $cost = OrderItem::whereHas('order', function($q) use ($date) {
                    $q->whereDate('created_at', $date)->where('status', 'completed');
                })->sum('cost');
                
                $profit = $sales - $cost - $expenses;

                return [
                    'date' => $date,
                    'sales' => $sales,
                    'expenses' => $expenses,
                    'profit' => $profit
                ];
            });
        }

        // Top Selling Products
        $topSelling = DB::table('order_items')
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get()
            ->map(function($item) {
                $item->product = Product::find($item->product_id);
                return $item;
            });
            
        // Worst Selling Products
        $worstSelling = Product::withCount(['orderItems as total_sold' => function($query) {
                $query->select(DB::raw('COALESCE(SUM(quantity), 0)'));
            }])
            ->orderBy('total_sold', 'asc')
            ->take(5)
            ->get();

        $categories = Category::withCount('products')->latest()->get();
        $recentInvoices = Invoice::with('customer')->latest()->take(5)->get();
        
        $lowStockList = Product::whereColumn('stock_quantity', '<=', 'min_stock_alert')
            ->orderBy('stock_quantity', 'asc')
            ->take(10)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'chartData' => $chartData,
            'topProducts' => $topSelling,
            'worstProducts' => $worstSelling,
            'topCustomers' => $topCustomers,
            'mostActiveCustomer' => $mostActiveCustomer,
            'categories' => $categories,
            'recentInvoices' => $recentInvoices,
            'lowStockList' => $lowStockList,
        ]);
    }
}
