<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\ClassArm;
use App\Models\AcademicSession;
use App\Models\Term;
use App\Models\User;
use App\Models\ParentGuardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Role;
use PDF;

class StudentController extends Controller
{
    /**
     * Display students overview page
     */
    public function overview()
    {
        // Get statistics for the overview page
        $totalStudents = Student::where('status', 'Active')->count();
        $newAdmissions = Student::whereDate('created_at', '>=', now()->startOfMonth())->count();
        $pendingPromotions = Student::where('status', 'Active')->count(); // This could be more specific based on your promotion logic
        
        return view('admin.students.overview', compact('totalStudents', 'newAdmissions', 'pendingPromotions'));
    }

    /**
     * Display a listing of students
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'classArm.schoolClass', 'academicSession']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('admission_no', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by class
        if ($request->filled('class_id')) {
            // students table has no direct class_id, filter via classArm -> schoolClass
            $query->whereHas('classArm', function($q) use ($request) {
                $q->where('school_class_id', $request->class_id);
            });
        }
        
        // Filter by class arm
        if ($request->filled('class_arm_id')) {
            $query->where('current_class_arm_id', $request->class_arm_id);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        
        // Filter by academic session
        if ($request->filled('academic_session_id')) {
            $query->where('academic_session_id', $request->academic_session_id);
        }
        
        $students = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get filter options
        $classes = SchoolClass::orderBy('name')->get();
        $classArms = ClassArm::orderBy('name')->get();
        $academicSessions = AcademicSession::orderBy('name', 'desc')->get();
        
        return view('admin.students.index', compact('students', 'classes', 'classArms', 'academicSessions'));
    }
    
    /**
     * Show the form for creating a new student
     */
    public function create()
    {
        $classes = SchoolClass::orderBy('name')->get();
        $classArms = ClassArm::orderBy('name')->get();
        $academicSessions = AcademicSession::orderBy('name', 'desc')->get();
        $terms = Term::orderBy('name')->get();
        
        return view('admin.students.create', compact('classes', 'classArms', 'academicSessions', 'terms'));
    }
    
    /**
     * Store a newly created student in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female',
            'state_of_origin' => 'nullable|string|max:255',
            'lga' => 'nullable|string|max:255',
            'class_arm_id' => 'required|exists:class_arms,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'admission_date' => 'required|date',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Generate admission number
            $admissionNumber = $this->generateAdmissionNumber();
            
            // Generate email from admission number if not provided
            $studentEmail = $request->email ?: strtolower($admissionNumber) . '@student.portal.com';
            
            // Default password is the admission number (simple for students)
            $defaultPassword = $admissionNumber;
            
            // Create user account for student
            $studentRole = Role::where('name', 'Student')->first();
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $studentEmail,
                'password' => Hash::make($defaultPassword),
                'role_id' => $studentRole ? $studentRole->id : 5,
                'status' => 'Active'
            ]);
            
            // Handle profile picture upload
            $profilePicturePath = null;
            if ($request->hasFile('profile_picture')) {
                $profilePicturePath = $request->file('profile_picture')->store('students/profiles', 'public');
            }
            
            // Create student record (aligned with schema)
            $student = Student::create([
                'user_id' => $user->id,
                'admission_no' => $admissionNumber,
                'surname' => $request->last_name,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'dob' => $request->date_of_birth,
                'gender' => $request->gender,
                'state_of_origin' => $request->state_of_origin,
                'lga' => $request->lga,
                'current_class_arm_id' => $request->class_arm_id,
                'academic_session_id' => $request->academic_session_id,
                'admission_date' => $request->admission_date,
                'photo_path' => $profilePicturePath,
                'status' => 'Active'
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.students.index')
                           ->with('success', 'Student registered successfully! Admission No: ' . $admissionNumber . ' | Login: ' . $studentEmail . ' (or ' . $admissionNumber . ') | Password: ' . $defaultPassword);
                           
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Failed to register student. Please try again.');
        }
    }
    
    /**
     * Display the specified student
     */
    public function show(Student $student)
    {
        // Redirect to profile overview page
        return redirect()->route('admin.students.profile.overview', $student->id);
    }
    
