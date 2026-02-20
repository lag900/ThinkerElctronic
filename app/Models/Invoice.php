<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'order_id',
        'invoice_number',
        'subtotal',
        'shipping',
        'total',
        'currency',
        'status',
        'pdf_path',
        'secure_token'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
