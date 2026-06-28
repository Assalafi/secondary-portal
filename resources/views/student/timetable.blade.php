@extends('layouts.student')

@section('title', 'My Timetable')
@section('page-title', 'Class Timetable')

@section('content')
@if(!$student || !$student->classArm)
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="ri-calendar-todo-line text-muted" style="font-size: 64px;"></i>
            <h5 class="mt-3 mb-2">No Timetable Available</h5>
            <p class="text-muted">Your class timetable will appear here once it is set up.</p>
        </div>
    </div>
@else
    <!-- Class Info -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="mb-1 fw-bold">{{ $student->classArm->schoolClass->name ?? '' }} {{ $student->classArm->name ?? '' }}</h6>
                    <small class="text-muted">{{ $timetables->count() }} timetable entries</small>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-primary px-3 py-2">{{ $globalSettings['current_term'] }}</span>
                    <span class="badge bg-info px-3 py-2">{{ $globalSettings['academic_session'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Timetable Grid -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="mb-0 fw-bold">
                <i class="ri-calendar-schedule-line me-2 text-primary"></i>Weekly Timetable
            </h6>
        </div>
        <div class="card-body p-0">
            @if($timetables->count() > 0)
                @php
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    $timeSlots = [
                        '08:00', '08:45', '09:30', '10:15', '10:45',
                        '11:30', '12:15', '01:00', '01:45', '02:30', '03:15'
                    ];
                    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                @endphp
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 timetable-table">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 100px;">Time</th>
                                @foreach($days as $day)
                                    <th class="text-center">{{ $day }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timeSlots as $timeSlot)
                                <tr>
                                    <td class="text-center fw-medium small" style="vertical-align: middle; background: #f8f9fa;">
                                        {{ $timeSlot }}
                                    </td>
                                    @foreach($days as $day)
                                        <td class="text-center" style="vertical-align: middle; min-width: 120px;">
                                            @php
                                                $timetable = null;
                                                foreach($timetables as $t) {
                                                    if($t->day === $day && $t->start_time->format('H:i') === $timeSlot) {
                                                        $timetable = $t;
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            @if($timetable)
                                                @php
                                                    $subjectIndex = $timetables->search(function($t) use ($timetable) {
                                                        return $t->id === $timetable->id;
                                                    });
                                                    $color = $colors[$subjectIndex % count($colors)];
                                                @endphp
                                                <div class="timetable-subject bg-{{ $color }} bg-opacity-10 p-2 rounded">
                                                    <small class="fw-bold text-{{ $color }} d-block">{{ $timetable->subject->name }}</small>
                                                    @if($timetable->teacher)
                                                        <small class="text-muted">{{ $timetable->teacher->name }}</small>
                                                    @endif
                                                    @if($timetable->room)
                                                        <small class="text-muted">{{ $timetable->room }}</small>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Legend -->
                <div class="card-footer bg-white border-0">
                    <h6 class="small fw-bold mb-2">Subject Legend</h6>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach($timetables->unique('subject_id') as $index => $timetable)
                            @php
                                $subjectIndex = $timetables->search(function($t) use ($timetable) {
                                    return $t->subject_id === $timetable->subject_id;
                                });
                                $color = $colors[$subjectIndex % count($colors)];
                            @endphp
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{ $color }} me-1">{{ $timetable->subject->code ?? substr($timetable->subject->name, 0, 3) }}</span>
                                <small class="text-muted">{{ $timetable->subject->name }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="ri-calendar-line text-muted" style="font-size: 48px;"></i>
                    <p class="mt-2 text-muted mb-0">No timetable entries for your class yet.</p>
                </div>
            @endif
        </div>
    </div>
@endif

<style>
    .timetable-table th {
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .timetable-table td {
        font-size: 12px;
    }
    .timetable-subject {
        min-height: 60px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transition: transform 0.2s;
    }
    .timetable-subject:hover {
        transform: scale(1.05);
    }
</style>
@endsection
