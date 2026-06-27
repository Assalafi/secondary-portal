@extends('layouts.admin')

@section('title', 'Assignment Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Assignment Details</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.index') }}" class="text-muted">Academic Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.assignments.index') }}" class="text-muted">Assignments</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Details</li>
            </ol>
        </nav>
    </div>

    @php
        $assignment = \App\Models\Assignment::with(['subject', 'class', 'classArm', 'teacher', 'createdBy'])->findOrFail($assignmentId);
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

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
                <h5 class="fw-semibold mb-0">{{ $assignment->title }}</h5>
                <div class="d-flex flex-column flex-md-row gap-2">
                    <a href="{{ route('admin.academic-management.assignments.edit', $assignment->id) }}" class="btn btn-outline-secondary btn-sm w-100 w-md-auto">Edit</a>
                    <form method="POST" action="{{ route('admin.academic-management.assignments.destroy', $assignment->id) }}" onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100 w-md-auto">Delete</button>
                    </form>
                    <a href="{{ route('admin.academic-management.assignments.index') }}" class="btn btn-outline-secondary btn-sm w-100 w-md-auto">Back</a>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <p class="mb-1"><strong>Level:</strong> {{ $assignment->level }}</p>
                    <p class="mb-1"><strong>Class:</strong> {{ $assignment->class->name ?? 'All Classes' }}</p>
                    <p class="mb-1"><strong>Arm:</strong> {{ $assignment->classArm->name ?? 'All Arms' }}</p>
                    <p class="mb-1"><strong>Subject:</strong> {{ $assignment->subject->name ?? '-' }}</p>
                    <p class="mb-1"><strong>Teacher:</strong> {{ $assignment->teacher->name ?? '-' }}</p>
                </div>
                <div class="col-12 col-md-6">
                    <p class="mb-1"><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}</p>
                    <p class="mb-1"><strong>Status:</strong> <span class="badge {{ $assignment->status === 'Active' ? 'bg-success' : ($assignment->status === 'Closed' ? 'bg-danger' : 'bg-secondary') }}">{{ $assignment->status }}</span></p>
                    <p class="mb-1"><strong>Created By:</strong> {{ $assignment->createdBy->name ?? '-' }}</p>
                    <p class="mb-1"><strong>Published At:</strong> {{ $assignment->published_at ? \Carbon\Carbon::parse($assignment->published_at)->format('M d, Y H:i') : 'Not published' }}</p>
                </div>
                <div class="col-12">
                    <p class="mb-1"><strong>Question:</strong></p>
                    <div class="bg-light p-3 rounded">{{ $assignment->question }}</div>
                </div>
                @if($assignment->instructions)
                <div class="col-12">
                    <p class="mb-1"><strong>Instructions:</strong></p>
                    <div class="bg-light p-3 rounded">{{ $assignment->instructions }}</div>
                </div>
                @endif
                @if($assignment->submission_info)
                <div class="col-12">
                    <p class="mb-1"><strong>Submission Info:</strong></p>
                    <div class="bg-light p-3 rounded">{{ $assignment->submission_info }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
