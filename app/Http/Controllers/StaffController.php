<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\ClassArm;
use App\Models\Subject;
use App\Models\ClassSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    /**
     * Display staff overview page
     */
    public function overview()
    {
        // Get quick stats
        $stats = [
            'total_staff' => Staff::count(),
            'active_staff' => Staff::where('status', 'Active')->count(),
            'departments' => Staff::distinct('department')->count('department'),
            'new_this_month' => Staff::whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)
                                   ->count(),
        ];

        // Get recent staff (last 5)
        $recent_staff = Staff::with(['user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.staff.overview', compact('stats', 'recent_staff'));
    }

    /**
     * Display role & class assignments page
     */
    public function assignments(Request $request)
    {
        // Get staff with their assignments
        $query = Staff::with(['user.role', 'assignedClasses.schoolClass'])
            ->select('staff.*');

        // Apply filters
        if ($request->filled('role')) {
            $query->where('designation', $request->role);
        }
        
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $staff = $query->orderBy('created_at', 'desc')->get();

        // Get classes, subjects, and roles for assignment modal
        $classes = SchoolClass::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        // Calculate stats
        $stats = [
            'assigned' => Staff::whereHas('assignedClasses')->count(),
            'unassigned' => Staff::whereDoesntHave('assignedClasses')->count(),
            'teachers' => Staff::where('designation', 'like', '%Teacher%')->count(),
            'admins' => Staff::where('designation', 'like', '%Admin%')->orWhere('designation', 'like', '%Principal%')->count(),
        ];

        return view('admin.staff.assignments', compact('staff', 'classes', 'subjects', 'roles', 'stats'));
    }

    /**
     * Get class arms for a specific class
     */
    public function getClassArms($classId)
    {
        $classArms = ClassArm::where('school_class_id', $classId)
            ->orderBy('name')
            ->get(['id', 'name']);
            
        return response()->json($classArms);
    }

    /**
     * Assign role and class to staff
     */
    public function assignRoleClass(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'role_id' => 'nullable|exists:roles,id',
            'department' => 'nullable|string',
            'assigned_class' => 'nullable|exists:school_classes,id',
            'class_arm' => 'nullable|array',
            'class_arm.*' => 'exists:class_arms,id',
            'assigned_subject' => 'nullable|array',
            'assigned_subject.*' => 'exists:subjects,id',
        ]);

        try {
            DB::beginTransaction();

            $staff = Staff::findOrFail($request->staff_id);

            // Update user role
            if ($request->filled('role_id')) {
                $staff->user->role_id = $request->role_id;
                $staff->user->save();
            }
            
            // Update staff department
            if ($request->filled('department')) {
                $staff->department = $request->department;
                $staff->save();
            }

            // Handle class arm assignments (as class teacher)
            if ($request->filled('class_arm')) {
                // Remove previous class teacher assignments
                ClassArm::where('class_teacher_id', $staff->user_id)->update(['class_teacher_id' => null]);
                
                // Assign new class arms
                ClassArm::whereIn('id', $request->class_arm)
                    ->update(['class_teacher_id' => $staff->user_id]);
            }

            // Handle subject assignments
            if ($request->filled('assigned_subject')) {
                // Remove previous subject assignments
                ClassSubject::where('teacher_id', $staff->user_id)->update(['teacher_id' => null]);
                
                // Assign new subjects
                if ($request->filled('class_arm')) {
                    foreach ($request->class_arm as $classArmId) {
                        foreach ($request->assigned_subject as $subjectId) {
                            ClassSubject::updateOrCreate([
                                'class_arm_id' => $classArmId,
                                'subject_id' => $subjectId,
                            ], [
                                'teacher_id' => $staff->user_id,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Staff assignment updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display staff directory
     */
    public function index(Request $request)
    {
        $query = Staff::with(['user'])
            ->select('staff.*');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('staff_id', 'like', "%{$search}%")
              ->orWhere('designation', 'like', "%{$search}%")
              ->orWhere('department', 'like', "%{$search}%");
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('gender', $request->gender);
            });
        }

        // Filter by role/department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $staff = $query->latest()->paginate(10);
        
        // Get filter options
        $departments = Staff::distinct()->pluck('department')->filter()->sort();
        $statuses = ['Active', 'Inactive', 'Suspended', 'Terminated'];

        return view('admin.staff.index', compact('staff', 'departments', 'statuses'));
    }

    /**
     * Show staff enrollment step 1
     */
    public function enrollStep1()
    {
        return view('admin.staff.enroll-step1');
    }

    /**
     * Process staff enrollment step 1
     */
    public function enrollStep1Store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:255',
            'state_of_origin' => 'required|string|max:255',
            'lga' => 'required|string|max:255',
        ]);

        // Store step 1 data in session
        session(['staff_enrollment_step1' => $request->all()]);

        return redirect()->route('admin.staff.enroll.step2');
    }

    /**
     * Show staff enrollment step 2
     */
    public function enrollStep2()
    {
        $step1Data = session('staff_enrollment_step1');
        if (!$step1Data) {
            return redirect()->route('admin.staff.enroll.step1')
                           ->with('error', 'Please complete step 1 first.');
        }

        return view('admin.staff.enroll-step2', compact('step1Data'));
    }

    /**
     * Process staff enrollment step 2
     */
    public function enrollStep2Store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_relationship' => 'required|string|max:255',
        ]);

        // Store step 2 data in session
        session(['staff_enrollment_step2' => $request->all()]);

        return redirect()->route('admin.staff.enroll.step3');
    }

    /**
     * Show staff enrollment step 3
     */
    public function enrollStep3()
    {
        $step1Data = session('staff_enrollment_step1');
        $step2Data = session('staff_enrollment_step2');
        
        if (!$step1Data || !$step2Data) {
            return redirect()->route('admin.staff.enroll.step1')
                           ->with('error', 'Please complete previous steps first.');
        }

        return view('admin.staff.enroll-step3', compact('step1Data', 'step2Data'));
    }

    /**
     * Process staff enrollment step 3
     */
    public function enrollStep3Store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|string|unique:staff,staff_id|max:255',
            'designation' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'date_of_employment' => 'required|date',
            'employment_type' => 'required|in:Full-time,Part-time,Contract,Temporary',
            'salary' => 'required|numeric|min:0',
            'qualifications' => 'nullable|string',
        ]);

        // Store step 3 data in session
        session(['staff_enrollment_step3' => $request->all()]);

        return redirect()->route('admin.staff.enroll.step4');
    }

    /**
     * Show staff enrollment step 4 (Portal Setup)
     */
    public function enrollStep4()
    {
        $step1Data = session('staff_enrollment_step1');
        $step2Data = session('staff_enrollment_step2');
        $step3Data = session('staff_enrollment_step3');
        
        if (!$step1Data || !$step2Data || !$step3Data) {
            return redirect()->route('admin.staff.enroll.step1')
                           ->with('error', 'Please complete previous steps first.');
        }

        // Get roles for staff (exclude student role)
        $roles = Role::whereNotIn('name', ['Student'])->get();

        return view('admin.staff.enroll-step4', compact('step1Data', 'step2Data', 'step3Data', 'roles'));
    }

    /**
     * Complete staff enrollment
     */
    public function completeEnrollment(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:Active,Inactive',
        ]);

        $step1Data = session('staff_enrollment_step1');
        $step2Data = session('staff_enrollment_step2');
        $step3Data = session('staff_enrollment_step3');

        if (!$step1Data || !$step2Data || !$step3Data) {
            return redirect()->route('admin.staff.enroll.step1')
                           ->with('error', 'Enrollment session expired. Please start again.');
        }

        DB::beginTransaction();

        try {
            // Generate default password using Staff ID + 2024 pattern
            $defaultPassword = $step3Data['staff_id'] . '2024';

            // Create user account for staff
            $user = User::create([
                'name' => $step1Data['first_name'] . ' ' . $step1Data['last_name'],
                'email' => $step2Data['email'],
                'password' => Hash::make($defaultPassword),
                'role_id' => $request->role_id,
                'status' => $request->status,
                'gender' => $step1Data['gender'],
                'date_of_birth' => $step1Data['date_of_birth'],
                'phone' => $step2Data['phone_number'],
                'address' => $step2Data['address'],
                'nationality' => $step1Data['nationality'],
                'state_of_origin' => $step1Data['state_of_origin'],
                'lga' => $step1Data['lga'],
            ]);

            // Create staff record
            $staff = Staff::create([
                'user_id' => $user->id,
                'staff_id' => $step3Data['staff_id'],
                'designation' => $step3Data['designation'],
                'department' => $step3Data['department'],
                'date_of_employment' => $step3Data['date_of_employment'],
                'employment_type' => $step3Data['employment_type'],
                'salary' => $step3Data['salary'],
                'qualifications' => $step3Data['qualifications'],
                'status' => $request->status,
            ]);

            // Clear enrollment session data
            session()->forget(['staff_enrollment_step1', 'staff_enrollment_step2', 'staff_enrollment_step3']);

            DB::commit();

            return redirect()->route('admin.staff.index')
                           ->with('success', "Staff member enrolled successfully! Staff ID: <strong>{$step3Data['staff_id']}</strong><br>Default Password: <strong>{$defaultPassword}</strong>");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to enroll staff member. Please try again.');
        }
    }

    /**
     * Show staff profile
     */
    public function show(Staff $staff)
    {
        $staff->load(['user', 'payrolls']);
        return view('admin.staff.profile.overview', compact('staff'));
    }

    /**
     * Show edit staff form
     */
    public function edit(Staff $staff)
    {
        $staff->load('user');
        $roles = Role::whereNotIn('name', ['Student'])->get();
        return view('admin.staff.edit', compact('staff', 'roles'));
    }

    /**
     * Update staff information
     */
    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($staff->user_id)],
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date|before:today',
            'address' => 'required|string',
            'nationality' => 'required|string|max:255',
            'state_of_origin' => 'required|string|max:255',
            'lga' => 'required|string|max:255',
            'staff_id' => ['required', 'string', Rule::unique('staff')->ignore($staff->id), 'max:255'],
            'designation' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'date_of_employment' => 'required|date',
            'employment_type' => 'required|in:Full-time,Part-time,Contract,Temporary',
            'salary' => 'required|numeric|min:0',
            'qualifications' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:Active,Inactive,Suspended,Terminated',
            'password' => 'nullable|string|min:6',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Handle photo upload
            $photoPath = $staff->user->photo_path;
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($photoPath) {
                    Storage::disk('public')->delete($photoPath);
                }
                $photoPath = $request->file('photo')->store('staff-photos', 'public');
            }

            // Update user information
            $userData = [
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'nationality' => $request->nationality,
                'state_of_origin' => $request->state_of_origin,
                'lga' => $request->lga,
                'role_id' => $request->role_id,
                'status' => $request->status,
                'photo_path' => $photoPath,
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $staff->user->update($userData);

            // Update staff information
            $staff->update([
                'staff_id' => $request->staff_id,
                'designation' => $request->designation,
                'department' => $request->department,
                'date_of_employment' => $request->date_of_employment,
                'employment_type' => $request->employment_type,
                'salary' => $request->salary,
                'qualifications' => $request->qualifications,
                'status' => $request->status,
            ]);

            DB::commit();

            return redirect()->route('admin.staff.show', $staff)
                           ->with('success', 'Staff information updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update staff information. Please try again.');
        }
    }

    /**
     * Delete staff member
     */
    public function destroy(Staff $staff)
    {
        try {
            DB::beginTransaction();

            // Delete photo if exists
            if ($staff->user->photo_path) {
                Storage::disk('public')->delete($staff->user->photo_path);
            }

            // Delete user and staff (cascade will handle relationships)
            $staff->user->delete();
            $staff->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Staff member deleted successfully!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Failed to delete staff member.']);
        }
    }

    /**
     * Reset staff password
     */
    public function resetPassword(Staff $staff)
    {
        try {
            // Generate password using Staff ID + 2024 pattern
            $newPassword = $this->generateDefaultPassword($staff);
            
            $staff->user->update([
                'password' => Hash::make($newPassword)
            ]);
            
            return back()->with('success', "Password reset successfully! New password: <strong>{$newPassword}</strong><br><small class='text-muted'>Default format: StaffID + 2024. Staff can change this after logging in.</small>");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reset password. Please try again.');
        }
    }

    /**
     * Generate default password using Staff ID + 2024 pattern
     */
    private function generateDefaultPassword(Staff $staff)
    {
        return $staff->staff_id . '2024';
    }

    /**
     * Bulk actions for staff
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,suspend,delete',
            'staff_ids' => 'required|array',
            'staff_ids.*' => 'exists:staff,id'
        ]);

        try {
            DB::beginTransaction();

            $staff = Staff::whereIn('id', $request->staff_ids)->with('user')->get();
            $count = $staff->count();

            switch ($request->action) {
                case 'activate':
                    foreach ($staff as $member) {
                        $member->update(['status' => 'Active']);
                        $member->user->update(['status' => 'Active']);
                    }
                    $message = "{$count} staff member(s) activated successfully!";
                    break;

                case 'deactivate':
                    foreach ($staff as $member) {
                        $member->update(['status' => 'Inactive']);
                        $member->user->update(['status' => 'Inactive']);
                    }
                    $message = "{$count} staff member(s) deactivated successfully!";
                    break;

                case 'suspend':
                    foreach ($staff as $member) {
                        $member->update(['status' => 'Suspended']);
                        $member->user->update(['status' => 'Suspended']);
                    }
                    $message = "{$count} staff member(s) suspended successfully!";
                    break;

                case 'delete':
                    foreach ($staff as $member) {
                        // Delete photo if exists
                        if ($member->user->photo_path) {
                            Storage::disk('public')->delete($member->user->photo_path);
                        }
                        $member->user->delete();
                        $member->delete();
                    }
                    $message = "{$count} staff member(s) deleted successfully!";
                    break;
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Bulk action failed. Please try again.']);
        }
    }
}
