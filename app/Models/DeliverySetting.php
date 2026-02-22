<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliverySetting extends Model
{
    protected $fillable = ['free_delivery_over', 'express_enabled'];

    protected $casts = [
        'free_delivery_over' => 'decimal:2',
        'express_enabled' => 'boolean',
    ];
}
