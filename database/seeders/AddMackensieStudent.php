<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\ClassArm;
use App\Models\AcademicSession;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AddMackensieStudent extends Seeder
{
    public function run()
    {
        // Find or create the SS 1 class
        $ss1 = SchoolClass::firstOrCreate([
            'name' => 'SS 1',
            'level' => 'SS',
            'numeric_level' => 1
        ]);

        // Find or create class arm A for SS 1
        $armA = ClassArm::firstOrCreate(['name' => 'A', 'school_class_id' => $ss1->id]);

        // Get current academic session
        $academicSession = AcademicSession::where('is_current', true)->first();
        if (!$academicSession) {
            $academicSession = AcademicSession::firstOrCreate([
                'name' => '2024/2025',
                'start_date' => '2024-09-01',
                'end_date' => '2025-08-31',
                'is_current' => true
            ]);
        }

        // Get Student role
        $studentRole = Role::where('name', 'Student')->first();

        // Check if user already exists
        $existingUser = User::where('email', 'mackensie.powers@student.com')->first();
        if ($existingUser) {
            $this->command->info('User mackensie.powers@student.com already exists!');
            return;
        }

        // Create user account
        $user = User::create([
            'name' => 'Mackensie Powers',
            'email' => 'mackensie.powers@student.com',
            'password' => Hash::make('password123'),
            'role_id' => $studentRole ? $studentRole->id : 5,
            'status' => 'Active',
            'email_verified_at' => now(),
        ]);

        // Generate unique admission number
        $lastStudent = Student::orderBy('id', 'desc')->first();
        $lastNumber = $lastStudent ? (int)substr($lastStudent->admission_no, -4) : 0;
        $admissionNumber = 'SSP2024' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        // Create student record
        Student::create([
            'user_id' => $user->id,
            'admission_no' => $admissionNumber,
            'admission_date' => now()->format('Y-m-d'),
            'surname' => 'Powers',
            'first_name' => 'Mackensie',
            'middle_name' => '',
            'gender' => 'Female',
            'dob' => '2007-05-15',
            'place_of_birth_town' => 'Sample Town',
            'place_of_birth_lga' => 'Sample LGA',
            'place_of_birth_state' => 'Lagos',
            'nationality' => 'Nigerian',
            'state_of_origin' => 'Lagos',
            'lga' => 'Sample LGA',
            'health_status' => 'Normal',
            'current_class_arm_id' => $armA->id,
            'academic_session_id' => $academicSession->id,
            'status' => 'Active'
        ]);

        $this->command->info('Student Mackensie Powers created successfully!');
        $this->command->info('Email: mackensie.powers@student.com');
        $this->command->info('Password: password123');
    }
}
