@extends('layouts.admin')

@section('title', 'Class Subjects')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Class Subjects</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.index') }}" class="text-muted">Academic Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.score-upload.index') }}" class="text-muted">Score Upload</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Class Subjects</li>
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
                <h5 class="fw-semibold mb-0">{{ $classArm->schoolClass->name }} {{ $classArm->name }} - Subjects</h5>
                <a href="{{ route('admin.academic-management.score-upload.index') }}" class="btn btn-outline-secondary btn-sm w-100 w-md-auto">Back</a>
            </div>

            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <p class="mb-1"><strong>Class:</strong> {{ $classArm->schoolClass->name }} {{ $classArm->name }}</p>
                    <p class="mb-1"><strong>Session:</strong> {{ $currentSession->name ?? 'Not set' }}</p>
                </div>
                <div class="col-12 col-md-6">
                    <p class="mb-1"><strong>Term:</strong> {{ $currentTerm->name ?? 'Not set' }}</p>
                    <p class="mb-1"><strong>Total Subjects:</strong> {{ $classArm->subjects->count() }}</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                            <th>Upload Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classArm->subjects as $subject)
                            @php
                                $scoreBatch = \App\Models\ScoreBatch::where('class_id', $classArm->school_class_id)
                                    ->where('subject_id', $subject->id)
                                    ->where('academic_session_id', $currentSession->id ?? null)
                                    ->where('term_id', $currentTerm->id ?? null)
                                    ->first();
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $subject->name }}</td>
                                <td>{{ $subject->teachers->first()?->name ?? '-' }}</td>
                                <td>
                                    @if($scoreBatch)
                                        <span class="badge {{ $scoreBatch->status === 'Published' ? 'bg-success' : ($scoreBatch->status === 'Uploaded' ? 'bg-primary' : 'bg-warning') }}">
                                            {{ $scoreBatch->status }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.academic-management.score-upload.subject', [$classId, $subject->id]) }}" class="btn btn-sm btn-primary">Upload Scores</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No subjects assigned to this class</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