    /**
     * Show the form for editing the specified student
     */
    public function edit(Student $student)
    {
        $student->load(['user', 'parentsGuardians']);
        $classes = SchoolClass::orderBy('name')->get();
        $classArms = ClassArm::with('schoolClass')->orderBy('name')->get();
        $academicSessions = AcademicSession::orderBy('name', 'desc')->get();
        $terms = Term::orderBy('name')->get();
        
        return view('admin.students.edit', compact('student', 'classes', 'classArms', 'academicSessions', 'terms'));
    }
    
    /**
     * Update the specified student in storage
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female',
            'state_of_origin' => 'nullable|string|max:255',
            'lga' => 'nullable|string|max:255',
            'class_arm_id' => 'required|exists:class_arms,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'admission_date' => 'required|date',
            'status' => 'required|in:Active,Inactive,Graduated',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'guardians' => 'nullable|array',
            'guardians.*.full_name' => 'required_with:guardians.*|string|max:255',
            'guardians.*.relationship_to_student' => 'required_with:guardians.*|string|max:255',
            'guardians.*.phone_residence' => 'nullable|string|max:20',
            'guardians.*.phone_office' => 'nullable|string|max:20',
            'guardians.*.email' => 'nullable|email|max:255',
            'guardians.*.present_address' => 'nullable|string',
            'primary_guardian' => 'nullable|integer'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update user account
            $student->user->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'status' => $request->status
            ]);
            
            // Handle profile picture upload
            $profilePicturePath = $student->photo_path;
            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture if exists
                if ($student->photo_path) {
                    \Storage::disk('public')->delete($student->photo_path);
                }
                $profilePicturePath = $request->file('profile_picture')->store('students/profiles', 'public');
            }
            
            // Update student record
            $student->update([
                'first_name' => $request->first_name,
                'surname' => $request->last_name,
                'middle_name' => $request->middle_name,
                'dob' => $request->date_of_birth,
                'gender' => $request->gender,
                'state_of_origin' => $request->state_of_origin,
                'lga' => $request->lga,
                'current_class_arm_id' => $request->class_arm_id,
                'academic_session_id' => $request->academic_session_id,
                'admission_date' => $request->admission_date,
                'photo_path' => $profilePicturePath,
                'status' => $request->status
            ]);

            // Handle guardian information
            if ($request->has('guardians') && is_array($request->guardians)) {
                $this->updateGuardians($student, $request->guardians, $request->primary_guardian);
            }
            
            DB::commit();
            
            return redirect()->route('admin.students.profile.overview', $student->id)
                           ->with('success', 'Student updated successfully!');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Failed to update student. Please try again.');
        }
    }
    
    /**
     * Remove the specified student from storage
     */
    public function destroy(Student $student)
    {
        DB::beginTransaction();
        
        try {
            // Delete profile picture if exists
            if ($student->photo_path) {
                \Storage::disk('public')->delete($student->photo_path);
            }
            
            // Delete user account
            $student->user->delete();
            
            // Delete student record
            $student->delete();
            
            DB::commit();
            
            return redirect()->route('admin.students.index')
                           ->with('success', 'Student deleted successfully!');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to delete student. Please try again.');
        }
    }
    
