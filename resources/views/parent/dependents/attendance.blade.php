@extends('layouts.parent')

@section('title', 'Attendance')
@section('page-title', 'Attendance')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.dependents.index') }}" class="text-decoration-none">My Dependents</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.dependents.show', $student->id) }}" class="text-decoration-none">{{ $student->user->name }}</a></li>
                <li class="breadcrumb-item active text-muted">Attendance</li>
            </ol>
        </nav>

        <!-- Filters -->
        <form method="GET" action="{{ route('parent.dependents.attendance', $student->id) }}" id="filterForm">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Session:</label>
                    <select class="form-select" name="session" id="sessionFilter" onchange="document.getElementById('filterForm').submit()">
                        <option value="">All Sessions</option>
                        <option value="2024/2025" {{ request('session') == '2024/2025' ? 'selected' : '' }}>2024/2025</option>
                        <option value="2023/2024" {{ request('session') == '2023/2024' ? 'selected' : '' }}>2023/2024</option>
                        <option value="2022/2023" {{ request('session') == '2022/2023' ? 'selected' : '' }}>2022/2023</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Term:</label>
                    <select class="form-select" name="term" id="termFilter" onchange="document.getElementById('filterForm').submit()">
                        <option value="">All Terms</option>
                        <option value="1st term" {{ request('term') == '1st term' ? 'selected' : '' }}>1st term</option>
                        <option value="2nd term" {{ request('term') == '2nd term' ? 'selected' : '' }}>2nd term</option>
                        <option value="3rd term" {{ request('term') == '3rd term' ? 'selected' : '' }}>3rd term</option>
                    </select>
                </div>
            </div>
        </form>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        @php
                            $totalDays = ($attendanceStats['present'] ?? 0) + ($attendanceStats['absent'] ?? 0) + ($attendanceStats['late'] ?? 0);
                            $percentage = $totalDays > 0 ? round((($attendanceStats['present'] ?? 0) / $totalDays) * 100) : 0;
                        @endphp
                        <h2 class="mb-1 fw-bold">{{ $percentage }}%<span class="fs-6 text-muted ms-2">Present</span></h2>
                        <p class="text-muted mb-0 small">Overall Performance</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <h2 class="mb-1 fw-bold">{{ $attendanceStats['present'] ?? 0 }}</h2>
                        <p class="text-muted mb-0 small">Total days present</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <h2 class="mb-1 fw-bold">{{ $attendanceStats['absent'] ?? 0 }}</h2>
                        <p class="text-muted mb-0 small">Total days absent</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Overview -->
        <h5 class="mb-3">Attendance Overview</h5>
        
        @if($attendances->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="ri-calendar-line" style="font-size: 48px; color: #ccc;"></i>
                    <h6 class="mt-3">No Attendance Records Found</h6>
                    <p class="text-muted small mb-0">There are no attendance records for this student yet.</p>
                </div>
            </div>
        @else
            <div class="attendance-grid">
                @foreach($attendances as $attendance)
                    @php
                        $date = \Carbon\Carbon::parse($attendance->date);
                        $statusClass = '';
                        $badgeClass = '';
                        $statusLabel = $attendance->status;
                        
                        if (strtolower($attendance->status) == 'present') {
                            $statusClass = 'bg-success bg-opacity-10';
                            $badgeClass = 'bg-success';
                        } elseif (strtolower($attendance->status) == 'absent') {
                            $statusClass = 'bg-danger bg-opacity-10';
                            $badgeClass = 'bg-danger';
                        } elseif (strtolower($attendance->status) == 'late') {
                            $statusClass = 'bg-warning bg-opacity-10';
                            $badgeClass = 'bg-warning';
                        } elseif (strtolower($attendance->status) == 'excused') {
                            $statusClass = 'bg-info bg-opacity-10';
                            $badgeClass = 'bg-info';
                        }
                    @endphp
                    <div class="attendance-day-card {{ $statusClass }}">
                        <div class="date-label">{{ $date->format('d M Y') }}</div>
                        <div class="day-label">{{ strtoupper($date->format('D')) }}</div>
                        <span class="badge {{ $badgeClass }} mt-2">{{ $statusLabel }}</span>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $attendances->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .attendance-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 15px;
    }
    
    .attendance-day-card {
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        border: 1px solid #e0e0e0;
    }
    
    .date-label {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #000;
    }
    
    .day-label {
        font-size: 11px;
        color: #6c757d;
        margin-bottom: 8px;
    }
    
    .badge {
        font-size: 11px;
        padding: 4px 10px;
        font-weight: 500;
    }
</style>
@endpush
@endsection
