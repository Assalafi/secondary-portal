<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolSettings;
use App\Models\GradeSettings;
use App\Models\SessionTerm;
use App\Models\User;
use App\Models\Role;
use App\Models\Staff;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Display the main settings dashboard
     */
    public function index()
    {
        $schoolSettings = SchoolSettings::first();
        return view('admin.settings.index', compact('schoolSettings'));
    }

    /**
     * School Information Settings
     */
    public function schoolInfo()
    {
        $settings = SchoolSettings::firstOrNew();
        return view('admin.settings.school-info', compact('settings'));
    }

    /**
     * Update School Information
     */
    public function updateSchoolInfo(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_address' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:64',
            'meta_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'meta_author' => 'nullable|string|max:255',
        ]);

        try {
            $settings = SchoolSettings::firstOrNew();
            
            $settings->school_name = $request->school_name;
            $settings->school_address = $request->school_address;
            $settings->phone_number = $request->phone_number;
            $settings->email = $request->email;
            $settings->website = $request->website;
            $settings->established_year = $request->established_year;
            $settings->meta_description = $request->meta_description;
            $settings->meta_keywords = $request->meta_keywords;
            $settings->meta_author = $request->meta_author;

            // Handle logo upload
            if ($request->hasFile('school_logo')) {
                try {
                    // Delete old logo if exists
                    if ($settings->school_logo && Storage::disk('public')->exists($settings->school_logo)) {
                        Storage::disk('public')->delete($settings->school_logo);
                    }
                    
                    $logoPath = $request->file('school_logo')->store('school/logos', 'public');
                    if ($logoPath) {
                        $settings->school_logo = $logoPath;
                    } else {
                        \Log::error('Failed to store logo');
                    }
                } catch (\Exception $e) {
                    \Log::error('Logo upload error: ' . $e->getMessage());
                }
            }

            // Handle favicon upload
            if ($request->hasFile('favicon')) {
                try {
                    // Delete old favicon if exists
                    if ($settings->favicon && Storage::disk('public')->exists($settings->favicon)) {
                        Storage::disk('public')->delete($settings->favicon);
                    }
                    
                    $faviconPath = $request->file('favicon')->store('school/favicons', 'public');
                    if ($faviconPath) {
                        $settings->favicon = $faviconPath;
                    } else {
                        \Log::error('Failed to store favicon');
                    }
                } catch (\Exception $e) {
                    \Log::error('Favicon upload error: ' . $e->getMessage());
                }
            }

            // Handle meta image upload
            if ($request->hasFile('meta_image')) {
                try {
                    // Delete old meta image if exists
                    if ($settings->meta_image && Storage::disk('public')->exists($settings->meta_image)) {
                        Storage::disk('public')->delete($settings->meta_image);
                    }
                    
                    $metaImagePath = $request->file('meta_image')->store('school/meta-images', 'public');
                    if ($metaImagePath) {
                        $settings->meta_image = $metaImagePath;
                    } else {
                        \Log::error('Failed to store meta image');
                    }
                } catch (\Exception $e) {
                    \Log::error('Meta image upload error: ' . $e->getMessage());
                }
            }

            $settings->save();

            return response()->json([
                'success' => true,
                'message' => 'School information updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update school information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Grading System Settings
     */
    public function gradingSystem()
    {
        $grades = GradeSettings::orderBy('level')
            ->orderBy('min_score', 'desc')
            ->get()
            ->groupBy('level');
            
        return view('admin.settings.grading-system', compact('grades'));
    }

    /**
     * Store new grade
     */
    public function storeGrade(Request $request)
    {
        $request->validate([
            'level' => 'required|in:Primary,Secondary',
            'grade' => 'required|string|max:5',
            'min_score' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:0|max:100',
            'gpa_point' => 'required|numeric|min:0|max:4',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            GradeSettings::create(array_merge($request->all(), ['status' => 'Active']));

            return response()->json([
                'success' => true,
                'message' => 'Grade added successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add grade: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update grade
     */
    public function updateGrade(Request $request, GradeSettings $grade)
    {
        $request->validate([
            'level' => 'required|in:Primary,Secondary',
            'grade' => 'required|string|max:5',
            'min_score' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:0|max:100',
            'gpa_point' => 'required|numeric|min:0|max:4',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $grade->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Grade updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update grade: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete grade
     */
    public function deleteGrade(GradeSettings $grade)
    {
        try {
            $grade->delete();

            return response()->json([
                'success' => true,
                'message' => 'Grade deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete grade: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Session/Term Management
     */
    public function sessionTerm()
    {
        $sessions = SessionTerm::orderBy('academic_year', 'desc')
            ->orderBy('term_name')
            ->get();
        return view('admin.settings.session-term', compact('sessions'));
    }

    /**
     * Store new session/term
     */
    public function storeSessionTerm(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|string|max:20',
            'term_name' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        try {
            // If marked as current, remove current flag from others
            if ($request->boolean('is_current')) {
                SessionTerm::where('is_current', true)->update(['is_current' => false]);
            }

            SessionTerm::create(array_merge($request->all(), [
                'status' => 'Active',
                'is_current' => $request->boolean('is_current')
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Session/Term created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create session/term: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Security Settings
     */
    public function security()
    {
        $settings = SchoolSettings::first();
        $securitySettings = $settings ? json_decode($settings->security_settings, true) : [];
        
        return view('admin.settings.security', compact('settings', 'securitySettings'));
    }

    /**
     * Update Security Settings
     */
    public function updateSecurity(Request $request)
    {
        $request->validate([
            'password_min_length' => 'required|integer|min:6|max:20',
            'session_timeout' => 'required|integer|min:15|max:1440',
            'max_login_attempts' => 'required|integer|min:3|max:10',
        ]);

        try {
            $settings = SchoolSettings::firstOrNew();
            
            $securitySettings = [
                'password_min_length' => $request->password_min_length,
                'password_require_uppercase' => $request->boolean('password_require_uppercase'),
                'password_require_lowercase' => $request->boolean('password_require_lowercase'),
                'password_require_numbers' => $request->boolean('password_require_numbers'),
                'password_require_symbols' => $request->boolean('password_require_symbols'),
                'enable_two_factor' => $request->boolean('enable_two_factor'),
                'session_timeout' => $request->session_timeout,
                'max_login_attempts' => $request->max_login_attempts,
                'lockout_duration' => $request->lockout_duration ?? 30,
            ];

            $settings->security_settings = json_encode($securitySettings);
            $settings->save();

            return response()->json([
                'success' => true,
                'message' => 'Security settings updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update security settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * User Management
     */
    public function userManagement()
    {
        $users = User::with('role')->latest()->paginate(15);
        $roles = Role::all();
        return view('admin.settings.user-management', compact('users', 'roles'));
    }

    /**
     * Create new user
     */
    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'phone' => $request->phone,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully!',
                'user' => $user->load('role')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Notification Preferences
     */
    public function notificationPreferences()
    {
        $settings = SchoolSettings::first();
        $notificationSettings = $settings ? json_decode($settings->notification_settings, true) : [
            'in_app' => [
                'all_notifications' => true,
                'academic_updates' => true,
                'payment_reminders' => true,
                'result_publication' => true,
            ],
            'email' => [
                'all_notifications' => true,
                'academic_updates' => true,
                'payment_reminders' => true,
                'result_publication' => true,
            ]
        ];
        
        return view('admin.settings.notification-preferences', compact('settings', 'notificationSettings'));
    }

    /**
     * Update Notification Preferences
     */
    public function updateNotificationPreferences(Request $request)
    {
        try {
            $settings = SchoolSettings::firstOrNew();
            
            $notificationSettings = [
                'in_app' => [
                    'all_notifications' => $request->boolean('in_app_all_notifications'),
                    'academic_updates' => $request->boolean('in_app_academic_updates'),
                    'payment_reminders' => $request->boolean('in_app_payment_reminders'),
                    'result_publication' => $request->boolean('in_app_result_publication'),
                ],
                'email' => [
                    'all_notifications' => $request->boolean('email_all_notifications'),
                    'academic_updates' => $request->boolean('email_academic_updates'),
                    'payment_reminders' => $request->boolean('email_payment_reminders'),
                    'result_publication' => $request->boolean('email_result_publication'),
                ]
            ];

            $settings->notification_settings = json_encode($notificationSettings);
            $settings->save();

            return response()->json([
                'success' => true,
                'message' => 'Notification preferences updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notification preferences: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Role & Permissions Management
     */
    public function rolePermissions()
    {
        $roles = Role::with('permissions')->get();
        return view('admin.settings.role-permissions', compact('roles'));
    }

    /**
     * Create new role
     */
    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:255',
            'permissions' => 'array',
        ]);

        try {
            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Attach permissions if any
            if ($request->permissions) {
                // You can implement permission attachment logic here
                // $role->permissions()->attach($request->permissions);
            }

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set current session/term
     */
    public function setCurrentSession(SessionTerm $session)
    {
        try {
            // Remove current flag from all sessions
            SessionTerm::where('is_current', true)->update(['is_current' => false]);
            
            // Set this session as current
            $session->update(['is_current' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Session/Term set as current successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set current session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * System Configuration
     */
    public function systemConfig()
    {
        $settings = SchoolSettings::first();
        $systemSettings = $settings ? json_decode($settings->system_settings, true) : [];
        return view('admin.settings.system-config', compact('systemSettings'));
    }

    /**
     * Update System Configuration
     */
    public function updateSystemConfig(Request $request)
    {
        try {
            $settings = SchoolSettings::firstOrNew();
            $settings->system_settings = json_encode($request->all());
            $settings->save();

            return response()->json([
                'success' => true,
                'message' => 'System configuration updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update system configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Backup & Restore
     */
    public function backupRestore()
    {
        return view('admin.settings.backup-restore');
    }
}
