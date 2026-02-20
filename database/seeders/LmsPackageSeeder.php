<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LmsPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            ['points' => 25, 'price' => '25 جنيه', 'sort_order' => 1],
            ['points' => 100, 'price' => '50 جنيه', 'sort_order' => 2],
            ['points' => 150, 'price' => '75 جنيه', 'sort_order' => 3],
            ['points' => 200, 'price' => '100 جنيه', 'sort_order' => 4],
        ];

        foreach ($packages as $package) {
            \App\Models\LmsPointPackage::updateOrCreate(['points' => $package['points']], $package);
        }
    }
}
