@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1 small">My Classes</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['total_classes'] }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(99, 102, 241, 0.1);">
                        <i class="ri-building-line text-primary" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1 small">My Subjects</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['total_subjects'] }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(16, 185, 129, 0.1);">
                        <i class="ri-book-open-line text-success" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1 small">Total Students</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['total_students'] }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(245, 158, 11, 0.1);">
                        <i class="ri-group-line text-warning" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1 small">Assignments</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['total_assignments'] }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(239, 68, 68, 0.1);">
                        <i class="ri-task-line text-danger" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Today's Schedule -->
    <div class="col-lg-7 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="ri-calendar-check-line me-2 text-primary"></i>Today's Schedule</h6>
                    <span class="badge bg-light text-dark">{{ now()->format('l, M d') }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($todaySchedule->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($todaySchedule as $slot)
                            <div class="list-group-item border-0 py-3 px-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 text-center" style="min-width: 60px;">
                                        <small class="text-muted d-block">{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}</small>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}</small>
                                    </div>
                                    <div class="vr me-3" style="height: 40px;"></div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-medium">{{ $slot->subject->name ?? 'N/A' }}</h6>
                                        <small class="text-muted">
                                            {{ $slot->classArm->schoolClass->name ?? '' }} {{ $slot->classArm->name ?? '' }}
                                            @if($slot->room) &bull; Room: {{ $slot->room }} @endif
                                        </small>
                                    </div>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ \Carbon\Carbon::parse($slot->start_time)->diffInMinutes(\Carbon\Carbon::parse($slot->end_time)) }} min</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ri-calendar-line text-muted" style="font-size: 48px;"></i>
                        <p class="text-muted mt-2 mb-0">No classes scheduled for today</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions + Upcoming Assignments -->
    <div class="col-lg-5 mb-4">
        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="ri-flashlight-line me-2 text-warning"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('teacher.scores.index') }}" class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center">
                            <i class="ri-file-list-3-line mb-1" style="font-size: 20px;"></i>
                            <small>Upload Scores</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('teacher.assignments.create') }}" class="btn btn-outline-success w-100 py-3 d-flex flex-column align-items-center">
                            <i class="ri-add-circle-line mb-1" style="font-size: 20px;"></i>
                            <small>New Assignment</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('teacher.attendance.index') }}" class="btn btn-outline-warning w-100 py-3 d-flex flex-column align-items-center">
                            <i class="ri-checkbox-multiple-line mb-1" style="font-size: 20px;"></i>
                            <small>Mark Attendance</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('teacher.timetable') }}" class="btn btn-outline-info w-100 py-3 d-flex flex-column align-items-center">
                            <i class="ri-calendar-2-line mb-1" style="font-size: 20px;"></i>
                            <small>View Timetable</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Assignments -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="ri-todo-line me-2 text-danger"></i>Upcoming Assignments</h6>
            </div>
            <div class="card-body p-0">
                @if($upcomingAssignments->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($upcomingAssignments as $assignment)
                            <div class="list-group-item border-0 py-2 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 small fw-medium">{{ $assignment->title }}</h6>
                                        <small class="text-muted">{{ $assignment->subject->name ?? 'N/A' }}</small>
                                    </div>
                                    <span class="badge bg-warning bg-opacity-10 text-warning small">{{ $assignment->due_date->format('M d') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <small class="text-muted">No upcoming assignments</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- My Classes Overview -->
@if($myClassArms->count() > 0)
<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="ri-school-line me-2 text-success"></i>My Classes (Class Teacher)</h6>
                    <a href="{{ route('teacher.my-classes') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4">Class</th>
                                <th class="border-0">Students</th>
                                <th class="border-0 text-end px-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myClassArms as $arm)
                                <tr>
                                    <td class="px-4">
                                        <span class="fw-medium">{{ $arm->schoolClass->level ?? '' }} {{ $arm->schoolClass->name ?? '' }} {{ $arm->name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary">{{ $arm->students->count() }} students</span>
                                    </td>
                                    <td class="text-end px-4">
                                        <a href="{{ route('teacher.attendance.mark', $arm->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Mark Attendance">
                                            <i class="ri-checkbox-line"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
