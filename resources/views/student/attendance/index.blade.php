@extends('layouts.student')

@section('title', 'My Attendance')
@section('page-title', 'Attendance Record')

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item active text-muted">Attendance</li>
    </ol>
</nav>

<!-- Month Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('student.attendance.index') }}" class="row align-items-end g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Select Month</label>
                <input type="month" class="form-control" name="month" value="{{ $selectedMonth }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ri-filter-line me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Statistics -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:50px;height:50px;background:rgba(102,126,234,0.1);">
                    <i class="ri-calendar-line text-primary" style="font-size:24px;"></i>
                </div>
                <h4 class="mb-0">{{ $stats['total'] }}</h4>
                <small class="text-muted">Total Days</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:50px;height:50px;background:rgba(40,167,69,0.1);">
                    <i class="ri-check-line text-success" style="font-size:24px;"></i>
                </div>
                <h4 class="mb-0 text-success">{{ $stats['present'] }}</h4>
                <small class="text-muted">Present</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:50px;height:50px;background:rgba(220,53,69,0.1);">
                    <i class="ri-close-line text-danger" style="font-size:24px;"></i>
                </div>
                <h4 class="mb-0 text-danger">{{ $stats['absent'] }}</h4>
                <small class="text-muted">Absent</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:50px;height:50px;background:rgba(255,193,7,0.1);">
                    <i class="ri-time-line text-warning" style="font-size:24px;"></i>
                </div>
                <h4 class="mb-0 text-warning">{{ $stats['late'] }}</h4>
                <small class="text-muted">Late</small>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Rate Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold">Attendance Rate</span>
            <span class="fw-bold {{ $stats['rate'] >= 75 ? 'text-success' : ($stats['rate'] >= 50 ? 'text-warning' : 'text-danger') }}">{{ $stats['rate'] }}%</span>
        </div>
        <div class="progress" style="height: 10px;">
            @php $rateColor = $stats['rate'] >= 75 ? 'success' : ($stats['rate'] >= 50 ? 'warning' : 'danger'); @endphp
            <div class="progress-bar bg-{{ $rateColor }}" style="width: {{ $stats['rate'] }}%"></div>
        </div>
        @if($stats['rate'] < 75)
            <small class="text-danger mt-1 d-block">
                <i class="ri-error-warning-line me-1"></i>Your attendance is below the recommended 75%. Please improve your attendance.
            </small>
        @else
            <small class="text-success mt-1 d-block">
                <i class="ri-checkbox-circle-line me-1"></i>Great job! Keep up the good attendance.
            </small>
        @endif
    </div>
</div>

<!-- Attendance Records -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold">
            <i class="ri-list-check me-2"></i>Daily Records - {{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}
        </h6>
    </div>
    <div class="card-body p-0">
        @if($attendanceRecords->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th class="text-center">Status</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendanceRecords as $record)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($record->date)->format('l') }}</td>
                                <td class="text-center">
                                    @if($record->status === 'present')
                                        <span class="badge bg-success"><i class="ri-check-line me-1"></i>Present</span>
                                    @elseif($record->status === 'absent')
                                        <span class="badge bg-danger"><i class="ri-close-line me-1"></i>Absent</span>
                                    @elseif($record->status === 'late')
                                        <span class="badge bg-warning text-dark"><i class="ri-time-line me-1"></i>Late</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($record->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $record->remark ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="ri-calendar-line text-muted" style="font-size: 48px;"></i>
                <p class="mt-2 text-muted mb-0">No attendance records for this month.</p>
            </div>
        @endif
    </div>
</div>
@endsection
