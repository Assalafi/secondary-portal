@extends('layouts.parent')

@section('title', 'Application Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('parent.admission.index') }}" class="text-decoration-none">Applications</a></li>
                    <li class="breadcrumb-item active text-muted">Application Details</li>
                </ol>
            </nav>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show">
                    <i class="ri-information-line me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Application Header -->
            <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center gap-3">
                                @if($application->passport_photo_path)
                                    <img src="{{ asset('storage/' . $application->passport_photo_path) }}" 
                                         alt="Photo" class="rounded-circle" 
                                         style="width: 80px; height: 80px; object-fit: cover; border: 3px solid white;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border: 3px solid white;">
                                        <i class="ri-user-line" style="font-size: 40px; color: white;"></i>
                                    </div>
                                @endif
                                <div class="text-white">
                                    <h4 class="text-white mb-1">{{ $application->full_name }}</h4>
                                    <p class="mb-1 opacity-90"><i class="ri-file-text-line me-2"></i>{{ $application->application_number }}</p>
                                    <p class="mb-0 small opacity-75"><i class="ri-calendar-line me-2"></i>Submitted: {{ $application->submitted_at?->format('M d, Y h:i A') ?? 'Not submitted' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <span class="badge {{ $application->getStatusBadgeClass() }} px-4 py-2" style="font-size: 1rem;">
                                {{ $application->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Student Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold"><i class="ri-user-line me-2 text-primary"></i>Student Information</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-user-3-line me-1"></i>First Name</small>
                                <div class="fw-semibold">{{ $application->first_name }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-user-3-line me-1"></i>Last Name</small>
                                <div class="fw-semibold">{{ $application->last_name }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-user-3-line me-1"></i>Other Name</small>
                                <div class="fw-semibold">{{ $application->other_name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-calendar-line me-1"></i>Date of Birth</small>
                                <div class="fw-semibold">{{ $application->date_of_birth?->format('M d, Y') }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-genderless-line me-1"></i>Gender</small>
                                <div class="fw-semibold">{{ $application->gender }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-global-line me-1"></i>Nationality</small>
                                <div class="fw-semibold">{{ $application->nationality }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-map-pin-line me-1"></i>State of Origin</small>
                                <div class="fw-semibold">{{ $application->state_of_origin ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-map-pin-2-line me-1"></i>LGA</small>
                                <div class="fw-semibold">{{ $application->lga ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-book-read-line me-1"></i>Religion</small>
                                <div class="fw-semibold">{{ $application->religion ?? 'N/A' }}</div>
                            </div>
                            
                            <!-- Place of Birth -->
                            <div class="col-12"><hr class="my-2"></div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-map-pin-user-line me-1"></i>Place of Birth (Town)</small>
                                <div class="fw-semibold">{{ $application->place_of_birth_town ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-map-pin-user-line me-1"></i>Place of Birth (LGA)</small>
                                <div class="fw-semibold">{{ $application->place_of_birth_lga ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-map-pin-user-line me-1"></i>Place of Birth (State)</small>
                                <div class="fw-semibold">{{ $application->place_of_birth_state ?? 'N/A' }}</div>
                            </div>
                            
                            <!-- Health Information -->
                            <div class="col-12"><hr class="my-2"></div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-heart-pulse-line me-1"></i>Blood Group</small>
                                <div class="fw-semibold">{{ $application->blood_group ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-health-book-line me-1"></i>Health Status</small>
                                <div class="fw-semibold">{{ $application->health_status ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-first-aid-kit-line me-1"></i>Medical Conditions</small>
                                <div class="fw-semibold">{{ $application->medical_conditions ?? 'None' }}</div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block mb-1"><i class="ri-wheelchair-line me-1"></i>Disability Details</small>
                                <div class="fw-semibold">{{ $application->disability_details ?? 'None' }}</div>
                            </div>
                            
                            <!-- Address -->
                            <div class="col-12"><hr class="my-2"></div>
                            <div class="col-12">
                                <small class="text-muted d-block mb-1"><i class="ri-home-line me-1"></i>Home Address</small>
                                <div class="fw-semibold">{{ $application->home_address ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold"><i class="ri-book-line me-2 text-primary"></i>Academic Information</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1"><i class="ri-book-open-line me-1"></i>Proposed Class</small>
                                <div class="fw-semibold">{{ $application->proposedClass->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1"><i class="ri-door-open-line me-1"></i>Class Arm</small>
                                <div class="fw-semibold">{{ $application->proposedClassArm->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1"><i class="ri-calendar-check-line me-1"></i>Academic Session</small>
                                <div class="fw-semibold">{{ $application->academicSession->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1"><i class="ri-school-line me-1"></i>Previous School</small>
                                <div class="fw-semibold">{{ $application->previous_school ?? 'N/A' }}</div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block mb-1"><i class="ri-question-line me-1"></i>Reason for Admission</small>
                                <div class="fw-semibold">{{ $application->reason_for_admission ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guardian Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold"><i class="ri-parent-line me-2 text-primary"></i>Guardian Information</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1"><i class="ri-user-heart-line me-1"></i>Guardian Name</small>
                                <div class="fw-semibold">{{ $application->guardian_name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1"><i class="ri-team-line me-1"></i>Relationship</small>
                                <div class="fw-semibold">{{ $application->guardian_relationship ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-phone-line me-1"></i>Phone Number</small>
                                <div class="fw-semibold">{{ $application->guardian_phone ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-mail-line me-1"></i>Email</small>
                                <div class="fw-semibold">{{ $application->guardian_email ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-briefcase-line me-1"></i>Occupation</small>
                                <div class="fw-semibold">{{ $application->guardian_occupation ?? 'N/A' }}</div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block mb-1"><i class="ri-home-4-line me-1"></i>Guardian Address</small>
                                <div class="fw-semibold">{{ $application->guardian_address ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1"><i class="ri-building-line me-1"></i>City</small>
                                <div class="fw-semibold">{{ $application->guardian_city ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1"><i class="ri-map-2-line me-1"></i>State</small>
                                <div class="fw-semibold">{{ $application->guardian_state ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold"><i class="ri-phone-fill me-2 text-danger"></i>Emergency Contact</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-user-add-line me-1"></i>Contact Name</small>
                                <div class="fw-semibold">{{ $application->emergency_contact_name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-phone-fill me-1"></i>Contact Phone</small>
                                <div class="fw-semibold">{{ $application->emergency_contact_phone ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1"><i class="ri-links-line me-1"></i>Relationship</small>
                                <div class="fw-semibold">{{ $application->emergency_contact_relationship ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($application->status === 'Rejected' && $application->admin_remarks)
                    <div class="alert alert-danger border-0 shadow-sm">
                        <h6 class="text-danger mb-2"><i class="ri-error-warning-line me-2"></i>Admin Remarks</h6>
                        <p class="mb-0">{{ $application->admin_remarks }}</p>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Documents -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold"><i class="ri-file-list-3-line me-2 text-primary"></i>Uploaded Documents</h6>
                    </div>
                    <div class="card-body p-3">
                        @if($application->passport_photo_path)
                            <a href="{{ asset('storage/' . $application->passport_photo_path) }}" target="_blank" 
                               class="d-flex align-items-center p-3 mb-2 text-decoration-none border rounded hover-shadow">
                                <div class="flex-shrink-0">
                                    <div class="rounded" style="width: 50px; height: 50px; background: #e3f2fd; display: flex; align-items: center; justify-content: center;">
                                        <i class="ri-image-2-line text-primary" style="font-size: 24px;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fw-semibold text-dark">Passport Photo</div>
                                    <small class="text-muted">Click to view</small>
                                </div>
                            </a>
                        @endif

                        @if($application->birth_certificate_path)
                            <a href="{{ asset('storage/' . $application->birth_certificate_path) }}" target="_blank" 
                               class="d-flex align-items-center p-3 mb-2 text-decoration-none border rounded hover-shadow">
                                <div class="flex-shrink-0">
                                    <div class="rounded" style="width: 50px; height: 50px; background: #fff3e0; display: flex; align-items: center; justify-content: center;">
                                        <i class="ri-file-text-line text-warning" style="font-size: 24px;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fw-semibold text-dark">Birth Certificate</div>
                                    <small class="text-muted">Click to view</small>
                                </div>
                            </a>
                        @endif

                        @if($application->previous_report_path)
                            <a href="{{ asset('storage/' . $application->previous_report_path) }}" target="_blank" 
                               class="d-flex align-items-center p-3 text-decoration-none border rounded hover-shadow">
                                <div class="flex-shrink-0">
                                    <div class="rounded" style="width: 50px; height: 50px; background: #f3e5f5; display: flex; align-items: center; justify-content: center;">
                                        <i class="ri-file-list-2-line text-purple" style="font-size: 24px;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fw-semibold text-dark">Previous Report</div>
                                    <small class="text-muted">Click to view</small>
                                </div>
                            </a>
                        @endif

                        @if(!$application->passport_photo_path && !$application->birth_certificate_path && !$application->previous_report_path)
                            <div class="text-center py-4 text-muted">
                                <i class="ri-folder-open-line" style="font-size: 48px; opacity: 0.3;"></i>
                                <p class="mt-2 mb-0 small">No documents uploaded</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Information -->
                @if($application->invoice || $application->payment)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold"><i class="ri-money-dollar-circle-line me-2 text-success"></i>Payment Details</h6>
                        </div>
                        <div class="card-body p-4">
                            @if($application->invoice)
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1"><i class="ri-receipt-line me-1"></i>Invoice Number</small>
                                    <div class="fw-semibold">{{ $application->invoice->invoice_number }}</div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1"><i class="ri-money-dollar-box-line me-1"></i>Total Amount</small>
                                    <div class="fw-semibold fs-5 text-success">₦{{ number_format($application->invoice->total_amount, 2) }}</div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1"><i class="ri-wallet-line me-1"></i>Amount Paid</small>
                                    <div class="fw-semibold text-success">₦{{ number_format($application->invoice->amount_paid, 2) }}</div>
                                </div>
                                @if($application->invoice->balance > 0)
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1"><i class="ri-error-warning-line me-1"></i>Balance</small>
                                        <div class="fw-semibold text-danger">₦{{ number_format($application->invoice->balance, 2) }}</div>
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1"><i class="ri-checkbox-circle-line me-1"></i>Status</small>
                                    <span class="badge {{ $application->invoice->status === 'Paid' ? 'bg-success' : 'bg-warning' }}">
                                        {{ $application->invoice->status }}
                                    </span>
                                </div>
                                @if($application->invoice->metadata)
                                    @php $metadata = json_decode($application->invoice->metadata, true); @endphp
                                    @if(isset($metadata['RRR']))
                                        <div class="mb-0">
                                            <small class="text-muted d-block mb-1"><i class="ri-key-line me-1"></i>RRR Reference</small>
                                            <div class="fw-semibold font-monospace small">{{ $metadata['RRR'] }}</div>
                                        </div>
                                    @endif
                                @endif
                            @elseif($application->payment)
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1"><i class="ri-money-dollar-box-line me-1"></i>Amount</small>
                                    <div class="fw-semibold fs-5 text-success">₦{{ number_format($application->payment->amount, 2) }}</div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1"><i class="ri-bank-card-line me-1"></i>Method</small>
                                    <div class="fw-semibold">{{ $application->payment->payment_method }}</div>
                                </div>
                                <div class="mb-0">
                                    <small class="text-muted d-block mb-1"><i class="ri-key-line me-1"></i>Reference</small>
                                    <div class="fw-semibold font-monospace small">{{ $application->payment->payment_reference }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3 d-grid gap-2">
                        <a href="{{ route('parent.admission.download-pdf', $application->id) }}" class="btn btn-primary">
                            <i class="ri-download-line me-2"></i>Download PDF
                        </a>
                        <a href="{{ route('parent.admission.index') }}" class="btn btn-outline-primary">
                            <i class="ri-arrow-left-line me-2"></i>Back to Applications
                        </a>
                        @if($application->status === 'Approved')
                            <button class="btn btn-success" onclick="alert('Enrollment process coming soon!')">
                                <i class="ri-user-add-line me-2"></i>Complete Enrollment
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@push('styles')
<style>
    .hover-shadow {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
</style>
@endpush
@endsection
