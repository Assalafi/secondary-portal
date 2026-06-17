<?php

namespace App\Http\Controllers;

use App\Models\ReportCard;
use App\Models\ReportCardItem;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\SessionTerm;
use App\Models\Result;
use App\Models\Score;
use App\Models\GradingProfile;
use App\Models\GradingScale;
use App\Models\AffectiveTrait;
use App\Models\PsychomotorTrait;
use App\Models\StudentAffectiveRating;
use App\Models\StudentPsychomotorRating;
use App\Models\ReportSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReportCardController extends Controller
{
    public function generateTermlyReport(Request $request, $classId, $studentId)
    {
        $request->validate([
            'session_id' => 'required|exists:session_terms,id',
            'term_id' => 'required|exists:session_terms,id',
        ]);

        $student = Student::findOrFail($studentId);
        $class = SchoolClass::findOrFail($classId);
        $session = SessionTerm::findOrFail($request->session_id);
        $term = SessionTerm::findOrFail($request->term_id);

        // Check if report card already exists
        $existingReport = ReportCard::where('student_id', $studentId)
            ->where('class_id', $classId)
            ->where('academic_session_id', $request->session_id)
            ->where('term_id', $request->term_id)
            ->where('report_type', 'termly')
            ->first();

        if ($existingReport) {
            return redirect()->route('admin.report-cards.show', $existingReport->id)
                ->with('info', 'Report card already exists for this student.');
        }

        // Generate report card
        $reportCard = $this->generateReportCard($student, $class, $session, $term, 'termly');

        return redirect()->route('admin.report-cards.show', $reportCard->id)
            ->with('success', 'Termly report card generated successfully.');
    }

    public function generateAnnualReport(Request $request, $classId, $studentId)
    {
        $request->validate([
            'session_id' => 'required|exists:session_terms,id',
        ]);

        $student = Student::findOrFail($studentId);
        $class = SchoolClass::findOrFail($classId);
        $session = SessionTerm::findOrFail($request->session_id);

        // Check if annual report already exists
        $existingReport = ReportCard::where('student_id', $studentId)
            ->where('class_id', $classId)
            ->where('academic_session_id', $request->session_id)
            ->where('report_type', 'annual')
            ->first();

        if ($existingReport) {
            return redirect()->route('admin.report-cards.show', $existingReport->id)
                ->with('info', 'Annual report card already exists for this student.');
        }

        // Generate annual report card
        $reportCard = $this->generateReportCard($student, $class, $session, null, 'annual');

        return redirect()->route('admin.report-cards.show', $reportCard->id)
            ->with('success', 'Annual report card generated successfully.');
    }

    private function generateReportCard($student, $class, $session, $term, $reportType)
    {
        DB::beginTransaction();
        try {
            $settings = ReportSettings::getSettings();
            
            // Get scores for the student
            $scores = Score::where('student_id', $student->id)
                ->whereHas('scoreBatch', function($query) use ($class, $session, $term) {
                    $query->where('class_id', $class->id);
                    if ($session) {
                        $query->where('session_id', $session->id);
                    }
                    if ($term) {
                        $query->where('term_id', $term->id);
                    }
                })
                ->with('subject')
                ->get();

            // Calculate totals and averages
            $totalScore = $scores->sum('total_score');
            $maximumScore = $scores->count() * 100;
            $averageScore = $maximumScore > 0 ? ($totalScore / $maximumScore) * 100 : 0;

            // Get grade and remark
            $grade = $this->calculateGrade($averageScore, $class->level);
            $remark = $grade['remark'] ?? '';

            // Create report card
            $reportCard = ReportCard::create([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'academic_session_id' => $session->id ?? null,
                'term_id' => $term->id ?? null,
                'report_type' => $reportType,
                'status' => 'draft',
                'total_score' => $totalScore,
                'maximum_score' => $maximumScore,
                'average_score' => $averageScore,
                'final_grade' => $grade['grade'] ?? '',
                'final_remark' => $remark,
                'verification_code' => $this->generateVerificationCode($student, $session, $term),
            ]);

            // Create report card items
            foreach ($scores as $score) {
                $itemGrade = $this->calculateGrade($score->total_score, $class->level);
                
                ReportCardItem::create([
                    'report_card_id' => $reportCard->id,
                    'subject_id' => $score->subject_id,
                    'subject_name' => $score->subject->name,
                    'ca_score' => $score->ca_score,
                    'exam_score' => $score->exam_score,
                    'total_score' => $score->total_score,
                    'grade' => $itemGrade['grade'] ?? '',
                    'grade_point' => $itemGrade['grade_point'] ?? 0,
                    'remark' => $itemGrade['remark'] ?? '',
                    'teacher_id' => $score->subject->teacher_id ?? null,
                ]);
            }

            // Calculate class position
            $this->calculateClassPosition($reportCard);

            // Initialize affective and psychomotor ratings with defaults
            $this->initializeDomainRatings($reportCard);

            DB::commit();
            return $reportCard->fresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function calculateGrade($score, $level)
    {
        $profile = GradingProfile::where('level', $level)
            ->where('is_default', true)
            ->first();

        if (!$profile) {
            // Use default grading scale
            if ($score >= 80) return ['grade' => 'A', 'remark' => 'Excellent', 'grade_point' => 5];
            if ($score >= 70) return ['grade' => 'B', 'remark' => 'Very Good', 'grade_point' => 4];
            if ($score >= 60) return ['grade' => 'C', 'remark' => 'Good', 'grade_point' => 3];
            if ($score >= 50) return ['grade' => 'D', 'remark' => 'Fair', 'grade_point' => 2];
            return ['grade' => 'F', 'remark' => 'Fail', 'grade_point' => 1];
        }

        $scale = $profile->scales()
            ->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();

        return [
            'grade' => $scale->grade ?? 'F',
            'remark' => $scale->remark ?? 'Fail',
            'grade_point' => $scale->grade_point ?? 1,
        ];
    }

    private function calculateClassPosition($reportCard)
    {
        $allReports = ReportCard::where('class_id', $reportCard->class_id)
            ->where('academic_session_id', $reportCard->academic_session_id)
            ->where('term_id', $reportCard->term_id)
            ->where('report_type', $reportCard->report_type)
            ->orderBy('average_score', 'desc')
            ->get();

        $position = 1;
        foreach ($allReports as $index => $otherReport) {
            if ($otherReport->id === $reportCard->id) {
                $reportCard->class_position = $position;
                $reportCard->number_in_class = $allReports->count();
                $reportCard->save();
                break;
            }
            if ($otherReport->average_score > $reportCard->average_score) {
                $position++;
            }
        }

        // Calculate class statistics
        $reportCard->class_highest_average = $allReports->max('average_score');
        $reportCard->class_lowest_average = $allReports->min('average_score');
        $reportCard->class_average = $allReports->avg('average_score');
        $reportCard->save();
    }

    private function initializeDomainRatings($reportCard)
    {
        // Initialize affective ratings with default values (3 = Good)
        $affectiveTraits = AffectiveTrait::active()->ordered()->get();
        foreach ($affectiveTraits as $trait) {
            StudentAffectiveRating::create([
                'report_card_id' => $reportCard->id,
                'student_id' => $reportCard->student_id,
                'trait_id' => $trait->id,
                'rating_value' => 3, // Default to Good
            ]);
        }

        // Initialize psychomotor ratings with default values (3 = Good)
        $psychomotorTraits = PsychomotorTrait::active()->ordered()->get();
        foreach ($psychomotorTraits as $trait) {
            StudentPsychomotorRating::create([
                'report_card_id' => $reportCard->id,
                'student_id' => $reportCard->student_id,
                'trait_id' => $trait->id,
                'rating_value' => 3, // Default to Good
            ]);
        }
    }

    private function generateVerificationCode($student, $session, $term)
    {
        $sessionPart = $session ? $session->id : 'ANNUAL';
        $termPart = $term ? $term->id : 'ALL';
        $randomPart = strtoupper(Str::random(4));
        return "RPT-{$sessionPart}-{$termPart}-{$student->id}-{$randomPart}";
    }

    public function show($id)
    {
        $reportCard = ReportCard::with([
            'student',
            'class',
            'academicSession',
            'term',
            'items.subject',
            'affectiveRatings.trait',
            'psychomotorRatings.trait',
            'classTeacher',
            'approvedBy',
            'publishedBy'
        ])->findOrFail($id);

        return view('admin.report-cards.show', compact('reportCard'));
    }

    public function editComments($id)
    {
        $reportCard = ReportCard::with(['student', 'class'])->findOrFail($id);
        return view('admin.report-cards.edit-comments', compact('reportCard'));
    }

    public function updateComments(Request $request, $id)
    {
        $request->validate([
            'class_teacher_comment' => 'nullable|string',
            'principal_comment' => 'nullable|string',
            'parent_comment' => 'nullable|string',
        ]);

        $reportCard = ReportCard::findOrFail($id);
        $reportCard->update([
            'class_teacher_comment' => $request->class_teacher_comment,
            'principal_comment' => $request->principal_comment,
            'parent_comment' => $request->parent_comment,
        ]);

        return redirect()->route('admin.report-cards.show', $reportCard->id)
            ->with('success', 'Comments updated successfully.');
    }

    public function editDomainRatings($id)
    {
        $reportCard = ReportCard::with([
            'student',
            'class',
            'affectiveRatings.trait',
            'psychomotorRatings.trait'
        ])->findOrFail($id);

        $affectiveTraits = AffectiveTrait::active()->ordered()->get();
        $psychomotorTraits = PsychomotorTrait::active()->ordered()->get();

        return view('admin.report-cards.edit-domain-ratings', compact(
            'reportCard',
            'affectiveTraits',
            'psychomotorTraits'
        ));
    }

    public function updateDomainRatings(Request $request, $id)
    {
        $request->validate([
            'affective_ratings' => 'required|array',
            'psychomotor_ratings' => 'required|array',
        ]);

        $reportCard = ReportCard::findOrFail($id);

        // Update affective ratings
        foreach ($request->affective_ratings as $traitId => $rating) {
            StudentAffectiveRating::updateOrCreate(
                [
                    'report_card_id' => $reportCard->id,
                    'trait_id' => $traitId,
                ],
                [
                    'rating_value' => $rating,
                    'rated_by' => auth()->id(),
                ]
            );
        }

        // Update psychomotor ratings
        foreach ($request->psychomotor_ratings as $traitId => $rating) {
            StudentPsychomotorRating::updateOrCreate(
                [
                    'report_card_id' => $reportCard->id,
                    'trait_id' => $traitId,
                ],
                [
                    'rating_value' => $rating,
                    'rated_by' => auth()->id(),
                ]
            );
        }

        return redirect()->route('admin.report-cards.show', $reportCard->id)
            ->with('success', 'Domain ratings updated successfully.');
    }

    public function approve($id)
    {
        $reportCard = ReportCard::findOrFail($id);
        $reportCard->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('admin.report-cards.show', $reportCard->id)
            ->with('success', 'Report card approved successfully.');
    }

    public function publish($id)
    {
        $reportCard = ReportCard::findOrFail($id);
        $settings = ReportSettings::getSettings();

        if ($settings->require_principal_approval && $reportCard->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'Report card must be approved before publishing.');
        }

        $reportCard->update([
            'status' => 'published',
            'published_by' => auth()->id(),
            'published_at' => now(),
            'verification_url' => url("/verify-result/{$reportCard->verification_code}"),
        ]);

        return redirect()->route('admin.report-cards.show', $reportCard->id)
            ->with('success', 'Report card published successfully.');
    }

    public function broadsheet($classId)
    {
        $class = SchoolClass::findOrFail($classId);
        
        $request = request();
        $sessionId = $request->query('session_id');
        $termId = $request->query('term_id');

        $reportCards = ReportCard::where('class_id', $classId)
            ->when($sessionId, function($query) use ($sessionId) {
                return $query->where('academic_session_id', $sessionId);
            })
            ->when($termId, function($query) use ($termId) {
                return $query->where('term_id', $termId);
            })
            ->with(['student', 'items'])
            ->orderBy('class_position')
            ->get();

        return view('admin.report-croadsheet', compact('class', 'reportCards'));
    }

    public function editPromotion($id)
    {
        $reportCard = ReportCard::with(['student', 'class', 'nextClass'])->findOrFail($id);
        $classes = SchoolClass::where('level', $reportCard->class->level)->get();
        
        return view('admin.report-cards.edit-promotion', compact('reportCard', 'classes'));
    }

    public function updatePromotion(Request $request, $id)
    {
        $request->validate([
            'promotion_decision' => 'required|in:Promoted,Promoted on Trial,Repeated,Withdrawn,Transferred,Graduated,Not Applicable',
            'next_class_id' => 'nullable|exists:school_classes,id',
            'vacation_date' => 'nullable|date',
            'next_term_begins' => 'nullable|date',
            'next_term_fee' => 'nullable|numeric',
            'outstanding_balance' => 'nullable|numeric',
        ]);

        $reportCard = ReportCard::findOrFail($id);
        $reportCard->update([
            'promotion_decision' => $request->promotion_decision,
            'next_class_id' => $request->next_class_id,
            'vacation_date' => $request->vacation_date,
            'next_term_begins' => $request->next_term_begins,
            'next_term_fee' => $request->next_term_fee,
            'outstanding_balance' => $request->outstanding_balance,
        ]);

        return redirect()->route('admin.report-cards.show', $reportCard->id)
            ->with('success', 'Promotion decision updated successfully.');
    }

    public function autoCalculatePromotion($id)
    {
        $reportCard = ReportCard::findOrFail($id);
        $averageScore = $reportCard->average_score;
        $passingScore = 50; // Default passing score

        // Determine promotion decision based on average score
        if ($averageScore >= $passingScore) {
            $decision = 'Promoted';
            
            // Auto-assign next class
            $currentClass = $reportCard->class;
            $nextClass = SchoolClass::where('level', $currentClass->level)
                ->where('id', '>', $currentClass->id)
                ->orderBy('id')
                ->first();
            
            $reportCard->update([
                'promotion_decision' => $decision,
                'next_class_id' => $nextClass ? $nextClass->id : null,
            ]);
        } elseif ($averageScore >= 40) {
            $decision = 'Promoted on Trial';
            
            // Auto-assign next class
            $currentClass = $reportCard->class;
            $nextClass = SchoolClass::where('level', $currentClass->level)
                ->where('id', '>', $currentClass->id)
                ->orderBy('id')
                ->first();
            
            $reportCard->update([
                'promotion_decision' => $decision,
                'next_class_id' => $nextClass ? $nextClass->id : null,
            ]);
        } else {
            $decision = 'Repeated';
            $reportCard->update([
                'promotion_decision' => $decision,
                'next_class_id' => $reportCard->class_id, // Stay in same class
            ]);
        }

        return redirect()->route('admin.report-cards.edit-promotion', $reportCard->id)
            ->with('success', 'Promotion decision auto-calculated based on academic performance.');
    }

    public function editAttendance($id)
    {
        $reportCard = ReportCard::with(['student', 'class'])->findOrFail($id);
        
        return view('admin.report-cards.edit-attendance', compact('reportCard'));
    }

    public function updateAttendance(Request $request, $id)
    {
        $request->validate([
            'attendance_opened' => 'required|integer|min:0',
            'attendance_present' => 'required|integer|min:0',
            'attendance_absent' => 'required|integer|min:0',
            'attendance_late' => 'required|integer|min:0',
        ]);

        $reportCard = ReportCard::findOrFail($id);
        
        // Calculate attendance percentage
        $totalDays = $request->attendance_opened;
        $presentDays = $request->attendance_present;
        $attendancePercentage = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;
        
        $reportCard->update([
            'attendance_opened' => $request->attendance_opened,
            'attendance_present' => $request->attendance_present,
            'attendance_absent' => $request->attendance_absent,
            'attendance_late' => $request->attendance_late,
            'attendance_percentage' => $attendancePercentage,
        ]);

        return redirect()->route('admin.report-cards.show', $reportCard->id)
            ->with('success', 'Attendance summary updated successfully.');
    }

    public function generatePDF($id)
    {
        $reportCard = ReportCard::with([
            'student',
            'class',
            'academicSession',
            'term',
            'items.subject',
            'affectiveRatings.trait',
            'psychomotorRatings.trait',
            'classTeacher',
            'approvedBy',
            'publishedBy'
        ])->findOrFail($id);

        $settings = ReportSettings::getSettings();

        $pdf = PDF::loadView('admin.report-cards.pdf', compact('reportCard', 'settings'));
        
        // Set PDF options
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Times New Roman',
        ]);

        $filename = "report_card_{$reportCard->student->admission_number}_{$reportCard->academicSession->name}_{$reportCard->term->name}.pdf";
        
        // Save PDF path
        $pdfPath = "report_cards/" . $filename;
        $pdf->save(storage_path('app/public/' . $pdfPath));
        
        // Update report card with PDF URL
        $reportCard->update([
            'pdf_url' => Storage::url($pdfPath)
        ]);

        return $pdf->download($filename);
    }

    public function downloadPDF($id)
    {
        $reportCard = ReportCard::findOrFail($id);
        
        if (!$reportCard->pdf_url) {
            return redirect()->back()
                ->with('error', 'PDF not generated yet. Please generate the PDF first.');
        }

        return response()->download(public_path($reportCard->pdf_url));
    }

    public function generateQRCode($id)
    {
        $reportCard = ReportCard::findOrFail($id);
        
        if (!$reportCard->verification_code) {
            return redirect()->back()
                ->with('error', 'Verification code not generated. Please publish the report card first.');
        }

        $verificationUrl = $reportCard->verification_url;
        
        // Generate QR code
        $qrCode = QrCode::format('png')
            ->size(200)
            ->margin(2)
            ->generate($verificationUrl);
        
        // Save QR code
        $qrCodePath = "qr_codes/report_card_{$reportCard->id}.png";
        Storage::disk('public')->put($qrCodePath, $qrCode);
        
        // Update report card with QR code URL
        $reportCard->update([
            'qr_code_url' => Storage::url($qrCodePath)
        ]);

        return redirect()->route('admin.report-cards.show', $reportCard->id)
            ->with('success', 'QR code generated successfully.');
    }

    public function verifyResult($verificationCode)
    {
        $reportCard = ReportCard::where('verification_code', $verificationCode)
            ->where('status', 'published')
            ->with(['student', 'class', 'academicSession', 'term', 'items'])
            ->first();

        if (!$reportCard) {
            return view('errors.404', ['message' => 'Invalid verification code or report not published.']);
        }

        return view('public.verify-result', compact('reportCard'));
    }

    public function studentReports()
    {
        $studentId = auth()->id(); // Assuming student is logged in
        $reportCards = ReportCard::where('student_id', $studentId)
            ->where('status', 'published')
            ->with(['class', 'academicSession', 'term'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.report-cards.index', compact('reportCards'));
    }

    public function studentReportShow($id)
    {
        $studentId = auth()->id();
        $reportCard = ReportCard::where('id', $id)
            ->where('student_id', $studentId)
            ->where('status', 'published')
            ->with([
                'student',
                'class',
                'academicSession',
                'term',
                'items.subject',
                'affectiveRatings.trait',
                'psychomotorRatings.trait',
                'classTeacher',
                'approvedBy',
                'publishedBy'
            ])
            ->firstOrFail();

        return view('student.report-cards.show', compact('reportCard'));
    }

    public function parentReports()
    {
        $parentId = auth()->id();
        // Get all students associated with this parent
        $studentIds = \App\Models\ParentGuardian::where('user_id', $parentId)
            ->with('students')
            ->first()
            ->students
            ->pluck('id');

        $reportCards = ReportCard::whereIn('student_id', $studentIds)
            ->where('status', 'published')
            ->with(['student', 'class', 'academicSession', 'term'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('parent.report-cards.index', compact('reportCards'));
    }

    public function parentReportShow($id)
    {
        $parentId = auth()->id();
        // Get all students associated with this parent
        $studentIds = \App\Models\ParentGuardian::where('user_id', $parentId)
            ->with('students')
            ->first()
            ->students
            ->pluck('id');

        $reportCard = ReportCard::where('id', $id)
            ->whereIn('student_id', $studentIds)
            ->where('status', 'published')
            ->with([
                'student',
                'class',
                'academicSession',
                'term',
                'items.subject',
                'affectiveRatings.trait',
                'psychomotorRatings.trait',
                'classTeacher',
                'approvedBy',
                'publishedBy'
            ])
            ->firstOrFail();

        return view('parent.report-cards.show', compact('reportCard'));
    }
}
