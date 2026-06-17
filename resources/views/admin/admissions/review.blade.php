@extends('layouts.admin')

@section('title', 'Review Application')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.admissions.index') }}">Applications</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.admissions.show', $application->id) }}">{{ $application->application_number }}</a></li>
            <li class="breadcrumb-item active">Review</li>
        </ol>
    </nav>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    @if($application->passport_photo_path)
                        <img src="{{ asset('storage/' . $application->passport_photo_path) }}" 
                             class="rounded" width="80" height="80" alt="Applicant Photo">
                    @else
                        <div class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <i class="ri-user-line text-primary" style="font-size: 32px;"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-grow-1 ms-3">
                    <h4 class="mb-1">{{ $application->full_name }}</h4>
                    <div class="d-flex gap-3 flex-wrap">
                        <span class="text-muted small"><i class="ri-hashtag me-1"></i>{{ $application->application_number }}</span>
                        <span class="text-muted small"><i class="ri-book-line me-1"></i>{{ $application->proposedClass->name ?? 'N/A' }}</span>
                        <span class="text-muted small"><i class="ri-user-line me-1"></i>{{ $application->parent->name ?? 'N/A' }}</span>
                    </div>
                </div>
                <span class="badge {{ $application->getStatusBadgeClass() }} px-3 py-2">{{ $application->status }}</span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Application Summary -->
        <div class="col-lg-7">
            <!-- Quick Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold"><i class="ri-information-line me-2"></i>Application Summary</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-2">Student Details</h6>
                            <div class="mb-1"><strong>{{ $application->full_name }}</strong></div>
                            <div class="text-muted small">{{ $application->gender }} • {{ $application->date_of_birth?->format('M d, Y') }}</div>
                            <div class="text-muted small">{{ $application->nationality }}</div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-2">Guardian</h6>
                            <div class="mb-1"><strong>{{ $application->guardian_name }}</strong></div>
                            <div class="text-muted small">{{ $application->guardian_relationship }}</div>
                            <div class="text-muted small">{{ $application->guardian_phone }}</div>
                            <div class="text-muted small">{{ $application->guardian_email }}</div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-2">Academic Info</h6>
                            <div class="mb-1"><strong>{{ $application->proposedClass->name ?? 'N/A' }}</strong></div>
                            <div class="text-muted small">{{ $application->academicSession->name ?? 'N/A' }}</div>
                            @if($application->previous_school)
                                <div class="text-muted small">From: {{ $application->previous_school }}</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted small mb-2">Documents</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                @if($application->passport_photo_path)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="ri-check-line me-1"></i>Photo
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="ri-close-line me-1"></i>Photo
                                    </span>
                                @endif
                                @if($application->birth_certificate_path)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="ri-check-line me-1"></i>Birth Cert
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="ri-close-line me-1"></i>Birth Cert
                                    </span>
                                @endif
                                @if($application->previous_report_path)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="ri-check-line me-1"></i>Report
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-top">
                        <a href="{{ route('admin.admissions.show', $application->id) }}" class="btn btn-outline-primary">
                            <i class="ri-eye-line me-2"></i>View Full Details
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Review Actions -->
        <div class="col-lg-5">
            <!-- Approve Application -->
            <div class="card border-0 shadow-sm mb-4 border-success">
                <div class="card-header bg-success bg-opacity-10 border-bottom border-success py-3">
                    <h6 class="mb-0 fw-semibold text-success">
                        <i class="ri-check-double-line me-2"></i>Approve Application
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.admissions.approve', $application->id) }}" method="POST">
                        @csrf
                        
                        <div class="alert alert-info small">
                            <i class="ri-information-line me-1"></i>
                            Approving will create a student account and link them to the parent
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Admission Number <span class="text-danger">*</span></label>
                            <input type="text" name="admission_no" class="form-control" 
                                   value="{{ old('admission_no') }}" 
                                   placeholder="e.g., 2025/0001" required>
                            <small class="text-muted">Must be unique</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Assign to Class Arm <span class="text-danger">*</span></label>
                            <select name="class_arm_id" class="form-select" required>
                                <option value="">Select Class Arm</option>
                                @php
                                    $classArms = \App\Models\ClassArm::with('schoolClass')->where('school_class_id', $application->proposed_class_id)->get();
                                @endphp
                                @foreach($classArms as $arm)
                                    <option value="{{ $arm->id }}">{{ $arm->schoolClass->name }} - {{ $arm->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Approval Remarks (Optional)</label>
                            <textarea name="remarks" class="form-control" rows="3" 
                                      placeholder="Welcome to our school...">{{ old('remarks') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Are you sure you want to approve this application? A student account will be created.')">
                            <i class="ri-check-line me-2"></i>Approve & Enroll Student
                        </button>
                    </form>
                </div>
            </div>

            <!-- Reject Application -->
            <div class="card border-0 shadow-sm border-danger">
                <div class="card-header bg-danger bg-opacity-10 border-bottom border-danger py-3">
                    <h6 class="mb-0 fw-semibold text-danger">
                        <i class="ri-close-circle-line me-2"></i>Reject Application
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.admissions.reject', $application->id) }}" method="POST">
                        @csrf
                        
                        <div class="alert alert-warning small">
                            <i class="ri-alert-line me-1"></i>
                            The parent will be notified of the rejection
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="remarks" class="form-control" rows="4" 
                                      placeholder="Explain why the application is being rejected..."
                                      required minlength="10">{{ old('remarks') }}</textarea>
                            <small class="text-muted">Minimum 10 characters</small>
                        </div>

                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to reject this application?')">
                            <i class="ri-close-line me-2"></i>Reject Application
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
