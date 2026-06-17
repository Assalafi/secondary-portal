<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassArm;
use App\Models\ScoreBatch;
use App\Models\Score;
use App\Models\AcademicSession;
use App\Models\Term;
use App\Models\ReportCard;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Http\Controllers\ReportCardController;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index()
    {
        return view('admin.academic-management.results.index');
    }

    public function classResults($classId)
    {
        $classArm = ClassArm::with(['schoolClass', 'students.user'])->findOrFail($classId);
        $currentSession = AcademicSession::where('is_current', true)->first();
        $currentTerm = Term::first();
        
        $sessionId = request('session') ?? ($currentSession->id ?? null);
        $termId = request('term') ?? ($currentTerm->id ?? null);
        
        // Get score batches for this class, session, and term
        $scoreBatches = ScoreBatch::where('class_id', $classArm->school_class_id)
            ->where('academic_session_id', $sessionId)
            ->where('term_id', $termId)
            ->get();
        
        // Calculate results for each student
        $studentResults = [];
        foreach ($classArm->students as $student) {
            $totalScore = 0;
            $maxScore = 0;
            $subjectCount = 0;
            
            foreach ($scoreBatches as $batch) {
                $score = Score::where('score_batch_id', $batch->id)
                    ->where('student_id', $student->id)
                    ->first();
                
                if ($score) {
                    $totalScore += $score->total;
                    $maxScore += ($batch->first_ca_max + $batch->second_ca_max + $batch->third_ca_max + $batch->exam_max);
                    $subjectCount++;
                }
            }
            
            $averageScore = $subjectCount > 0 ? ($totalScore / $subjectCount) : 0;
            $percentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;
            
            // Calculate grade based on percentage
            $finalGrade = $this->calculateGrade($percentage, $classArm->schoolClass->level ?? 'Primary');
            
            $studentResults[$student->id] = [
                'total_score' => $totalScore,
                'maximum_score' => $maxScore,
                'average_score' => $averageScore,
                'final_grade' => $finalGrade,
                'subject_count' => $subjectCount,
                'status' => $subjectCount > 0 ? 'Available' : 'Not Available',
            ];
        }
        
        // Calculate positions
        $sortedResults = collect($studentResults)->sortByDesc('total_score');
        $position = 1;
        foreach ($sortedResults as $studentId => $result) {
            if ($result['subject_count'] > 0) {
                $studentResults[$studentId]['position'] = $position;
                $position++;
            } else {
                $studentResults[$studentId]['position'] = '-';
            }
        }
        
        return view('admin.academic-management.results.class', compact(
            'classId', 
            'classArm', 
            'currentSession', 
            'currentTerm',
            'studentResults'
        ));
    }

    public function studentResult($classId, $studentId)
    {
        $classArm = ClassArm::with(['schoolClass', 'students.user'])->findOrFail($classId);
        $currentSession = AcademicSession::where('is_current', true)->first();
        $currentTerm = Term::first();
        
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
        
        $averageScore = count($subjectScores) > 0 ? ($totalScore / count($subjectScores)) : 0;
        $percentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;
        $finalGrade = $this->calculateGrade($percentage, $classArm->schoolClass->level ?? 'Primary');
        
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

    public function generateTermlyReportCard($classId, $studentId, Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
            'term_id' => 'required|exists:terms,id',
        ]);

        $classArm = ClassArm::with(['schoolClass'])->findOrFail($classId);
        $student = Student::findOrFail($studentId);

        // Check if report card already exists
        $existingReport = ReportCard::where('student_id', $studentId)
            ->where('class_id', $classArm->school_class_id)
            ->where('academic_session_id', $request->session_id)
            ->where('term_id', $request->term_id)
            ->where('report_type', 'termly')
            ->first();

        if ($existingReport) {
            return redirect()->route('admin.academic-management.report-cards.show', $existingReport->id)
                ->with('info', 'Report card already exists for this student.');
        }

        // Generate report card using the ReportCardController
        $reportCardController = new ReportCardController();
        return $reportCardController->generateTermlyReport($request, $classArm->school_class_id, $studentId);
    }

    public function generateAnnualReportCard($classId, $studentId, Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
        ]);

        $classArm = ClassArm::with(['schoolClass'])->findOrFail($classId);
        $student = Student::findOrFail($studentId);

        // Check if annual report already exists
        $existingReport = ReportCard::where('student_id', $studentId)
            ->where('class_id', $classArm->school_class_id)
            ->where('academic_session_id', $request->session_id)
            ->where('report_type', 'annual')
            ->first();

        if ($existingReport) {
            return redirect()->route('admin.academic-management.report-cards.show', $existingReport->id)
                ->with('info', 'Annual report card already exists for this student.');
        }

        // Generate report card using the ReportCardController
        $reportCardController = new ReportCardController();
        return $reportCardController->generateAnnualReport($request, $classArm->school_class_id, $studentId);
    }

    public function viewReportCard($classId, $studentId)
    {
        $classArm = ClassArm::with(['schoolClass'])->findOrFail($classId);
        $currentSession = AcademicSession::where('is_current', true)->first();
        $currentTerm = Term::first();
        
        $sessionId = request('session') ?? ($currentSession->id ?? null);
        $termId = request('term') ?? ($currentTerm->id ?? null);

        // Find the report card
        $reportCard = ReportCard::where('student_id', $studentId)
            ->where('class_id', $classArm->school_class_id)
            ->where('academic_session_id', $sessionId)
            ->where('term_id', $termId)
            ->where('report_type', 'termly')
            ->first();

        if (!$reportCard) {
            return redirect()->back()
                ->with('error', 'No report card found for this student. Please generate a report card first.');
        }

        return redirect()->route('admin.academic-management.report-cards.show', $reportCard->id);
    }
    
    private function calculateGrade($percentage, $level)
    {
        if ($level === 'Nursery') {
            if ($percentage >= 90) return 'Excellent';
            if ($percentage >= 80) return 'Very Good';
            if ($percentage >= 70) return 'Good';
            if ($percentage >= 60) return 'Fair';
            return 'Needs Improvement';
        }
        
        if ($level === 'SS') {
            if ($percentage >= 75) return 'A1';
            if ($percentage >= 70) return 'B2';
            if ($percentage >= 65) return 'B3';
            if ($percentage >= 60) return 'C4';
            if ($percentage >= 55) return 'C5';
            if ($percentage >= 50) return 'C6';
            if ($percentage >= 45) return 'D7';
            if ($percentage >= 40) return 'E8';
            return 'F9';
        }
        
        // Primary and JSS
        if ($percentage >= 70) return 'A';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 45) return 'D';
        if ($percentage >= 40) return 'E';
        return 'F';
    }
}
