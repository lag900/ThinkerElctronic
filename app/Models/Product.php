<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'name_ar',
        'sku',
        'description',
        'description_ar',
        'description_en',
        'price',
        'cost_price',
        'stock_quantity',
        'image_url',
        'youtube_url',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function specs()
    {
        return $this->hasMany(ProductSpec::class)->orderBy('sort');
    }
    
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    
    public function codes()
    {
        return $this->hasMany(ProductCode::class);
    }
}
