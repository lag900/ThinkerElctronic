<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $appends = ['is_on_sale', 'effective_price', 'discount_percentage'];

    protected $fillable = [
        'name',
        'slug',
        'name_ar',
        'sku',
        'description',
        'description_ar',
        'description_en',
        'price',
        'cost_price',
        'purchase_price',
        'stock_quantity',
        'min_stock_alert',
        'image_url',
        'youtube_url',
        'video_provider',
        'video_url',
        'video_path',
        'category_id',
        'sale_price',
        'sale_start_date',
        'sale_end_date',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = \Illuminate\Support\Str::slug($product->name);
            }
        });
        static::updating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = \Illuminate\Support\Str::slug($product->name);
            }
        });
    }

    protected $casts = [
        'sale_start_date' => 'datetime',
        'sale_end_date' => 'datetime',
        'price' => 'float',
        'sale_price' => 'float',
    ];

    public function getIsOnSaleAttribute()
    {
        if (!$this->sale_price) return false;
        
        $now = now();
        
        if ($this->sale_start_date && $now->lt($this->sale_start_date)) return false;
        if ($this->sale_end_date && $now->gt($this->sale_end_date)) return false;
        
        return true;
    }

    public function getEffectivePriceAttribute()
    {
        return $this->is_on_sale ? $this->sale_price : $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->is_on_sale) return 0;
        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

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

    public function batches()
    {
        return $this->hasMany(ProductBatch::class)->orderBy('created_at');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
