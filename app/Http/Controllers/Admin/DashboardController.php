<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Staff;
use App\Models\ClassArm;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\SessionTerm;
use App\Models\PayrollRecord;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get current session and term from SessionTerm table
        $currentSessionTerm = SessionTerm::where('is_current', true)->first()
            ?? SessionTerm::latest()->first();
        $currentSession = $currentSessionTerm ? $currentSessionTerm->academic_year : null;
        $currentTerm = $currentSessionTerm ? $currentSessionTerm->term_name : null;

        // Get dashboard statistics
        $totalStudents = Student::where('status', 'Active')->count();
        $totalStaff = Staff::where('status', 'Active')->count();
        $totalClasses = ClassArm::count();
        
        // Calculate fees
        $totalFeesCollected = Invoice::where('status', 'Paid')->sum('amount_paid');
        $totalFeesOutstanding = Invoice::whereIn('status', ['Pending', 'Partial', 'Overdue'])->sum('balance');
        $totalFeesExpected = $totalFeesCollected + $totalFeesOutstanding;
        
        // Calculate percentages for pie chart
        $feesCollectedPercent = $totalFeesExpected > 0 
            ? round(($totalFeesCollected / $totalFeesExpected) * 100, 1) 
            : 0;
        $feesOutstandingPercent = $totalFeesExpected > 0 
            ? round(($totalFeesOutstanding / $totalFeesExpected) * 100, 1) 
            : 0;

        $stats = [
            'total_students' => $totalStudents,
            'total_staff' => $totalStaff,
            'total_classes' => $totalClasses,
            'total_fees_collected' => $totalFeesCollected,
            'total_fees_outstanding' => $totalFeesOutstanding,
            'fees_collected_percent' => $feesCollectedPercent,
            'fees_outstanding_percent' => $feesOutstandingPercent,
        ];

        // Get attendance data for last 5 days
        $attendanceData = [];
        $attendanceLabels = [];
        $totalStudentsForAttendance = $totalStudents > 0 ? $totalStudents : 1; // Prevent division by zero
        
        for ($i = 4; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $presentCount = Attendance::where('date', $date->format('Y-m-d'))
                ->where('status', 'Present')
                ->count();
            
            $attendancePercent = $totalStudentsForAttendance > 0 
                ? round(($presentCount / $totalStudentsForAttendance) * 100, 1) 
                : 0;
            
            $attendanceData[] = $attendancePercent;
            $attendanceLabels[] = $date->format('D'); // Mon, Tue, Wed, etc.
        }

        // Get recent activities
        $recentActivities = collect();

        // Recent students (last 4)
        $recentStudents = Student::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->get()
            ->map(function ($student) {
                return [
                    'type' => 'student_enrolled',
                    'icon' => 'ri-user-add-line',
                    'title' => 'New Student enrolled',
                    'description' => $student->user->name ?? 'N/A',
                    'time' => $student->created_at,
                    'url' => route('admin.students.show', $student->id)
                ];
            });

        // Recent payroll
        $recentPayroll = PayrollRecord::orderBy('created_at', 'desc')
            ->limit(1)
            ->get()
            ->map(function ($payroll) {
                return [
                    'type' => 'payroll_processed',
                    'icon' => 'ri-wallet-line',
                    'title' => 'Payroll for ' . Carbon::parse($payroll->month . ' ' . $payroll->year)->format('M Y') . ' Processed',
                    'description' => '₦' . number_format($payroll->total_amount, 2),
                    'time' => $payroll->created_at,
                    'url' => route('admin.payments.payroll')
                ];
            });

        // Recent classes
        $recentClasses = ClassArm::with('schoolClass')
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->get()
            ->map(function ($class) {
                return [
                    'type' => 'class_created',
                    'icon' => 'ri-book-2-line',
                    'title' => 'New Class Created',
                    'description' => ($class->schoolClass->name ?? 'N/A') . ' - ' . $class->name,
                    'time' => $class->created_at,
                    'url' => '#'
                ];
            });

        // Recent staff
        $recentStaff = Staff::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->get()
            ->map(function ($staff) {
                return [
                    'type' => 'staff_added',
                    'icon' => 'ri-user-star-line',
                    'title' => 'New Staff Added',
                    'description' => $staff->user->name ?? 'N/A',
                    'time' => $staff->created_at,
                    'url' => route('admin.staff.show', $staff->id)
                ];
            });

        // Merge and sort activities
        $recentActivities = $recentStudents
            ->merge($recentPayroll)
            ->merge($recentClasses)
            ->merge($recentStaff)
            ->sortByDesc('time')
            ->take(4);

        return view('admin.dashboard', compact(
            'stats',
            'currentSession',
            'currentTerm',
            'attendanceData',
            'attendanceLabels',
            'recentActivities'
        ));
    }
}
