<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionApplication;
use App\Models\Student;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdmissionApplicationController extends Controller
{
    /**
     * Display all applications
     */
    public function index(Request $request)
    {
        $query = AdmissionApplication::with(['parent', 'proposedClass', 'academicSession', 'payment']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('guardian_email', 'like', "%{$search}%");
            });
        }

        $applications = $query->latest()->paginate(20);

        return view('admin.admissions.index', compact('applications'));
    }

    /**
     * Show application details
     */
    public function show(AdmissionApplication $application)
    {
        $application->load(['parent', 'proposedClass', 'proposedClassArm', 'academicSession', 'payment', 'reviewer']);

        return view('admin.admissions.show', compact('application'));
    }

    /**
     * Show review form
     */
    public function review(AdmissionApplication $application)
    {
        if (!in_array($application->status, ['Submitted', 'Under Review'])) {
            return back()->with('error', 'This application cannot be reviewed.');
        }

        // Update status to Under Review if it's Submitted
        if ($application->status === 'Submitted') {
            $application->update(['status' => 'Under Review']);
        }

        return view('admin.admissions.review', compact('application'));
    }

    /**
     * Approve application and create student
     */
    public function approve(Request $request, AdmissionApplication $application)
    {
        $request->validate([
            'admission_no' => 'required|string|unique:students,admission_no',
            'class_arm_id' => 'required|exists:class_arms,id',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Get student role
            $studentRole = Role::where('name', 'Student')->first();

            // Create user account for student
            // Login: admission number or email | Password: admission number
            $admissionNo = $request->admission_no;
            $studentEmail = strtolower($admissionNo) . '@student.portal.com';
            $defaultPassword = $admissionNo;
            $user = User::create([
                'name' => $application->full_name,
                'email' => $studentEmail,
                'password' => Hash::make($defaultPassword),
                'role_id' => $studentRole->id,
                'phone' => $application->guardian_phone,
                'address' => $application->home_address,
                'status' => 'Active',
                'gender' => $application->gender,
                'date_of_birth' => $application->date_of_birth,
                'nationality' => $application->nationality,
                'state_of_origin' => $application->state_of_origin,
                'lga' => $application->lga,
                'photo_path' => $application->passport_photo_path,
            ]);

            // Create student record (matching students table schema)
            $student = Student::create([
                'user_id' => $user->id,
                'admission_no' => $request->admission_no,
                'first_name' => $application->first_name,
                'surname' => $application->last_name,
                'middle_name' => $application->other_name,
                'gender' => $application->gender,
                'dob' => $application->date_of_birth,
                'place_of_birth_town' => $application->place_of_birth_town,
                'place_of_birth_lga' => $application->place_of_birth_lga,
                'place_of_birth_state' => $application->place_of_birth_state,
                'nationality' => $application->nationality ?? 'Nigerian',
                'state_of_origin' => $application->state_of_origin,
                'lga' => $application->lga,
                'health_status' => $application->health_status ?? 'Normal',
                'disability_details' => $application->disability_details,
                'previous_school_details' => $application->previous_school,
                'current_class_arm_id' => $request->class_arm_id,
                'academic_session_id' => $application->academic_session_id,
                'admission_date' => now(),
                'status' => 'Active',
                'photo_path' => $application->passport_photo_path,
            ]);

            // Link parent to student (using the parent_student pivot table)
            $parent = User::find($application->parent_id);
            if ($parent) {
                $parent->dependents()->attach($student->id, [
                    'relationship' => $application->guardian_relationship ?? 'Guardian',
                    'is_primary' => true,
                    'date_added' => now()->toDateString(),
                ]);
            }

            // Update application status
            $application->update([
                'status' => 'Approved',
                'admin_remarks' => $request->remarks,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.admissions.show', $application->id)
                ->with('success', 'Application approved! Student enrolled. Login: ' . $admissionNo . ' | Password: ' . $defaultPassword);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve application: ' . $e->getMessage());
        }
    }

    /**
     * Reject application
     */
    public function reject(Request $request, AdmissionApplication $application)
    {
        $request->validate([
            'remarks' => 'required|string|min:10',
        ]);

        $application->update([
            'status' => 'Rejected',
            'admin_remarks' => $request->remarks,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.admissions.show', $application->id)
            ->with('success', 'Application has been rejected.');
    }

    /**
     * Delete application
     */
    public function destroy(AdmissionApplication $application)
    {
        if (!in_array($application->status, ['Draft', 'Pending Payment', 'Rejected'])) {
            return back()->with('error', 'This application cannot be deleted.');
        }

        $application->delete();

        return redirect()->route('admin.admissions.index')
            ->with('success', 'Application deleted successfully.');
    }
}
