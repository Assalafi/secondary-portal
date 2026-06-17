<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'description' => 'Full system access with all permissions'
            ],
            [
                'name' => 'Admin',
                'description' => 'Administrative access to manage school operations'
            ],
            [
                'name' => 'Principal',
                'description' => 'School principal with oversight permissions'
            ],
            [
                'name' => 'Vice Principal',
                'description' => 'Assistant to principal with management permissions'
            ],
            [
                'name' => 'Teacher',
                'description' => 'Teaching staff with class and student management'
            ],
            [
                'name' => 'Accountant',
                'description' => 'Financial management and fee collection'
            ],
            [
                'name' => 'Librarian',
                'description' => 'Library management and resources'
            ],
            [
                'name' => 'Student',
                'description' => 'Student access to portal features'
            ],
            [
                'name' => 'Parent',
                'description' => 'Parent/Guardian access to student information'
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}
