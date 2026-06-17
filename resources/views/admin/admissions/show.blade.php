@extends('layouts.admin')

@section('title', 'Application Details')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.admissions.index') }}">Applications</a></li>
            <li class="breadcrumb-item active">{{ $application->application_number }}</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h4 class="mb-2">{{ $application->full_name }}</h4>
                    <div class="d-flex gap-3">
                        <span class="text-muted small"><i class="ri-hashtag me-1"></i>{{ $application->application_number }}</span>
                        <span class="text-muted small"><i class="ri-calendar-line me-1"></i>{{ $application->created_at->format('M d, Y H:i') }}</span>
                        <span class="text-muted small"><i class="ri-user-line me-1"></i>{{ $application->parent->name ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge {{ $application->getStatusBadgeClass() }} px-3 py-2">{{ $application->status }}</span>
                    @if(in_array($application->status, ['Submitted', 'Under Review']))
                        <a href="{{ route('admin.admissions.review', $application->id) }}" class="btn btn-success">
                            <i class="ri-check-line me-2"></i>Review Application
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Student Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold"><i class="ri-user-line me-2"></i>Student Information</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6"><small class="text-muted">Full Name</small><div class="fw-semibold">{{ $application->full_name }}</div></div>
                        <div class="col-md-3"><small class="text-muted">Gender</small><div class="fw-semibold">{{ $application->gender }}</div></div>
                        <div class="col-md-3"><small class="text-muted">DOB</small><div class="fw-semibold">{{ $application->date_of_birth?->format('M d, Y') }}</div></div>
                        <div class="col-md-6"><small class="text-muted">Nationality</small><div class="fw-semibold">{{ $application->nationality }}</div></div>
                        <div class="col-md-6"><small class="text-muted">State</small><div class="fw-semibold">{{ $application->state_of_origin ?? 'N/A' }}</div></div>
                        <div class="col-md-6"><small class="text-muted">LGA</small><div class="fw-semibold">{{ $application->lga ?? 'N/A' }}</div></div>
                        <div class="col-md-6"><small class="text-muted">Religion</small><div class="fw-semibold">{{ $application->religion ?? 'N/A' }}</div></div>
                        <div class="col-md-6"><small class="text-muted">Blood Group</small><div class="fw-semibold">{{ $application->blood_group ?? 'N/A' }}</div></div>
                        <div class="col-md-6"><small class="text-muted">Medical</small><div class="fw-semibold">{{ $application->medical_conditions ?? 'None' }}</div></div>
                        <div class="col-12"><small class="text-muted">Address</small><div class="fw-semibold">{{ $application->home_address ?? 'N/A' }}</div></div>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold"><i class="ri-book-line me-2"></i>Academic Information</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6"><small class="text-muted">Proposed Class</small><div class="fw-semibold">{{ $application->proposedClass->name ?? 'N/A' }}</div></div>
                        <div class="col-md-6"><small class="text-muted">Class Arm</small><div class="fw-semibold">{{ $application->proposedClassArm->name ?? 'Not specified' }}</div></div>
                        <div class="col-md-6"><small class="text-muted">Session</small><div class="fw-semibold">{{ $application->academicSession->name ?? 'N/A' }}</div></div>
                        <div class="col-md-6"><small class="text-muted">Previous School</small><div class="fw-semibold">{{ $application->previous_school ?? 'N/A' }}</div></div>
                        <div class="col-12"><small class="text-muted">Reason for Admission</small><div class="fw-semibold">{{ $application->reason_for_admission ?? 'N/A' }}</div></div>
                    </div>
                </div>
            </div>

            <!-- Guardian Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold"><i class="ri-parent-line me-2"></i>Guardian Information</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6"><small class="text-muted">Name</small><div class="fw-semibold">{{ $application->guardian_name }}</div></div>
                        <div class="col-md-6"><small class="text-muted">Relationship</small><div class="fw-semibold">{{ $application->guardian_relationship }}</div></div>
                        <div class="col-md-6"><small class="text-muted">Phone</small><div class="fw-semibold">{{ $application->guardian_phone }}</div></div>
                        <div class="col-md-6"><small class="text-muted">Email</small><div class="fw-semibold">{{ $application->guardian_email }}</div></div>
                        <div class="col-md-6"><small class="text-muted">Occupation</small><div class="fw-semibold">{{ $application->guardian_occupation ?? 'N/A' }}</div></div>
                        <div class="col-md-6"><small class="text-muted">Address</small><div class="fw-semibold">{{ $application->guardian_address ?? 'N/A' }}</div></div>
                    </div>
                </div>
            </div>

            @if($application->admin_remarks)
                <div class="alert alert-{{ $application->status === 'Approved' ? 'success' : 'danger' }}">
                    <h6 class="fw-semibold mb-2"><i class="ri-message-line me-2"></i>Admin Remarks</h6>
                    <p class="mb-2">{{ $application->admin_remarks }}</p>
                    <small class="text-muted">By {{ $application->reviewer->name ?? 'N/A' }} on {{ $application->reviewed_at?->format('M d, Y H:i') }}</small>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Documents -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold"><i class="ri-file-line me-2"></i>Documents</h6>
                </div>
                <div class="card-body p-4">
                    @if($application->passport_photo_path)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $application->passport_photo_path) }}" class="img-fluid rounded mb-2" alt="Passport">
                            <a href="{{ asset('storage/' . $application->passport_photo_path) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                <i class="ri-download-line me-1"></i>Download Photo
                            </a>
                        </div>
                    @endif
                    @if($application->birth_certificate_path)
                        <div class="mb-3">
                            <a href="{{ asset('storage/' . $application->birth_certificate_path) }}" target="_blank" class="btn btn-sm btn-outline-success w-100">
                                <i class="ri-file-text-line me-1"></i>Birth Certificate
                            </a>
                        </div>
                    @endif
                    @if($application->previous_report_path)
                        <div class="mb-0">
                            <a href="{{ asset('storage/' . $application->previous_report_path) }}" target="_blank" class="btn btn-sm btn-outline-info w-100">
                                <i class="ri-file-list-line me-1"></i>Previous Report
                            </a>
                        </div>
                    @endif
                    @if(!$application->passport_photo_path && !$application->birth_certificate_path && !$application->previous_report_path)
                        <p class="text-muted small mb-0">No documents uploaded</p>
                    @endif
                </div>
            </div>

            @if($application->payment)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold"><i class="ri-money-dollar-circle-line me-2"></i>Payment</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-2"><small class="text-muted">Amount</small><div class="fw-semibold">₦{{ number_format($application->payment->amount, 2) }}</div></div>
                        <div class="mb-2"><small class="text-muted">Method</small><div class="fw-semibold">{{ $application->payment->payment_method }}</div></div>
                        <div class="mb-2"><small class="text-muted">Reference</small><div class="fw-semibold">{{ $application->payment->payment_reference }}</div></div>
                        <div><small class="text-muted">Date</small><div class="fw-semibold">{{ $application->payment->payment_date?->format('M d, Y H:i') }}</div></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
