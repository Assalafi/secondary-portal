<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradingSystem;
use Illuminate\Http\Request;

class GradingSystemController extends Controller
{
    public function index()
    {
        $levels = ['Nursery', 'Primary', 'JSS', 'SS'];
        $gradingSystems = GradingSystem::orderBy('level')->orderBy('min_score')->get()->groupBy('level');
        
        return view('admin.settings.grading-system', compact('levels', 'gradingSystems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'level' => 'required|in:Nursery,Primary,JSS,SS',
            'grade' => 'required|string|max:5',
            'min_score' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:0|max:100|gte:min_score',
            'gpa_point' => 'required|numeric|min:0|max:4|step:0.01',
            'description' => 'nullable|string|max:100',
        ]);

        GradingSystem::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Grading system added successfully'
            ]);
        }

        return back()->with('success', 'Grading system added successfully');
    }

    public function update(Request $request, $id)
    {
        $gradingSystem = GradingSystem::findOrFail($id);

        $validated = $request->validate([
            'level' => 'required|in:Nursery,Primary,JSS,SS',
            'grade' => 'required|string|max:5',
            'min_score' => 'required|numeric|min:0|max:100',
            'max_score' => 'required|numeric|min:0|max:100|gte:min_score',
            'gpa_point' => 'required|numeric|min:0|max:4|step:0.01',
            'description' => 'nullable|string|max:100',
        ]);

        $gradingSystem->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Grading system updated successfully'
            ]);
        }

        return back()->with('success', 'Grading system updated successfully');
    }

    public function destroy($id)
    {
        $gradingSystem = GradingSystem::findOrFail($id);
        $gradingSystem->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Grading system deleted successfully'
            ]);
        }

        return back()->with('success', 'Grading system deleted successfully');
    }
}
