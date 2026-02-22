<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'remaining_quantity',
        'cost_price',
        'total_cost',
        'supplier',
        'invoice_number',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
