<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'image', 'show_on_homepage'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
