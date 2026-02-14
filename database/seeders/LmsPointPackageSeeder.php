<?php

namespace Database\Seeders;

use App\Models\LmsPointPackage;
use Illuminate\Database\Seeder;

class LmsPointPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            ['points' => 25, 'price' => '25 جنيه', 'payment_link' => null, 'order' => 1],
            ['points' => 100, 'price' => '50 جنيه', 'payment_link' => null, 'order' => 2],
            ['points' => 150, 'price' => '75 جنيه', 'payment_link' => null, 'order' => 3],
            ['points' => 200, 'price' => '100 جنيه', 'payment_link' => null, 'order' => 4],
        ];

        foreach ($packages as $pkg) {
            LmsPointPackage::firstOrCreate(
                ['points' => $pkg['points']],
                $pkg
            );
        }
    }
}
