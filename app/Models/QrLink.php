<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrLink extends Model
{
    protected $fillable = [
        'title',
        'platform',
        'url',
        'icon',
        'custom_icon',
        'color',
        'sort_order',
        'is_active',
        'clicks',
        'last_clicked_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'clicks' => 'integer',
        'last_clicked_at' => 'datetime',
    ];
}
