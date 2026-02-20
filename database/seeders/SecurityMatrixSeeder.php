<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SecurityMatrixSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Core Permissions
        $permissions = [
            ['name' => 'users.view', 'label' => 'View Users', 'group' => 'Identity'],
            ['name' => 'users.create', 'label' => 'Create Users', 'group' => 'Identity'],
            ['name' => 'users.edit', 'label' => 'Edit Users', 'group' => 'Identity'],
            ['name' => 'users.delete', 'label' => 'Terminate Users', 'group' => 'Identity'],
            ['name' => 'roles.view', 'label' => 'View Roles', 'group' => 'Security'],
            ['name' => 'roles.manage', 'label' => 'Manage Security Protocols', 'group' => 'Security'],
            ['name' => 'products.manage', 'label' => 'Manage Hardware Nodes', 'group' => 'Inventory'],
        ];

        foreach ($permissions as $p) {
            \App\Models\Permission::updateOrCreate(['name' => $p['name']], $p);
        }

        // 2. Create Super Admin Role
        $adminRole = \App\Models\Role::updateOrCreate(
            ['name' => 'super_admin'],
            [
                'label' => 'System Overlord',
                'description' => 'Full architectural control over all system nodes and protocols.'
            ]
        );

        // 3. Sync All Permissions to Super Admin
        $adminRole->permissions()->sync(\App\Models\Permission::all());

        // 4. Update existing admin users if they exist
        $adminUser = \App\Models\User::where('email', 'lag900@thinker.com')->first();
        if ($adminUser) {
            $adminUser->update(['role_id' => $adminRole->id]);
        }
    }
}
