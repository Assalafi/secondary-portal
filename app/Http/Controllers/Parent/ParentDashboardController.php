<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all dependents (students) for this parent
        $dependents = $user->dependents()->with(['classArm.schoolClass', 'user'])->get();
        $dependentIds = $dependents->pluck('id');
        
        // Get admission applications
        $applications = \App\Models\AdmissionApplication::where('parent_id', $user->id)
            ->with(['proposedClass', 'academicSession'])
            ->latest()
            ->limit(3)
            ->get();
        
        // Calculate statistics
        $stats = [
            'total_dependents' => $dependents->count(),
            'active_students' => $dependents->where('status', 'active')->count(),
            'average_attendance' => $this->calculateAverageAttendance($dependentIds),
            'pending_payments' => $this->getPendingPayments($dependentIds),
        ];
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities($dependentIds);
        
        // Get upcoming events
        $upcomingEvents = $this->getUpcomingEvents();
        
        return view('parent.dashboard', compact('stats', 'dependents', 'recentActivities', 'upcomingEvents', 'applications'));
    }
    
    private function calculateAverageAttendance($dependentIds)
    {
        if ($dependentIds->isEmpty()) {
            return 0;
        }
        
        // Calculate attendance for current month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $totalRecords = Attendance::whereIn('student_id', $dependentIds)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->count();
            
        if ($totalRecords == 0) {
            return 0;
        }
        
        $presentRecords = Attendance::whereIn('student_id', $dependentIds)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('status', 'present')
            ->count();
        
        return round(($presentRecords / $totalRecords) * 100, 1);
    }
    
    private function getPendingPayments($dependentIds)
    {
        return Invoice::whereIn('student_id', $dependentIds)
            ->where('status', 'pending')
            ->count();
    }
    
    private function getRecentActivities($dependentIds)
    {
        $activities = collect();
        
        // Get recent attendance records
        $recentAttendance = Attendance::whereIn('student_id', $dependentIds)
            ->with('student.user')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($record) {
                return [
                    'type' => 'attendance',
                    'message' => $record->student->user->name . ' was marked ' . $record->status . ' for today',
                    'time' => $record->created_at,
                    'icon' => 'ri-user-follow-line',
                ];
            });
        
        $activities = $activities->merge($recentAttendance);
        
        // Get recent payment reminders
        $pendingPayments = Invoice::whereIn('student_id', $dependentIds)
            ->where('status', 'pending')
            ->where('due_date', '>=', now())
            ->with('student.user')
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($invoice) {
                return [
                    'type' => 'payment',
                    'message' => 'School fees payment reminder',
                    'time' => $invoice->created_at,
                    'icon' => 'ri-money-dollar-circle-line',
                ];
            });
        
        $activities = $activities->merge($pendingPayments);
        
        return $activities->sortByDesc('time')->take(4);
    }
    
    private function getUpcomingEvents()
    {
        // Placeholder for upcoming events
        // This would come from an events table in the future
        return collect([
            [
                'title' => 'PTA Meeting',
                'date' => Carbon::now()->addDays(2),
            ],
            [
                'title' => 'Mid-Term Break',
                'date' => Carbon::now()->addDays(10),
            ],
        ]);
    }
}
