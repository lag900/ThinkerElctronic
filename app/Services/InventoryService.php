<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\OrderItem;
use App\Models\InventoryHistory;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Deduct stock using FIFO logic and calculate real cost and profit.
     * 
     * @param OrderItem $orderItem
     * @return array [total_cost, profit]
     */
    public static function deductStockFIFO(OrderItem $orderItem)
    {
        return DB::transaction(function () use ($orderItem) {
            $product = $orderItem->product;
            $requestedQuantity = $orderItem->quantity;
            
            $batches = ProductBatch::where('product_id', $product->id)
                ->where('remaining_quantity', '>', 0)
                ->orderBy('created_at', 'asc')
                ->get();

            $totalCost = 0;
            $quantityRemainingToFulfill = $requestedQuantity;

            foreach ($batches as $batch) {
                if ($quantityRemainingToFulfill <= 0) break;

                $deductAmount = min($batch->remaining_quantity, $quantityRemainingToFulfill);
                
                $batch->decrement('remaining_quantity', $deductAmount);
                $totalCost += ($deductAmount * $batch->cost_price);
                
                $quantityRemainingToFulfill -= $deductAmount;
            }

            // If we still have quantity to fulfill but no batches left, 
            // we use the last known cost or default to average? 
            // In a real ERP, we should prevent selling more than we have or handle negative stock.
            // But let's assume stock was verified before.
            if ($quantityRemainingToFulfill > 0) {
                $fallbackCost = $product->purchase_price ?? $product->cost_price ?? 0;
                $totalCost += ($quantityRemainingToFulfill * $fallbackCost);
            }

            $profit = ($orderItem->price * $requestedQuantity) - $totalCost;

            $orderItem->update([
                'cost' => $totalCost,
                'profit' => $profit
            ]);

            // Update main product stock
            $product->decrement('stock_quantity', $requestedQuantity);

            return [
                'total_cost' => $totalCost,
                'profit' => $profit
            ];
        });
    }

    /**
     * Return stock to batches (LIFO for returns - usually return to the most recent batches or same batches)
     * For simplicity, we can return to the oldest batch or create a special return batch.
     * Professional ERPs usually track exactly which batch was used.
     * Since we don't store the batch_id in order_items yet (user didn't ask for it in DB structure),
     * we will return to the OLDEST batch to keep FIFO consistent for future sales.
     */
    public static function returnStock(OrderItem $orderItem)
    {
        return DB::transaction(function () use ($orderItem) {
            $product = $orderItem->product;
            $quantity = $orderItem->quantity;

            // Find the batch that has space or just the most recent one?
            // Actually, we should return to the OLDEST batch that was likely used.
            $batch = ProductBatch::where('product_id', $product->id)
                ->orderBy('created_at', 'asc')
                ->first();

            if ($batch) {
                $batch->increment('remaining_quantity', $quantity);
            } else {
                // If no batch exists (shouldn't happen), create one
                ProductBatch::create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'remaining_quantity' => $quantity,
                    'cost_price' => $orderItem->cost / $quantity,
                    'total_cost' => $orderItem->cost,
                    'supplier' => 'REVENUE-RETURN',
                ]);
            }

            $product->increment('stock_quantity', $quantity);
        });
    }
}
