@extends('layouts.admin')

@section('title', 'Class Results')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Class Results</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.index') }}" class="text-muted">Academic Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.results.index') }}" class="text-muted">Results & Grades</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Class Results</li>
            </ol>
        </nav>
    </div>

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

            @if(session('bulk_report_warnings'))
                <div class="alert alert-warning">
                    <strong>Some students were skipped:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach(session('bulk_report_warnings') as $warning)
                            <li>{{ $warning }}</li>
                        @endforeach
                    </ul>
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
                <h5 class="fw-semibold mb-0">{{ $classArm->schoolClass->name }} {{ $classArm->name }} - Results</h5>
                <a href="{{ route('admin.academic-management.results.index') }}" class="btn btn-outline-secondary btn-sm w-100 w-md-auto">Back</a>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('admin.academic-management.results.class', $classId) }}" class="row g-3 mb-4">
                <div class="col-12 col-md-3">
                    <label class="form-label">Session</label>
                    <select name="session" class="form-select">
                        <option value="">All Sessions</option>
                        @foreach(\App\Models\AcademicSession::all() as $session)
                            <option value="{{ $session->id }}" {{ request('session') == $session->id ? 'selected' : '' }}>{{ $session->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Term</label>
                    <select name="term" class="form-select">
                        <option value="">All Terms</option>
                        @foreach(\App\Models\Term::all() as $term)
                            <option value="{{ $term->id }}" {{ request('term') == $term->id ? 'selected' : '' }}>{{ $term->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.academic-management.results.class', $classId) }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.academic-management.results.bulk-generate', $classId) }}" class="card border bg-light mb-4">
                @csrf
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-end gap-3">
                        <div class="flex-grow-1">
                            <label class="form-label fw-semibold">Bulk report-card generation</label>
                            <p class="text-muted small mb-0">Generate or refresh report cards for every student with scores in this class.</p>
                        </div>
                        <input type="hidden" name="session_id" value="{{ $sessionId }}">
                        <input type="hidden" name="term_id" value="{{ $termId }}">
                        <div>
                            <label class="form-label">Report type</label>
                            <select name="report_type" class="form-select">
                                <option value="termly">Termly</option>
                                <option value="annual">Annual</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success" onclick="return confirm('Generate report cards for all eligible students in this class?')">
                            <i class="ri-file-add-line me-1"></i>Generate for Class
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Admission No</th>
                            <th>Total Score</th>
                            <th>Average</th>
                            <th>Grade</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classArm->students as $student)
                            @php
                                $result = $studentResults[$student->id] ?? null;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $student->full_name ?? $student->user->name ?? '-' }}</td>
                                <td>{{ $student->admission_no ?? '-' }}</td>
                                <td>{{ $result['total_score'] ?? '-' }}</td>
                                <td>{{ $result['average_score'] ? number_format($result['average_score'], 1) : '-' }}</td>
                                <td>{{ $result['final_grade'] ?? '-' }}</td>
                                <td>{{ $result['position'] ?? '-' }}</td>
                                <td>
                                    @if($result && $result['subject_count'] > 0)
                                        <span class="badge bg-success">{{ $result['status'] }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $result['status'] ?? 'Not Available' }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        @if($result && $result['subject_count'] > 0)
                                            <a href="{{ route('admin.academic-management.results.student', [$classId, $student->id]) }}" class="btn btn-outline-primary">View Details</a>
                                        @else
                                            <button class="btn btn-outline-secondary" disabled>No Results</button>
                                        @endif
                                        <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            Generate Report
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <form action="{{ route('admin.academic-management.results.generate-termly-card', [$classId, $student->id]) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="session_id" value="{{ request('session') ?? ($currentSession->id ?? '') }}">
                                                    <input type="hidden" name="term_id" value="{{ request('term') ?? ($currentTerm->id ?? '') }}">
                                                    <button type="submit" class="dropdown-item">Termly Report Card</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.academic-management.results.generate-annual-card', [$classId, $student->id]) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="session_id" value="{{ request('session') ?? ($currentSession->id ?? '') }}">
                                                    <button type="submit" class="dropdown-item">Annual Report Card</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">No students found in this class</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
