@extends('layouts.admin')

@section('title', 'Take Attendance')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Take Attendance</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.index') }}" class="text-muted">Academic Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.attendance.index') }}" class="text-muted">Attendance</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Take Attendance</li>
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
                <h5 class="fw-semibold mb-0">Class Attendance</h5>
                <a href="{{ route('admin.academic-management.attendance.index') }}" class="btn btn-outline-secondary btn-sm w-100 w-md-auto">Back to Classes</a>
            </div>

            @php
                $classArm = \App\Models\ClassArm::with(['schoolClass', 'students.user'])->findOrFail($classId);
                $today = \Carbon\Carbon::today();
            @endphp

            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <p class="mb-1"><strong>Class:</strong> {{ $classArm->schoolClass->name }} {{ $classArm->name }}</p>
                    <p class="mb-1"><strong>Date:</strong> {{ $today->format('M d, Y') }}</p>
                </div>
                <div class="col-12 col-md-6 text-md-end">
                    <p class="mb-1"><strong>Total Students:</strong> {{ $classArm->students->count() }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.academic-management.attendance.store') }}">
                @csrf
                <input type="hidden" name="class_arm_id" value="{{ $classId }}">
                <input type="hidden" name="date" value="{{ $today->format('Y-m-d') }}">

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Admission No</th>
                                <th>Attendance Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classArm->students as $student)
                                @php
                                    $existingAttendance = \App\Models\Attendance::where('student_id', $student->id)
                                        ->where('date', $today)
                                        ->first();
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $student->full_name ?? $student->user->name ?? '-' }}</td>
                                    <td>{{ $student->admission_no ?? '-' }}</td>
                                    <td>
                                        <select name="attendance[{{ $student->id }}][status]" class="form-select form-select-sm" required>
                                            <option value="Present" {{ $existingAttendance && $existingAttendance->status === 'Present' ? 'selected' : '' }}>Present</option>
                                            <option value="Absent" {{ $existingAttendance && $existingAttendance->status === 'Absent' ? 'selected' : '' }}>Absent</option>
                                            <option value="Late" {{ $existingAttendance && $existingAttendance->status === 'Late' ? 'selected' : '' }}>Late</option>
                                            <option value="Excused" {{ $existingAttendance && $existingAttendance->status === 'Excused' ? 'selected' : '' }}>Excused</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="attendance[{{ $student->id }}][remarks]" class="form-control form-control-sm" value="{{ $existingAttendance->remarks ?? '' }}" placeholder="Optional">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">No students found in this class</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex flex-column flex-md-row gap-2">
                    <button type="submit" class="btn btn-primary w-100 w-md-auto">Save Attendance</button>
                    <a href="{{ route('admin.academic-management.attendance.history', $classId) }}" class="btn btn-outline-secondary w-100 w-md-auto">View History</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
