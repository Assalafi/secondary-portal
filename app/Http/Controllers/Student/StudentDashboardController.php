<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Invoice;
use App\Models\Score;
use App\Models\Student;
use App\Models\ReportCard;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return view('student.dashboard', [
                'student' => null,
                'stats' => $this->emptyStats(),
                'recentResults' => collect(),
                'attendanceChart' => [],
                'upcomingEvents' => $this->getUpcomingEvents(),
                'pendingPayments' => collect(),
                'subjects' => collect(),
            ]);
        }

        $student->load(['classArm.schoolClass', 'classArm.subjects']);

        $stats = [
            'attendance_rate' => $this->calculateAttendanceRate($student->id),
            'total_subjects' => $student->classArm && $student->classArm->subjects ? $student->classArm->subjects->count() : 0,
            'pending_payments' => Invoice::where('student_id', $student->id)->whereIn('status', ['Pending', 'Partial', 'Overdue'])->count(),
            'outstanding_balance' => Invoice::where('student_id', $student->id)->whereIn('status', ['Pending', 'Partial', 'Overdue'])->sum('balance'),
            'average_score' => $this->calculateAverageScore($student->id),
            'class_position' => $this->getClassPosition($student),
            'report_cards' => ReportCard::where('student_id', $student->id)->where('status', 'published')->count(),
        ];

        $recentResults = Score::where('student_id', $student->id)
            ->with(['scoreBatch.subject', 'scoreBatch.academicSession', 'scoreBatch.term'])
            ->latest()
            ->limit(6)
            ->get();

        $attendanceChart = $this->getAttendanceChart($student->id);

        $pendingPayments = Invoice::where('student_id', $student->id)
            ->whereIn('status', ['Pending', 'Partial', 'Overdue'])
            ->with(['invoiceItems.feeSetup', 'term'])
            ->latest()
            ->limit(5)
            ->get();

        $subjects = $student->classArm && $student->classArm->subjects
            ? $student->classArm->subjects
            : collect();

        $upcomingEvents = $this->getUpcomingEvents();

        return view('student.dashboard', compact(
            'student', 'stats', 'recentResults', 'attendanceChart',
            'upcomingEvents', 'pendingPayments', 'subjects'
        ));
    }

    private function calculateAttendanceRate($studentId)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $total = Attendance::where('student_id', $studentId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->count();

        if ($total == 0) return 0;

        $present = Attendance::where('student_id', $studentId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('status', 'present')
            ->count();

        return round(($present / $total) * 100, 1);
    }

    private function calculateAverageScore($studentId)
    {
        $avg = Score::where('student_id', $studentId)->avg('total');
        return $avg ? round($avg, 1) : 0;
    }

    private function getClassPosition($student)
    {
        if (!$student->classArm) return '-';

        $classStudents = Student::where('current_class_arm_id', $student->current_class_arm_id)->pluck('id');

        $averages = Score::whereIn('student_id', $classStudents)
            ->select('student_id', DB::raw('AVG(total) as avg_score'))
            ->groupBy('student_id')
            ->orderByDesc('avg_score')
            ->get();

        $position = $averages->search(function ($item) use ($student) {
            return $item->student_id == $student->id;
        });

        if ($position === false) return '-';
        return $this->ordinal($position + 1);
    }

    private function ordinal($number)
    {
        $suffixes = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        }
        return $number . $suffixes[$number % 10];
    }

    private function getAttendanceChart($studentId)
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $total = Attendance::where('student_id', $studentId)
                ->whereBetween('date', [$start, $end])
                ->count();

            $present = Attendance::where('student_id', $studentId)
                ->whereBetween('date', [$start, $end])
                ->where('status', 'present')
                ->count();

            $months[] = [
                'month' => $date->format('M'),
                'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            ];
        }
        return $months;
    }

    private function getUpcomingEvents()
    {
        return collect([
            ['title' => 'Mid-Term Test', 'date' => Carbon::now()->addDays(5), 'type' => 'exam'],
            ['title' => 'Inter-House Sports', 'date' => Carbon::now()->addDays(12), 'type' => 'event'],
            ['title' => 'PTA Meeting', 'date' => Carbon::now()->addDays(18), 'type' => 'meeting'],
        ]);
    }

    private function emptyStats()
    {
        return [
            'attendance_rate' => 0,
            'total_subjects' => 0,
            'pending_payments' => 0,
            'outstanding_balance' => 0,
            'average_score' => 0,
            'class_position' => '-',
            'report_cards' => 0,
        ];
    }
}
