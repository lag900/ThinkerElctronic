<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        if (!request()->user()->isSuperAdmin()) {
            abort(403, 'Access denied. Super Admin protocol required.');
        }

        $now = Carbon::now();
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Revenue over time (Last 30 days)
        $revenueData = DB::table('orders')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'))
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Orders over time (Last 30 days)
        $ordersCountData = DB::table('orders')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top selling products
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Customer acquisition (New customers per day)
        $customerAcquisition = DB::table('customers')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Category distribution
        $categorySales = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(order_items.quantity) as count'))
            ->groupBy('categories.id', 'categories.name')
            ->get();

        $stats = [
            'total_revenue' => Order::where('status', 'completed')->sum('total'),
            'total_orders' => Order::count(),
            'total_customers' => Customer::count(),
            'avg_order_value' => Order::where('status', 'completed')->avg('total') ?: 0,
            'conversion_rate' => 3.2, // Placeholder
        ];

        return Inertia::render('Admin/Analytics', [
            'revenueData' => $revenueData,
            'ordersCountData' => $ordersCountData,
            'topProducts' => $topProducts,
            'customerAcquisition' => $customerAcquisition,
            'categorySales' => $categorySales,
            'stats' => $stats,
        ]);
    }
}
