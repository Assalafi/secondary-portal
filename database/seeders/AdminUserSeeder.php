<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $adminRole = Role::where('name', 'Admin')->first();

        // Create Super Admin User
        User::firstOrCreate(
            ['email' => 'superadmin@school.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password123'),
                'role_id' => $superAdminRole->id,
                'phone' => '+234-800-000-0001',
                'address' => 'School Administrative Office',
                'status' => 'Active',
                'email_verified_at' => now(),
            ]
        );

        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@school.com'],
            [
                'name' => 'School Administrator',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
                'phone' => '+234-800-000-0002',
                'address' => 'School Administrative Office',
                'status' => 'Active',
                'email_verified_at' => now(),
            ]
        );

        // Create Principal User
        $principalRole = Role::where('name', 'Principal')->first();
        User::firstOrCreate(
            ['email' => 'principal@school.com'],
            [
                'name' => 'School Principal',
                'password' => Hash::make('principal123'),
                'role_id' => $principalRole->id,
                'phone' => '+234-800-000-0003',
                'address' => 'Principal Office',
                'status' => 'Active',
                'email_verified_at' => now(),
            ]
        );
    }
}
