<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\AffectiveTrait;
use App\Models\ClassArm;
use App\Models\PsychomotorTrait;
use App\Models\ReportCard;
use App\Models\ReportSettings;
use App\Models\SchoolClass;
use App\Models\SchoolSettings;
use App\Models\Student;
use App\Models\StudentAffectiveRating;
use App\Models\StudentPsychomotorRating;
use App\Models\Term;
use App\Services\ReportCardService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReportCardController extends Controller
{
    public function generateTermlyReport(
        Request $request,
        $classId,
        $studentId,
        ReportCardService $reportCards
    ) {
        $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
            'term_id' => 'required|exists:terms,id',
        ]);

        $student = Student::findOrFail($studentId);
        $class = SchoolClass::findOrFail($classId);
        $classArm = ClassArm::whereKey($student->current_class_arm_id)
            ->where('school_class_id', $class->id)
            ->firstOrFail();
        $session = AcademicSession::findOrFail($request->session_id);
        $term = Term::findOrFail($request->term_id);
        $reportCard = $reportCards->generate($student, $classArm, $session, $term);

        return redirect()->route('admin.academic-management.report-cards.show', $reportCard)
            ->with('success', 'Termly report card generated successfully.');
    }

    public function generateAnnualReport(
        Request $request,
        $classId,
        $studentId,
        ReportCardService $reportCards
    ) {
        $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
        ]);

        $student = Student::findOrFail($studentId);
        $class = SchoolClass::findOrFail($classId);
        $classArm = ClassArm::whereKey($student->current_class_arm_id)
            ->where('school_class_id', $class->id)
            ->firstOrFail();
        $session = AcademicSession::findOrFail($request->session_id);
        $reportCard = $reportCards->generate($student, $classArm, $session, null, 'annual');

        return redirect()->route('admin.academic-management.report-cards.show', $reportCard)
            ->with('success', 'Annual report card generated successfully.');
    }

    public function index()
    {
        $reportCards = ReportCard::with(['student', 'class', 'academicSession', 'term'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.report-cards.index', compact('reportCards'));
    }

    public function show($id)
    {
        $reportCard = $this->loadReportCard($id);

        return view('admin.report-cards.show', $this->cardViewData($reportCard));
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

        return redirect()->route('admin.academic-management.report-cards.show', $reportCard)
            ->with('success', 'Comments updated successfully.');
    }

    public function editDomainRatings($id)
    {
        $reportCard = ReportCard::with([
            'student',
            'class',
            'affectiveRatings.trait',
            'psychomotorRatings.trait',
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
            'affective_ratings.*' => 'required|integer|between:1,5',
            'psychomotor_ratings' => 'required|array',
            'psychomotor_ratings.*' => 'required|integer|between:1,5',
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
                    'student_id' => $reportCard->student_id,
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
                    'student_id' => $reportCard->student_id,
                    'rating_value' => $rating,
                    'rated_by' => auth()->id(),
                ]
            );
        }

        return redirect()->route('admin.academic-management.report-cards.show', $reportCard)
            ->with('success', 'Domain ratings updated successfully.');
    }

    public function approve($id)
    {
        $reportCard = ReportCard::findOrFail($id);
        $this->approveReportCard($reportCard);

        return redirect()->route('admin.academic-management.report-cards.show', $reportCard)
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

        $this->publishReportCard($reportCard);

        return redirect()->route('admin.academic-management.report-cards.show', $reportCard)
            ->with('success', 'Report card published successfully.');
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,publish,approve_publish',
            'report_card_ids' => 'required|array|min:1',
            'report_card_ids.*' => 'integer|exists:report_cards,id',
        ]);

        $settings = ReportSettings::getSettings();
        $reportCards = ReportCard::whereIn('id', $validated['report_card_ids'])->get();

        $approved = 0;
        $published = 0;
        $skipped = [];

        foreach ($reportCards as $reportCard) {
            if ($validated['action'] === 'approve') {
                if ($reportCard->status === 'draft') {
                    $this->approveReportCard($reportCard);
                    $approved++;
                } else {
                    $skipped[] = $this->bulkSkipLabel($reportCard, 'Only draft report cards can be approved.');
                }

                continue;
            }

            if ($validated['action'] === 'publish') {
                if ($reportCard->status === 'published') {
                    $skipped[] = $this->bulkSkipLabel($reportCard, 'Already published.');
                    continue;
                }

                if ($settings->require_principal_approval && $reportCard->status !== 'approved') {
                    $skipped[] = $this->bulkSkipLabel($reportCard, 'Approval is required before publishing.');
                    continue;
                }

                $this->publishReportCard($reportCard);
                $published++;
                continue;
            }

            if ($validated['action'] === 'approve_publish') {
                if ($reportCard->status === 'published') {
                    $skipped[] = $this->bulkSkipLabel($reportCard, 'Already published.');
                    continue;
                }

                if ($reportCard->status === 'draft') {
                    $this->approveReportCard($reportCard);
                    $reportCard->refresh();
                    $approved++;
                }

                if ($reportCard->status === 'approved') {
                    $this->publishReportCard($reportCard);
                    $published++;
                } else {
                    $skipped[] = $this->bulkSkipLabel($reportCard, 'Only draft or approved report cards can be published.');
                }
            }
        }

        $messageParts = [];

        if ($approved > 0) {
            $messageParts[] = "{$approved} report card" . ($approved === 1 ? '' : 's') . ' approved';
        }

        if ($published > 0) {
            $messageParts[] = "{$published} report card" . ($published === 1 ? '' : 's') . ' published';
        }

        return redirect()
            ->route('admin.academic-management.report-cards.index')
            ->with($messageParts ? 'success' : 'info', $messageParts ? implode(' and ', $messageParts) . '.' : 'No report cards were updated.')
            ->with('bulk_report_card_warnings', $skipped);
    }

    public function broadsheet($classId)
    {
        $class = SchoolClass::findOrFail($classId);

        $request = request();
        $sessionId = $request->query('session_id');
        $termId = $request->query('term_id');

        $reportCards = ReportCard::where('class_id', $classId)
            ->when($sessionId, function ($query) use ($sessionId) {
                return $query->where('academic_session_id', $sessionId);
            })
            ->when($termId, function ($query) use ($termId) {
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

        return redirect()->route('admin.academic-management.report-cards.show', $reportCard)
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

        return redirect()->route('admin.academic-management.report-cards.edit-promotion', $reportCard)
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
            'attendance_present' => 'required|integer|min:0|lte:attendance_opened',
            'attendance_absent' => 'required|integer|min:0|lte:attendance_opened',
            'attendance_late' => 'required|integer|min:0|lte:attendance_present',
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

        return redirect()->route('admin.academic-management.report-cards.show', $reportCard)
            ->with('success', 'Attendance summary updated successfully.');
    }

    public function generatePDF($id)
    {
        return $this->downloadReportPdf($this->loadReportCard($id));
    }

    public function downloadPDF($id)
    {
        $reportCard = ReportCard::findOrFail($id);

        if (! $reportCard->pdf_url) {
            return redirect()->back()
                ->with('error', 'PDF not generated yet. Please generate the PDF first.');
        }

        return response()->download(public_path($reportCard->pdf_url));
    }

    public function generateQRCode($id)
    {
        $reportCard = ReportCard::findOrFail($id);

        if (! $reportCard->verification_code) {
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
            'qr_code_url' => Storage::url($qrCodePath),
        ]);

        return redirect()->route('admin.academic-management.report-cards.show', $reportCard)
            ->with('success', 'QR code generated successfully.');
    }

    public function verifyResult($verificationCode)
    {
        $reportCard = ReportCard::where('verification_code', $verificationCode)
            ->where('status', 'published')
            ->with($this->cardRelations())
            ->first();

        if (! $reportCard) {
            return view('errors.404', ['message' => 'Invalid verification code or report not published.']);
        }

        return view('public.verify-result', $this->cardViewData($reportCard));
    }

    public function studentReports()
    {
        $user = auth()->user();
        $student = $user->student;
        $studentId = $student ? $student->id : 0;
        $reportCards = ReportCard::where('student_id', $studentId)
            ->where('status', 'published')
            ->with(['class', 'academicSession', 'term'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.report-cards.index', compact('reportCards'));
    }

    public function studentReportShow($id)
    {
        $user = auth()->user();
        $student = $user->student;
        $studentId = $student ? $student->id : 0;
        $reportCard = ReportCard::where('id', $id)
            ->where('student_id', $studentId)
            ->where('status', 'published')
            ->with($this->cardRelations())
            ->firstOrFail();

        return view('student.report-cards.show', $this->cardViewData($reportCard));
    }

    public function studentDownloadPDF($id)
    {
        $studentId = auth()->user()->student?->id ?? 0;
        $reportCard = ReportCard::whereKey($id)
            ->where('student_id', $studentId)
            ->where('status', 'published')
            ->with($this->cardRelations())
            ->firstOrFail();

        return $this->downloadReportPdf($reportCard);
    }

    public function parentReports()
    {
        $studentIds = $this->parentStudentIds();

        $reportCards = ReportCard::whereIn('student_id', $studentIds)
            ->where('status', 'published')
            ->with(['student', 'class', 'academicSession', 'term'])
            ->orderBy('created_at', 'desc')
            ->get();

        $reportSettings = ReportSettings::getSettings();

        return view('parent.report-cards.index', compact('reportCards', 'reportSettings'));
    }

    public function parentReportShow($id)
    {
        $studentIds = $this->parentStudentIds();

        $reportCard = ReportCard::where('id', $id)
            ->whereIn('student_id', $studentIds)
            ->where('status', 'published')
            ->with($this->cardRelations())
            ->firstOrFail();

        return view('parent.report-cards.show', $this->cardViewData($reportCard));
    }

    public function parentDownloadPDF($id)
    {
        abort_unless(ReportSettings::getSettings()->allow_parent_download, 403);

        $reportCard = ReportCard::whereKey($id)
            ->whereIn('student_id', $this->parentStudentIds())
            ->where('status', 'published')
            ->with($this->cardRelations())
            ->firstOrFail();

        return $this->downloadReportPdf($reportCard);
    }

    private function parentStudentIds()
    {
        return auth()->user()?->dependents()->pluck('students.id') ?? collect();
    }

    private function approveReportCard(ReportCard $reportCard): void
    {
        if ($reportCard->status === 'published') {
            return;
        }

        $reportCard->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);
    }

    private function publishReportCard(ReportCard $reportCard): void
    {
        if (! $reportCard->verification_code) {
            $reportCard->verification_code = 'RPT-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));
        }

        $reportCard->status = 'published';
        $reportCard->published_by = auth()->id();
        $reportCard->published_at = now();
        $reportCard->verification_url = url("/verify-result/{$reportCard->verification_code}");
        $reportCard->save();
    }

    private function bulkSkipLabel(ReportCard $reportCard, string $reason): string
    {
        $reportCard->loadMissing('student');
        $studentName = collect([
            $reportCard->student?->surname,
            $reportCard->student?->first_name,
        ])->filter()->implode(', ');

        return ($studentName ?: "Report card #{$reportCard->id}") . ": {$reason}";
    }

    private function cardRelations(): array
    {
        return [
            'student',
            'class',
            'academicSession',
            'term',
            'items.subject',
            'affectiveRatings.trait',
            'psychomotorRatings.trait',
            'classTeacher.user',
            'approvedBy',
            'publishedBy',
            'nextClass',
        ];
    }

    private function loadReportCard($id): ReportCard
    {
        return ReportCard::with($this->cardRelations())->findOrFail($id);
    }

    private function cardViewData(ReportCard $reportCard): array
    {
        return [
            'reportCard' => $reportCard,
            'reportSettings' => ReportSettings::getSettings(),
            'schoolSettings' => SchoolSettings::first(),
        ];
    }

    private function downloadReportPdf(ReportCard $reportCard)
    {
        $reportCard->loadMissing($this->cardRelations());
        $data = $this->cardViewData($reportCard);
        $pdf = PDF::loadView('admin.report-cards.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Times New Roman',
            ]);

        $filename = sprintf(
            'report-card-%s-%s-%s.pdf',
            Str::slug($reportCard->student->admission_no ?: $reportCard->student->full_name),
            Str::slug($reportCard->session_name),
            Str::slug($reportCard->term_name)
        );
        $pdfPath = 'report_cards/'.$filename;

        Storage::disk('public')->makeDirectory('report_cards');
        $pdf->save(storage_path('app/public/'.$pdfPath));
        $reportCard->update(['pdf_url' => Storage::disk('public')->url($pdfPath)]);

        return $pdf->download($filename);
    }
}
