@extends('layouts.teacher')

@section('title', 'Attendance')
@section('page-title', 'Mark Attendance')

@section('content')
<!-- Class Teacher Classes -->
@if($myClassArms->count() > 0)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold"><i class="ri-user-star-line me-2 text-primary"></i>My Classes (Class Teacher)</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($myClassArms as $arm)
                <div class="col-md-6 col-lg-4">
                    <div class="card border shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; background: rgba(99, 102, 241, 0.1);">
                                <i class="ri-building-line text-primary" style="font-size: 24px;"></i>
                            </div>
                            <h6 class="fw-bold mb-1">{{ $arm->schoolClass->level ?? '' }} {{ $arm->schoolClass->name ?? '' }} {{ $arm->name }}</h6>
                            <p class="text-muted small mb-3">{{ $arm->students->count() }} students</p>
                            <a href="{{ route('teacher.attendance.mark', $arm->id) }}" class="btn btn-primary btn-sm px-4">
                                <i class="ri-checkbox-multiple-line me-1"></i>Mark Attendance
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Subject Classes -->
@if($subjectClassArms->count() > 0)
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold"><i class="ri-book-open-line me-2 text-success"></i>Subject Classes</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($subjectClassArms as $arm)
                <div class="col-md-6 col-lg-4">
                    <div class="card border shadow-sm h-100">
                        <div class="card-body text-center py-4">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; background: rgba(16, 185, 129, 0.1);">
                                <i class="ri-book-2-line text-success" style="font-size: 24px;"></i>
                            </div>
                            <h6 class="fw-bold mb-1">{{ $arm->schoolClass->level ?? '' }} {{ $arm->schoolClass->name ?? '' }} {{ $arm->name }}</h6>
                            <p class="text-muted small mb-3">{{ $arm->students->count() }} students</p>
                            <a href="{{ route('teacher.attendance.mark', $arm->id) }}" class="btn btn-outline-success btn-sm px-4">
                                <i class="ri-checkbox-multiple-line me-1"></i>Mark Attendance
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@if($myClassArms->count() === 0 && $subjectClassArms->count() === 0)
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="ri-checkbox-multiple-line text-muted" style="font-size: 64px;"></i>
        <h5 class="text-muted mt-3">No Classes Available</h5>
        <p class="text-muted">You have no classes assigned for attendance marking.</p>
    </div>
</div>
@endif
@endsection
