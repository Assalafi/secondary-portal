@extends('layouts.admin')

@section('title', 'Create Schedule')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Create Schedule</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.index') }}" class="text-muted">Academic Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.test-exam-schedule.index') }}" class="text-muted">Test/Exam Schedule</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.test-exam-schedule.class', $classId) }}" class="text-muted">Class Schedule</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Create</li>
            </ol>
        </nav>
    </div>

    @php
        $classArm = \App\Models\ClassArm::with(['schoolClass', 'subjects'])->findOrFail($classId);
        $currentSession = \App\Models\AcademicSession::where('is_current', true)->first();
        $currentTerm = \App\Models\Term::first();
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
                <h5 class="fw-semibold mb-0">Create New Schedule</h5>
                <a href="{{ route('admin.academic-management.test-exam-schedule.class', $classId) }}" class="btn btn-outline-secondary btn-sm w-100 w-md-auto">Back</a>
            </div>

            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <p class="mb-1"><strong>Class:</strong> {{ $classArm->schoolClass->name }} {{ $classArm->name }}</p>
                    <p class="mb-1"><strong>Session:</strong> {{ $currentSession->name ?? 'Not set' }}</p>
                </div>
                <div class="col-12 col-md-6">
                    <p class="mb-1"><strong>Term:</strong> {{ $currentTerm->name ?? 'Not set' }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.academic-management.test-exam-schedule.store', $classId) }}">
                @csrf

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Subject *</label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">Select Subject</option>
                            @foreach($classArm->subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Assessment Type *</label>
                        <select name="assessment_type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="First_CA">First CA</option>
                            <option value="Second_CA">Second CA</option>
                            <option value="Third_CA">Third CA</option>
                            <option value="Exam">Exam</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Scheduled Date *</label>
                        <input type="date" name="scheduled_date" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Scheduled Time *</label>
                        <input type="time" name="scheduled_time" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-select" required>
                            <option value="Pending">Pending</option>
                            <option value="Scheduled">Scheduled</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-12 d-flex flex-column flex-md-row gap-2">
                        <button type="submit" class="btn btn-primary w-100 w-md-auto">Create Schedule</button>
                        <a href="{{ route('admin.academic-management.test-exam-schedule.class', $classId) }}" class="btn btn-outline-secondary w-100 w-md-auto">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
