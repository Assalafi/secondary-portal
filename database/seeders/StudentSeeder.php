<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\ClassArm;
use App\Models\AcademicSession;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run()
    {
        // Create sample academic data first
        $academicSession = AcademicSession::firstOrCreate([
            'name' => '2024/2025',
            'start_date' => '2024-09-01',
            'end_date' => '2025-08-31',
            'is_current' => true
        ]);

        // Create sample classes
        $jss3 = SchoolClass::firstOrCreate([
            'name' => 'JSS 3',
            'level' => 'JSS',
            'numeric_level' => 3
        ]);
        $jss2 = SchoolClass::firstOrCreate([
            'name' => 'JSS 2',
            'level' => 'JSS',
            'numeric_level' => 2
        ]);
        $ss1 = SchoolClass::firstOrCreate([
            'name' => 'SS 1',
            'level' => 'SS',
            'numeric_level' => 1
        ]);

        // Create sample class arms
        $armA = ClassArm::firstOrCreate(['name' => 'A', 'school_class_id' => $jss3->id]);
        $armB = ClassArm::firstOrCreate(['name' => 'B', 'school_class_id' => $jss2->id]);

        // Sample students data
        $studentsData = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'middle_name' => 'Michael',
                'email' => 'john.doe@student.portal.com',
                'gender' => 'Male',
                'class_id' => $jss3->id,
                'class_arm_id' => $armA->id,
                'status' => 'Active'
            ],
            [
                'first_name' => 'Mary',
                'last_name' => 'Johnson',
                'middle_name' => 'Grace',
                'email' => 'mary.johnson@student.portal.com',
                'gender' => 'Female',
                'class_id' => $jss2->id,
                'class_arm_id' => $armB->id,
                'status' => 'Active'
            ],
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Ibrahim',
                'middle_name' => 'Musa',
                'email' => 'ahmed.ibrahim@student.portal.com',
                'gender' => 'Male',
                'class_id' => $jss3->id,
                'class_arm_id' => $armA->id,
                'status' => 'Active'
            ],
            [
                'first_name' => 'Fatima',
                'last_name' => 'Aliyu',
                'middle_name' => 'Khadija',
                'email' => 'fatima.aliyu@student.portal.com',
                'gender' => 'Female',
                'class_id' => $jss2->id,
                'class_arm_id' => $armB->id,
                'status' => 'Inactive'
            ],
            [
                'first_name' => 'Samuel',
                'last_name' => 'Okafor',
                'middle_name' => 'Chidi',
                'email' => 'samuel.okafor@student.portal.com',
                'gender' => 'Male',
                'class_id' => $jss3->id,
                'class_arm_id' => $armA->id,
                'status' => 'Active'
            ]
        ];

        foreach ($studentsData as $index => $studentData) {
            // Create user account for student
            $user = User::create([
                'name' => $studentData['first_name'] . ' ' . $studentData['last_name'],
                'email' => $studentData['email'],
                'password' => Hash::make('password123'),
                'role_id' => 5, // Student role
                'status' => $studentData['status']
            ]);

            // Generate admission number
            $admissionNumber = 'SSP2024' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);

            // Create student record
            Student::create([
                'user_id' => $user->id,
                'admission_no' => $admissionNumber,
                'admission_date' => now()->subMonths(rand(1, 12))->format('Y-m-d'),
                'surname' => $studentData['last_name'],
                'first_name' => $studentData['first_name'],
                'middle_name' => $studentData['middle_name'],
                'gender' => $studentData['gender'],
                'dob' => now()->subYears(rand(14, 18))->format('Y-m-d'),
                'place_of_birth_town' => 'Sample Town',
                'place_of_birth_lga' => 'Sample LGA',
                'place_of_birth_state' => ['Lagos', 'Kano', 'Rivers', 'Kaduna', 'Oyo'][array_rand(['Lagos', 'Kano', 'Rivers', 'Kaduna', 'Oyo'])],
                'nationality' => 'Nigerian',
                'state_of_origin' => ['Lagos', 'Kano', 'Rivers', 'Kaduna', 'Oyo'][array_rand(['Lagos', 'Kano', 'Rivers', 'Kaduna', 'Oyo'])],
                'lga' => 'Sample LGA',
                'health_status' => 'Normal',
                'current_class_arm_id' => $studentData['class_arm_id'],
                'academic_session_id' => $academicSession->id,
                'status' => $studentData['status']
            ]);
        }

        $this->command->info('Sample students created successfully!');
    }
}
