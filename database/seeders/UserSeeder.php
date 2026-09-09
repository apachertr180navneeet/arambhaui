<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Seed the user accounts.
     */
    public function run(): void
    {
        // 1. System Administrator
        User::updateOrCreate(
            ['email' => 'admin@garmenterp.com'],
            [
                'name' => 'System Administrator',
                'role' => 'admin',
                'status' => 'active',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Production Supervisor
        User::updateOrCreate(
            ['email' => 'supervisor@garmenterp.com'],
            [
                'name' => 'Production Supervisor',
                'role' => 'supervisor',
                'status' => 'active',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
    }
}
