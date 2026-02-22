<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'governorate_id', 
        'name_en', 
        'name_ar', 
        'delivery_price', 
        'express_price', 
        'delivery_days', 
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'delivery_price' => 'decimal:2',
        'express_price' => 'decimal:2',
    ];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }
}
