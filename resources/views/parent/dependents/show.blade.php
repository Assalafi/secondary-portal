@extends('layouts.parent')

@section('title', $student->user->name)
@section('page-title', $student->user->name)

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.dependents.index') }}" class="text-decoration-none">My Dependents</a></li>
                <li class="breadcrumb-item active text-muted">{{ $student->user->name }}</li>
            </ol>
        </nav>

        <!-- Student Info Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    @if($student->user->photo_path)
                        <img src="{{ asset('storage/' . $student->user->photo_path) }}" 
                             alt="{{ $student->user->name }}"
                             class="rounded-circle" 
                             style="width: 60px; height: 60px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px;">
                            <i class="ri-user-line text-white" style="font-size: 30px;"></i>
                        </div>
                    @endif
                    <div>
                        <h5 class="mb-1">{{ $student->user->name }}</h5>
                        <p class="mb-0 text-muted">Secondary Section</p>
                    </div>
                </div>

                <!-- Class and Attendance -->
                <div class="row g-3">
                    <div class="col-6">
                        <p class="mb-1 text-muted small">Class</p>
                        <p class="mb-0 fw-bold">{{ optional(optional($student->classArm)->schoolClass)->name ?? 'N/A' }} {{ optional($student->classArm)->name ?? '' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-1 text-muted small">Attendance</p>
                        <p class="mb-0 fw-bold">91%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Cards -->
        <div class="row g-3">
            <!-- Profile Card -->
            <div class="col-12 col-md-6">
                <a href="{{ route('parent.dependents.profile', $student->id) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm hover-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="rounded-circle bg-dark d-flex align-items-center justify-content-center me-3"
                                 style="width: 50px; height: 50px;">
                                <i class="ri-user-line text-white" style="font-size: 24px;"></i>
                            </div>
                            <h6 class="mb-0 flex-grow-1">Profile</h6>
                            <i class="ri-arrow-right-line" style="font-size: 20px;"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Attendance Card -->
            <div class="col-12 col-md-6">
                <a href="{{ route('parent.dependents.attendance', $student->id) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm hover-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="rounded-circle bg-dark d-flex align-items-center justify-content-center me-3"
                                 style="width: 50px; height: 50px;">
                                <i class="ri-calendar-check-line text-white" style="font-size: 24px;"></i>
                            </div>
                            <h6 class="mb-0 flex-grow-1">Attendance</h6>
                            <i class="ri-arrow-right-line" style="font-size: 20px;"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Assignment Card -->
            <div class="col-12 col-md-6">
                <a href="{{ route('parent.dependents.assignments', $student->id) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm hover-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="rounded-circle bg-dark d-flex align-items-center justify-content-center me-3"
                                 style="width: 50px; height: 50px;">
                                <i class="ri-file-edit-line text-white" style="font-size: 24px;"></i>
                            </div>
                            <h6 class="mb-0 flex-grow-1">Assignment</h6>
                            <i class="ri-arrow-right-line" style="font-size: 20px;"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Test & Exam Schedule Card -->
            <div class="col-12 col-md-6">
                <a href="{{ route('parent.dependents.schedule', $student->id) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm hover-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="rounded-circle bg-dark d-flex align-items-center justify-content-center me-3"
                                 style="width: 50px; height: 50px;">
                                <i class="ri-file-edit-line text-white" style="font-size: 24px;"></i>
                            </div>
                            <h6 class="mb-0 flex-grow-1">Test & Exam Schedule</h6>
                            <i class="ri-arrow-right-line" style="font-size: 20px;"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Timetable Card -->
            <div class="col-12 col-md-6">
                <a href="{{ route('parent.dependents.timetable', $student->id) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm hover-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="rounded-circle bg-dark d-flex align-items-center justify-content-center me-3"
                                 style="width: 50px; height: 50px;">
                                <i class="ri-calendar-line text-white" style="font-size: 24px;"></i>
                            </div>
                            <h6 class="mb-0 flex-grow-1">Weekly Timetable</h6>
                            <i class="ri-arrow-right-line" style="font-size: 20px;"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Result Card -->
            <div class="col-12 col-md-6">
                <a href="{{ route('parent.dependents.results', $student->id) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm hover-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="rounded-circle bg-dark d-flex align-items-center justify-content-center me-3"
                                 style="width: 50px; height: 50px;">
                                <i class="ri-bar-chart-line text-white" style="font-size: 24px;"></i>
                            </div>
                            <h6 class="mb-0 flex-grow-1">Result</h6>
                            <i class="ri-arrow-right-line" style="font-size: 20px;"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Payment Card -->
            <div class="col-12 col-md-6">
                <a href="{{ route('parent.dependents.payments', $student->id) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm hover-card">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="rounded-circle bg-dark d-flex align-items-center justify-content-center me-3"
                                 style="width: 50px; height: 50px;">
                                <i class="ri-wallet-line text-white" style="font-size: 24px;"></i>
                            </div>
                            <h6 class="mb-0 flex-grow-1">Payment</h6>
                            <i class="ri-arrow-right-line" style="font-size: 20px;"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .hover-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
</style>
@endpush
@endsection
