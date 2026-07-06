<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ClassArm;
use App\Models\ReportCard;
use App\Models\Score;
use App\Models\ScoreBatch;
use App\Models\SessionTerm;
use App\Models\Student;
use App\Models\Term;
use App\Services\ReportCardService;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index()
    {
        return view('admin.academic-management.results.index');
    }

    public function classResults($classId, ReportCardService $reportCards)
    {
        $classArm = ClassArm::with(['schoolClass', 'students.user'])->findOrFail($classId);

        // Get current session/term from SessionTerm (source of truth)
        $currentSessionTerm = SessionTerm::where('is_current', true)->first();
        $currentSession = $currentSessionTerm
            ? AcademicSession::where('name', $currentSessionTerm->academic_year)->first()
            : AcademicSession::where('is_current', true)->first();
        $currentTerm = $currentSessionTerm
            ? Term::where('name', $currentSessionTerm->term_name)->first()
            : Term::first();

        $sessionId = request('session') ?? ($currentSession->id ?? null);
        $termId = request('term') ?? ($currentTerm->id ?? null);

        abort_unless($sessionId && $termId, 422, 'Please configure an academic session and term.');

        $scoreBatches = ScoreBatch::where('class_id', $classArm->school_class_id)
            ->where('academic_session_id', $sessionId)
            ->where('term_id', $termId)
            ->with('scores')
            ->get();

        $studentResults = [];
        foreach ($classArm->students as $student) {
            $totalScore = 0;
            $maxScore = 0;
            $subjectCount = 0;

            foreach ($scoreBatches as $batch) {
                $score = $batch->scores->firstWhere('student_id', $student->id);

                if ($score) {
                    $totalScore += $score->total;
                    $maxScore += ($batch->first_ca_max + $batch->second_ca_max + $batch->third_ca_max + $batch->exam_max);
                    $subjectCount++;
                }
            }

            $percentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;
            $finalGrade = $reportCards->gradeFor($percentage, $classArm->schoolClass->level)['grade'];

            $studentResults[$student->id] = [
                'total_score' => $totalScore,
                'maximum_score' => $maxScore,
                'average_score' => $percentage,
                'final_grade' => $finalGrade,
                'subject_count' => $subjectCount,
                'status' => $subjectCount > 0 ? 'Available' : 'Not Available',
            ];
        }

        $sortedResults = collect($studentResults)->sortByDesc('average_score');
        $lastScore = null;
        $lastPosition = 0;
        $index = 0;
        foreach ($sortedResults as $studentId => $result) {
            if ($result['subject_count'] > 0) {
                $score = round($result['average_score'], 2);
                $position = $lastScore !== null && $score === $lastScore ? $lastPosition : $index + 1;
                $studentResults[$studentId]['position'] = $position;
                $lastScore = $score;
                $lastPosition = $position;
                $index++;
            } else {
                $studentResults[$studentId]['position'] = '-';
            }
        }

        return view('admin.academic-management.results.class', compact(
            'classId',
            'classArm',
            'currentSession',
            'currentTerm',
            'sessionId',
            'termId',
            'studentResults'
        ));
    }

    public function studentResult($classId, $studentId, ReportCardService $reportCards)
    {
        $classArm = ClassArm::with(['schoolClass', 'students.user'])->findOrFail($classId);

        // Get current session/term from SessionTerm (source of truth)
        $currentSessionTerm = SessionTerm::where('is_current', true)->first();
        $currentSession = $currentSessionTerm
            ? AcademicSession::where('name', $currentSessionTerm->academic_year)->first()
            : AcademicSession::where('is_current', true)->first();
        $currentTerm = $currentSessionTerm
            ? Term::where('name', $currentSessionTerm->term_name)->first()
            : Term::first();

        $sessionId = request('session') ?? ($currentSession->id ?? null);
        $termId = request('term') ?? ($currentTerm->id ?? null);

        // Get score batches for this class, session, and term
        $scoreBatches = ScoreBatch::where('class_id', $classArm->school_class_id)
            ->where('academic_session_id', $sessionId)
            ->where('term_id', $termId)
            ->with('subject')
            ->get();

        // Get student's scores
        $subjectScores = [];
        $totalScore = 0;
        $maxScore = 0;

        foreach ($scoreBatches as $batch) {
            $score = Score::where('score_batch_id', $batch->id)
                ->where('student_id', $studentId)
                ->first();

            if ($score) {
                $subjectScores[] = [
                    'subject' => $batch->subject->name ?? 'Unknown',
                    'first_ca' => $score->first_ca,
                    'second_ca' => $score->second_ca,
                    'third_ca' => $score->third_ca,
                    'exam' => $score->exam,
                    'total' => $score->total,
                    'grade' => $score->grade,
                    'remark' => $score->remark,
                    'max_score' => $batch->first_ca_max + $batch->second_ca_max + $batch->third_ca_max + $batch->exam_max,
                ];
                $totalScore += $score->total;
                $maxScore += ($batch->first_ca_max + $batch->second_ca_max + $batch->third_ca_max + $batch->exam_max);
            }
        }

        $percentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;
        $averageScore = $percentage;
        $finalGrade = $reportCards->gradeFor($percentage, $classArm->schoolClass->level)['grade'];

        return view('admin.academic-management.results.student', compact(
            'classId',
            'studentId',
            'classArm',
            'currentSession',
            'currentTerm',
            'subjectScores',
            'totalScore',
            'maxScore',
            'averageScore',
            'finalGrade',
            'percentage'
        ));
    }

    public function generateTermlyReportCard(
        $classId,
        $studentId,
        Request $request,
        ReportCardService $reportCards
    ) {
        $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
            'term_id' => 'required|exists:terms,id',
        ]);

        $classArm = ClassArm::with(['schoolClass'])->findOrFail($classId);
        $student = Student::findOrFail($studentId);
        $session = AcademicSession::findOrFail($request->session_id);
        $term = Term::findOrFail($request->term_id);
        $reportCard = $reportCards->generate($student, $classArm, $session, $term);

        return redirect()->route('admin.academic-management.report-cards.show', $reportCard)
            ->with('success', 'Termly report card generated successfully.');
    }

    public function generateAnnualReportCard(
        $classId,
        $studentId,
        Request $request,
        ReportCardService $reportCards
    ) {
        $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
        ]);

        $classArm = ClassArm::with(['schoolClass'])->findOrFail($classId);
        $student = Student::findOrFail($studentId);
        $session = AcademicSession::findOrFail($request->session_id);
        $reportCard = $reportCards->generate($student, $classArm, $session, null, 'annual');

        return redirect()->route('admin.academic-management.report-cards.show', $reportCard)
            ->with('success', 'Annual report card generated successfully.');
    }

    public function generateBulkReportCards(
        $classId,
        Request $request,
        ReportCardService $reportCards
    ) {
        $validated = $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
            'term_id' => 'nullable|required_if:report_type,termly|exists:terms,id',
            'report_type' => 'required|in:termly,annual',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'integer|exists:students,id',
        ]);

        $classArm = ClassArm::with(['schoolClass'])->findOrFail($classId);
        $session = AcademicSession::findOrFail($validated['session_id']);
        $term = $validated['report_type'] === 'termly'
            ? Term::findOrFail($validated['term_id'])
            : null;

        $result = $reportCards->generateBulk(
            $classArm,
            $session,
            $term,
            $validated['report_type'],
            $validated['student_ids'] ?? null
        );

        $message = "{$result['generated']} report card(s) generated or refreshed.";
        if ($result['skipped']) {
            $message .= ' '.count($result['skipped']).' skipped; review the warnings below.';
        }

        return back()
            ->with($result['generated'] ? 'success' : 'error', $message)
            ->with('bulk_report_warnings', $result['skipped']);
    }

    public function viewReportCard($classId, $studentId)
    {
        $classArm = ClassArm::with(['schoolClass'])->findOrFail($classId);
        $currentSession = AcademicSession::where('is_current', true)->first();
        $currentTerm = Term::first();

        $sessionId = request('session') ?? ($currentSession->id ?? null);
        $termId = request('term') ?? ($currentTerm->id ?? null);
        $session = AcademicSession::find($sessionId);
        $term = Term::find($termId);
        $sessionTerm = $session && $term
            ? SessionTerm::where('academic_year', $session->name)
                ->where('term_name', $term->name)
                ->first()
            : null;

        $reportCard = ReportCard::where('student_id', $studentId)
            ->where('class_id', $classArm->school_class_id)
            ->where('academic_session_id', $sessionTerm?->id)
            ->where('term_id', $sessionTerm?->id)
            ->where('report_type', 'termly')
            ->first();

        if (! $reportCard) {
            return redirect()->back()
                ->with('error', 'No report card found for this student. Please generate a report card first.');
        }

        return redirect()->route('admin.academic-management.report-cards.show', $reportCard->id);
    }
}
