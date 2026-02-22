<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Governorate extends Model
{
    use HasFactory;

    protected $fillable = ['name_en', 'name_ar', 'default_delivery_price', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'default_delivery_price' => 'decimal:2',
    ];

    public function areas()
    {
        return $this->hasMany(Area::class);
    }
}
