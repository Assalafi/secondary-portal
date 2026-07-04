@extends('layouts.parent')

@section('title', 'Weekly Timetable')
@section('page-title', 'Weekly Timetable')

@push('styles')
<style>
    .timetable-card {
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }
    .timetable-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }
    .day-monday .timetable-card { border-left-color: #6366f1; }
    .day-tuesday .timetable-card { border-left-color: #10b981; }
    .day-wednesday .timetable-card { border-left-color: #f59e0b; }
    .day-thursday .timetable-card { border-left-color: #ef4444; }
    .day-friday .timetable-card { border-left-color: #8b5cf6; }
    .day-header {
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.dependents.index') }}" class="text-decoration-none">My Dependents</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.dependents.show', $student->id) }}" class="text-decoration-none">{{ $student->user->name }}</a></li>
                <li class="breadcrumb-item active text-muted">Weekly Timetable</li>
            </ol>
        </nav>

        <!-- Student Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3">
                    @if($student->user->photo_path)
                        <img src="{{ asset('storage/' . $student->user->photo_path) }}" 
                             alt="{{ $student->user->name }}"
                             class="rounded-circle" 
                             style="width: 48px; height: 48px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                             style="width: 48px; height: 48px;">
                            <i class="ri-user-line text-white" style="font-size: 24px;"></i>
                        </div>
                    @endif
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $student->user->name }}</h6>
                        <small class="text-muted">{{ optional(optional($student->classArm)->schoolClass)->name ?? 'N/A' }} {{ optional($student->classArm)->name ?? '' }}</small>
                    </div>
                </div>
            </div>
        </div>

        @php
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            $dayColors = [
                'Monday' => 'primary',
                'Tuesday' => 'success',
                'Wednesday' => 'warning',
                'Thursday' => 'danger',
                'Friday' => 'purple'
            ];
        @endphp

        @if(collect($timetable)->flatten()->count() > 0)
            <div class="row">
                @foreach($days as $day)
                    <div class="col-12 mb-4 day-{{ strtolower($day) }}">
                        <div class="d-flex align-items-center mb-3">
                            <span class="day-header text-{{ $dayColors[$day] ?? 'primary' }}">
                                <i class="ri-calendar-line me-1"></i>{{ $day }}
                            </span>
                            @if(now()->format('l') === $day)
                                <span class="badge bg-primary ms-2">Today</span>
                            @endif
                            <span class="badge bg-light text-dark ms-2">{{ isset($timetable[$day]) ? $timetable[$day]->count() : 0 }} periods</span>
                        </div>

                        @if(isset($timetable[$day]) && $timetable[$day]->count() > 0)
                            <div class="row g-3">
                                @foreach($timetable[$day] as $slot)
                                    <div class="col-lg-4 col-md-6">
                                        <div class="card border-0 shadow-sm timetable-card h-100">
                                            <div class="card-body py-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="fw-bold mb-0">{{ $slot->subject->name ?? 'N/A' }}</h6>
                                                    <span class="badge bg-{{ $dayColors[$day] ?? 'primary' }} bg-opacity-10 text-{{ $dayColors[$day] ?? 'primary' }} small">
                                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                                    </span>
                                                </div>
                                                <p class="text-muted small mb-2">
                                                    <i class="ri-building-line me-1"></i>{{ $slot->classArm->schoolClass->name ?? '' }} {{ $slot->classArm->name ?? '' }}
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="ri-time-line me-1"></i>{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                    </small>
                                                    @if($slot->room)
                                                        <small class="text-muted">
                                                            <i class="ri-map-pin-line me-1"></i>{{ $slot->room }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="card border-0 shadow-sm">
                                <div class="card-body py-3 text-center">
                                    <small class="text-muted">No classes scheduled</small>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="ri-calendar-line text-muted" style="font-size: 64px;"></i>
                    <h5 class="text-muted mt-3">No Timetable Available</h5>
                    <p class="text-muted">The timetable for this class has not been set up yet. Please contact the school administration.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
