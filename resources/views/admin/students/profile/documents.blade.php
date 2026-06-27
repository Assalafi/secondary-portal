@extends('layouts.admin')

@section('title', 'Student Profile - Documents')

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
                    <i class="ri-upload-2-line"></i>
                    Upload Document
                </button>
                <button class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="ri-download-line"></i>
                    Download All
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
                    <a class="nav-link fw-medium" href="{{ route('admin.students.profile.attendance', $student->id) }}">
                        <i class="ri-calendar-check-line me-2"></i>Attendance
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link active fw-medium" href="{{ route('admin.students.profile.documents', $student->id) }}">
                        <i class="ri-file-text-line me-2"></i>Documents
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Document Summary Cards -->
    @php
        $hasPhoto = !empty($student->photo_path);
        $totalDocs = $hasPhoto ? 1 : 0;
        $verifiedDocs = $hasPhoto ? 1 : 0;
    @endphp
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 48px; height: 48px;">
                                <i class="ri-file-text-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">{{ $totalDocs }}</h6>
                            <p class="text-secondary mb-0 small">Total Documents</p>
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
                            <div class="d-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 48px; height: 48px;">
                                <i class="ri-check-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">{{ $verifiedDocs }}</h6>
                            <p class="text-secondary mb-0 small">Verified</p>
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
                            <h6 class="mb-0 fw-semibold">0</h6>
                            <p class="text-secondary mb-0 small">Pending Review</p>
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
                            <div class="d-flex align-items-center justify-content-center bg-info-subtle text-info rounded-circle" style="width: 48px; height: 48px;">
                                <i class="ri-folder-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">—</h6>
                            <p class="text-secondary mb-0 small">Total Size</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Grid -->
    <div class="row g-4">
        <!-- Personal Documents -->
        <div class="col-lg-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0">
                        <i class="ri-user-line me-2 text-primary"></i>Personal Documents
                    </h6>
                    <span class="badge bg-primary-subtle text-primary">{{ $hasPhoto ? '1 File' : '0 Files' }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @if($hasPhoto)
                            <div class="list-group-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="d-flex align-items-center justify-content-center bg-success-subtle text-success rounded" style="width: 40px; height: 40px;">
                                            <i class="ri-image-line"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-medium">Passport Photograph</h6>
                                        <p class="mb-0 small text-secondary">Profile photo</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge bg-success-subtle text-success">Verified</span>
                                    <a href="{{ asset('storage/' . $student->photo_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                </div>
                            </div>
                        @endif
                        @if(!$hasPhoto)
                            <div class="list-group-item text-center py-4">
                                <i class="ri-file-text-line text-muted" style="font-size: 32px;"></i>
                                <p class="text-muted mb-0 mt-2">No personal documents uploaded</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Documents -->
        <div class="col-lg-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0">
                        <i class="ri-graduation-cap-line me-2 text-primary"></i>Academic Documents
                    </h6>
                    <span class="badge bg-primary-subtle text-primary">0 Files</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item text-center py-4">
                            <i class="ri-file-text-line text-muted" style="font-size: 32px;"></i>
                            <p class="text-muted mb-0 mt-2">No academic documents uploaded</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
