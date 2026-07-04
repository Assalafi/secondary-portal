<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassArm;
use App\Models\ClassSubject;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Assignment;
use App\Models\Timetable;
use App\Models\Attendance;
use App\Models\ScoreBatch;
use App\Models\Score;
use App\Models\AcademicSession;
use App\Models\Term;
use App\Models\SessionTerm;
use App\Models\GradingSystem;
use App\Models\SchoolSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    /**
     * Teacher Dashboard
     */
    public function dashboard()
    {
        $teacher = Auth::user();

        // Get classes where teacher is class teacher
        $myClassArms = ClassArm::where('class_teacher_id', $teacher->id)
            ->with(['schoolClass', 'students'])
            ->get();

        // Get subjects assigned to this teacher
        $mySubjects = ClassSubject::where('teacher_id', $teacher->id)
            ->with(['classArm.schoolClass', 'subject'])
            ->get();

        // Total students in teacher's classes
        $totalStudents = 0;
        foreach ($myClassArms as $classArm) {
            $totalStudents += $classArm->students->count();
        }

        // Also count students from subject assignments (unique)
        $subjectClassArmIds = $mySubjects->pluck('class_arm_id')->unique();
        $totalSubjectStudents = Student::whereIn('current_class_arm_id', $subjectClassArmIds)
            ->where('status', 'Active')
            ->count();

        // Get current session/term
        $currentSessionTerm = SessionTerm::where('is_current', true)->first();
        $currentSession = $currentSessionTerm
            ? AcademicSession::where('name', $currentSessionTerm->academic_year)->first()
            : AcademicSession::where('is_current', true)->first();
        $currentTerm = $currentSessionTerm
            ? Term::where('name', $currentSessionTerm->term_name)->first()
            : Term::first();

        // Upcoming assignments
        $upcomingAssignments = Assignment::where('teacher_id', $teacher->id)
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Today's timetable
        $today = now()->format('l'); // Monday, Tuesday, etc.
        $todaySchedule = Timetable::where('teacher_id', $teacher->id)
            ->where('day', $today)
            ->where('status', 'active')
            ->with(['classArm.schoolClass', 'subject'])
            ->orderBy('start_time')
            ->get();

        // Recent score uploads
        $recentScores = ScoreBatch::where('uploaded_by', $teacher->id)
            ->with(['subject', 'class'])
            ->orderBy('uploaded_at', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            'total_classes' => $myClassArms->count(),
            'total_subjects' => $mySubjects->count(),
            'total_students' => max($totalStudents, $totalSubjectStudents),
            'total_assignments' => Assignment::where('teacher_id', $teacher->id)->count(),
        ];

        return view('teacher.dashboard', compact(
            'stats', 'myClassArms', 'mySubjects', 'upcomingAssignments',
            'todaySchedule', 'recentScores', 'currentSession', 'currentTerm'
        ));
    }

    /**
     * My Classes page
     */
    public function myClasses()
    {
        $teacher = Auth::user();

        // Classes where teacher is class teacher
        $classTeacherArms = ClassArm::where('class_teacher_id', $teacher->id)
            ->with(['schoolClass', 'students.user'])
            ->get();

        // Classes where teacher teaches subjects
        $subjectClasses = ClassSubject::where('teacher_id', $teacher->id)
            ->with(['classArm.schoolClass', 'classArm.students', 'subject'])
            ->get()
            ->groupBy('class_arm_id');

        return view('teacher.my-classes', compact('classTeacherArms', 'subjectClasses'));
    }

    /**
     * My Subjects page
     */
    public function mySubjects()
    {
        $teacher = Auth::user();

        $mySubjects = ClassSubject::where('teacher_id', $teacher->id)
            ->with(['classArm.schoolClass', 'subject'])
            ->get()
            ->groupBy(function ($item) {
                return $item->subject->name ?? 'Unknown';
            });

        return view('teacher.my-subjects', compact('mySubjects'));
    }

    /**
     * Score upload index - list classes/subjects to upload scores
     */
    public function scoresIndex()
    {
        $teacher = Auth::user();

        $mySubjects = ClassSubject::where('teacher_id', $teacher->id)
            ->with(['classArm.schoolClass', 'subject'])
            ->get();

        // Get current session/term
        $currentSessionTerm = SessionTerm::where('is_current', true)->first();
        $currentSession = $currentSessionTerm
            ? AcademicSession::where('name', $currentSessionTerm->academic_year)->first()
            : AcademicSession::where('is_current', true)->first();
        $currentTerm = $currentSessionTerm
            ? Term::where('name', $currentSessionTerm->term_name)->first()
            : Term::first();

        return view('teacher.scores.index', compact('mySubjects', 'currentSession', 'currentTerm'));
    }

    /**
     * Score upload form for a specific class/subject
     */
    public function scoresUpload($classArmId, $subjectId)
    {
        $teacher = Auth::user();

        // Verify teacher teaches this subject in this class
        $classSubject = ClassSubject::where('teacher_id', $teacher->id)
            ->where('class_arm_id', $classArmId)
            ->where('subject_id', $subjectId)
            ->firstOrFail();

        $classArm = ClassArm::with(['schoolClass', 'students.user'])->findOrFail($classArmId);
        $subject = Subject::findOrFail($subjectId);
        $level = $classArm->schoolClass->level ?? 'Secondary';

        // Get grading system
        $gradingSystems = GradingSystem::where('level', $level)
            ->where('is_active', true)
            ->orderBy('min_score')
            ->get();

        // Get current session/term
        $currentSessionTerm = SessionTerm::where('is_current', true)->first();
        $currentSession = $currentSessionTerm
            ? AcademicSession::where('name', $currentSessionTerm->academic_year)->first()
            : AcademicSession::where('is_current', true)->first();
        $currentTerm = $currentSessionTerm
            ? Term::where('name', $currentSessionTerm->term_name)->first()
            : Term::first();

        // Get existing score batch
        $scoreBatch = ScoreBatch::where('class_id', $classArm->school_class_id)
            ->where('subject_id', $subjectId)
            ->where('academic_session_id', $currentSession->id ?? null)
            ->where('term_id', $currentTerm->id ?? null)
            ->first();

        $existingScores = [];
        if ($scoreBatch) {
            $existingScores = Score::where('score_batch_id', $scoreBatch->id)
                ->get()
                ->keyBy('student_id');
        }

        return view('teacher.scores.upload', compact(
            'classArm', 'subject', 'gradingSystems', 'level',
            'existingScores', 'scoreBatch', 'currentSession', 'currentTerm'
        ));
    }

    /**
     * Store scores
     */
    public function scoresStore(Request $request)
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

        $scoreBatch->update([
            'first_ca_max' => $validated['first_ca_max'],
            'second_ca_max' => $validated['second_ca_max'],
            'third_ca_max' => $validated['third_ca_max'],
            'exam_max' => $validated['exam_max'],
            'status' => 'Uploaded',
            'uploaded_by' => auth()->id(),
            'uploaded_at' => now(),
        ]);

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

        return redirect()->route('teacher.scores.index')
            ->with('success', 'Scores uploaded successfully!');
    }

    /**
     * Assignments index
     */
    public function assignmentsIndex()
    {
        $teacher = Auth::user();

        $assignments = Assignment::where('teacher_id', $teacher->id)
            ->with(['subject', 'classArm.schoolClass'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('teacher.assignments.index', compact('assignments'));
    }

    /**
     * Create assignment form
     */
    public function assignmentsCreate()
    {
        $teacher = Auth::user();

        $mySubjects = ClassSubject::where('teacher_id', $teacher->id)
            ->with(['classArm.schoolClass', 'subject'])
            ->get();

        return view('teacher.assignments.create', compact('mySubjects'));
    }

    /**
     * Store assignment
     */
    public function assignmentsStore(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'question' => 'required|string',
            'instructions' => 'nullable|string',
            'submission_info' => 'nullable|string',
            'class_arm_id' => 'required|exists:class_arms,id',
            'subject_id' => 'required|exists:subjects,id',
            'due_date' => 'required|date|after:today',
        ]);

        $teacher = Auth::user();
        $classArm = ClassArm::with('schoolClass')->findOrFail($validated['class_arm_id']);

        Assignment::create([
            'title' => $validated['title'],
            'question' => $validated['question'],
            'instructions' => $validated['instructions'],
            'submission_info' => $validated['submission_info'],
            'level' => $classArm->schoolClass->level,
            'class_id' => $classArm->school_class_id,
            'class_arm_id' => $validated['class_arm_id'],
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $teacher->id,
            'due_date' => $validated['due_date'],
            'status' => 'Published',
            'created_by' => $teacher->id,
            'published_at' => now(),
        ]);

        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Assignment created successfully!');
    }

    /**
     * Timetable
     */
    public function timetable()
    {
        $teacher = Auth::user();

        $timetable = Timetable::where('teacher_id', $teacher->id)
            ->where('status', 'active')
            ->with(['classArm.schoolClass', 'subject'])
            ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday')")
            ->orderBy('start_time')
            ->get()
            ->groupBy('day');

        return view('teacher.timetable', compact('timetable'));
    }

    /**
     * Attendance index - list teacher's classes for marking
     */
    public function attendanceIndex()
    {
        $teacher = Auth::user();

        $myClassArms = ClassArm::where('class_teacher_id', $teacher->id)
            ->with(['schoolClass', 'students'])
            ->get();

        // Also include class arms where teacher teaches subjects
        $subjectClassArmIds = ClassSubject::where('teacher_id', $teacher->id)
            ->pluck('class_arm_id')
            ->unique();

        $subjectClassArms = ClassArm::whereIn('id', $subjectClassArmIds)
            ->whereNotIn('id', $myClassArms->pluck('id'))
            ->with(['schoolClass', 'students'])
            ->get();

        return view('teacher.attendance.index', compact('myClassArms', 'subjectClassArms'));
    }

    /**
     * Mark attendance for a class
     */
    public function attendanceMark($classArmId)
    {
        $classArm = ClassArm::with(['schoolClass', 'students.user'])->findOrFail($classArmId);

        // Get today's existing attendance
        $today = now()->format('Y-m-d');
        $existingAttendance = Attendance::where('class_arm_id', $classArmId)
            ->where('date', $today)
            ->get()
            ->keyBy('student_id');

        return view('teacher.attendance.mark', compact('classArm', 'existingAttendance', 'today'));
    }

    /**
     * Store attendance
     */
    public function attendanceStore(Request $request)
    {
        $validated = $request->validate([
            'class_arm_id' => 'required|exists:class_arms,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
        ]);

        foreach ($validated['attendance'] as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'class_arm_id' => $validated['class_arm_id'],
                    'date' => $validated['date'],
                ],
                [
                    'status' => $status,
                    'marked_by' => auth()->id(),
                ]
            );
        }

        return redirect()->route('teacher.attendance.index')
            ->with('success', 'Attendance marked successfully!');
    }

    /**
     * Profile page
     */
    public function profile()
    {
        $teacher = Auth::user();
        $staff = $teacher->staff;

        return view('teacher.profile', compact('teacher', 'staff'));
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password updated successfully!');
    }
}
