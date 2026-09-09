<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\CompanySetting;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Seed the user roles, system accounts, and permission matrix.
     */
    public function run(): void
    {
        // 1. User Accounts for each organizational role
        $users = [
            [
                'email' => 'admin@garmenterp.com',
                'name' => 'System Administrator',
                'role' => 'Administrator',
                'status' => 'active',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ],
            [
                'email' => 'supervisor@garmenterp.com',
                'name' => 'Production Supervisor',
                'role' => 'Production Supervisor',
                'status' => 'active',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ],
            [
                'email' => 'store@garmenterp.com',
                'name' => 'Inventory & Store Manager',
                'role' => 'Store Manager',
                'status' => 'active',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ],
            [
                'email' => 'accounts@garmenterp.com',
                'name' => 'Finance & Accounts Manager',
                'role' => 'Accounts Manager',
                'status' => 'active',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ],
            [
                'email' => 'dispatch@garmenterp.com',
                'name' => 'Logistics & Dispatch Manager',
                'role' => 'Dispatch Manager',
                'status' => 'active',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ],
            [
                'email' => 'qc@garmenterp.com',
                'name' => 'Quality Inspection Officer',
                'role' => 'Quality Inspector',
                'status' => 'active',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // 2. Roles & Permissions RBAC Matrix
        $rolesMatrix = [
            [
                'role' => 'Administrator',
                'description' => 'Full unrestricted system and configuration access',
                'permissions' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true, 'approve' => true, 'export' => true]
            ],
            [
                'role' => 'Production Supervisor',
                'description' => 'Oversees job assignments, order routing, cutting, and stitching',
                'permissions' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false, 'approve' => true, 'export' => true]
            ],
            [
                'role' => 'Store Manager',
                'description' => 'Manages raw material stock, fabric receipts, and trim inventory',
                'permissions' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false, 'approve' => false, 'export' => true]
            ],
            [
                'role' => 'Accounts Manager',
                'description' => 'Handles customer ledgers, payment collections, and GST invoicing',
                'permissions' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false, 'approve' => true, 'export' => true]
            ],
            [
                'role' => 'Dispatch Manager',
                'description' => 'Prepares delivery challans, shipment barcodes, and transit logs',
                'permissions' => ['view' => true, 'create' => true, 'edit' => false, 'delete' => false, 'approve' => false, 'export' => true]
            ],
            [
                'role' => 'Quality Inspector',
                'description' => 'Performs inline inspection and lot tracking verification',
                'permissions' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false, 'approve' => true, 'export' => false]
            ]
        ];

        CompanySetting::updateOrCreate(
            ['key' => 'roles_permissions_matrix'],
            [
                'value' => json_encode($rolesMatrix),
                'group' => 'security'
            ]
        );
    }
}
