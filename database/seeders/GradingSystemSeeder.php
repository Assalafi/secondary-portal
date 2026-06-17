<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GradingSystem;

class GradingSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nigerian Grading System for Nursery (Qualitative)
        $nurseryGrades = [
            [
                'level' => 'Nursery',
                'grade' => 'Excellent',
                'remark' => 'Outstanding performance',
                'min_score' => 90,
                'max_score' => 100,
                'gpa_point' => 4.0,
                'description' => 'Exceptional understanding and application',
            ],
            [
                'level' => 'Nursery',
                'grade' => 'Very Good',
                'remark' => 'High performance',
                'min_score' => 80,
                'max_score' => 89,
                'gpa_point' => 3.5,
                'description' => 'Very good understanding',
            ],
            [
                'level' => 'Nursery',
                'grade' => 'Good',
                'remark' => 'Satisfactory performance',
                'min_score' => 70,
                'max_score' => 79,
                'gpa_point' => 3.0,
                'description' => 'Good understanding',
            ],
            [
                'level' => 'Nursery',
                'grade' => 'Fair',
                'remark' => 'Average performance',
                'min_score' => 60,
                'max_score' => 69,
                'gpa_point' => 2.0,
                'description' => 'Fair understanding, needs improvement',
            ],
            [
                'level' => 'Nursery',
                'grade' => 'Needs Improvement',
                'remark' => 'Below average',
                'min_score' => 0,
                'max_score' => 59,
                'gpa_point' => 1.0,
                'description' => 'Requires additional support',
            ],
        ];

        // Nigerian Grading System for Primary (Letter Grades)
        $primaryGrades = [
            [
                'level' => 'Primary',
                'grade' => 'A',
                'remark' => 'Excellent',
                'min_score' => 70,
                'max_score' => 100,
                'gpa_point' => 4.0,
                'description' => 'Outstanding performance',
            ],
            [
                'level' => 'Primary',
                'grade' => 'B',
                'remark' => 'Very Good',
                'min_score' => 60,
                'max_score' => 69,
                'gpa_point' => 3.5,
                'description' => 'Very good performance',
            ],
            [
                'level' => 'Primary',
                'grade' => 'C',
                'remark' => 'Good',
                'min_score' => 50,
                'max_score' => 59,
                'gpa_point' => 3.0,
                'description' => 'Good performance',
            ],
            [
                'level' => 'Primary',
                'grade' => 'D',
                'remark' => 'Fair',
                'min_score' => 45,
                'max_score' => 49,
                'gpa_point' => 2.0,
                'description' => 'Fair performance',
            ],
            [
                'level' => 'Primary',
                'grade' => 'E',
                'remark' => 'Pass',
                'min_score' => 40,
                'max_score' => 44,
                'gpa_point' => 1.0,
                'description' => 'Satisfactory pass',
            ],
            [
                'level' => 'Primary',
                'grade' => 'F',
                'remark' => 'Fail',
                'min_score' => 0,
                'max_score' => 39,
                'gpa_point' => 0,
                'description' => 'Failed - needs to retake',
            ],
        ];

        // Nigerian Grading System for JSS (Junior Secondary School)
        $jssGrades = [
            [
                'level' => 'JSS',
                'grade' => 'A',
                'remark' => 'Excellent',
                'min_score' => 70,
                'max_score' => 100,
                'gpa_point' => 4.0,
                'description' => 'Distinction level',
            ],
            [
                'level' => 'JSS',
                'grade' => 'B',
                'remark' => 'Very Good',
                'min_score' => 60,
                'max_score' => 69,
                'gpa_point' => 3.5,
                'description' => 'Very good credit',
            ],
            [
                'level' => 'JSS',
                'grade' => 'C',
                'remark' => 'Good',
                'min_score' => 50,
                'max_score' => 59,
                'gpa_point' => 3.0,
                'description' => 'Credit level',
            ],
            [
                'level' => 'JSS',
                'grade' => 'D',
                'remark' => 'Fair',
                'min_score' => 45,
                'max_score' => 49,
                'gpa_point' => 2.0,
                'description' => 'Pass level',
            ],
            [
                'level' => 'JSS',
                'grade' => 'E',
                'remark' => 'Pass',
                'min_score' => 40,
                'max_score' => 44,
                'gpa_point' => 1.0,
                'description' => 'Low pass',
            ],
            [
                'level' => 'JSS',
                'grade' => 'F',
                'remark' => 'Fail',
                'min_score' => 0,
                'max_score' => 39,
                'gpa_point' => 0,
                'description' => 'Failure',
            ],
        ];

        // Nigerian Grading System for SS (Senior Secondary School - WAEC/NECO standard)
        $ssGrades = [
            [
                'level' => 'SS',
                'grade' => 'A1',
                'remark' => 'Excellent',
                'min_score' => 75,
                'max_score' => 100,
                'gpa_point' => 4.0,
                'description' => 'Excellent - Distinction',
            ],
            [
                'level' => 'SS',
                'grade' => 'B2',
                'remark' => 'Very Good',
                'min_score' => 70,
                'max_score' => 74,
                'gpa_point' => 3.5,
                'description' => 'Very Good',
            ],
            [
                'level' => 'SS',
                'grade' => 'B3',
                'remark' => 'Good',
                'min_score' => 65,
                'max_score' => 69,
                'gpa_point' => 3.25,
                'description' => 'Good Credit',
            ],
            [
                'level' => 'SS',
                'grade' => 'C4',
                'remark' => 'Credit',
                'min_score' => 60,
                'max_score' => 64,
                'gpa_point' => 3.0,
                'description' => 'Credit',
            ],
            [
                'level' => 'SS',
                'grade' => 'C5',
                'remark' => 'Credit',
                'min_score' => 55,
                'max_score' => 59,
                'gpa_point' => 2.75,
                'description' => 'Credit',
            ],
            [
                'level' => 'SS',
                'grade' => 'C6',
                'remark' => 'Credit',
                'min_score' => 50,
                'max_score' => 54,
                'gpa_point' => 2.5,
                'description' => 'Credit',
            ],
            [
                'level' => 'SS',
                'grade' => 'D7',
                'remark' => 'Pass',
                'min_score' => 45,
                'max_score' => 49,
                'gpa_point' => 2.0,
                'description' => 'Pass',
            ],
            [
                'level' => 'SS',
                'grade' => 'E8',
                'remark' => 'Pass',
                'min_score' => 40,
                'max_score' => 44,
                'gpa_point' => 1.5,
                'description' => 'Pass',
            ],
            [
                'level' => 'SS',
                'grade' => 'F9',
                'remark' => 'Fail',
                'min_score' => 0,
                'max_score' => 39,
                'gpa_point' => 0,
                'description' => 'Fail',
            ],
        ];

        // Insert all grades
        foreach ($nurseryGrades as $grade) {
            GradingSystem::create($grade);
        }

        foreach ($primaryGrades as $grade) {
            GradingSystem::create($grade);
        }

        foreach ($jssGrades as $grade) {
            GradingSystem::create($grade);
        }

        foreach ($ssGrades as $grade) {
            GradingSystem::create($grade);
        }

        $this->command->info('Nigerian grading system seeded successfully!');
    }
}
