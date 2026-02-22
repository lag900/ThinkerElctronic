<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\InventoryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Purchases/Index', [
            'products' => Product::select('id', 'name', 'stock_quantity')->get(),
            'batches' => ProductBatch::with('product')->latest()->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'cost_price' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::findOrFail($validated['product_id']);
            
            ProductBatch::create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'remaining_quantity' => $validated['quantity'],
                'cost_price' => $validated['cost_price'],
                'total_cost' => $validated['quantity'] * $validated['cost_price'],
                'supplier' => $validated['supplier'],
                'invoice_number' => $validated['invoice_number'],
            ]);

            // Sync product main stock
            $product->increment('stock_quantity', $validated['quantity']);

            // Update product's last cost price for reference
            $product->update([
                'cost_price' => $validated['cost_price'],
                'purchase_price' => $validated['cost_price']
            ]);

            InventoryHistory::create([
                'product_id' => $product->id,
                'quantity_change' => $validated['quantity'],
                'type' => 'entry',
                'description' => "Purchase Entry - Invoice: " . ($validated['invoice_number'] ?? 'N/A'),
            ]);
        });

        return redirect()->back()->with('success', 'Stock batch added successfully using FIFO protocol.');
    }
}
