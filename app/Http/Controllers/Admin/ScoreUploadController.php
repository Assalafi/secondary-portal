<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ClassArm;
use App\Models\GradingSystem;
use App\Models\Score;
use App\Models\ScoreBatch;
use App\Models\SessionTerm;
use App\Models\Student;
use App\Models\Term;
use Illuminate\Http\Request;

class ScoreUploadController extends Controller
{
    public function index()
    {
        return view('admin.academic-management.score-upload.index');
    }

    public function classSubject($classId)
    {
        return view('admin.academic-management.score-upload.class', compact('classId'));
    }

    public function subject($classId, $subjectId)
    {
        $classArm = ClassArm::with(['schoolClass', 'students.user'])->findOrFail($classId);
        $level = $classArm->schoolClass->level ?? 'Primary';

        // Get grading system for this level
        $gradingSystems = GradingSystem::where('level', $level)
            ->where('is_active', true)
            ->orderBy('min_score')
            ->get();

        // Get current session and term from SessionTerm (source of truth)
        $currentSessionTerm = SessionTerm::where('is_current', true)->first();
        $currentSession = $currentSessionTerm
            ? AcademicSession::where('name', $currentSessionTerm->academic_year)->first()
            : AcademicSession::where('is_current', true)->first();
        $currentTerm = $currentSessionTerm
            ? Term::where('name', $currentSessionTerm->term_name)->first()
            : Term::first();

        // Get existing score batch for this class, subject, session, and term
        $scoreBatch = ScoreBatch::where('class_id', $classArm->school_class_id)
            ->where('subject_id', $subjectId)
            ->where('academic_session_id', $currentSession->id ?? null)
            ->where('term_id', $currentTerm->id ?? null)
            ->first();

        // Get existing scores if batch exists
        $existingScores = [];
        if ($scoreBatch) {
            $existingScores = Score::where('score_batch_id', $scoreBatch->id)
                ->get()
                ->keyBy('student_id');
        }

        return view('admin.academic-management.score-upload.subject', compact(
            'classId',
            'subjectId',
            'gradingSystems',
            'level',
            'existingScores',
            'scoreBatch',
            'currentSession',
            'currentTerm'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:class_arms,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'term_id' => 'required|exists:terms,id',
            'first_ca_max' => 'required|numeric|min:0',
            'second_ca_max' => 'required|numeric|min:0',
            'third_ca_max' => 'required|numeric|min:0',
            'exam_max' => 'required|numeric|min:0',
            'scores' => 'required|array',
        ]);

        $classArm = ClassArm::findOrFail($validated['class_id']);

        // Find or create score batch
        $scoreBatch = ScoreBatch::firstOrCreate(
            [
                'class_id' => $classArm->school_class_id,
                'subject_id' => $validated['subject_id'],
                'academic_session_id' => $validated['academic_session_id'],
                'term_id' => $validated['term_id'],
            ],
            [
                'first_ca_max' => $validated['first_ca_max'],
                'second_ca_max' => $validated['second_ca_max'],
                'third_ca_max' => $validated['third_ca_max'],
                'exam_max' => $validated['exam_max'],
                'status' => 'Uploaded',
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now(),
            ]
        );

        // Update max marks and status if batch already exists
        $scoreBatch->update([
            'first_ca_max' => $validated['first_ca_max'],
            'second_ca_max' => $validated['second_ca_max'],
            'third_ca_max' => $validated['third_ca_max'],
            'exam_max' => $validated['exam_max'],
            'status' => 'Uploaded',
            'uploaded_by' => auth()->id(),
            'uploaded_at' => now(),
        ]);

        // Save or update scores for each student
        foreach ($validated['scores'] as $studentId => $scoreData) {
            Score::updateOrCreate(
                [
                    'score_batch_id' => $scoreBatch->id,
                    'student_id' => $studentId,
                ],
                [
                    'first_ca' => $scoreData['first_ca'] ?? 0,
                    'second_ca' => $scoreData['second_ca'] ?? 0,
                    'third_ca' => $scoreData['third_ca'] ?? 0,
                    'exam' => $scoreData['exam'] ?? 0,
                    'total' => $scoreData['total'] ?? 0,
                    'grade' => $scoreData['grade'] ?? 'F',
                    'remark' => $scoreData['remark'] ?? 'Fail',
                ]
            );
        }

        return back()->with('success', 'Scores saved successfully');
    }
}
