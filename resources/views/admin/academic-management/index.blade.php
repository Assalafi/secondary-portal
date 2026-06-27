@extends('layouts.admin')

@section('title', 'Academic Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Academic Management</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Academic Management</li>
            </ol>
        </nav>
    </div>

    @push('styles')
    <style>
        .quick-card {
            background-color: #f8f9fc;
            border: 1px solid #edf0f5;
            border-radius: 16px;
            padding: 24px 20px;
            transition: background-color .2s ease, transform .2s ease, box-shadow .2s ease;
            height: 100%;
        }
        .quick-card:hover {
            background-color: #f5f7fb;
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(17, 24, 39, 0.1);
        }
        .icon-badge {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid #eceff3;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #111827;
        }
        .icon-badge.attendance { color: #10b981; border-color: #d1fae5; background: #ecfdf5; }
        .icon-badge.assignment { color: #3b82f6; border-color: #dbeafe; background: #eff6ff; }
        .icon-badge.timetable { color: #06b6d4; border-color: #cffafe; background: #ecfeff; }
        .icon-badge.schedule { color: #f59e0b; border-color: #fef3c7; background: #fffbeb; }
        .icon-badge.score { color: #8b5cf6; border-color: #ede9fe; background: #f5f3ff; }
        .icon-badge.result { color: #ec4899; border-color: #fce7f3; background: #fdf2f8; }
    </style>
    @endpush

    <!-- Module Cards -->
    <div class="row g-4">
        <!-- Attendance -->
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('admin.academic-management.attendance.index') }}" class="text-decoration-none">
                <div class="quick-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-badge attendance">
                            <i class="ri-calendar-check-line"></i>
                        </div>
                        <i class="ri-arrow-right-s-line text-muted fs-5"></i>
                    </div>
                    <h6 class="mb-1 text-dark fw-semibold fs-18">Attendance</h6>
                    <p class="text-muted mb-0 small">Manage daily student attendance records</p>
                </div>
            </a>
        </div>

        <!-- Assignment -->
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('admin.academic-management.assignments.index') }}" class="text-decoration-none">
                <div class="quick-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-badge assignment">
                            <i class="ri-file-list-line"></i>
                        </div>
                        <i class="ri-arrow-right-s-line text-muted fs-5"></i>
                    </div>
                    <h6 class="mb-1 text-dark fw-semibold fs-18">Assignment</h6>
                    <p class="text-muted mb-0 small">Create and manage student assignments</p>
                </div>
            </a>
        </div>

        <!-- Timetable -->
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('admin.academic-management.timetables.index') }}" class="text-decoration-none">
                <div class="quick-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-badge timetable">
                            <i class="ri-calendar-schedule-line"></i>
                        </div>
                        <i class="ri-arrow-right-s-line text-muted fs-5"></i>
                    </div>
                    <h6 class="mb-1 text-dark fw-semibold fs-18">Timetable</h6>
                    <p class="text-muted mb-0 small">Manage class timetables and schedules</p>
                </div>
            </a>
        </div>

        <!-- Test/Exam Schedule -->
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('admin.academic-management.test-exam-schedule.index') }}" class="text-decoration-none">
                <div class="quick-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-badge schedule">
                            <i class="ri-calendar-event-line"></i>
                        </div>
                        <i class="ri-arrow-right-s-line text-muted fs-5"></i>
                    </div>
                    <h6 class="mb-1 text-dark fw-semibold fs-18">Test/Exam Schedule</h6>
                    <p class="text-muted mb-0 small">Schedule tests and exams for classes</p>
                </div>
            </a>
        </div>

        <!-- Score Upload -->
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('admin.academic-management.score-upload.index') }}" class="text-decoration-none">
                <div class="quick-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-badge score">
                            <i class="ri-edit-circle-line"></i>
                        </div>
                        <i class="ri-arrow-right-s-line text-muted fs-5"></i>
                    </div>
                    <h6 class="mb-1 text-dark fw-semibold fs-18">Score Upload</h6>
                    <p class="text-muted mb-0 small">Upload and manage student scores</p>
                </div>
            </a>
        </div>

        <!-- Result & Grades -->
        <div class="col-lg-4 col-md-6">
            <a href="{{ route('admin.academic-management.results.index') }}" class="text-decoration-none">
                <div class="quick-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="icon-badge result">
                            <i class="ri-bar-chart-grouped-line"></i>
                        </div>
                        <i class="ri-arrow-right-s-line text-muted fs-5"></i>
                    </div>
                    <h6 class="mb-1 text-dark fw-semibold fs-18">Result & Grades</h6>
                    <p class="text-muted mb-0 small">View and publish student results</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
