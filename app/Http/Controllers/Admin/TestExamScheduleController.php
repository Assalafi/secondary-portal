<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssessmentSchedule;

class TestExamScheduleController extends Controller
{
    public function index()
    {
        return view('admin.academic-management.schedule.index');
    }

    public function classSchedule($classId)
    {
        return view('admin.academic-management.schedule.class', compact('classId'));
    }

    public function create($classId)
    {
        return view('admin.academic-management.schedule.create', compact('classId'));
    }

    public function store(Request $request, $classId)
    {
        // Validation
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'assessment_type' => 'required|in:First_CA,Second_CA,Third_CA,Exam',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required',
            'status' => 'required|in:Pending,Scheduled,Completed,Cancelled',
        ]);

        // Create schedule
        AssessmentSchedule::create([
            'subject_id' => $validated['subject_id'],
            'class_id' => \App\Models\ClassArm::findOrFail($classId)->school_class_id,
            'academic_session_id' => \App\Models\AcademicSession::where('is_current', true)->first()->id ?? null,
            'term_id' => \App\Models\Term::first()->id ?? null,
            'assessment_type' => $validated['assessment_type'],
            'scheduled_date' => $validated['scheduled_date'],
            'scheduled_time' => $validated['scheduled_time'],
            'status' => $validated['status'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.academic-management.test-exam-schedule.class', $classId)
            ->with('success', 'Schedule created successfully');
    }
}
