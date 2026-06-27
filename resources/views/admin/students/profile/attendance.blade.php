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
                <button class="btn btn-outline-secondary d-flex align-items-center gap-2" onclick="exportAttendance()">
                    <i class="ri-download-line"></i>
                    Export
                </button>
                <button class="btn btn-primary d-flex align-items-center gap-2" onclick="printAttendance()">
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
                @php
                    // Get the first attendance date to determine the month
                    $firstDate = $att->first()->date ?? $att->first()->created_at;
                    $currentMonth = \Carbon\Carbon::parse($firstDate);
                    $attendanceByDate = $att->keyBy(function($item) {
                        return $item->date ? \Carbon\Carbon::parse($item->date)->format('Y-m-d') : null;
                    });
                    
                    // Build calendar for the month
                    $startOfMonth = $currentMonth->copy()->startOfMonth();
                    $endOfMonth = $currentMonth->copy()->endOfMonth();
                    $daysInMonth = $endOfMonth->day;
                    $startDayOfWeek = $startOfMonth->dayOfWeek; // 0 = Sunday, 6 = Saturday
                @endphp
                <div class="table-responsive">
                    <table class="table table-bordered text-center mb-0" style="table-layout: fixed;">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-semibold">Sun</th>
                                <th class="fw-semibold">Mon</th>
                                <th class="fw-semibold">Tue</th>
                                <th class="fw-semibold">Wed</th>
                                <th class="fw-semibold">Thu</th>
                                <th class="fw-semibold">Fri</th>
                                <th class="fw-semibold">Sat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $day = 1;
                                $blankDays = $startDayOfWeek;
                            @endphp
                            @while($day <= $daysInMonth)
                                <tr>
                                    @for($i = 0; $i < 7; $i++)
                                        @if($blankDays > 0 || $day > $daysInMonth)
                                            <td class="bg-light text-secondary p-2">
                                                @if($blankDays > 0)
                                                    @php $blankDays--; @endphp
                                                @endif
                                            </td>
                                        @else
                                            @php
                                                $dateStr = $currentMonth->format('Y-m-') . str_pad($day, 2, '0', STR_PAD_LEFT);
                                                $attendance = $attendanceByDate->get($dateStr);
                                                $status = $attendance->status ?? null;
                                                $isWeekend = in_array($i, [0, 6]);
                                            @endphp
                                            <td class="p-2 fw-bold position-relative @if($isWeekend) bg-light text-secondary @elseif($status == 'Present') bg-success text-white @elseif($status == 'Absent') bg-danger text-white @elseif($status == 'Late') bg-warning text-white @else text-dark @endif" style="height: 50px;">
                                                {{ $day }}
                                                @if($status == 'Present')
                                                    <i class="ri-check-line position-absolute top-0 end-0 me-1 mt-1 small"></i>
                                                @elseif($status == 'Absent')
                                                    <i class="ri-close-line position-absolute top-0 end-0 me-1 mt-1 small"></i>
                                                @elseif($status == 'Late')
                                                    <i class="ri-time-line position-absolute top-0 end-0 me-1 mt-1 small"></i>
                                                @endif
                                            </td>
                                            @php $day++; @endphp
                                        @endif
                                    @endfor
                                </tr>
                            @endwhile
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

@push('scripts')
    <script>
        function printAttendance() {
            window.open('{{ route('admin.students.profile.attendance.pdf', $student->id) }}', '_blank');
        }

        function exportAttendance() {
            const attendanceData = {
                student_name: "{{ $student->full_name ?? '' }}",
                admission_no: "{{ $student->admission_no ?? '' }}",
                class: "{{ trim((data_get($student, 'classArm.schoolClass.name') ?: '') . ' ' . (data_get($student, 'classArm.name') ?: '')) }}",
                academic_year: "{{ data_get($student, 'academicSession.name', '') }}",
                total_days: "{{ $att->count() }}",
                present_days: "{{ $att->where('status', 'Present')->count() }}",
                absent_days: "{{ $att->where('status', 'Absent')->count() }}",
                late_days: "{{ $att->where('status', 'Late')->count() }}",
                attendance_rate: "{{ $attendanceRate !== null ? $attendanceRate.'%' : 'N/A' }}"
            };

            const records = [];
            @foreach($att as $rec)
                records.push({
                    date: "{{ ($rec->date ?? $rec->created_at) ? \Carbon\Carbon::parse($rec->date ?? $rec->created_at)->format('jS M Y') : '—' }}",
                    day: "{{ ($rec->date ?? $rec->created_at) ? \Carbon\Carbon::parse($rec->date ?? $rec->created_at)->format('l') : '—' }}",
                    check_in: "{{ $rec->check_in ?? '—' }}",
                    check_out: "{{ $rec->check_out ?? '—' }}",
                    status: "{{ ucfirst($rec->status ?? '—') }}",
                    remarks: "{{ $rec->remarks ?? '—' }}"
                });
            @endforeach

            let csvContent = '"FIELD","VALUE"\n';
            Object.entries(attendanceData).forEach(([key, value]) => {
                csvContent += `"${key.replace(/_/g, ' ').toUpperCase()}","${value}"\n`;
            });

            csvContent += '\n"DATE","DAY","CHECK IN","CHECK OUT","STATUS","REMARKS"\n';
            records.forEach(r => {
                csvContent += `"${r.date}","${r.day}","${r.check_in}","${r.check_out}","${r.status}","${r.remarks}"\n`;
            });

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `attendance_${attendanceData.admission_no}_report.csv`;
            link.click();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Export Successful',
                    text: 'Attendance report has been exported to CSV file.',
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                alert('Attendance report exported successfully!');
            }
        }
    </script>
@endpush
