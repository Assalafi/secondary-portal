<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('admin.academic-management.attendance.index');
    }

    public function take($classId)
    {
        return view('admin.academic-management.attendance.take', compact('classId'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'class_arm_id' => 'required|exists:class_arms,id',
                'date' => 'required|date',
                'attendance' => 'required|array',
            ]);

            foreach ($request->attendance as $studentId => $data) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'date' => $request->date,
                    ],
                    [
                        'status' => $data['status'],
                        'remarks' => $data['remarks'] ?? null,
                        'class_arm_id' => $request->class_arm_id,
                        'marked_by' => auth()->id(),
                    ]
                );
            }

            return back()->with('success', 'Attendance saved successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to save attendance: ' . $e->getMessage());
        }
    }

    public function history($classId)
    {
        return view('admin.academic-management.attendance.history', compact('classId'));
    }
}
