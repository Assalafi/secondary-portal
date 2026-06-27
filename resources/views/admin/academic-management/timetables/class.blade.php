@extends('layouts.admin')

@section('title', 'Class Timetable')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Class Timetable</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.index') }}" class="text-muted">Academic Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.timetables.index') }}" class="text-muted">Timetables</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">{{ $classArm->schoolClass->name ?? '' }} {{ $classArm->name ?? '' }}</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-semibold mb-0">{{ $classArm->schoolClass->name ?? '' }} {{ $classArm->name ?? '' }} Timetable</h5>
                <a href="{{ route('admin.academic-management.timetables.create') }}" class="btn btn-primary btn-sm">Add Entry</a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered timetable-table">
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
                                            $timetable = $timetables->firstWhere('day', $day, function($t) use ($timeSlot) {
                                                return $t->start_time->format('H:i') === $timeSlot;
                                            });
                                        @endphp
                                        @if($timetable)
                                            <div class="timetable-subject bg-primary bg-opacity-10 p-2 rounded">
                                                <small class="fw-bold text-primary d-block">{{ $timetable->subject->name }}</small>
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

            @if($timetables->count() === 0)
                <div class="text-center py-5">
                    <i class="ri-calendar-line text-muted" style="font-size: 48px;"></i>
                    <p class="mt-2 text-muted mb-0">No timetable entries for this class yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

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
