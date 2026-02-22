<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PosController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Pos/Index', [
            'products' => Product::where('is_active', true)->where('stock_quantity', '>', 0)->get(),
            'initial_customers' => Customer::latest()->limit(10)->get(),
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        return Product::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%")
                  ->orWhere('id', 'LIKE', "%{$query}%");
            })
            ->get();
    }

    public function searchCustomers(Request $request)
    {
        $query = $request->input('q');
        return Customer::where('name', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();
    }

    public function storeCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        $phone = $validated['phone'] ?? '0000';

        // Smart find or create for Walk-in or same phone
        $customer = Customer::where('phone', $phone)->first();
        
        if (!$customer) {
            $customer = Customer::create([
                'name' => $validated['name'],
                'phone' => $phone,
                'notes' => $validated['notes'] ?? '',
                'email' => $validated['email'] ?? (Str::random(10) . '@thinker.com'),
            ]);
        }

        return response()->json($customer);
    }

    public function customerStats($id)
    {
        $customer = Customer::findOrFail($id);
        
        $totalSpent = Order::where('customer_id', $id)->sum('total');
        $ordersCount = Order::where('customer_id', $id)->count();
        $lastOrder = Order::where('customer_id', $id)->latest()->first();
        
        // Debt calculation: Sum of (total - amount_paid) from all invoices
        $debt = Invoice::where('customer_id', $id)->get()->sum(function($invoice) {
            return $invoice->total - $invoice->amount_paid;
        });

        // total paid
        $totalPaid = Invoice::where('customer_id', $id)->sum('amount_paid');

        return response()->json([
            'name' => $customer->name,
            'total_spent' => $totalSpent,
            'orders_count' => $ordersCount,
            'last_order_at' => $lastOrder ? $lastOrder->created_at->diffForHumans() : 'Never',
            'total_paid' => $totalPaid,
            'debt' => max(0, $debt),
            'notes' => $customer->notes,
            'spending_badge' => $this->getSpendingBadge($totalSpent)
        ]);
    }

    public function customerHistory($id)
    {
        $customer = Customer::findOrFail($id);
        $invoices = Invoice::where('customer_id', $id)
            ->latest()
            ->get()
            ->map(function($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'total' => $invoice->total,
                    'status' => $invoice->status,
                    'created_at' => $invoice->created_at->format('Y-m-d H:i'),
                ];
            });

        return response()->json([
            'customer' => $customer,
            'invoices' => $invoices
        ]);
    }

    private function getSpendingBadge($amount)
    {
        if ($amount > 5000) return 'VIP';
        if ($amount > 1000) return 'Regular';
        return 'New';
    }

    public function generateInvoice(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'customer_id' => 'required|exists:customers,id',
            'payment_method' => 'required|string',
            'amount_paid' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function() use ($request) {
            $totalSubtotal = 0;
            $totalCost = 0;
            $totalProfit = 0;
            $items = $request->items;
            
            // Create Order first to get ID
            $order = Order::create([
                'customer_id' => $request->customer_id,
                'created_by' => \Illuminate\Support\Facades\Auth::id(),
                'order_number' => 'POS-' . strtoupper(Str::random(8)),
                'subtotal' => 0,
                'total' => 0, 
                'total_cost' => 0,
                'total_profit' => 0,
                'status' => 'completed',
                'payment_status' => 'paid', // POS is usually paid immediately
                'payment_method' => $request->payment_method,
                'discount' => $request->discount ?? 0,
            ]);

            foreach ($items as $item) {
                $product = Product::findOrFail($item['id']);
                
                // Check stock
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                $price = $item['price'];
                $subtotal = $price * $item['quantity'];
                
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'cost' => 0,
                    'profit' => 0,
                ]);

                // FIFO DEDUCTION & REAL PROFIT
                $stats = \App\Services\InventoryService::deductStockFIFO($orderItem);

                $totalSubtotal += $subtotal;
                $totalCost += $stats['total_cost'];
                $totalProfit += $stats['profit'];
            }

            $total = $totalSubtotal - ($request->discount ?? 0);
            
            // Adjust profit for overall order discount (proportionally usually, but here we just deduct from total profit)
            $finalProfit = $totalProfit - ($request->discount ?? 0);

            $order->update([
                'subtotal' => $totalSubtotal,
                'total' => $total,
                'total_cost' => $totalCost,
                'total_profit' => $finalProfit,
                'payment_status' => $request->amount_paid >= $total ? 'paid' : 'partial',
            ]);

            // Create Invoice
            $invoice = Invoice::create([
                'order_id' => $order->id,
                'customer_id' => $request->customer_id ?? null,
                'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'subtotal' => $totalSubtotal,
                'total' => $total,
                'status' => $request->amount_paid >= $total ? 'paid' : 'pending',
                'amount_paid' => $request->amount_paid,
                'secure_token' => Str::random(32),
            ]);

            return response()->json([
                'success' => true,
                'invoice' => $invoice,
                'order' => $order
            ]);
        });
    }
}
