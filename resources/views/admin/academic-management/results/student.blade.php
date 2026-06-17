@extends('layouts.admin')

@section('title', 'Student Result')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Student Result</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.index') }}" class="text-muted">Academic Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.results.index') }}" class="text-muted">Results & Grades</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.results.class', $classId) }}" class="text-muted">Class Results</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Student Result</li>
            </ol>
        </nav>
    </div>

    @php
        $student = \App\Models\Student::with(['user', 'classArm.schoolClass'])->findOrFail($studentId);
        $classArm = \App\Models\ClassArm::with('schoolClass')->findOrFail($classId);
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
                <h5 class="fw-semibold mb-0">Student Result - {{ $student->full_name ?? $student->user->name }}</h5>
                <div class="d-flex flex-column flex-md-row gap-2">
                    <button class="btn btn-outline-secondary btn-sm w-100 w-md-auto" onclick="window.print()">Print Report Card</button>
                    <a href="{{ route('admin.academic-management.results.class', $classId) }}" class="btn btn-outline-secondary btn-sm w-100 w-md-auto">Back</a>
                </div>
            </div>

            @if(count($subjectScores) > 0)
            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <p class="mb-1"><strong>Student:</strong> {{ $student->full_name ?? $student->user->name }}</p>
                    <p class="mb-1"><strong>Admission No:</strong> {{ $student->admission_no ?? '-' }}</p>
                    <p class="mb-1"><strong>Class:</strong> {{ $classArm->schoolClass->name }} {{ $classArm->name }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Total Score:</strong> {{ number_format($totalScore, 2) }}</p>
                    <p class="mb-1"><strong>Average:</strong> {{ number_format($averageScore, 1) }}</p>
                    <p class="mb-1"><strong>Final Grade:</strong> <span class="badge bg-primary">{{ $finalGrade }}</span></p>
                    <p class="mb-1"><strong>Percentage:</strong> {{ number_format($percentage, 1) }}%</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>1st CA</th>
                            <th>2nd CA</th>
                            <th>3rd CA</th>
                            <th>Exam</th>
                            <th>Total</th>
                            <th>Grade</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjectScores as $score)
                            <tr>
                                <td>{{ $score['subject'] }}</td>
                                <td>{{ number_format($score['first_ca'], 2) }}</td>
                                <td>{{ number_format($score['second_ca'], 2) }}</td>
                                <td>{{ number_format($score['third_ca'], 2) }}</td>
                                <td>{{ number_format($score['exam'], 2) }}</td>
                                <td><strong>{{ number_format($score['total'], 2) }}</strong></td>
                                <td><span class="badge bg-secondary">{{ $score['grade'] }}</span></td>
                                <td>{{ $score['remark'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No subject results found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-warning">
                No result found for this student in the selected session/term.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
