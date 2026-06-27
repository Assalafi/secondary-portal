<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Models\ClassArm;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index()
    {
        $timetables = Timetable::with(['classArm.schoolClass', 'subject', 'teacher'])
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        $classArms = ClassArm::with('schoolClass')->get();

        return view('admin.academic-management.timetables.index', compact('timetables', 'classArms'));
    }

    public function create()
    {
        $classArms = ClassArm::with('schoolClass')->get();
        $subjects = Subject::all();
        $teachers = User::whereHas('role', function($q) {
            $q->where('name', 'Teacher');
        })->get();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        return view('admin.academic-management.timetables.create', compact('classArms', 'subjects', 'teachers', 'days'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_arm_id' => 'required|exists:class_arms,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:users,id',
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
            'status' => 'required|in:Active,Inactive',
        ]);

        Timetable::create($request->all());

        return redirect()->route('admin.academic-management.timetables.index')
            ->with('success', 'Timetable entry created successfully.');
    }

    public function show(Timetable $timetable)
    {
        $timetable->load(['classArm.schoolClass', 'subject', 'teacher']);

        return view('admin.academic-management.timetables.show', compact('timetable'));
    }

    public function edit(Timetable $timetable)
    {
        $timetable->load(['classArm.schoolClass', 'subject', 'teacher']);

        $classArms = ClassArm::with('schoolClass')->get();
        $subjects = Subject::all();
        $teachers = User::whereHas('role', function($q) {
            $q->where('name', 'Teacher');
        })->get();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        return view('admin.academic-management.timetables.edit', compact('timetable', 'classArms', 'subjects', 'teachers', 'days'));
    }

    public function update(Request $request, Timetable $timetable)
    {
        $request->validate([
            'class_arm_id' => 'required|exists:class_arms,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:users,id',
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:50',
            'status' => 'required|in:Active,Inactive',
        ]);

        $timetable->update($request->all());

        return redirect()->route('admin.academic-management.timetables.index')
            ->with('success', 'Timetable entry updated successfully.');
    }

    public function destroy(Timetable $timetable)
    {
        $timetable->delete();

        return redirect()->route('admin.academic-management.timetables.index')
            ->with('success', 'Timetable entry deleted successfully.');
    }

    public function classTimetable($classArmId)
    {
        $classArm = ClassArm::with('schoolClass')->findOrFail($classArmId);

        $timetables = Timetable::where('class_arm_id', $classArmId)
            ->where('status', 'Active')
            ->with(['subject', 'teacher'])
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $timeSlots = [
            '08:00', '08:45', '09:30', '10:15', '10:45',
            '11:30', '12:15', '01:00', '01:45', '02:30', '03:15'
        ];

        return view('admin.academic-management.timetables.class', compact('classArm', 'timetables', 'days', 'timeSlots'));
    }
}
