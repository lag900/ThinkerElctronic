<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class InventoryReportController extends Controller
{
    public function dashboard()
    {
        // Total stock value (at cost)
        $totalStockValue = ProductBatch::where('remaining_quantity', '>', 0)
            ->sum(DB::raw('remaining_quantity * cost_price'));

        // Revenue and Profit
        $stats = Order::where('status', 'completed')
            ->select(
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('SUM(total_profit) as total_profit'),
                DB::raw('SUM(total_cost) as total_cost')
            )->first();

        // Pending Profit
        $pendingProfit = Order::where('status', 'pending')->sum('total_profit');

        // Losses (items where sell price < cost price)
        $totalLoss = OrderItem::whereRaw('profit < 0')->sum('profit');

        // Low Stock Alert
        $lowStockProducts = Product::where('stock_quantity', '<=', DB::raw('min_stock_alert'))
            ->limit(10)
            ->get();

        // Top Selling Products
        $topSelling = OrderItem::with('product')
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(profit) as total_profit'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Inventory/Dashboard', [
            'metrics' => [
                'total_stock_value' => (float)$totalStockValue,
                'total_revenue' => (float)($stats->total_revenue ?? 0),
                'total_profit' => (float)($stats->total_profit ?? 0),
                'pending_profit' => (float)$pendingProfit,
                'total_loss' => abs((float)$totalLoss),
            ],
            'lowStockProducts' => $lowStockProducts,
            'topSelling' => $topSelling,
        ]);
    }

    public function profit(Request $request)
    {
        $range = $request->query('range', 'month'); // day, week, month, year
        
        $query = Order::where('status', 'completed');

        if ($range === 'day') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($range === 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($range === 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        } elseif ($range === 'year') {
            $query->whereYear('created_at', Carbon::now()->year);
        }

        $report = $query->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total) as revenue'),
            DB::raw('SUM(total_cost) as cost'),
            DB::raw('SUM(total_profit) as profit')
        )
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->get();

        return Inertia::render('Admin/Reports/Profit', [
            'report' => $report,
            'range' => $range
        ]);
    }

    public function valuation()
    {
        $valuation = Product::with(['batches' => function($q) {
            $q->where('remaining_quantity', '>', 0);
        }])
        ->get()
        ->map(function($product) {
            $stockValue = $product->batches->sum(function($batch) {
                return $batch->remaining_quantity * $batch->cost_price;
            });

            return [
                'id' => $product->id,
                'name' => $product->name,
                'current_stock' => $product->stock_quantity,
                'valuation' => $stockValue,
                'batches_count' => $product->batches->count()
            ];
        })
        ->filter(fn($p) => $p['current_stock'] > 0)
        ->values();

        return Inertia::render('Admin/Reports/Valuation', [
            'valuation' => $valuation,
            'total_valuation' => $valuation->sum('valuation')
        ]);
    }
}
