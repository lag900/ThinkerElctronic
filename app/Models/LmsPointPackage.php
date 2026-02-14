<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsPointPackage extends Model
{
    protected $fillable = ['points', 'price', 'payment_link', 'order'];
}