    /**
     * Generate unique admission number
     */
    private function generateAdmissionNumber()
    {
        $year = date('Y');
        $prefix = 'SSP' . $year;
        
        // Get the last admission number for this year
        $lastStudent = Student::where('admission_no', 'like', $prefix . '%')
                             ->orderBy('admission_no', 'desc')
                             ->first();
        
        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->admission_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get class arms for a specific class (AJAX)
     */
    public function getClassArms(Request $request)
    {
        $classId = $request->class_id;
        $classArms = ClassArm::where('school_class_id', $classId)->orderBy('name')->get();
        
        return response()->json($classArms);
    }
    
    /**
     * Reset student password
     */
    public function resetPassword(Student $student)
    {
        try {
            // Generate password using Student ID + 2024 pattern
            $newPassword = $this->generateDefaultPassword($student);
            
            $student->user->update([
                'password' => Hash::make($newPassword)
            ]);
            
            return back()->with('success', "Password reset successfully! New password: <strong>{$newPassword}</strong><br><small class='text-muted'>Default format: StudentID + 2024. Student can change this after logging in.</small>");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reset password. Please try again.');
        }
    }

    /**
     * Generate default password using Student ID + 2024 pattern
     */
    private function generateDefaultPassword(Student $student)
    {
        // Use admission number + 2024 (e.g., SSP001 -> SSP0012024)
        $studentId = $student->admission_no ?? 'STUDENT' . $student->id;
        return $studentId . '2024';
    }
    
    /**
     * Bulk actions for students
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete,promote',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);
        
        $studentIds = $request->student_ids;
        $action = $request->action;
        
        DB::beginTransaction();
        
        try {
            switch ($action) {
                case 'activate':
                    Student::whereIn('id', $studentIds)->update(['status' => 'Active']);
                    User::whereIn('id', function($query) use ($studentIds) {
                        $query->select('user_id')->from('students')->whereIn('id', $studentIds);
                    })->update(['status' => 'Active']);
                    $message = 'Students activated successfully!';
                    break;
                    
                case 'deactivate':
                    Student::whereIn('id', $studentIds)->update(['status' => 'Inactive']);
                    User::whereIn('id', function($query) use ($studentIds) {
                        $query->select('user_id')->from('students')->whereIn('id', $studentIds);
                    })->update(['status' => 'Inactive']);
                    $message = 'Students deactivated successfully!';
                    break;
                    
                case 'delete':
                    $students = Student::whereIn('id', $studentIds)->get();
                    foreach ($students as $student) {
                        if ($student->photo_path) {
                            \Storage::disk('public')->delete($student->photo_path);
                        }
                        $student->user->delete();
                        $student->delete();
                    }
                    $message = 'Students deleted successfully!';
                    break;
                    
                case 'promote':
                    // This would require additional logic for class promotion
                    $message = 'Promotion feature coming soon!';
                    break;
            }
            
            DB::commit();
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Bulk action failed. Please try again.');
        }
    }

    /**
     * Show enrollment step 1 form
     */
    public function enrollStep1()
    {
        $step1Data = session('enrollment_step1');
        return view('admin.students.enroll-step1', compact('step1Data'));
    }

    /**
     * Show enrollment step 2 (GET) with required dropdown data
     */
    public function showEnrollStep2()
    {
        $classes = SchoolClass::orderBy('name')->get();
        $classesLight = $classes->map(function ($c) {
            return [
                'id' => $c->id,
                'level' => $c->level,
                'name' => $c->name,
                'group' => $c->group,
            ];
        })->values()->all();
        $academicSessions = AcademicSession::orderBy('name', 'desc')->get();
        $step2Data = session('enrollment_step2');

        return view('admin.students.enroll-step2', compact('classes', 'classesLight', 'academicSessions', 'step2Data'));
    }

    /**
     * Process enrollment step 1 and show step 2
     */
    public function enrollStep2(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date',
            'state_of_origin' => 'required|string|max:255',
            'lga' => 'required|string|max:255',
        ]);

        // Store step 1 data in session
        session(['enrollment_step1' => $request->all()]);

        $classes = SchoolClass::orderBy('name')->get();
        $classesLight = $classes->map(function ($c) {
            return [
                'id' => $c->id,
                'level' => $c->level,
                'name' => $c->name,
                'group' => $c->group,
            ];
        })->values()->all();
        $academicSessions = AcademicSession::orderBy('name', 'desc')->get();
        $step2Data = session('enrollment_step2');

