@extends('layouts.student')

@section('title', 'Assessment Schedule')
@section('page-title', 'Assessment Schedule')

@section('content')
@if(!$student || !$student->classArm)
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="ri-calendar-event-line text-muted" style="font-size: 64px;"></i>
            <h5 class="mt-3 mb-2">No Assessment Schedule Available</h5>
            <p class="text-muted">Your assessment schedule will appear here once it is set up.</p>
        </div>
    </div>
@else
    <!-- Class Info -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="mb-1 fw-bold">{{ $student->classArm->schoolClass->name ?? '' }} {{ $student->classArm->name ?? '' }}</h6>
                    <small class="text-muted">{{ $schedules->count() }} scheduled assessments</small>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-primary px-3 py-2">{{ $globalSettings['current_term'] }}</span>
                    <span class="badge bg-info px-3 py-2">{{ $globalSettings['academic_session'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Assessment Schedule -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="mb-0 fw-bold">
                <i class="ri-calendar-check-line me-2 text-primary"></i>Upcoming Assessments
            </h6>
        </div>
        <div class="card-body p-0">
            @if($schedules->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Subject</th>
                                <th>Assessment Type</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                                <tr>
                                    <td>
                                        <span class="fw-medium">{{ $schedule->subject->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $schedule->assessment_type }}</span>
                                    </td>
                                    <td>
                                        <span>{{ $schedule->scheduled_date->format('M d, Y') }}</span>
                                    </td>
                                    <td>
                                        <span>{{ $schedule->scheduled_time }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusColor = match($schedule->status) {
                                                'Pending' => 'secondary',
                                                'Scheduled' => 'primary',
                                                'Completed' => 'success',
                                                'Cancelled' => 'danger',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }}">{{ $schedule->status }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="ri-calendar-line text-muted" style="font-size: 48px;"></i>
                    <p class="mt-2 text-muted mb-0">No assessments scheduled for your class yet.</p>
                </div>
            @endif
        </div>
    </div>
@endif
@endsection
