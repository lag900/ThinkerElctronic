<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Inertia\Inertia;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $employeeId = $request->input('employee_id');

        $orders = Order::with(['customer', 'invoice', 'creator'])
            ->when($search, function ($query, $search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($employeeId, function ($query, $employeeId) {
                $query->where('created_by', $employeeId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Get list of employees who have created orders for the filter
        $creators = \App\Models\User::whereHas('ordersCreated')->get(['id', 'name']);

        return Inertia::render('Admin/Orders', [
            'orders' => $orders,
            'employees' => $creators,
            'filters' => [
                'search' => $search,
                'employee_id' => $employeeId
            ]
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.product', 'invoice', 'creator']);
        
        return Inertia::render('Admin/OrderShow', [
            'order' => $order
        ]);
    }

    public function printInvoice(Order $order)
    {
        $order->load(['customer', 'items.product', 'invoice', 'creator']);
        return view('admin.invoice.print', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,failed'
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        if ($oldStatus !== $newStatus) {
            $order->status = $newStatus;
            
            // Logic: Failed -> Return products to stock automatically
            if ($newStatus === 'failed' && $oldStatus !== 'failed') {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        \App\Services\InventoryService::returnStock($item);
                        
                        // Log inventory change
                        \App\Models\InventoryHistory::create([
                            'product_id' => $item->product->id,
                            'quantity_change' => $item->quantity,
                            'type' => 'restock',
                            'description' => 'Order Failed - FIFO Restock #' . $order->order_number,
                        ]);
                    }
                }
            } 
            
            // If recovering from a failed state, deduct stock again
            if ($oldStatus === 'failed' && $newStatus !== 'failed') {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        // FIFO DEDUCTION
                        \App\Services\InventoryService::deductStockFIFO($item);
                    }
                }
            }

            $order->save();
        }

        return redirect()->back()->with('success', 'Order status updated successfully');
    }

    public function updateItem(Request $request, Order $order, \App\Models\OrderItem $item)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0'
        ]);

        $newQty = (int)$validated['quantity'];
        
        if ($newQty > $item->quantity) {
            return redirect()->back()->with('error', 'Cannot inject unauthorized quantity.');
        }

        $difference = $item->quantity - $newQty;

        if ($difference > 0) {
            // Restore exact missing items back to stock
            if ($item->product) {
                // Return difference to stock
                $dummyItem = clone $item;
                $dummyItem->quantity = $difference;
                \App\Services\InventoryService::returnStock($dummyItem);
            }

            // Financial Rectification Protocol
            // We need to re-calculate the cost of the remaining items.
            // Simplified: we prorate the cost based on original per-item cost.
            $perItemCost = $item->cost / $item->quantity;
            $deductValue = $item->price * $difference;
            $deductCost = $perItemCost * $difference;

            if ($newQty === 0) {
                $item->delete();
            } else {
                $item->update([
                    'quantity' => $newQty,
                    'cost' => $item->cost - $deductCost,
                    'profit' => ($item->price * $newQty) - ($item->cost - $deductCost)
                ]);
            }

            $order->decrement('total', $deductValue);
            $order->decrement('subtotal', $deductValue);
            $order->decrement('total_cost', $deductCost);
            $order->decrement('total_profit', $deductValue - $deductCost);

            if ($order->invoice) {
                $order->invoice->decrement('total', $deductValue);
                $order->invoice->decrement('subtotal', $deductValue);
            }

            if ($order->customer) {
                $order->customer->decrement('total_spent', $deductValue);
            }
        }

        return redirect()->back()->with('success', 'Entity extracted and value recalculated.');
    }
}
