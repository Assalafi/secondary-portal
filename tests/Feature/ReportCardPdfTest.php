<?php

namespace Tests\Feature;

use App\Models\ClassArm;
use App\Models\ReportCard;
use App\Models\ReportCardItem;
use App\Models\ReportSettings;
use App\Models\SchoolClass;
use App\Models\SessionTerm;
use App\Models\Student;
use App\Models\Subject;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportCardPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_portrait_report_with_fifteen_subjects_fits_on_one_page(): void
    {
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
            'admission_no' => 'SSP/2026/0113',
            'admission_date' => '2024-09-01',
            'surname' => 'Garba',
            'first_name' => 'Adebayo',
            'middle_name' => 'Nkechi',
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
        $reportCard = ReportCard::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_session_id' => $sessionTerm->id,
            'term_id' => $sessionTerm->id,
            'report_type' => 'termly',
            'status' => 'published',
            'total_score' => 975,
            'maximum_score' => 1500,
            'average_score' => 65,
            'final_grade' => 'B3',
            'final_remark' => 'Good',
            'class_position' => 6,
            'number_in_class' => 20,
            'class_average' => 62,
            'attendance_percentage' => 95,
            'class_teacher_comment' => 'A strong performance. Keep working consistently.',
            'principal_comment' => 'Good progress.',
            'promotion_decision' => 'Promoted',
            'verification_code' => 'RPT-PDF-113',
        ]);

        foreach (range(1, 15) as $index) {
            $subject = Subject::create([
                'name' => "Subject {$index}",
                'code' => "SUB{$index}",
            ]);
            ReportCardItem::create([
                'report_card_id' => $reportCard->id,
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'ca_score' => 25,
                'exam_score' => 40,
                'total_score' => 65,
                'grade' => 'B3',
                'remark' => 'Good',
                'class_average' => 62,
            ]);
        }

        $reportCard->load(['student', 'class', 'academicSession', 'term', 'items', 'nextClass']);
        $pdf = Pdf::loadView('admin.report-cards.pdf', [
            'reportCard' => $reportCard,
            'reportSettings' => ReportSettings::getSettings(),
            'schoolSettings' => null,
        ])->setPaper('a4', 'portrait');
        $domPdf = $pdf->getDomPDF();
        $domPdf->render();

        $this->assertSame(1, $domPdf->getCanvas()->get_page_count());
        $this->assertLessThan(
            $domPdf->getCanvas()->get_height(),
            $domPdf->getCanvas()->get_width(),
            'The PDF canvas must be portrait.'
        );
    }
}
