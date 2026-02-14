<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::where('name', 'Electronics')->first();
        $software = Category::where('name', 'Software')->first();
        $printing = Category::where('name', '3D Printing')->first();
        $systems = Category::where('name', 'Smart Systems')->first();

        Product::create([
            'name' => 'Thinker Smart Speaker S1',
            'description' => 'A futuristic smart speaker with high-fidelity sound and native AI integration.',
            'price' => 2999.00,
            'image_url' => '/images/products/speaker.png',
            'category_id' => $electronics->id,
        ]);

        Product::create([
            'name' => 'Thinker Watch Pro',
            'description' => 'The ultimate companion for your digital life. Sleek, powerful, and intelligent.',
            'price' => 4999.00,
            'image_url' => '/images/products/watch.png',
            'category_id' => $electronics->id,
        ]);

        Product::create([
            'name' => 'Thinker IoT Core',
            'description' => 'A powerful development board for building the next generation of smart systems.',
            'price' => 1250.00,
            'image_url' => '/images/products/iot_device.png',
            'category_id' => $systems->id,
        ]);

        Product::create([
            'name' => 'Thinker OS Pro',
            'description' => 'Advanced software solution for managing smart building infrastructure.',
            'price' => 8500.00,
            'image_url' => '/images/products/iot_device.png',
            'category_id' => $software->id,
        ]);

        Product::create([
            'name' => 'Thinker 3D Resin X',
            'description' => 'High-precision resin for ultra-detailed 3D printed components.',
            'price' => 1500.00,
            'image_url' => '/images/products/iot_device.png',
            'category_id' => $printing->id,
        ]);
        
        Product::create([
            'name' => 'Test Item',
            'description' => 'A test item with 1 EGP price.',
            'price' => 1.00,
            'image_url' => '/images/products/iot_device.png',
            'category_id' => $electronics->id,
        ]);
    }
}
