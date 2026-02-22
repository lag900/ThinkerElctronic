<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ["customer_id", "created_by", "order_number", "subtotal", "delivery_price", "discount", "total", "final_total", "total_cost", "total_profit", "status", "shipping_address", "city", "province", "payment_method", "payment_status", "notes", "area_id", "governorate_id", "governorate_name", "area_name"];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
