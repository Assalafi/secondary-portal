@extends('layouts.parent')

@section('title', 'New Admission Application')
@section('page-title', 'New Admission Application')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('parent.dashboard') }}" class="text-decoration-none">
                            <i class="ri-home-4-line me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('parent.admission.index') }}" class="text-decoration-none">Applications</a>
                    </li>
                    <li class="breadcrumb-item active">New Application</li>
                </ol>
            </nav>

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="ri-information-line me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Main Application Card -->
            <div class="card border-0 shadow-sm overflow-hidden">
                <!-- Header with Gradient -->

                <div class="card-body p-4 p-md-5">
                    <!-- Application Steps -->
                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <div class="text-center p-4 h-100 border rounded-3 position-relative"
                                style="background: linear-gradient(to bottom, #f8f9fa, #ffffff);">
                                <div class="position-absolute top-0 start-50 translate-middle">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        <span class="fw-bold">1</span>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="text-primary mb-2" style="font-size: 2.5rem;">
                                        <i class="ri-money-dollar-circle-line"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Pay Application Fee</h5>
                                    <p class="text-muted small mb-0">Complete secure payment via Remita gateway</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-4 h-100 border rounded-3 position-relative"
                                style="background: linear-gradient(to bottom, #f8f9fa, #ffffff);">
                                <div class="position-absolute top-0 start-50 translate-middle">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        <span class="fw-bold">2</span>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="text-primary mb-2" style="font-size: 2.5rem;">
                                        <i class="ri-file-edit-line"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Fill Application Form</h5>
                                    <p class="text-muted small mb-0">Provide student details and documents</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-4 h-100 border rounded-3 position-relative"
                                style="background: linear-gradient(to bottom, #f8f9fa, #ffffff);">
                                <div class="position-absolute top-0 start-50 translate-middle">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        <span class="fw-bold">3</span>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="text-primary mb-2" style="font-size: 2.5rem;">
                                        <i class="ri-send-plane-line"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Submit & Review</h5>
                                    <p class="text-muted small mb-0">Wait for admin approval decision</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fee Information Card -->
                    <div class="card bg-primary bg-opacity-10 border-primary border-opacity-25 mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-25 rounded-circle p-3">
                                        <i class="ri-price-tag-3-line text-primary" style="font-size: 1.5rem;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-0">Application Fee</h5>
                                    <h3 class="text-primary mb-0">₦{{ number_format($applicationFee, 2) }}</h3>
                                </div>
                            </div>
                            <div class="alert alert-warning mb-0 py-2 px-3">
                                <small><i class="ri-alert-line me-1"></i><strong>Note:</strong> Application fee is
                                    non-refundable</small>
                            </div>
                        </div>
                    </div>

                    <!-- Important Information -->
                    <div class="card border-info border-opacity-25 mb-4">
                        <div class="card-header bg-info bg-opacity-10 border-info border-opacity-25">
                            <h6 class="mb-0">
                                <i class="ri-information-line me-2"></i>Important Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="ri-checkbox-circle-line text-success mt-1 me-2"></i>
                                        <div>
                                            <strong>Payment Method:</strong><br>
                                            <small class="text-muted">Secure online payment via Remita</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="ri-time-line text-info mt-1 me-2"></i>
                                        <div>
                                            <strong>Processing Time:</strong><br>
                                            <small class="text-muted">3-5 business days</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="ri-file-list-line text-warning mt-1 me-2"></i>
                                        <div>
                                            <strong>Required Documents:</strong><br>
                                            <small class="text-muted">Birth Certificate, Passport Photo, Previous
                                                Report</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <i class="ri-mail-line text-primary mt-1 me-2"></i>
                                        <div>
                                            <strong>Notifications:</strong><br>
                                            <small class="text-muted">Email updates on application status</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-3">
                            <div class="alert alert-success mb-0 py-2">
                                <small>
                                    <i class="ri-shield-check-line me-1"></i>
                                    Once approved, the student will be enrolled and automatically linked to your account
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-center">
                        <form action="{{ route('parent.admission.initiate-payment') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg px-5 py-3 shadow-sm">
                                <i class="ri-secure-payment-line me-2"></i>Proceed to Payment
                            </button>
                        </form>
                        <div class="mt-3">
                            <a href="{{ route('parent.admission.index') }}"
                                class="btn btn-link text-muted text-decoration-none">
                                <i class="ri-arrow-left-line me-1"></i>Back to Applications
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
