<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ActivityLog extends Model
    {
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'payload',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'payload' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
