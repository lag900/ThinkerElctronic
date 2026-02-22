<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Models\Area;
use Illuminate\Http\Request;

class DeliveryApiController extends Controller
{
    public function getAreas(Governorate $governorate)
    {
        return response()->json($governorate->areas()->where('is_active', true)->get());
    }

    public function getPrice(Area $area)
    {
        $area->load('governorate');
        
        $price = (float) $area->delivery_price;
        if ($price <= 0 && $area->governorate && $area->governorate->default_delivery_price > 0) {
            $price = (float) $area->governorate->default_delivery_price;
        }

        return response()->json([
            'delivery_price' => $price,
            'express_price' => (float) $area->express_price,
            'delivery_days' => $area->delivery_days,
            'governorate_id' => $area->governorate_id,
            'governorate_name_en' => $area->governorate->name_en,
            'governorate_name_ar' => $area->governorate->name_ar,
        ]);
    }
}
