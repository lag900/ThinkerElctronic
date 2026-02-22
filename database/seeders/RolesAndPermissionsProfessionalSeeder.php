<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsProfessionalSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('permission_role')->truncate();
        Permission::truncate();
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $permissions = [
            'Users Management' => [
                'view_users' => 'View Users',
                'create_users' => 'Create Users',
                'edit_users' => 'Edit Users',
                'delete_users' => 'Delete Users',
            ],
            'Orders & Invoices' => [
                'view_orders' => 'View Orders',
                'create_orders' => 'Create Orders',
                'edit_orders' => 'Edit Orders',
                'delete_orders' => 'Delete Orders',
                'change_order_status' => 'Change Order Status',
                'refund_orders' => 'Refund Orders',
                'export_orders' => 'Export Orders',
            ],
            'Products & Categories' => [
                'view_products' => 'View Products',
                'create_products' => 'Create Products',
                'edit_products' => 'Edit Products',
                'delete_products' => 'Delete Products',
                'manage_stock' => 'Manage Stock',
            ],
            'Customers' => [
                'view_customers' => 'View Customers',
                'create_customers' => 'Create Customers',
                'edit_customers' => 'Edit Customers',
                'delete_customers' => 'Delete Customers',
                'view_customer_balance' => 'View Customer Balance',
            ],
            'POS & Sales' => [
                'open_pos' => 'Open POS',
                'create_invoice' => 'Create Invoice',
                'cancel_invoice' => 'Cancel Invoice',
                'give_discount' => 'Give Discount',
                'view_today_sales' => 'View Today Sales',
            ],
            'Finance' => [
                'view_expenses' => 'View Expenses',
                'add_expenses' => 'Add Expenses',
                'delete_expenses' => 'Delete Expenses',
                'view_profit' => 'View Profit',
                'view_cashbox' => 'View Cashbox',
            ],
            'Analytics' => [
                'view_dashboard' => 'View Dashboard',
                'view_analytics' => 'View Analytics',
            ],
            'Content & Pages' => [
                'manage_content' => 'Manage Content',
                'edit_pages' => 'Edit Pages',
            ],
            'QR System' => [
                'view_qr' => 'View QR',
                'generate_qr' => 'Generate QR',
            ],
            'Settings & Security' => [
                'manage_roles' => 'Manage Roles',
                'manage_users' => 'Manage Users',
                'system_settings' => 'System Settings',
            ],
            'Cash Control' => [
                'view_money' => 'View Money',
                'withdraw_cash' => 'Withdraw Cash',
                'view_reports' => 'View Reports',
            ]
        ];

        $allPerms = [];
        foreach ($permissions as $group => $perms) {
            foreach ($perms as $name => $label) {
                $allPerms[] = Permission::create([
                    'name' => $name,
                    'label' => $label,
                    'group' => $group
                ]);
            }
        }

        $superAdmin = Role::create([
            'name' => 'super_admin',
            'label' => 'Super Admin',
            'description' => 'Full access to all system features.'
        ]);
        $superAdmin->permissions()->sync(collect($allPerms)->pluck('id'));

        $manager = Role::create([
            'name' => 'manager',
            'label' => 'Manager',
            'description' => 'Manage operations without vital security access.'
        ]);
        $managerPerms = Permission::whereNotIn('name', [
            'manage_roles', 'delete_expenses', 'system_settings', 'view_money', 'withdraw_cash'
        ])->pluck('id');
        $manager->permissions()->sync($managerPerms);

        $cashier = Role::create([
            'name' => 'cashier',
            'label' => 'Cashier',
            'description' => 'Process sales and create invoices.'
        ]);
        $cashierPerms = Permission::whereIn('name', [
            'open_pos', 'create_invoice', 'view_today_sales', 'view_orders', 'view_products', 'view_customers'
        ])->pluck('id');
        $cashier->permissions()->sync($cashierPerms);

        $sales = Role::create([
            'name' => 'sales_staff',
            'label' => 'Sales Staff',
            'description' => 'Handle customers and view stock.'
        ]);
        $salesPerms = Permission::whereIn('name', [
            'view_products', 'view_customers', 'create_customers', 'view_orders', 'create_orders'
        ])->pluck('id');
        $sales->permissions()->sync($salesPerms);

        $accountant = Role::create([
            'name' => 'accountant',
            'label' => 'Accountant',
            'description' => 'Manage finance, expenses, and invoices.'
        ]);
        $accountantPerms = Permission::whereIn('name', [
            'view_orders', 'view_customers', 'view_customer_balance', 'view_expenses', 'add_expenses', 'view_profit', 'view_cashbox', 'view_reports', 'view_money'
        ])->pluck('id');
        $accountant->permissions()->sync($accountantPerms);

        $viewer = Role::create([
            'name' => 'viewer',
            'label' => 'Viewer',
            'description' => 'Read-only access to specific areas.'
        ]);
        $viewerPerms = Permission::whereIn('name', [
            'view_products', 'view_customers', 'view_orders', 'view_dashboard'
        ])->pluck('id');
        $viewer->permissions()->sync($viewerPerms);
        
        $adminUser = \App\Models\User::where('email', 'admin@thinker.com')->first();
        if ($adminUser) {
            $adminUser->role_id = $superAdmin->id;
            // Also set the legacy role column if it exists
            $adminUser->role = 'super_admin';
            $adminUser->save();
        }
    }
}
