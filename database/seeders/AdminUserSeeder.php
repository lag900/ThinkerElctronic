<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@thinker.com'],
            [   
                'name' => 'Admin',
                'password' => Hash::make(env('ADMIN_PASSWORD', '123456789')),
                'email_verified_at' => now(),
            ]
        );
    }
}
