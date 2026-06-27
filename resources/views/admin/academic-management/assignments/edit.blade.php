@extends('layouts.admin')

@section('title', 'Edit Assignment')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Edit Assignment</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.index') }}" class="text-muted">Academic Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.assignments.index') }}" class="text-muted">Assignments</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Edit</li>
            </ol>
        </nav>
    </div>

    @php
        $assignment = \App\Models\Assignment::findOrFail($assignmentId);
    @endphp

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.academic-management.assignments.update', $assignmentId) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" value="{{ $assignment->title }}" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Level *</label>
                        <select name="level" id="levelSelect" class="form-select" required onchange="filterClasses()">
                            <option value="">Select Level</option>
                            <option value="Nursery" {{ $assignment->level === 'Nursery' ? 'selected' : '' }}>Nursery</option>
                            <option value="Primary" {{ $assignment->level === 'Primary' ? 'selected' : '' }}>Primary</option>
                            <option value="JSS" {{ $assignment->level === 'JSS' ? 'selected' : '' }}>JSS</option>
                            <option value="SS" {{ $assignment->level === 'SS' ? 'selected' : '' }}>SS</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Class</label>
                        <select name="class_id" id="classSelect" class="form-select" onchange="loadClassArms()">
                            <option value="">Select Class</option>
                            @foreach(\App\Models\SchoolClass::all() as $class)
                                <option value="{{ $class->id }}" data-level="{{ $class->level }}" {{ $assignment->class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Arm</label>
                        <select name="class_arm_id" id="armSelect" class="form-select">
                            <option value="">All Arms</option>
                            @foreach(\App\Models\ClassArm::with('schoolClass')->get() as $arm)
                                <option value="{{ $arm->id }}" data-class-id="{{ $arm->school_class_id }}" {{ $assignment->class_arm_id == $arm->id ? 'selected' : '' }} style="display: none;">{{ $arm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Subject *</label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">Select Subject</option>
                            @foreach(\App\Models\Subject::all() as $subject)
                                <option value="{{ $subject->id }}" {{ $assignment->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Due Date *</label>
                        <input type="date" name="due_date" class="form-control" value="{{ \Carbon\Carbon::parse($assignment->due_date)->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Teacher</label>
                        <select name="teacher_id" class="form-select">
                            <option value="">Select Teacher</option>
                            @foreach(\App\Models\User::whereHas('role', function($q) { $q->where('name', 'Teacher'); })->get() as $teacher)
                                <option value="{{ $teacher->id }}" {{ $assignment->teacher_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Question *</label>
                        <textarea name="question" class="form-control" rows="4" required>{{ $assignment->question }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Instructions</label>
                        <textarea name="instructions" class="form-control" rows="3">{{ $assignment->instructions ?? '' }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Submission Info</label>
                        <textarea name="submission_info" class="form-control" rows="2">{{ $assignment->submission_info ?? '' }}</textarea>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="Draft" {{ $assignment->status === 'Draft' ? 'selected' : '' }}>Draft</option>
                            <option value="Active" {{ $assignment->status === 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Closed" {{ $assignment->status === 'Closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Publish</label>
                        <div class="form-check mt-2">
                            <input type="checkbox" name="publish" value="1" class="form-check-input" id="publishCheck" {{ $assignment->published_at ? 'checked' : '' }}>
                            <label class="form-check-label" for="publishCheck">Publish immediately</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100 w-md-auto">Update Assignment</button>
                        <a href="{{ route('admin.academic-management.assignments.show', $assignmentId) }}" class="btn btn-outline-secondary w-100 w-md-auto">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterClasses() {
    const level = document.getElementById('levelSelect').value;
    const classSelect = document.getElementById('classSelect');
    const options = classSelect.querySelectorAll('option[data-level]');

    if (level === '') {
        // Show all classes
        options.forEach(option => {
            option.style.display = '';
        });
    } else {
        // Filter classes by level
        options.forEach(option => {
            if (option.dataset.level === level) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
    }

    // Reset class selection
    classSelect.value = '';
    // Reset arm selection and hide all arm options
    const armSelect = document.getElementById('armSelect');
    const armOptions = armSelect.querySelectorAll('option[data-class-id]');
    armOptions.forEach(option => {
        option.style.display = 'none';
    });
    armSelect.value = '';
}

function loadClassArms() {
    const classId = document.getElementById('classSelect').value;
    const armSelect = document.getElementById('armSelect');
    const armOptions = armSelect.querySelectorAll('option[data-class-id]');

    if (!classId) {
        // Hide all arm options
        armOptions.forEach(option => {
            option.style.display = 'none';
        });
        armSelect.value = '';
        return;
    }

    // Show only arms for the selected class
    armOptions.forEach(option => {
        if (option.dataset.classId === classId) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    });

    // Reset arm selection
    armSelect.value = '';
}

// Run filter on page load to set initial state
document.addEventListener('DOMContentLoaded', function() {
    filterClasses();
    // If a class is already selected, load its arms
    const classId = document.getElementById('classSelect').value;
    if (classId) {
        loadClassArms();
    }
});
</script>
@endpush
