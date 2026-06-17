@extends('layouts.admin')

@section('title', 'Attendance History')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Attendance History</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.index') }}" class="text-muted">Academic Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.attendance.index') }}" class="text-muted">Attendance</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">History</li>
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
                <h5 class="fw-semibold mb-0">Attendance History</h5>
                <a href="{{ route('admin.academic-management.attendance.index') }}" class="btn btn-outline-secondary btn-sm w-100 w-md-auto">Back to Classes</a>
            </div>

            @php
                $classArm = \App\Models\ClassArm::with('schoolClass')->findOrFail($classId);
            @endphp

            <div class="row mb-4">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Class:</strong> {{ $classArm->schoolClass->name }} {{ $classArm->name }}</p>
                </div>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('admin.academic-management.attendance.history', $classId) }}" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.academic-management.attendance.history', $classId) }}" class="btn btn-outline-secondary">Reset</a>
                        <button type="button" class="btn btn-success" onclick="window.print()">Print</button>
                    </div>
                </div>
            </form>

            @php
                $query = \App\Models\Attendance::where('class_arm_id', $classId)
                    ->with('student.user')
                    ->orderBy('date', 'desc');

                if (request('from_date')) {
                    $query->where('date', '>=', request('from_date'));
                }
                if (request('to_date')) {
                    $query->where('date', '<=', request('to_date'));
                }

                $attendances = $query->paginate(30);
            @endphp

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Marked By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}</td>
                                <td>{{ $attendance->student->full_name ?? $attendance->student->user->name ?? '-' }}</td>
                                <td>
                                    @php
                                        $badgeClass = match(strtolower($attendance->status)) {
                                            'present' => 'bg-success',
                                            'absent' => 'bg-danger',
                                            'late' => 'bg-warning',
                                            'excused' => 'bg-info',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $attendance->status }}</span>
                                </td>
                                <td>{{ $attendance->remarks ?? '-' }}</td>
                                <td>{{ $attendance->markedBy->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No attendance records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
