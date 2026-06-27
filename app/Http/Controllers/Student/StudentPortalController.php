<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Score;
use App\Models\ReportCard;
use App\Models\AcademicSession;
use App\Models\Term;
use App\Models\Assignment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentPortalController extends Controller
{
    // ─── Results ───
    public function results()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return view('student.results.index', ['results' => collect(), 'student' => null]);
        }

        $student->load('classArm.schoolClass');

        $results = Score::where('student_id', $student->id)
            ->with(['scoreBatch.subject', 'scoreBatch.academicSession', 'scoreBatch.term'])
            ->get()
            ->groupBy(function ($score) {
                $session = optional($score->scoreBatch->academicSession)->name ?? 'N/A';
                $term = optional($score->scoreBatch->term)->name ?? 'N/A';
                return $session . ' - ' . $term;
            });

        return view('student.results.index', compact('results', 'student'));
    }

    public function resultShow($sessionTermKey)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.results.index');
        }

        $student->load('classArm.schoolClass');

        $results = Score::where('student_id', $student->id)
            ->with(['scoreBatch.subject', 'scoreBatch.academicSession', 'scoreBatch.term'])
            ->get();

        return view('student.results.show', compact('results', 'student'));
    }

    // ─── Attendance ───
    public function attendance(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return view('student.attendance.index', [
                'attendanceRecords' => collect(),
                'stats' => ['total' => 0, 'present' => 0, 'absent' => 0, 'late' => 0, 'rate' => 0],
                'student' => null,
                'selectedMonth' => now()->format('Y-m'),
            ]);
        }

        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($selectedMonth)->startOfMonth();
        $endDate = Carbon::parse($selectedMonth)->endOfMonth();

        $attendanceRecords = Attendance::where('student_id', $student->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        $total = $attendanceRecords->count();
        $present = $attendanceRecords->where('status', 'present')->count();
        $absent = $attendanceRecords->where('status', 'absent')->count();
        $late = $attendanceRecords->where('status', 'late')->count();

        $stats = [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
        ];

        return view('student.attendance.index', compact('attendanceRecords', 'stats', 'student', 'selectedMonth'));
    }

    // ─── Timetable ───
    public function timetable()
    {
        $user = Auth::user();
        $student = $user->student;

        $subjects = collect();
        if ($student && $student->classArm) {
            $student->load('classArm.schoolClass', 'classArm.subjects');
            $subjects = $student->classArm->subjects ?? collect();
        }

        return view('student.timetable', compact('student', 'subjects'));
    }

    // ─── Assignments ───
    public function assignments()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return view('student.assignments.index', ['assignments' => collect(), 'student' => null]);
        }

        $student->load('classArm.schoolClass');

        // Get assignments specifically for the student's class or arm
        // Logic: Show assignment if it matches the student's class_arm_id
        // OR if it matches the student's class_id AND has no specific arm
        $assignments = Assignment::where('status', 'Active')
            ->where(function($query) use ($student) {
                $query->where('class_arm_id', $student->class_arm_id)
                      ->orWhere(function($q) use ($student) {
                          $q->where('class_id', $student->classArm->school_class_id)
                            ->whereNull('class_arm_id');
                      });
            })
            ->with(['subject', 'class', 'classArm', 'teacher'])
            ->orderBy('due_date', 'asc')
            ->get();

        return view('student.assignments.index', compact('assignments', 'student'));
    }

    // ─── Profile ───
    public function profile()
    {
        $user = Auth::user();
        $student = $user->student;

        if ($student) {
            $student->load('classArm.schoolClass');
        }

        return view('student.profile', compact('student'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $user->update($request->only('phone', 'address'));

        return back()->with('success', 'Profile updated successfully.');
    }

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

        return back()->with('success', 'Password changed successfully.');
    }
}
