<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
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

        $stats = [
            'total_sales_today' => Order::whereDate('created_at', $today)->sum('total'),
            'monthly_revenue' => Order::where('created_at', '>=', $startOfMonth)->sum('total'),
            'orders_count' => Order::where('created_at', '>=', $startOfMonth)->count(),
            'new_customers' => User::where('role', 'customer')->where('created_at', '>=', $startOfMonth)->count(),
            'low_stock_products' => Product::where('stock_quantity', '<', 10)->where('stock_quantity', '>', 0)->count(),
            'out_of_stock_products' => Product::where('stock_quantity', '<=', 0)->count(),
        ];

        // Calculated Profits (Revenue - Cost)
        $stats['net_profit'] = Order::where('created_at', '>=', $startOfMonth)->sum('total_profit');
        $stats['expenses'] = Order::where('created_at', '>=', $startOfMonth)->sum('total_cost');

        // Analytics Data (Last 7 days)
        $analytics = DB::table('orders')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topProducts = Product::withCount(['specs' => function($q) { $q->where('label', 'Sold'); }]) // Heuristic
            ->take(5)
            ->get();

        $categories = Category::withCount('products')->latest()->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'analytics' => $analytics,
            'topProducts' => $topProducts,
            'categories' => $categories,
        ]);
    }
}
