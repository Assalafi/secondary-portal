<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;

class AssignmentController extends Controller
{
    public function index()
    {
        return view('admin.academic-management.assignments.index');
    }

    public function create()
    {
        return view('admin.academic-management.assignments.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'level' => 'required|in:Nursery,Primary,JSS,SS',
                'class_id' => 'nullable|exists:school_classes,id',
                'subject_id' => 'required|exists:subjects,id',
                'teacher_id' => 'nullable|exists:users,id',
                'due_date' => 'required|date',
                'total_marks' => 'nullable|numeric|min:0',
                'question' => 'required|string',
                'instructions' => 'nullable|string',
                'submission_info' => 'nullable|string',
                'status' => 'required|in:Draft,Active,Closed',
            ]);

            $publish = $request->has('publish');

            $assignment = Assignment::create([
                'title' => $validated['title'],
                'level' => $validated['level'],
                'class_id' => $validated['class_id'] ?? null,
                'subject_id' => $validated['subject_id'],
                'teacher_id' => $validated['teacher_id'] ?? null,
                'due_date' => $validated['due_date'],
                'total_marks' => $validated['total_marks'] ?? 100,
                'question' => $validated['question'],
                'instructions' => $validated['instructions'] ?? null,
                'submission_info' => $validated['submission_info'] ?? null,
                'status' => $validated['status'],
                'published_at' => $publish ? now() : null,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('admin.academic-management.assignments.index')->with('success', 'Assignment created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create assignment: ' . $e->getMessage())->withInput();
        }
    }

    public function show($assignmentId)
    {
        return view('admin.academic-management.assignments.show', compact('assignmentId'));
    }

    public function edit($assignmentId)
    {
        return view('admin.academic-management.assignments.edit', compact('assignmentId'));
    }

    public function update(Request $request, $assignmentId)
    {
        try {
            $assignment = Assignment::findOrFail($assignmentId);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'level' => 'required|in:Nursery,Primary,JSS,SS',
                'class_id' => 'nullable|exists:school_classes,id',
                'subject_id' => 'required|exists:subjects,id',
                'teacher_id' => 'nullable|exists:users,id',
                'due_date' => 'required|date',
                'total_marks' => 'nullable|numeric|min:0',
                'question' => 'required|string',
                'instructions' => 'nullable|string',
                'submission_info' => 'nullable|string',
                'status' => 'required|in:Draft,Active,Closed',
            ]);

            $publish = $request->has('publish');

            $assignment->update([
                'title' => $validated['title'],
                'level' => $validated['level'],
                'class_id' => $validated['class_id'] ?? null,
                'subject_id' => $validated['subject_id'],
                'teacher_id' => $validated['teacher_id'] ?? null,
                'due_date' => $validated['due_date'],
                'total_marks' => $validated['total_marks'] ?? 100,
                'question' => $validated['question'],
                'instructions' => $validated['instructions'] ?? null,
                'submission_info' => $validated['submission_info'] ?? null,
                'status' => $validated['status'],
                'published_at' => $publish ? now() : null,
            ]);

            return redirect()->route('admin.academic-management.assignments.show', $assignmentId)->with('success', 'Assignment updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update assignment: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($assignmentId)
    {
        try {
            $assignment = Assignment::findOrFail($assignmentId);
            $assignment->delete();
            return redirect()->route('admin.academic-management.assignments.index')->with('success', 'Assignment deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete assignment: ' . $e->getMessage());
        }
    }
}
