<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\ClassArm;
use App\Models\SchoolClass;
use App\Models\Score;
use App\Models\ScoreBatch;
use App\Models\SessionTerm;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use App\Services\ReportCardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ReportCardServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_an_idempotent_normalised_termly_report_card(): void
    {
        $user = User::factory()->create();
        $session = AcademicSession::create([
            'name' => '2025/2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-07-15',
            'is_current' => true,
        ]);
        $term = Term::create(['name' => 'First Term', 'number' => 1]);
        $sessionTerm = SessionTerm::create([
            'academic_year' => $session->name,
            'term_name' => $term->name,
            'start_date' => '2025-09-01',
            'end_date' => '2025-12-15',
            'is_current' => true,
            'status' => 'Active',
        ]);
        $class = SchoolClass::create([
            'level' => 'JSS',
            'name' => 'JSS 1',
            'numeric_level' => 1,
        ]);
        $classArm = ClassArm::create([
            'school_class_id' => $class->id,
            'name' => 'A',
        ]);
        $student = Student::create([
            'user_id' => $user->id,
            'admission_no' => 'STD001',
            'admission_date' => '2025-09-01',
            'surname' => 'Okafor',
            'first_name' => 'Ada',
            'gender' => 'Female',
            'dob' => '2013-03-12',
            'current_class_arm_id' => $classArm->id,
            'academic_session_id' => $session->id,
        ]);
        $subject = Subject::create(['name' => 'Mathematics', 'code' => 'MTH']);
        $batch = ScoreBatch::create([
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'academic_session_id' => $session->id,
            'term_id' => $term->id,
            'first_ca_max' => 10,
            'second_ca_max' => 10,
            'third_ca_max' => 10,
            'exam_max' => 70,
            'status' => 'Uploaded',
            'uploaded_by' => $user->id,
        ]);
        Score::create([
            'score_batch_id' => $batch->id,
            'student_id' => $student->id,
            'first_ca' => 8,
            'second_ca' => 9,
            'third_ca' => 8,
            'exam' => 60,
            'total' => 85,
        ]);

        $service = app(ReportCardService::class);
        $report = $service->generate($student, $classArm->load('schoolClass'), $session, $term);
        $regenerated = $service->generate($student, $classArm, $session, $term);

        $this->assertSame($report->id, $regenerated->id);
        $this->assertSame($sessionTerm->id, $report->academic_session_id);
        $this->assertSame($sessionTerm->id, $report->term_id);
        $this->assertEquals(85.0, (float) $report->average_score);
        $this->assertSame('A', $report->final_grade);
        $this->assertCount(1, $regenerated->items);
        $this->assertEquals(25.0, (float) $regenerated->items->first()->ca_score);
        $this->assertEquals(60.0, (float) $regenerated->items->first()->exam_score);
        $this->assertDatabaseCount('report_cards', 1);
        $this->assertDatabaseCount('report_card_items', 1);

        $report->update(['status' => 'published']);
        $this->expectException(ValidationException::class);
        $service->generate($student, $classArm, $session, $term);
    }

    public function test_default_senior_secondary_grading_uses_wassce_style_labels(): void
    {
        $service = app(ReportCardService::class);

        $this->assertSame('A1', $service->gradeFor(75, 'SS')['grade']);
        $this->assertSame('C6', $service->gradeFor(50, 'SS')['grade']);
        $this->assertSame('F9', $service->gradeFor(39.99, 'SS')['grade']);
    }
}
