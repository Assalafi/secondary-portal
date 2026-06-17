<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ParentRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Parent role if it doesn't exist
        $parentRole = Role::firstOrCreate(
            ['name' => 'Parent'],
            [
                'description' => 'Parent/Guardian role with access to dependent student information',
                'permissions' => json_encode([
                    'view_dependents',
                    'view_attendance',
                    'view_results',
                    'view_assignments',
                    'make_payments',
                    'create_support_tickets',
                ]),
            ]
        );

        // Create a test parent user
        $parent = User::firstOrCreate(
            ['email' => 'parent@school.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password123'),
                'role_id' => $parentRole->id,
                'phone' => '08012345678',
                'address' => 'House No. 2, Ahmadu Bello Way, Gwoza',
                'gender' => 'Male',
                'date_of_birth' => '1985-05-15',
                'status' => 'active',
            ]
        );

        $this->command->info('Parent role and test user created successfully!');
        $this->command->info('Email: parent@school.com');
        $this->command->info('Password: password123');
    }
}
