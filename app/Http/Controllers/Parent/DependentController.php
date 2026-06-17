<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\Attendance;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DependentController extends Controller
{
    /**
     * Display a listing of the parent's dependents.
     */
    public function index()
    {
        $user = Auth::user();
        $dependents = $user->dependents()
            ->with(['classArm.schoolClass', 'user'])
            ->get()
            ->map(function ($student) {
                $attendance = $this->calculateAttendance($student->id);
                return [
                    'student' => $student,
                    'attendance' => $attendance,
                ];
            });
        
        return view('parent.dependents.index', compact('dependents'));
    }
    
    /**
     * Display the specified dependent's profile.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // Verify this student belongs to the parent
        $student = $user->dependents()
            ->with(['classArm.schoolClass', 'classArm.subjects', 'user'])
            ->findOrFail($id);
        
        $relationship = $user->dependents()
            ->where('students.id', $id)
            ->first()
            ->pivot;
        
        return view('parent.dependents.show', compact('student', 'relationship'));
    }
    
    /**
     * Display the profile page for a specific dependent.
     */
    public function profile($id)
    {
        $user = Auth::user();
        
        // Verify this student belongs to the parent
        $student = $user->dependents()
            ->with(['classArm.schoolClass', 'classArm.subjects', 'user'])
            ->findOrFail($id);
        
        $relationship = $user->dependents()
            ->where('students.id', $id)
            ->first()
            ->pivot;
        
        $globalSettings = [
            'academic_session' => '2024/2025',
            'current_term' => '3rd Term',
        ];
        
        return view('parent.dependents.profile', compact('student', 'relationship', 'globalSettings'));
    }
    
    /**
     * Update personal information for a dependent.
     */
    public function updatePersonal(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            // Verify this student belongs to the parent
            $student = $user->dependents()->findOrFail($id);
            
            // Validate the request
            $validated = $request->validate([
                'date_of_birth' => 'nullable|date',
                'state_of_origin' => 'nullable|string|max:255',
                'lga' => 'nullable|string|max:255',
                'religion' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:20',
            ]);
            
            // Update the student's user information
            $student->user->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Personal information updated successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update personal information. ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update parent/guardian information.
     */
    public function updateParent(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            // Verify this student belongs to the parent
            $student = $user->dependents()->findOrFail($id);
            
            // Validate the request
            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'occupation' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'relationship' => 'nullable|string|in:Father,Mother,Guardian',
            ]);
            
            // Update parent/guardian (auth user) information
            $parentData = collect($validated)->except('relationship')->toArray();
            $user->update($parentData);
            
            // Update relationship in pivot table if provided
            if (isset($validated['relationship'])) {
                $user->dependents()->updateExistingPivot($id, [
                    'relationship' => $validated['relationship']
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Parent/Guardian information updated successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update parent information. ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display attendance for a specific dependent.
     */
    public function attendance(Request $request, $id)
    {
        $user = Auth::user();
        $student = $user->dependents()->with(['user', 'classArm.schoolClass'])->findOrFail($id);
        
        // Build query with filters
        $query = Attendance::where('student_id', $id);
        
        // Define date ranges for sessions and terms
        $dateFilters = $this->getDateRangeForFilter($request->session, $request->term);
        
        if ($dateFilters) {
            $query->whereBetween('date', [$dateFilters['start'], $dateFilters['end']]);
        }
        
        $attendances = $query->latest('date')->paginate(30);
        
        // Calculate stats based on filtered data
        $attendanceStats = $this->calculateDetailedAttendance($id, $request->session, $request->term);
        
        return view('parent.dependents.attendance', compact('student', 'attendances', 'attendanceStats'));
    }
    
    /**
     * Display assignments for a specific dependent.
     */
    public function assignments($id)
    {
        $user = Auth::user();
        $student = $user->dependents()->with(['user', 'classArm.schoolClass'])->findOrFail($id);
        
        // Get assignments for the student's class
        $assignments = collect(); // Empty collection by default
        
        if ($student->class_arm_id) {
            $assignments = Assessment::where('class_arm_id', $student->class_arm_id)
                ->where('type', 'Assignment')
                ->with('subject')
                ->latest('assessment_date')
                ->paginate(10);
        }
        
        return view('parent.dependents.assignments', compact('student', 'assignments'));
    }
    
    /**
     * Display test and exam schedule for a specific dependent.
     */
    public function schedule($id)
    {
        $user = Auth::user();
        $student = $user->dependents()->with(['user', 'classArm.schoolClass'])->findOrFail($id);
        
        // Get upcoming tests and exams
        $schedule = collect(); // Empty collection by default
        
        if ($student->class_arm_id) {
            $schedule = Assessment::where('class_arm_id', $student->class_arm_id)
                ->whereIn('type', ['Test', 'Exam'])
                ->where('assessment_date', '>=', now()->toDateString())
                ->with('subject')
                ->orderBy('assessment_date')
                ->get();
        }
        
        return view('parent.dependents.schedule', compact('student', 'schedule'));
    }
    
    /**
     * Display results for a specific dependent.
     */
    public function results(Request $request, $id)
    {
        $user = Auth::user();
        $student = $user->dependents()->with(['user', 'classArm.schoolClass'])->findOrFail($id);
        
        // Get all assessment results for the student
        $query = AssessmentResult::where('student_id', $id)
            ->with(['assessment.subject', 'assessment.term', 'assessment.academicSession']);
        
        // Apply session filter
        if ($request->has('session') && $request->session) {
            $query->whereHas('assessment.academicSession', function($q) use ($request) {
                $q->where('name', $request->session);
            });
        }
        
        // Apply term filter
        if ($request->has('term') && $request->term) {
            $query->whereHas('assessment.term', function($q) use ($request) {
                $q->where('name', $request->term);
            });
        }
        
        $results = $query->latest('created_at')->get();
        
        // Group results by subject for display
        $subjectResults = $results->groupBy(function($result) {
            return $result->assessment->subject->name ?? 'Unknown';
        });
        
        // Calculate statistics
        $totalScore = $results->sum('score');
        $averageScore = $results->avg('score');
        $totalSubjects = $subjectResults->count();
        
        // Get available sessions and terms for filters
        $sessions = \App\Models\AcademicSession::orderBy('start_date', 'desc')->get();
        $terms = \App\Models\Term::all();
        
        return view('parent.dependents.results', compact('student', 'results', 'subjectResults', 'totalScore', 'averageScore', 'totalSubjects', 'sessions', 'terms'));
    }
    
    /**
     * Display payment history for a specific dependent.
     */
    public function payments($id)
    {
        $user = Auth::user();
        $student = $user->dependents()->with(['user', 'classArm.schoolClass'])->findOrFail($id);
        
        // Get invoices for the student with their payments
        $invoices = Invoice::where('student_id', $id)
            ->with(['payments', 'term', 'academicSession', 'invoiceItems.feeSetup'])
            ->latest()
            ->get();
        
        $paymentSummary = [
            'total_paid' => Invoice::where('student_id', $id)
                ->where('status', 'Paid')
                ->sum('amount_paid'),
            'pending' => Invoice::where('student_id', $id)
                ->whereIn('status', ['Pending', 'Partial', 'Overdue'])
                ->sum('balance'),
        ];
        
        return view('parent.dependents.payments', compact('student', 'invoices', 'paymentSummary'));
    }
    
    /**
     * Remove a dependent from the parent's account.
     */
    public function remove($id)
    {
        try {
            $user = Auth::user();
            
            // Verify this student belongs to the parent
            $student = $user->dependents()->findOrFail($id);
            
            // Detach the dependent from the parent
            $user->dependents()->detach($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Dependent removed successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove dependent. ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function calculateAttendance($studentId)
    {
        $totalDays = Attendance::where('student_id', $studentId)->count();
        if ($totalDays == 0) {
            return 0;
        }
        
        $presentDays = Attendance::where('student_id', $studentId)
            ->where('status', 'present')
            ->count();
        
        return round(($presentDays / $totalDays) * 100, 1);
    }
    
    private function calculateDetailedAttendance($studentId, $session = null, $term = null)
    {
        $query = Attendance::where('student_id', $studentId);
        
        // Apply date range filters
        $dateFilters = $this->getDateRangeForFilter($session, $term);
        
        if ($dateFilters) {
            $query->whereBetween('date', [$dateFilters['start'], $dateFilters['end']]);
        } else {
            // If no filters, get current month
            $query->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        }
        
        $attendances = $query->get();
        
        return [
            'present' => $attendances->where('status', 'Present')->count(),
            'absent' => $attendances->where('status', 'Absent')->count(),
            'late' => $attendances->where('status', 'Late')->count(),
            'excused' => $attendances->where('status', 'Excused')->count(),
            'percentage' => $attendances->count() > 0 
                ? round(($attendances->where('status', 'Present')->count() / $attendances->count()) * 100, 1)
                : 0,
        ];
    }
    
    /**
     * Get date range based on session and term filters
     */
    private function getDateRangeForFilter($session = null, $term = null)
    {
        if (!$session && !$term) {
            return null;
        }
        
        // Define academic year date ranges
        $sessionYears = [
            '2024/2025' => ['start' => '2024-09-01', 'end' => '2025-08-31'],
            '2023/2024' => ['start' => '2023-09-01', 'end' => '2024-08-31'],
            '2022/2023' => ['start' => '2022-09-01', 'end' => '2023-08-31'],
        ];
        
        $termMonths = [
            '1st term' => ['start_month' => 9, 'end_month' => 12],  // Sept - Dec
            '2nd term' => ['start_month' => 1, 'end_month' => 4],   // Jan - April
            '3rd term' => ['start_month' => 5, 'end_month' => 8],   // May - Aug
        ];
        
        $start = null;
        $end = null;
        
        if ($session && isset($sessionYears[$session])) {
            $start = $sessionYears[$session]['start'];
            $end = $sessionYears[$session]['end'];
            
            // If term is also specified, narrow down the date range
            if ($term && isset($termMonths[$term])) {
                $year = explode('/', $session)[0];
                $startMonth = $termMonths[$term]['start_month'];
                $endMonth = $termMonths[$term]['end_month'];
                
                // Adjust year for 2nd and 3rd terms
                if ($startMonth < 9) {
                    $year = explode('/', $session)[1];
                }
                
                $start = Carbon::create($year, $startMonth, 1)->startOfMonth()->format('Y-m-d');
                $end = Carbon::create($year, $endMonth, 1)->endOfMonth()->format('Y-m-d');
            }
        }
        
        return $start && $end ? ['start' => $start, 'end' => $end] : null;
    }
}
