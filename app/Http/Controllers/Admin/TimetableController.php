<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Models\ClassArm;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $schoolClasses = \App\Models\SchoolClass::all();
        $classArms = ClassArm::with('schoolClass')->get();
        $teachers = User::whereHas('role', function($q) {
            $q->where('name', 'Teacher');
        })->get();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('admin.academic-management.timetables.create', compact('schoolClasses', 'classArms', 'teachers', 'days'));
    }

    public function getClassArmsByClass(Request $request)
    {
        $schoolClassId = $request->query('school_class_id');
        if (!$schoolClassId) {
            return response()->json(['classArms' => []]);
        }

        $classArms = ClassArm::where('school_class_id', $schoolClassId)
            ->select('id', 'name')
            ->get();

        return response()->json(['classArms' => $classArms]);
    }

    public function getSubjectsByClassArm(Request $request)
    {
        $classArmId = $request->query('class_arm_id');
        if (!$classArmId) {
            return response()->json(['subjects' => []]);
        }

        $subjects = DB::table('class_subject')
            ->join('subjects', 'class_subject.subject_id', '=', 'subjects.id')
            ->where('class_subject.class_arm_id', $classArmId)
            ->select('subjects.id', 'subjects.name', 'subjects.code')
            ->get();

        return response()->json(['subjects' => $subjects]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_arm_id' => 'required|exists:class_arms,id',
            'entries' => 'required|array|min:1',
            'entries.*.subject_id' => 'required|exists:subjects,id',
            'entries.*.day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'entries.*.start_time' => 'required|date_format:H:i',
            'entries.*.end_time' => 'required|date_format:H:i|after:entries.*.start_time',
            'entries.*.room' => 'nullable|string|max:50',
            'entries.*.teacher_id' => 'nullable|exists:users,id',
            'status' => 'required|in:Active,Inactive',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->entries as $entry) {
                Timetable::create([
                    'class_arm_id' => $request->class_arm_id,
                    'subject_id' => $entry['subject_id'],
                    'teacher_id' => $entry['teacher_id'] ?? null,
                    'day' => $entry['day'],
                    'start_time' => $entry['start_time'],
                    'end_time' => $entry['end_time'],
                    'room' => $entry['room'] ?? null,
                    'status' => $request->status,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create timetable entries: ' . $e->getMessage());
        }

        return redirect()->route('admin.academic-management.timetables.index')
            ->with('success', 'Timetable entries created successfully.');
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
