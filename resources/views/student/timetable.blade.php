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
                    $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                @endphp
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 timetable-table">
                        <thead class="table-light">
                            <tr>
                                @foreach($days as $day)
                                    <th class="text-center">{{ $day }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($days as $day)
                                <tr>
                                    <td style="vertical-align: top; min-width: 160px;">
                                        @php
                                            $dayTimetables = $timetables->where('day', $day)->sortBy('start_time');
                                        @endphp
                                        @if($dayTimetables->count() > 0)
                                            @foreach($dayTimetables as $timetable)
                                                @php
                                                    $subjectIndex = $timetables->search(function($t) use ($timetable) {
                                                        return $t->id === $timetable->id;
                                                    });
                                                    $color = $colors[$subjectIndex % count($colors)];
                                                @endphp
                                                <div class="timetable-subject bg-{{ $color }} bg-opacity-10 p-3 rounded mb-2">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div class="fw-bold text-{{ $color }}">{{ $timetable->subject->name }}</div>
                                                        <div class="text-muted small">
                                                            {{ $timetable->start_time->format('H:i') }} - {{ $timetable->end_time->format('H:i') }}
                                                        </div>
                                                    </div>
                                                    @if($timetable->room)
                                                        <div class="text-muted small">
                                                            <i class="ri-map-pin-line me-1"></i>{{ $timetable->room }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-center text-muted small py-3">No classes</div>
                                        @endif
                                    </td>
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
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 8px;
    }
    .timetable-table td {
        font-size: 13px;
        padding: 8px;
    }
    .timetable-subject {
        min-height: 70px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    .timetable-subject:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .timetable-table tbody tr:hover td {
        background-color: #f8f9fa;
    }
</style>
@endsection