        return view('admin.students.enroll-step2', compact('classes', 'classesLight', 'academicSessions', 'step2Data'));
    }

    /**
     * Process enrollment step 2 and show step 3
     */
    public function enrollStep3(Request $request)
    {
        $request->validate([
            'academic_level' => 'required|string',
            'class' => 'required', // can be ID
            'class_arm' => 'required|string',
            'session' => 'required|in:Morning,Afternoon',
            'admission_number' => 'required|string|unique:students,admission_no',
            'enrollment_date' => 'required|date',
        ]);

        // Store step 2 data in session
        session(['enrollment_step2' => $request->all()]);

        return view('admin.students.enroll-step3');
    }

    /**
     * Show enrollment step 3 (GET)
     */
    public function showEnrollStep3()
    {
        $step3Data = session('enrollment_step3');
        return view('admin.students.enroll-step3', compact('step3Data'));
    }

    /**
     * Process enrollment step 3 and show step 4 (confirmation)
     */
    public function enrollStep4(Request $request)
    {
        $request->validate([
            'guardian_name' => 'required|string|max:255',
            'relationship' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email_address' => 'nullable|email|max:255',
            'occupation' => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:20',
            'address' => 'required|string',
            'city' => 'nullable|string|max:255',
            'guardian_state' => 'nullable|string|max:255',
        ]);

        // Store step 3 data in session
        session(['enrollment_step3' => $request->all()]);

        // Get all enrollment data for confirmation
        $step1Data = session('enrollment_step1');
        $step2Data = session('enrollment_step2');
        $step3Data = session('enrollment_step3');

        return view('admin.students.enroll-step4', compact('step1Data', 'step2Data', 'step3Data'));
    }

    /**
     * Show enrollment step 4 (GET)
     */
    public function showEnrollStep4()
    {
        $step1Data = session('enrollment_step1');
        $step2Data = session('enrollment_step2');
        $step3Data = session('enrollment_step3');

        // If enrollment just completed, allow rendering without step session data
        if (!$step1Data || !$step2Data || !$step3Data) {
            if (session('enrolled_success')) {
                return view('admin.students.enroll-step4', compact('step1Data', 'step2Data', 'step3Data'));
            }
            return redirect()->route('admin.students.enroll.step1')
                           ->with('error', 'Enrollment session expired. Please start again.');
        }

        return view('admin.students.enroll-step4', compact('step1Data', 'step2Data', 'step3Data'));
    }

    /**
     * Complete student enrollment
     */
    public function completeEnrollment(Request $request)
    {
        $step1Data = session('enrollment_step1');
        $step2Data = session('enrollment_step2');
        $step3Data = session('enrollment_step3');

        if (!$step1Data || !$step2Data || !$step3Data) {
            return redirect()->route('admin.students.enroll.step1')
                           ->with('error', 'Enrollment session expired. Please start again.');
        }

        DB::beginTransaction();

        try {
            // Create user account for student
            $studentRole = Role::where('name', 'Student')->first();
            $admissionNo = $step2Data['admission_number'];
            $defaultPassword = $admissionNo; // Simple: admission number is the default password
            $studentEmail = strtolower($admissionNo) . '@student.portal.com';
            $user = User::create([
                'name' => $step1Data['first_name'] . ' ' . $step1Data['last_name'],
                'email' => $studentEmail,
                'password' => Hash::make($defaultPassword),
                'role_id' => $studentRole ? $studentRole->id : 5,
                'status' => 'Active'
            ]);

            // Find class and class arm IDs (prefer IDs from session if available)
            $class = null;
            if (!empty($step2Data['class_id'])) {
                $class = SchoolClass::find($step2Data['class_id']);
            }
            if (!$class) {
                $class = SchoolClass::where('name', $step2Data['class'] ?? '')->first();
            }

            $classArm = null;
            if (!empty($step2Data['class_arm_id'])) {
                $classArm = ClassArm::find($step2Data['class_arm_id']);
            }
            if (!$classArm && $class) {
                $classArm = ClassArm::where('name', $step2Data['class_arm'] ?? '')
                                    ->where('school_class_id', $class->id)
                                    ->first();
            }
            // Use the session selected in step 2, fallback to current session
            $academicSession = AcademicSession::where('name', $step2Data['academic_session'] ?? '')->first();
            if (!$academicSession) {
                $academicSession = AcademicSession::where('is_current', true)->first();
            }

            // Create student record (align with students table schema)
            $student = Student::create([
                'user_id' => $user->id,
                'admission_no' => $step2Data['admission_number'],
                'first_name' => $step1Data['first_name'],
                'surname' => $step1Data['last_name'] ?? ($step1Data['surname'] ?? null),
                'middle_name' => $step1Data['middle_name'] ?? null,
                'gender' => $step1Data['gender'],
                'dob' => $step1Data['date_of_birth'],
                'state_of_origin' => $step1Data['state_of_origin'] ?? null,
                'lga' => $step1Data['lga'] ?? null,
                'nationality' => 'Nigerian',
                'current_class_arm_id' => $classArm ? $classArm->id : null,
                'academic_session_id' => $academicSession ? $academicSession->id : null,
                'admission_date' => $step2Data['enrollment_date'],
                'status' => 'Active',
            ]);

            // Create guardian record
            if (!empty($step3Data['guardian_name'])) {
                // Build address string including city and state if provided
                $fullAddress = $step3Data['address'];
                if (!empty($step3Data['city']) || !empty($step3Data['guardian_state'])) {
                    $additionalInfo = [];
                    if (!empty($step3Data['city'])) $additionalInfo[] = $step3Data['city'];
                    if (!empty($step3Data['guardian_state'])) $additionalInfo[] = $step3Data['guardian_state'];
                    $fullAddress .= (!empty($additionalInfo) ? "\n" . implode(', ', $additionalInfo) : '');
                }

                $guardian = ParentGuardian::create([
                    'full_name' => $step3Data['guardian_name'],
                    'relationship_to_student' => $step3Data['relationship'],
                    'phone_residence' => $step3Data['phone_number'],
                    'phone_office' => $step3Data['emergency_contact'] ?? null,
                    'email' => $step3Data['email_address'] ?? null,
                    'present_address' => $fullAddress,
                    'occupation' => $step3Data['occupation'] ?? null,
                ]);

                // Attach guardian to student as primary contact
                $student->parentsGuardians()->attach($guardian->id, [
                    'is_primary_contact' => true
                ]);
            }

            // Clear enrollment session data
            session()->forget(['enrollment_step1', 'enrollment_step2', 'enrollment_step3']);

            DB::commit();

            // Redirect back to step 4 to show success modal
            return redirect()->route('admin.students.enroll.step4.show')
                           ->with('enrolled_success', [
                               'student_id' => $student->id, 
                               'admission_no' => $step2Data['admission_number'],
                               'default_password' => $defaultPassword
                           ]);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to enroll student. Please try again.');
        }
    }

    /**
     * Show student profile with tabs
     */
    public function profile($id, $tab = 'overview')
    {
        $student = Student::with(['user', 'classArm.schoolClass', 'academicSession'])->findOrFail($id);
        
        return view('admin.students.profile.' . $tab, compact('student'));
    }

    /**
     * Show student profile overview tab
     */
    public function profileOverview(Student $student)
    {
        $student->load(['user', 'classArm.schoolClass', 'academicSession', 'parentsGuardians', 'attendances']);
        
        return view('admin.students.profile.overview', compact('student'));
    }

    /**
     * Show student profile academic tab
     */
    public function profileAcademic(Student $student)
    {
        $student->load(['user', 'classArm.schoolClass', 'academicSession', 'scores.scoreBatch.subject', 'scores.scoreBatch.term', 'scores.scoreBatch.academicSession']);
        
        // Get available terms and sessions for filtering
        $terms = Term::orderBy('id')->get();
        $sessions = AcademicSession::orderBy('name', 'desc')->get();
        
        return view('admin.students.profile.academic-info', compact('student', 'terms', 'sessions'));
    }

    /**
     * Show student profile fees tab
     */
    public function profileFees(Student $student)
    {
        $student->load(['user', 'classArm.schoolClass', 'academicSession', 'invoices.term']);
        
        return view('admin.students.profile.fees', compact('student'));
    }

    /**
     * Show student profile attendance tab
     */
    public function profileAttendance(Student $student)
    {
        $student->load(['user', 'classArm.schoolClass', 'academicSession', 'attendances']);
        
        return view('admin.students.profile.attendance', compact('student'));
    }

    /**
     * Show student profile documents tab
     */
    public function profileDocuments(Student $student)
    {
        $student->load(['user', 'classArm.schoolClass', 'academicSession']);
        
        return view('admin.students.profile.documents', compact('student'));
    }

    /**
     * Generate PDF for student profile
     */
    public function profilePdf(Student $student)
    {
        $student->load(['user', 'classArm.schoolClass', 'academicSession', 'parentsGuardians']);
        
        $pdf = PDF::loadView('admin.students.profile.pdf', compact('student'));
        
        return $pdf->download('student_' . $student->admission_no . '_profile.pdf');
    }

    /**
     * Generate PDF for student academic report
     */
    public function profileAcademicPdf(Student $student)
    {
        $student->load(['user', 'classArm.schoolClass', 'academicSession', 'scores.scoreBatch.subject', 'scores.scoreBatch.term']);
        
        $pdf = PDF::loadView('admin.students.profile.academic-pdf', compact('student'));
        
        return $pdf->download('academic_' . $student->admission_no . '_report.pdf');
    }

    /**
     * Generate PDF for student fee statement
     */
    public function profileFeesPdf(Student $student)
    {
        $student->load(['user', 'classArm.schoolClass', 'academicSession', 'invoices']);
        
        $pdf = PDF::loadView('admin.students.profile.fees-pdf', compact('student'));
        
        return $pdf->download('fees_' . $student->admission_no . '_statement.pdf');
    }

    /**
     * Generate PDF for student attendance report
     */
    public function profileAttendancePdf(Student $student)
    {
        $student->load(['user', 'classArm.schoolClass', 'academicSession', 'attendances']);
        
        $pdf = PDF::loadView('admin.students.profile.attendance-pdf', compact('student'));
        
        return $pdf->download('attendance_' . $student->admission_no . '_report.pdf');
    }

    /**
     * Show promote/transfer index page
     */
    public function promoteIndex()
    {
        $schoolClasses = SchoolClass::with('classArms.students')
            ->orderBy('numeric_level')
            ->get();

        $totalStudents = Student::where('status', 'Active')->count();
        $graduatingStudents = Student::where('status', 'Active')
            ->whereHas('classArm.schoolClass', function($query) {
                $query->where('name', 'like', 'SS3%');
            })
            ->count();

        return view('admin.students.promote.index', compact('schoolClasses', 'totalStudents', 'graduatingStudents'));
    }

    /**
     * Show students in a class for promotion/transfer
     */
    public function promoteClass($class)
    {
        $students = Student::with(['user', 'classArm.schoolClass'])
                          ->whereHas('classArm.schoolClass', function($query) use ($class) {
                              $query->where('name', $class);
                          })
                          ->where('status', 'Active')
                          ->get();

        return view('admin.students.promote.class', compact('students', 'class'));
    }

    /**
     * Process bulk promotion
     */
    public function bulkPromote(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'target_class' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $targetClass = SchoolClass::where('name', $request->target_class)->first();
            
            if (!$targetClass) {
                return back()->with('error', 'Target class not found.');
            }

            // Choose a default class arm for the target class (first by name)
            $targetArm = ClassArm::where('school_class_id', $targetClass->id)
                                 ->orderBy('name')
                                 ->first();

            if (!$targetArm) {
                DB::rollBack();
                return back()->with('error', 'No class arms found for the target class.');
            }

            Student::whereIn('id', $request->student_ids)
                   ->update(['current_class_arm_id' => $targetArm->id]);

            DB::commit();

            return back()->with('success', count($request->student_ids) . ' students promoted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Promotion failed. Please try again.');
        }
    }

    /**
     * Process bulk transfer
     */
    public function bulkTransfer(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'target_session' => 'required|string',
            'target_class_arm' => 'required|string',
            'target_class_group' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $targetClassArm = ClassArm::where('name', $request->target_class_arm)->first();
            
            if (!$targetClassArm) {
                return back()->with('error', 'Target class arm not found.');
            }

            Student::whereIn('id', $request->student_ids)
                   ->update(['current_class_arm_id' => $targetClassArm->id]);

            DB::commit();

            return back()->with('success', count($request->student_ids) . ' students transferred successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Transfer failed. Please try again.');
        }
    }

    /**
     * Update guardians for a student
     */
    private function updateGuardians(Student $student, array $guardians, $primaryGuardianIndex = null)
    {
        // Get existing guardian IDs to track which ones to keep
        $existingGuardianIds = [];
        $updatedGuardianIds = [];

        foreach ($guardians as $index => $guardianData) {
            // Skip empty guardian data
            if (empty($guardianData['full_name'])) {
                continue;
            }

            $isPrimary = ($primaryGuardianIndex !== null && $primaryGuardianIndex == $index);

            if (isset($guardianData['id']) && !empty($guardianData['id'])) {
                // Update existing guardian
                $guardian = ParentGuardian::find($guardianData['id']);
                if ($guardian) {
                    $guardian->update([
                        'full_name' => $guardianData['full_name'],
                        'relationship_to_student' => $guardianData['relationship_to_student'],
                        'phone_residence' => $guardianData['phone_residence'] ?? null,
                        'phone_office' => $guardianData['phone_office'] ?? null,
                        'email' => $guardianData['email'] ?? null,
                        'present_address' => $guardianData['present_address'] ?? null,
                    ]);

                    // Update pivot table
                    $student->parentsGuardians()->updateExistingPivot($guardian->id, [
                        'is_primary_contact' => $isPrimary
                    ]);

                    $updatedGuardianIds[] = $guardian->id;
                }
            } else {
                // Create new guardian
                $guardian = ParentGuardian::create([
                    'full_name' => $guardianData['full_name'],
                    'relationship_to_student' => $guardianData['relationship_to_student'],
                    'phone_residence' => $guardianData['phone_residence'] ?? null,
                    'phone_office' => $guardianData['phone_office'] ?? null,
                    'email' => $guardianData['email'] ?? null,
                    'present_address' => $guardianData['present_address'] ?? null,
                ]);

                // Attach to student
                $student->parentsGuardians()->attach($guardian->id, [
                    'is_primary_contact' => $isPrimary
                ]);

                $updatedGuardianIds[] = $guardian->id;
            }
        }

        // Remove guardians that are no longer associated
        $currentGuardianIds = $student->parentsGuardians()->pluck('parents_guardians.id')->toArray();
        $guardiansToRemove = array_diff($currentGuardianIds, $updatedGuardianIds);
        
        if (!empty($guardiansToRemove)) {
            $student->parentsGuardians()->detach($guardiansToRemove);
        }

        // Ensure at least one primary guardian if guardians exist
        if (!empty($updatedGuardianIds) && $primaryGuardianIndex === null) {
            $firstGuardianId = $updatedGuardianIds[0];
            $student->parentsGuardians()->updateExistingPivot($firstGuardianId, [
                'is_primary_contact' => true
            ]);
        }
    }
}
