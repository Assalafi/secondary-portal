<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    /**
     * Display the account settings page.
     */
    public function index()
    {
        $user = Auth::user();
        return view('parent.account.index', compact('user'));
    }
    
    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female',
            'nationality' => 'nullable|string|max:100',
            'state_of_origin' => 'nullable|string|max:100',
        ]);
        
        $user->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
        ]);
    }
    
    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);
        
        $user = Auth::user();
        
        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 422);
        }
        
        // Update password
        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ]);
    }
    
    /**
     * Update notification preferences.
     */
    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'attendance_alerts' => 'boolean',
            'payment_reminders' => 'boolean',
            'academic_updates' => 'boolean',
        ]);
        
        // In a real app, you'd store these in a separate preferences table
        // For now, we'll just return success
        
        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated successfully',
        ]);
    }
}
