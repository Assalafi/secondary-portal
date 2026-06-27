@extends('layouts.admin')

@section('title', 'Student Profile - Attendance')

@section('content')
<div class="main-content-container overflow-hidden">
    <!-- Student Profile Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary me-3 d-flex align-items-center gap-2">
                    <i class="ri-arrow-left-line"></i>
                    Back to Students
                </a>
                <div>
                    <h3 class="fs-20 fw-semibold mb-1">Student Profile</h3>
                    <p class="text-secondary mb-0">{{ $student->full_name ?? '-' }} - {{ $student->admission_no ?? '-' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="d-flex gap-2 justify-content-md-end">
                <button class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <i class="ri-calendar-check-line"></i>
                    Mark Attendance
                </button>
                <button class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="ri-printer-line"></i>
                    Print Report
                </button>
            </div>
        </div>
    </div>
    
    <!-- Tab Navigation -->
    <div class="card custom-shadow rounded-3 bg-white border mb-4">
        <div class="card-body p-0">
            <ul class="nav nav-tabs border-0 px-4 pt-3" id="studentProfileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium" href="{{ route('admin.students.profile.overview', $student->id) }}">
                        <i class="ri-user-line me-2"></i>Overview
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium" href="{{ route('admin.students.profile.academic', $student->id) }}">
                        <i class="ri-graduation-cap-line me-2"></i>Academic Info
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium" href="{{ route('admin.students.profile.fees', $student->id) }}">
                        <i class="ri-money-dollar-circle-line me-2"></i>Fees & Payments
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link active fw-medium" href="{{ route('admin.students.profile.attendance', $student->id) }}">
                        <i class="ri-calendar-check-line me-2"></i>Attendance
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium" href="{{ route('admin.students.profile.documents', $student->id) }}">
                        <i class="ri-file-text-line me-2"></i>Documents
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Attendance Summary Cards -->
    @php
        $att = isset($student->attendances) ? $student->attendances : collect();
        $presentCount = $att->where('status', 'Present')->count();
        $absentCount = $att->where('status', 'Absent')->count();
        $lateCount = $att->where('status', 'Late')->count();
        $totalCount = $att->count();
        $attendanceRate = $totalCount > 0 ? round(($presentCount / max(1, $totalCount)) * 100, 1) : null;
    @endphp
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 48px; height: 48px;">
                                <i class="ri-check-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">{{ $attendanceRate !== null ? $attendanceRate.'%' : '—' }}</h6>
                            <p class="text-secondary mb-0 small">Attendance Rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 48px; height: 48px;">
                                <i class="ri-calendar-check-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">{{ $presentCount }}</h6>
                            <p class="text-secondary mb-0 small">Days Present</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle" style="width: 48px; height: 48px;">
                                <i class="ri-close-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">{{ $absentCount }}</h6>
                            <p class="text-secondary mb-0 small">Days Absent</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle" style="width: 48px; height: 48px;">
                                <i class="ri-time-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">{{ $lateCount }}</h6>
                            <p class="text-secondary mb-0 small">Late Arrivals</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Calendar -->
    <div class="card custom-shadow rounded-3 bg-white border mb-4">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-semibold mb-0">
                <i class="ri-calendar-line me-2 text-primary"></i>Monthly Attendance
            </h6>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="ri-calendar-line me-1"></i>Select Month
                    </button>
                    <ul class="dropdown-menu">
                        @foreach($sessions ?? [] as $session)
                            <li><a class="dropdown-item" href="#">{{ $session->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <button class="btn btn-sm btn-outline-primary">
                    <i class="ri-download-line me-1"></i>Export
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($att->isEmpty())
                <div class="text-center py-5">
                    <i class="ri-calendar-line text-muted" style="font-size: 48px;"></i>
                    <p class="text-muted mt-3">No attendance records found for this student.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered text-center mb-0" style="table-layout: fixed;">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-semibold">Mon</th>
                                <th class="fw-semibold">Tue</th>
                                <th class="fw-semibold">Wed</th>
                                <th class="fw-semibold">Thu</th>
                                <th class="fw-semibold">Fri</th>
                                <th class="fw-semibold">Sat</th>
                                <th class="fw-semibold">Sun</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Group attendance by date and build calendar
                                $attendanceByDate = $att->keyBy(function($item) {
                                    return $item->date ? \Carbon\Carbon::parse($item->date)->format('Y-m-d') : null;
                                });
                            @endphp
                            <!-- Calendar rows will be dynamically generated when attendance data exists -->
                            <tr>
                                <td colspan="7" class="text-center text-secondary p-4">
                                    Calendar view requires attendance data with dates
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex flex-wrap gap-2">
                    <span class="badge bg-success d-flex align-items-center gap-1">
                        <i class="ri-check-line"></i>Present
                    </span>
                    <span class="badge bg-danger d-flex align-items-center gap-1">
                        <i class="ri-close-line"></i>Absent
                    </span>
                    <span class="badge bg-warning d-flex align-items-center gap-1">
                        <i class="ri-time-line"></i>Late
                    </span>
                    <span class="badge bg-light text-dark d-flex align-items-center gap-1">
                        <i class="ri-calendar-line"></i>Weekend
                    </span>
                    <span class="badge bg-secondary d-flex align-items-center gap-1">
                        <i class="ri-gift-line"></i>Holiday
                    </span>
                </div>
            @endif
        </div>
    </div>

    <!-- Attendance Details -->
    <div class="card custom-shadow rounded-3 bg-white border">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-semibold mb-0">
                <i class="ri-list-check-line me-2 text-primary"></i>Recent Attendance Records
            </h6>
            <button class="btn btn-sm btn-outline-primary">
                <i class="ri-eye-line me-1"></i>View All
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">Date</th>
                            <th class="fw-semibold">Day</th>
                            <th class="fw-semibold">Check In</th>
                            <th class="fw-semibold">Check Out</th>
                            <th class="fw-semibold">Status</th>
                            <th class="fw-semibold">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $recent = isset($student->attendances) ? $student->attendances->sortByDesc(function($a){
                                return $a->date ?? $a->created_at;
                            })->take(10) : collect();
                        @endphp
                        @forelse($recent as $rec)
                            @php
                                $dateVal = $rec->date ?? $rec->created_at;
                                $dateText = $dateVal ? \Carbon\Carbon::parse($dateVal)->format('jS M Y') : '—';
                                $dayText = $dateVal ? \Carbon\Carbon::parse($dateVal)->format('l') : '—';
                                $status = $rec->status ?? '—';
                                $badge = strtolower($status) === 'present' ? 'bg-success' : (strtolower($status) === 'late' ? 'bg-warning' : (strtolower($status) === 'absent' ? 'bg-danger' : 'bg-secondary'));
                            @endphp
                            <tr>
                                <td class="fw-medium">{{ $dateText }}</td>
                                <td>{{ $dayText }}</td>
                                <td>{{ $rec->check_in ?? '—' }}</td>
                                <td>{{ $rec->check_out ?? '—' }}</td>
                                <td><span class="badge {{ $badge }}">{{ ucfirst($status) }}</span></td>
                                <td class="text-secondary">{{ $rec->remarks ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-secondary">No attendance records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
