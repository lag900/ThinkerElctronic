<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'order_id',
        'customer_id',
        'invoice_number',
        'subtotal',
        'shipping',
        'total',
        'amount_paid',
        'currency',
        'status',
        'notes',
        'pdf_path',
        'secure_token'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
