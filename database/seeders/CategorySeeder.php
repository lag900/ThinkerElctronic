<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create(['name' => 'Electronics']);
        Category::create(['name' => 'Software']);
        Category::create(['name' => '3D Printing']);
        Category::create(['name' => 'Smart Systems']);
    }
}
