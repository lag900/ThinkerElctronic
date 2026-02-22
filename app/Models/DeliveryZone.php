<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'governorate_name', 
        'zone_name',
        'delivery_price', 
        'estimated_days', 
        'status', 
        'express_price'
    ];

    protected $casts = [
        'delivery_price' => 'decimal:2',
        'express_price' => 'decimal:2',
    ];
}
