<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\InventoryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;

class OrderController extends Controller
{
    public function checkout()
    {
        $customer = null;
        if (Auth::check()) {
            $customer = Customer::where('user_id', Auth::id())->first();
        }

        return Inertia::render('Checkout', [
            'savedCustomer' => $customer,
            'governorates' => \App\Models\Governorate::where('is_active', true)->get(),
            'deliverySettings' => \App\Models\DeliverySetting::first(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
            'governorate_id' => 'required|exists:governorates,id',
            'area_id' => 'required|exists:areas,id',
            'use_express' => 'nullable|boolean',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            // SMART CUSTOMER SYSTEM: Track by email OR phone
            $customer = Customer::where('email', $validated['customer_email'])
                ->orWhere('phone', $validated['customer_phone'])
                ->first();

            if ($customer) {
                $customer->update([
                    'name' => $validated['customer_name'],
                    'email' => $validated['customer_email'],
                    'phone' => $validated['customer_phone'],
                    'address' => $validated['shipping_address'],
                    'user_id' => $customer->user_id ?? Auth::id(),
                    'last_order_at' => now(),
                ]);
            } else {
                $customer = Customer::create([
                    'name' => $validated['customer_name'],
                    'email' => $validated['customer_email'],
                    'phone' => $validated['customer_phone'],
                    'address' => $validated['shipping_address'],
                    'user_id' => Auth::id(),
                    'last_order_at' => now(),
                    'total_orders' => 0,
                    'total_spent' => 0,
                ]);
            }

            $area = \App\Models\Area::with('governorate')->findOrFail($validated['area_id']);
            $governorate = $area->governorate;

            // ERP Delivery Logic Priority: Area Price -> Governorate Default -> 0
            $deliveryPrice = (float) $area->delivery_price;
            if ($deliveryPrice <= 0 && $governorate && $governorate->default_delivery_price > 0) {
                $deliveryPrice = (float) $governorate->default_delivery_price;
            }
            
            if (!empty($validated['use_express']) && $area->express_price) {
                $deliveryPrice = (float) $area->express_price;
            }

            $settings = \App\Models\DeliverySetting::first();
            $freeDeliveryOver = $settings ? (float) $settings->free_delivery_over : null;

            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => 'THR-' . strtoupper(Str::random(6)),
                'subtotal' => 0,
                'delivery_price' => 0,
                'discount' => 0,
                'total' => 0,
                'total_cost' => 0,
                'total_profit' => 0,
                'status' => 'pending',
                'shipping_address' => $validated['shipping_address'],
                'city' => $governorate->name_en,
                'province' => $area->name_en,
                'governorate_name' => $governorate->name_en,
                'area_name' => $area->name_en,
                'governorate_id' => $governorate->id,
                'area_id' => $area->id,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            $totalAmount = 0;
            $totalCost = 0;
            $totalProfit = 0;

            foreach ($validated['items'] as $itemData) {
                $product = Product::findOrFail($itemData['id']);
                $quantity = $itemData['quantity'];
                
                // SMART INVENTORY PROTECTION: Verify stock before processing
                if ($product->stock_quantity < $quantity) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items' => ["Product {$product->name} is out of stock or insufficient quantity available."]
                    ]);
                }

                $price = $product->discount_price ?? $product->price;

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    // Initial cost/profit will be updated by service
                    'cost' => 0,
                    'profit' => 0,
                ]);

                // FIFO DEDUCTION & REAL PROFIT
                $stats = \App\Services\InventoryService::deductStockFIFO($orderItem);

                InventoryHistory::create([
                    'product_id' => $product->id,
                    'quantity_change' => -$quantity,
                    'type' => 'sale',
                    'description' => 'Protocol Execution #' . $order->order_number,
                ]);

                $totalAmount += $price * $quantity;
                $totalCost += $stats['total_cost'];
                $totalProfit += $stats['profit'];
            }

            // ERP PROTOCOL: Use fetched delivery price strictly from database
            // No threshold override allowed per Rule 8

            $order->update([
                'subtotal' => $totalAmount,
                'delivery_price' => $deliveryPrice,
                'total' => $totalAmount + $deliveryPrice,
                'final_total' => $totalAmount + $deliveryPrice,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
            ]);

            // Build Customer History
            $customer->increment('total_spent', $totalAmount);
            $customer->increment('total_orders', 1);

            session()->forget('cart');

            return redirect()->route('thank.you', ['id' => $order->id]);
        });
    }

    public function thankYou($id)
    {
        $order = Order::with(['items.product', 'customer'])->findOrFail($id);
        
        return Inertia::render('OrderSuccess', [
            'order' => $order
        ]);
    }

    public function downloadInvoice($order_number)
    {
        $order = Order::with(['items.product', 'customer', 'invoice'])->where('order_number', $order_number)->firstOrFail();
        
        $invoice = $order->invoice;

        if (!$invoice) {
            $invoice = \App\Models\Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => \App\Services\InvoiceNumberGenerator::generate(),
                'subtotal' => $order->subtotal,
                'shipping' => $order->delivery_price ?? 0,
                'total' => $order->total,
                'currency' => 'EGP',
                'status' => 'paid',
                'secure_token' => Str::random(32),
            ]);
            
            // We could trigger PDF generation here, but the download logic handles missing files
        }

        return redirect()->route('admin.invoices.download', $invoice->secure_token);
    }
}
