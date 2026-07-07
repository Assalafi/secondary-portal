<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $loginInput = trim($request->email);
        $email = $loginInput;

        // If input is not an email, try to find user by admission number
        if (!filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $student = Student::where('admission_no', $loginInput)->first();
            if ($student && $student->user) {
                $email = $student->user->email;
            }
        }

        $credentials = ['email' => $email, 'password' => $request->password];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Update last login timestamp
            $user->update(['last_login_at' => now()]);
            
            // Check if user is active (if status field exists and is set)
            if ($user->status && $user->status !== 'Active') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Your account is not active. Please contact the administrator.',
                ]);
            }

            // Log successful login
            AuditLog::record('login', 'auth', "User {$user->name} logged in", $user);

            // Redirect based on user role
            return $this->redirectBasedOnRole($user);
        }

        // Log failed login attempt
        AuditLog::record('failed_login', 'auth', "Failed login attempt for {$loginInput}", null, null, [
            'attempted_email' => $loginInput,
        ]);

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            AuditLog::record('logout', 'auth', "User {$user->name} logged out", $user);
        }

        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Redirect user based on their role.
     */
    private function redirectBasedOnRole(User $user)
    {
        $roleName = $user->role->name ?? 'Student';

        return match ($roleName) {
            'Super Admin', 'Admin', 'Principal', 'Vice Principal' => redirect()->route('admin.dashboard'),
            'Teacher' => redirect()->route('teacher.dashboard'),
            'Accountant' => redirect()->route('accountant.dashboard'),
            'Librarian' => redirect()->route('librarian.dashboard'),
            'Student' => redirect()->route('student.dashboard'),
            'Parent' => redirect()->route('parent.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }

    /**
     * Show parent registration form.
     */
    public function showParentRegisterForm()
    {
        return view('auth.parent-register');
    }

    /**
     * Handle parent registration.
     */
    public function registerParent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'occupation' => 'nullable|string|max:255',
            'primary_relationship' => 'nullable|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        DB::beginTransaction();
        try {
            // Get Parent role
            $parentRole = Role::where('name', 'Parent')->first();
            
            if (!$parentRole) {
                return back()->withInput()
                    ->with('error', 'Parent role not configured in system. Please contact school administrator.');
            }

            // Create parent account
            $parent = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'occupation' => $request->occupation,
                'role_id' => $parentRole->id,
                'status' => 'Active',
                'password' => Hash::make($request->password),
            ]);

            DB::commit();

            // Auto-login the parent
            Auth::login($parent);
            $parent->update(['last_login_at' => now()]);

            return redirect()->route('parent.dashboard')
                ->with('success', 'Registration successful! Welcome to the portal. Please contact the school administrator to link your child(ren) to your account.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }
}
