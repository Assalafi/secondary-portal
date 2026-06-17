<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'description' => 'System Administrator with full access',
            ],
            [
                'name' => 'Teacher',
                'description' => 'Teaching staff with academic access',
            ],
            [
                'name' => 'Student',
                'description' => 'Students with limited access',
            ],
            [
                'name' => 'Accountant',
                'description' => 'Financial staff with payment access',
            ],
            [
                'name' => 'Librarian',
                'description' => 'Library staff with resource access',
            ],
            [
                'name' => 'Staff',
                'description' => 'General staff members',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
