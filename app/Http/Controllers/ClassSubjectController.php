<?php

namespace App\Http\Controllers;

use App\Models\ClassArm;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassSubjectController extends Controller
{
    // Classes Management
    public function classesIndex(Request $request)
    {
        // Start building the ClassArm query
        $classArmsQuery = ClassArm::with(['schoolClass', 'students', 'classTeacher']);

        // Apply filters from the request
        if ($request->filled('level')) {
            $classArmsQuery->whereHas('schoolClass', function ($q) use ($request) {
                $q->where('level', $request->level);
            });
        }
        if ($request->filled('class')) {
            $classArmsQuery->whereHas('schoolClass', function ($q) use ($request) {
                $q->where('name', $request->class);
            });
        }
        if ($request->filled('group')) {
            $classArmsQuery->whereHas('schoolClass', function ($q) use ($request) {
                $q->where('group', $request->group);
            });
        }
        if ($request->filled('arm')) {
            $classArmsQuery->where('name', $request->arm);
        }

        // Handle search term
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $classArmsQuery->where(function ($q) use ($searchTerm) {
                $q
                    ->whereHas('schoolClass', function ($sq) use ($searchTerm) {
                        $sq
                            ->where('name', 'like', "%{$searchTerm}%")
                            ->orWhere('level', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('classTeacher', function ($tq) use ($searchTerm) {
                        $tq->where('name', 'like', "%{$searchTerm}%");
                    })
                    ->orWhere('name', 'like', "%{$searchTerm}%");  // Search arm name as well
            });
        }

        $classArms = $classArmsQuery->get();

        // Data for filter dropdowns
        $levels = SchoolClass::distinct()->pluck('level');
        $classNames = SchoolClass::distinct()->pluck('name');
        $arms = ClassArm::distinct()->pluck('name');
        $groups = SchoolClass::whereNotNull('group')->distinct()->pluck('group');
        $arms = ClassArm::distinct()->pluck('name');
        $teachers = User::whereHas('role', function ($query) {
            $query->where('name', 'Teacher');
        })->get();
        $allClasses = SchoolClass::orderBy('level')->orderBy('name')->get();

        return view('admin.classes.index', compact('classArms', 'levels', 'classNames', 'arms', 'groups', 'teachers', 'allClasses'));
    }

    public function classesCreate()
    {
        return view('admin.classes.create');
    }

    public function classesStore(Request $request)
    {
        $request->validate([
            'level' => 'required|string|in:JSS,SS,Primary',
            'class_name' => 'required|string',
            'group' => 'nullable|string|in:Science,Arts,Commercial',
            'arm' => 'required|string|max:1|in:A,B,C,D,E,F',
            'capacity' => 'required|integer|min:1|max:100',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        // Find or create the SchoolClass
        $schoolClass = SchoolClass::firstOrCreate(
            [
                'level' => $request->level,
                'name' => $request->class_name,
                'group' => $request->group,
            ],
            [
                'status' => 'Active',
                'numeric_level' => $this->getNumericLevel($request->level, $request->class_name)
            ]
        );

        // Check if the arm already exists for this class
        $existingArm = ClassArm::where('school_class_id', $schoolClass->id)
            ->where('name', $request->arm)
            ->first();

        if ($existingArm) {
            return back()->with('error', 'The arm "' . $request->arm . '" already exists for ' . $schoolClass->name . '.');
        }

        // Create the new ClassArm
        $classArm = new ClassArm();
        $classArm->school_class_id = $schoolClass->id;
        $classArm->name = $request->arm;
        $classArm->capacity = $request->capacity;
        $classArm->class_teacher_id = $request->teacher_id;
        $classArm->save();

        return redirect()->route('admin.classes.index')->with('success', 'Class ' . $schoolClass->name . ' ' . $request->arm . ' created successfully with capacity of ' . $request->capacity . ' students.');
    }

    private function getNumericLevel($level, $className)
    {
        // Convert level and class name to numeric level for sorting
        $levelMap = [
            'Primary' => 1,
            'JSS' => 2,
            'SS' => 3
        ];

        $classNum = 1;
        if (preg_match('/(\d+)/', $className, $matches)) {
            $classNum = (int) $matches[1];
        }

        return ($levelMap[$level] ?? 0) * 10 + $classNum;
    }

    public function classesShow(SchoolClass $class, $armId = null)
    {
        $class->load(['classArms.students', 'classArms.classTeacher', 'classArms.subjects']);

        // If no specific arm is requested, show the first arm
        $classArm = $armId ? $class->classArms->find($armId) : $class->classArms->first();

        if (!$classArm) {
            return redirect()->route('admin.classes.index')->with('error', 'Class arm not found.');
        }

        $teachers = User::whereHas('role', function ($query) {
            $query->where('name', 'Teacher');
        })->get();

        // Get IDs of subjects already assigned to the class arm
        $assignedSubjectIds = $classArm->subjects->pluck('id');

        // Fetch subjects that are not already assigned
        $subjects = Subject::whereNotIn('id', $assignedSubjectIds)->get();

        return view('admin.classes.show', compact('class', 'classArm', 'teachers', 'subjects'));
    }

    public function classesEdit(SchoolClass $class)
    {
        return view('admin.classes.edit', compact('class'));
    }

    public function classesUpdate(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|in:1,2,3',
            'level' => 'required|string|in:JSS,SS,Primary',
            'group' => 'nullable|string|in:Science,Arts,Commercial',
            'arm_name' => 'required|string|max:1',
            'class_arm_id' => 'required|exists:class_arms,id',
        ]);

        // Update the main class details
        $class->update([
            'name' => $validated['name'],
            'level' => $validated['level'],
            'group' => $validated['group'],
        ]);

        // Find and update the class arm name
        $classArm = ClassArm::find($validated['class_arm_id']);
        if ($classArm) {
            $classArm->name = $validated['arm_name'];
            $classArm->save();
        }

        return redirect()
            ->route('admin.classes.show', ['class' => $class->id])
            ->with('success', 'Class details updated successfully.');
    }

    public function classesDestroy(SchoolClass $class)
    {
        $class->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Class deleted successfully!');
    }

    public function removeStudentFromClass(Request $request, SchoolClass $class, Student $student)
    {
        // Assuming a student can only be in one class arm at a time,
        // and that the student belongs to an arm of the given $class.
        if ($student->classArm && $student->classArm->school_class_id == $class->id) {
            $student->class_arm_id = null;
            $student->save();
            return back()->with('success', 'Student removed from class successfully.');
        }

        return back()->with('error', 'Student not found in this class.');
    }

    public function addSubjectToClass(Request $request, ClassArm $class_arm)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
        ]);

        // Check if the subject is already attached to prevent duplicates
        if ($class_arm->subjects()->where('subject_id', $validated['subject_id'])->exists()) {
            return back()->with('error', 'This subject is already assigned to the class.');
        }

        $class_arm->subjects()->attach($validated['subject_id']);

        return back()->with('success', 'Subject added to class successfully.');
    }

    public function removeSubjectFromClass(Request $request, ClassArm $class_arm, Subject $subject)
    {
        $class_arm->subjects()->detach($subject->id);

        return back()->with('success', 'Subject removed from class successfully.');
    }

    // Subjects Management
    public function subjectsIndex(Request $request)
    {
        $query = Subject::with(['classArms.schoolClass', 'teachers']);

        // Filters
        if ($request->filled('code')) {
            $query->where('code', $request->code);
        }
        if ($request->filled('subject_name')) {
            $query->where('name', $request->subject_name);
        }
        if ($request->filled('level')) {
            $query->whereHas('classArms.schoolClass', function ($q) use ($request) {
                $q->where('level', $request->level);
            });
        }
        if ($request->filled('class')) {
            $query->whereHas('classArms.schoolClass', function ($q) use ($request) {
                $q->where('name', $request->class);
            });
        }
        if ($request->filled('group')) {
            $query->whereHas('classArms.schoolClass', function ($q) use ($request) {
                $q->where('group', $request->group);
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('code', 'like', "%$s%")
                  ->orWhereHas('teachers', function ($tq) use ($s) {
                      $tq->where('name', 'like', "%$s%");
                  })
                  ->orWhereHas('classArms', function ($aq) use ($s) {
                      $aq->whereHas('schoolClass', function ($sq) use ($s) {
                          $sq->where('level', 'like', "%$s%")
                             ->orWhere('name', 'like', "%$s%")
                             ->orWhere('group', 'like', "%$s%");
                      });
                  });
            });
        }

        $subjects = $query->get();

        // Transform subjects to include additional display data
        $subjects = $subjects->map(function ($subject) {
            $firstArm = $subject->classArms->first();
            $subject->level = $firstArm?->schoolClass?->level ?? null;
            $subject->class_name = $firstArm?->schoolClass?->name ?? null;
            $subject->group = $firstArm?->schoolClass?->group ?? null;
            $subject->arm = $firstArm?->name ?? null;
            $firstTeacher = $subject->teachers->first();
            $subject->teacher_name = $firstTeacher?->name;
            $subject->teacher_id = $firstTeacher?->id;
            return $subject;
        });

        $totalSubjects = $subjects->count();
        $coreSubjects = $subjects->where('type', 'Core')->count();
        $electiveSubjects = $subjects->where('type', 'Elective')->count();

        // Filter sources
        $codes = Subject::distinct()->pluck('code');
        $subjectNames = Subject::distinct()->pluck('name');
        $levels = SchoolClass::distinct()->pluck('level');
        $classNames = SchoolClass::distinct()->pluck('name');
        $groups = SchoolClass::whereNotNull('group')->distinct()->pluck('group');
        $classArms = ClassArm::with('schoolClass')->get();
        $arms = ClassArm::distinct()->pluck('name');
        $teachers = User::whereHas('role', function ($q) { $q->where('name', 'Teacher'); })->get();

        return view('admin.subjects.index', compact(
            'subjects', 'totalSubjects', 'coreSubjects', 'electiveSubjects',
            'codes', 'subjectNames', 'levels', 'classNames', 'groups', 'arms', 'classArms', 'teachers'
        ));
    }

    public function subjectsCreate()
    {
        return view('admin.subjects.create');
    }

    public function subjectsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:subjects,code',
            'description' => 'nullable|string',
            'type' => 'nullable|in:Core,Elective',
            // Optional assignment inputs
            'level' => 'nullable|string',
            'class_name' => 'nullable|string',
            'group' => 'nullable|string',
            'arm' => 'nullable|string|max:255',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        // Generate code if not provided
        $code = $validated['code'] ?? null;
        if (!$code) {
            $prefix = isset($validated['level']) && $validated['level']
                ? strtoupper(substr($validated['level'], 0, 1))
                : 'S';
            $last = Subject::where('code', 'like', $prefix . '-%')->orderBy('code', 'desc')->value('code');
            $nextNum = 1;
            if ($last && preg_match('/^' . preg_quote($prefix, '/') . '-(\d{2,})$/', $last, $m)) {
                $nextNum = intval($m[1]) + 1;
            }
            $code = $prefix . '-' . str_pad((string) $nextNum, 2, '0', STR_PAD_LEFT);
        }

        $subject = Subject::create([
            'name' => $validated['name'],
            'code' => $code,
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'] ?? 'Core',
        ]);

        // Optional: attach to class arm with teacher
        if (!empty($validated['level']) && !empty($validated['class_name']) && !empty($validated['arm'])) {
            $schoolClass = SchoolClass::firstOrCreate([
                'level' => $validated['level'],
                'name' => $validated['class_name'],
                'group' => $validated['group'] ?? null,
            ], [
                'status' => 'Active',
            ]);

            $classArm = ClassArm::firstOrCreate([
                'school_class_id' => $schoolClass->id,
                'name' => $validated['arm'],
            ]);

            if (!$subject->classArms()->where('class_arm_id', $classArm->id)->exists()) {
                $subject->classArms()->attach($classArm->id, [
                    'teacher_id' => $validated['teacher_id'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.subjects.index')->with('success', 'Subject created successfully!');
    }

    public function subjectsShow(Subject $subject)
    {
        $subject->load(['classArms.schoolClass', 'classArms.students', 'teachers']);

        $firstArm = $subject->classArms->first();
        $summary = [
            'level' => $firstArm?->schoolClass?->level,
            'class_name' => $firstArm?->schoolClass?->name,
            'group' => $firstArm?->schoolClass?->group,
            'arm' => $firstArm?->name,
            'teacher' => $subject->teachers->first(),
            'enrollment' => $subject->classArms->reduce(function ($carry, $arm) {
                return $carry + ($arm->students?->count() ?? 0);
            }, 0),
            'total_subjects' => Subject::count(),
        ];

        // Lists for modals
        $levels = SchoolClass::distinct()->pluck('level');
        $classNames = SchoolClass::distinct()->pluck('name');
        $groups = SchoolClass::whereNotNull('group')->distinct()->pluck('group');
        $arms = ClassArm::distinct()->pluck('name');
        $teachers = User::whereHas('role', function ($q) { $q->where('name', 'Teacher'); })->get();

        return view('admin.subjects.show', compact('subject', 'summary', 'levels', 'classNames', 'groups', 'arms', 'teachers'));
    }

    public function subjectsEdit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function subjectsUpdate(Request $request, Subject $subject)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string',
            'type' => 'nullable|in:Core,Elective',
            // Optional assignment updates
            'level' => 'nullable|string',
            'class_name' => 'nullable|string',
            'group' => 'nullable|string',
            'arm' => 'nullable|string|max:255',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.subjects.index')
                ->withErrors($validator)
                ->with('openEditId', $subject->id)
                ->withInput();
        }

        $validated = $validator->validated();

        // Update basic subject info
        $subject->name = $validated['name'];
        if (array_key_exists('code', $validated) && $validated['code']) {
            $subject->code = $validated['code'];
        }
        if (array_key_exists('description', $validated)) {
            $subject->description = $validated['description'];
        }
        if (array_key_exists('type', $validated) && $validated['type']) {
            $subject->type = $validated['type'];
        }
        $subject->save();

        // Optional: ensure attachment to class arm and update teacher
        if (!empty($validated['level']) && !empty($validated['class_name']) && !empty($validated['arm'])) {
            $schoolClass = SchoolClass::firstOrCreate([
                'level' => $validated['level'],
                'name' => $validated['class_name'],
                'group' => $validated['group'] ?? null,
            ], [
                'status' => 'Active',
            ]);

            $classArm = ClassArm::firstOrCreate([
                'school_class_id' => $schoolClass->id,
                'name' => $validated['arm'],
            ]);

            if ($subject->classArms()->where('class_arm_id', $classArm->id)->exists()) {
                // Update pivot teacher
                $subject->classArms()->updateExistingPivot($classArm->id, [
                    'teacher_id' => $validated['teacher_id'] ?? null,
                ]);
            } else {
                $subject->classArms()->attach($classArm->id, [
                    'teacher_id' => $validated['teacher_id'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.subjects.index')->with('success', 'Subject updated successfully!');
    }

    public function subjectsDestroy(Subject $subject)
    {
        // Detach from class arms (pivot: class_subject)
        try {
            $subject->classArms()->detach();
        } catch (\Throwable $e) {
            // continue; may not be attached
        }

        // Delete related assessments if cascade is not defined at DB level
        try {
            if (method_exists($subject, 'assessments')) {
                $subject->assessments()->delete();
            }
        } catch (\Throwable $e) {
            // ignore
        }

        // Finally delete the subject
        $subject->delete();

        return redirect()->route('admin.subjects.index')->with('success', 'Subject deleted successfully.');
    }

    public function classArmsCreate()
    {
        $classes = SchoolClass::all();
        $teachers = User::where('role_id', 5)->get();  // Assuming role_id 5 is for teachers
        return view('admin.class-arms.create', compact('classes', 'teachers'));
    }

    public function updateTeacher(Request $request, ClassArm $class_arm)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
        ]);

        $class_arm->class_teacher_id = $validated['teacher_id'];
        $class_arm->save();

        return back()->with('success', 'Class teacher updated successfully.');
    }

    // Classes & Subjects Overview
    public function overview()
    {
        $totalClasses = SchoolClass::count();
        $totalSubjects = Subject::count();
        $totalClassArms = ClassArm::count();
        $activeClasses = SchoolClass::count(); // Assuming all are active

        return view('admin.classes-subjects.overview', compact(
            'totalClasses', 'totalSubjects', 'totalClassArms', 'activeClasses'
        ));
    }

    // Teacher Assignment
    public function teacherAssign(Request $request)
    {
        $query = ClassArm::with(['schoolClass', 'classTeacher']);

        if ($request->filled('level')) {
            $query->whereHas('schoolClass', function ($q) use ($request) {
                $q->where('level', $request->level);
            });
        }
        if ($request->filled('class')) {
            $query->whereHas('schoolClass', function ($q) use ($request) {
                $q->where('name', $request->class);
            });
        }
        if ($request->filled('group')) {
            $query->whereHas('schoolClass', function ($q) use ($request) {
                $q->where('group', $request->group);
            });
        }
        if ($request->filled('arm')) {
            $query->where('name', $request->arm);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('schoolClass', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%$search%")
                       ->orWhere('level', 'like', "%$search%")
                       ->orWhere('group', 'like', "%$search%");
                })->orWhereHas('classTeacher', function ($tq) use ($search) {
                    $tq->where('name', 'like', "%$search%");
                });
            });
        }

        $classArms = $query->orderBy('school_class_id')->orderBy('name')->get();

        $levels = SchoolClass::distinct()->pluck('level');
        $classNames = SchoolClass::distinct()->pluck('name');
        $arms = ClassArm::distinct()->pluck('name');
        $groups = SchoolClass::whereNotNull('group')->distinct()->pluck('group');

        $teachers = User::whereHas('role', function ($q) {
            $q->where('name', 'Teacher');
        })->get();

        return view('admin.teachers.assign', compact('classArms', 'teachers', 'levels', 'classNames', 'arms', 'groups'));
    }

    public function removeTeacher(ClassArm $class_arm)
    {
        $class_arm->class_teacher_id = null;
        $class_arm->save();
        return back()->with('success', 'Teacher removed successfully.');
    }

    public function teacherAssignStore(Request $request)
    {
        $request->validate([
            'class_arm_id' => 'required|exists:class_arms,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $classArm = ClassArm::findOrFail($request->class_arm_id);
        $classArm->update(['class_teacher_id' => $request->teacher_id]);

        return redirect()->route('admin.teachers.assign')->with('success', 'Teacher assigned successfully!');
    }

    public function getClassDetailsByLevel(Request $request)
    {
        $level = $request->query('level');
        if (!$level) {
            return response()->json(['classNames' => [], 'groups' => []]);
        }

        // Predefined class names based on level
        $classNames = [];
        $groups = [];

        switch ($level) {
            case 'Primary':
                $classNames = ['Primary 1', 'Primary 2', 'Primary 3', 'Primary 4', 'Primary 5', 'Primary 6'];
                $groups = [];
                break;
            case 'JSS':
                $classNames = ['JSS 1', 'JSS 2', 'JSS 3'];
                $groups = [];
                break;
            case 'SS':
                $classNames = ['SS 1', 'SS 2', 'SS 3'];
                $groups = ['Science', 'Arts', 'Commercial'];
                break;
        }

        return response()->json(['classNames' => $classNames, 'groups' => $groups]);
    }
}
