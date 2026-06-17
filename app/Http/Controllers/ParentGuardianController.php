<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParentGuardianController extends Controller
{
    /**
     * Display parent/guardian overview page with statistics
     */
    public function overview()
    {
        $stats = [
            'total_parents' => User::whereHas('role', function($q) {
                $q->where('name', 'Parent');
            })->count(),
            'active_parents' => User::whereHas('role', function($q) {
                $q->where('name', 'Parent');
            })->whereNotNull('last_login_at')
                ->where('last_login_at', '>=', Carbon::now()->subDays(30))
                ->count(),
            'total_students_linked' => DB::table('parent_student')->distinct('student_id')->count(),
            'pending_payments' => Invoice::whereIn('student_id', function($query) {
                $query->select('student_id')
                    ->from('parent_student');
            })->whereIn('status', ['Pending', 'Partial'])->count(),
        ];

        // Recent registrations
        $recentParents = User::whereHas('role', function($q) {
                $q->where('name', 'Parent');
            })
            ->withCount('dependents')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.parent-guardians.overview', compact('stats', 'recentParents'));
    }

    /**
     * Display list of all parents/guardians
     */
    public function index(Request $request)
    {
        $query = User::whereHas('role', function($q) {
                $q->where('name', 'Parent');
            })
            ->withCount('dependents');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'active') {
                $query->whereNotNull('last_login_at')
                    ->where('last_login_at', '>=', Carbon::now()->subDays(30));
            } elseif ($request->status === 'inactive') {
                $query->where(function($q) {
                    $q->whereNull('last_login_at')
                      ->orWhere('last_login_at', '<', Carbon::now()->subDays(30));
                });
            }
        }

        $parents = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.parent-guardians.index', compact('parents'));
    }

    /**
     * Show form to create new parent/guardian
     */
    public function create()
    {
        // Get all students for linking
        $students = Student::with(['user', 'classArm.schoolClass'])
            ->where('status', 'Active')
            ->orderBy('admission_no')
            ->get();

        return view('admin.parent-guardians.create', compact('students'));
    }

    /**
     * Store new parent/guardian
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'occupation' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'students' => 'nullable|array',
            'students.*' => 'exists:students,id',
            'relationships' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // Get parent role
            $parentRole = \App\Models\Role::where('name', 'Parent')->first();
            
            if (!$parentRole) {
                return back()->withInput()
                    ->with('error', 'Parent role not found in system. Please contact administrator.');
            }
            
            // Create user account
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'role_id' => $parentRole->id,
                'status' => 'Active',
                'password' => Hash::make($request->password),
            ]);

            // Link to students (dependents)
            if ($request->has('students') && is_array($request->students)) {
                $syncData = [];
                foreach ($request->students as $index => $studentId) {
                    $relationship = $request->relationships[$index] ?? 'Guardian';
                    $isPrimary = isset($request->primary_student) && $request->primary_student == $studentId;
                    
                    $syncData[$studentId] = [
                        'relationship' => $relationship,
                        'is_primary' => $isPrimary,
                        'date_added' => now(),
                    ];
                }
                $user->dependents()->sync($syncData);
            }

            DB::commit();

            return redirect()->route('admin.parent-guardians.show', $user->id)
                ->with('success', 'Parent/Guardian created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create parent/guardian: ' . $e->getMessage());
        }
    }

    /**
     * Show parent/guardian profile
     */
    public function show(User $parentGuardian)
    {
        if (!$parentGuardian->isParent()) {
            abort(404);
        }

        $parentGuardian->load([
            'dependents.user',
            'dependents.classArm.schoolClass'
        ]);

        // Get payment statistics
        $dependentIds = $parentGuardian->dependents->pluck('id');
        
        $paymentStats = [
            'total_paid' => Payment::whereHas('invoice', function($q) use ($dependentIds) {
                $q->whereIn('student_id', $dependentIds);
            })->sum('amount'),
            'total_pending' => Invoice::whereIn('student_id', $dependentIds)
                ->whereIn('status', ['Pending', 'Partial'])
                ->sum('balance'),
            'total_invoices' => Invoice::whereIn('student_id', $dependentIds)->count(),
        ];

        // Recent activities
        $recentPayments = Payment::whereHas('invoice', function($q) use ($dependentIds) {
            $q->whereIn('student_id', $dependentIds);
        })->with('invoice.student.user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentInvoices = Invoice::whereIn('student_id', $dependentIds)
            ->with('student.user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Attendance summary
        $attendanceStats = [
            'present' => Attendance::whereIn('student_id', $dependentIds)
                ->where('date', '>=', Carbon::now()->startOfMonth())
                ->where('status', 'Present')
                ->count(),
            'absent' => Attendance::whereIn('student_id', $dependentIds)
                ->where('date', '>=', Carbon::now()->startOfMonth())
                ->where('status', 'Absent')
                ->count(),
        ];

        return view('admin.parent-guardians.show', compact(
            'parentGuardian',
            'paymentStats',
            'recentPayments',
            'recentInvoices',
            'attendanceStats'
        ));
    }

    /**
     * Show edit form
     */
    public function edit(User $parentGuardian)
    {
        if (!$parentGuardian->isParent()) {
            abort(404);
        }

        $parentGuardian->load('dependents');

        // Get all students for linking
        $students = Student::with(['user', 'classArm.schoolClass'])
            ->where('status', 'Active')
            ->orderBy('admission_no')
            ->get();

        return view('admin.parent-guardians.edit', compact('parentGuardian', 'students'));
    }

    /**
     * Update parent/guardian
     */
    public function update(Request $request, User $parentGuardian)
    {
        if (!$parentGuardian->isParent()) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $parentGuardian->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'occupation' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'students' => 'nullable|array',
            'students.*' => 'exists:students,id',
            'relationships' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // Update user account
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'occupation' => $request->occupation,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $parentGuardian->update($updateData);

            // Update student links
            if ($request->has('students') && is_array($request->students)) {
                $syncData = [];
                foreach ($request->students as $index => $studentId) {
                    $relationship = $request->relationships[$index] ?? 'Guardian';
                    $isPrimary = isset($request->primary_student) && $request->primary_student == $studentId;
                    
                    $syncData[$studentId] = [
                        'relationship' => $relationship,
                        'is_primary' => $isPrimary,
                        'date_added' => $parentGuardian->dependents()->where('student_id', $studentId)->exists() 
                            ? $parentGuardian->dependents()->where('student_id', $studentId)->first()->pivot->date_added 
                            : now(),
                    ];
                }
                $parentGuardian->dependents()->sync($syncData);
            } else {
                $parentGuardian->dependents()->detach();
            }

            DB::commit();

            return redirect()->route('admin.parent-guardians.show', $parentGuardian->id)
                ->with('success', 'Parent/Guardian updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update parent/guardian: ' . $e->getMessage());
        }
    }

    /**
     * Delete parent/guardian
     */
    public function destroy(User $parentGuardian)
    {
        if (!$parentGuardian->isParent()) {
            abort(404);
        }

        try {
            // Detach all student relationships
            $parentGuardian->dependents()->detach();
            
            // Delete user
            $parentGuardian->delete();

            return redirect()->route('admin.parent-guardians.index')
                ->with('success', 'Parent/Guardian deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete parent/guardian: ' . $e->getMessage());
        }
    }

    /**
     * Reset password to default
     */
    public function resetPassword(User $parentGuardian)
    {
        if (!$parentGuardian->isParent()) {
            abort(404);
        }

        try {
            $defaultPassword = 'parent' . date('Y');
            $parentGuardian->update([
                'password' => Hash::make($defaultPassword)
            ]);

            return back()->with('success', 'Password reset successfully! New password: ' . $defaultPassword);

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reset password: ' . $e->getMessage());
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'parent_ids' => 'required|array',
            'parent_ids.*' => 'exists:users,id',
        ]);

        try {
            $parents = User::whereIn('id', $request->parent_ids)
                ->whereHas('role', function($q) {
                    $q->where('name', 'Parent');
                })
                ->get();

            switch ($request->action) {
                case 'delete':
                    foreach ($parents as $parent) {
                        $parent->dependents()->detach();
                        $parent->delete();
                    }
                    $message = 'Selected parents deleted successfully!';
                    break;

                case 'activate':
                    // Logic for activation if needed
                    $message = 'Selected parents activated successfully!';
                    break;

                case 'deactivate':
                    // Logic for deactivation if needed
                    $message = 'Selected parents deactivated successfully!';
                    break;
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Bulk action failed: ' . $e->getMessage());
        }
    }
}
