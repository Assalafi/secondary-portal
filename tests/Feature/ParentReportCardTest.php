<?php

namespace Tests\Feature;

use App\Models\ClassArm;
use App\Models\ReportCard;
use App\Models\SchoolClass;
use App\Models\SessionTerm;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ParentReportCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_sees_published_cards_for_students_from_active_dependents_relationship(): void
    {
        $parent = User::factory()->create(['name' => 'Mrs. Zainab Garba']);
        $studentUser = User::factory()->create(['name' => 'Adebayo Garba']);
        $class = SchoolClass::create([
            'level' => 'SS',
            'name' => 'SS 3',
            'numeric_level' => 3,
        ]);
        $classArm = ClassArm::create([
            'school_class_id' => $class->id,
            'name' => 'B',
        ]);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'admission_no' => 'SSP/2026/0113',
            'admission_date' => '2024-09-01',
            'surname' => 'Garba',
            'first_name' => 'Adebayo',
            'gender' => 'Male',
            'dob' => '2008-06-10',
            'current_class_arm_id' => $classArm->id,
        ]);
        $sessionTerm = SessionTerm::create([
            'academic_year' => '2024/2025',
            'term_name' => '3rd Term',
            'start_date' => '2025-04-21',
            'end_date' => '2025-07-18',
            'is_current' => true,
            'status' => 'Active',
        ]);

        DB::table('parent_student')->insert([
            'parent_id' => $parent->id,
            'student_id' => $student->id,
            'relationship' => 'Mother',
            'date_added' => now()->toDateString(),
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ReportCard::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_session_id' => $sessionTerm->id,
            'report_type' => 'annual',
            'status' => 'published',
            'average_score' => 65.25,
            'final_grade' => 'B3',
            'verification_code' => 'RPT-PARENT-113',
        ]);

        $this->actingAs($parent)
            ->get(route('parent.report-cards'))
            ->assertOk()
            ->assertSee('Garba')
            ->assertSee('Adebayo')
            ->assertSee('B3');
    }
}
