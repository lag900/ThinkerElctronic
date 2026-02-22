<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Governorate;
use App\Models\Area;
use App\Models\DeliverySetting;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        // Global Settings
        DeliverySetting::create([
            'free_delivery_over' => 500,
            'express_enabled' => true
        ]);

        // Cairo
        $cairo = Governorate::create(['name_en' => 'Cairo', 'name_ar' => 'القاهرة']);
        Area::create([
            'governorate_id' => $cairo->id,
            'name_en' => 'Maadi',
            'name_ar' => 'المعادي',
            'delivery_price' => 50,
            'express_price' => 80,
            'delivery_days' => '1-2 Days'
        ]);
        Area::create([
            'governorate_id' => $cairo->id,
            'name_en' => 'Nasr City',
            'name_ar' => 'مدينة نصر',
            'delivery_price' => 45,
            'express_price' => 70,
            'delivery_days' => '1-2 Days'
        ]);

        // Alexandria
        $alex = Governorate::create(['name_en' => 'Alexandria', 'name_ar' => 'الإسكندرية']);
        Area::create([
            'governorate_id' => $alex->id,
            'name_en' => 'Sidi Gaber',
            'name_ar' => 'سيدي جابر',
            'delivery_price' => 30,
            'express_price' => 50,
            'delivery_days' => 'Same Day'
        ]);
        Area::create([
            'governorate_id' => $alex->id,
            'name_en' => 'Borg El Arab',
            'name_ar' => 'برج العرب',
            'delivery_price' => 20,
            'express_price' => 40,
            'delivery_days' => 'Same Day'
        ]);
    }
}
