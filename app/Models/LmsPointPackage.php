<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsPointPackage extends Model
{
    protected $fillable = ['name', 'points', 'price', 'payment_link', 'active', 'sort_order'];
}
