@extends('layouts.admin')

@section('title', 'Promote/Transfer Students')

@section('content')
<div class="main-content-container overflow-hidden">
    <!-- Page Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary me-3 d-flex align-items-center gap-2">
                    <i class="ri-arrow-left-line"></i>
                    Back to Students
                </a>
                <div>
                    <h3 class="fs-20 fw-semibold mb-1">Promote/Transfer Students</h3>
                    <p class="text-secondary mb-0">Select a class to promote or transfer students</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="d-flex gap-2 justify-content-md-end">
                <button class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <i class="ri-history-line"></i>
                    Promotion History
                </button>
                <button class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="ri-download-line"></i>
                    Export Report
                </button>
            </div>
        </div>
    </div>

    <!-- Academic Session Info -->
    <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
        <i class="ri-information-line me-2 fs-5"></i>
        <div>
            <strong>Current Academic Session:</strong> 2024/2025 | <strong>Term:</strong> 1st Term
            <br>
            <small>Select a class below to view students eligible for promotion or transfer</small>
        </div>
    </div>

    <!-- Class Selection Grid -->
    <div class="row g-4">
        <!-- Junior Secondary Section -->
        <div class="col-lg-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-semibold mb-0">
                        <i class="ri-graduation-cap-line me-2 text-primary"></i>Junior Secondary School (JSS)
                    </h6>
                </div>
                <div class="card-body">
                    <!-- JSS 1 -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-medium mb-0">JSS 1</h6>
                            <span class="badge bg-primary-subtle text-primary">3 Classes</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.students.promote.class', 'JSS1A') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                                <i class="ri-group-line"></i>
                                JSS 1A
                                <span class="badge bg-primary text-white ms-1">28</span>
                            </a>
                            <a href="{{ route('admin.students.promote.class', 'JSS1B') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                                <i class="ri-group-line"></i>
                                JSS 1B
                                <span class="badge bg-primary text-white ms-1">25</span>
                            </a>
                            <a href="{{ route('admin.students.promote.class', 'JSS1C') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                                <i class="ri-group-line"></i>
                                JSS 1C
                                <span class="badge bg-primary text-white ms-1">30</span>
                            </a>
                        </div>
                    </div>

                    <!-- JSS 2 -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-medium mb-0">JSS 2</h6>
                            <span class="badge bg-primary-subtle text-primary">2 Classes</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.students.promote.class', 'JSS2A') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                                <i class="ri-group-line"></i>
                                JSS 2A
                                <span class="badge bg-primary text-white ms-1">32</span>
                            </a>
                            <a href="{{ route('admin.students.promote.class', 'JSS2B') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                                <i class="ri-group-line"></i>
                                JSS 2B
                                <span class="badge bg-primary text-white ms-1">29</span>
                            </a>
                        </div>
                    </div>

                    <!-- JSS 3 -->
                    <div class="mb-0">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-medium mb-0">JSS 3</h6>
                            <span class="badge bg-warning-subtle text-warning">Graduation Year</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.students.promote.class', 'JSS3A') }}" class="btn btn-warning d-flex align-items-center gap-2">
                                <i class="ri-group-line"></i>
                                JSS 3A
                                <span class="badge bg-white text-warning ms-1">27</span>
                            </a>
                            <a href="{{ route('admin.students.promote.class', 'JSS3B') }}" class="btn btn-outline-warning d-flex align-items-center gap-2">
                                <i class="ri-group-line"></i>
                                JSS 3B
                                <span class="badge bg-warning text-white ms-1">24</span>
                            </a>
                            <a href="{{ route('admin.students.promote.class', 'JSS3C') }}" class="btn btn-outline-warning d-flex align-items-center gap-2">
                                <i class="ri-group-line"></i>
                                JSS 3C
                                <span class="badge bg-warning text-white ms-1">26</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Senior Secondary Section -->
        <div class="col-lg-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-semibold mb-0">
                        <i class="ri-graduation-cap-fill me-2 text-success"></i>Senior Secondary School (SS)
                    </h6>
                </div>
                <div class="card-body">
                    <!-- SS 1 -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-medium mb-0">SS 1</h6>
                            <span class="badge bg-success-subtle text-success">3 Classes</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.students.promote.class', 'SS1A') }}" class="btn btn-outline-success d-flex align-items-center gap-2">
                                <i class="ri-group-line"></i>
                                SS 1A
                                <span class="badge bg-success text-white ms-1">22</span>
                            </a>
                            <a href="{{ route('admin.students.promote.class', 'SS1B') }}" class="btn btn-outline-success d-flex align-items-center gap-2">
                                <i class="ri-group-line"></i>
                                SS 1B
                                <span class="badge bg-success text-white ms-1">20</span>
                            </a>
                            <a href="{{ route('admin.students.promote.class', 'SS1C') }}" class="btn btn-outline-success d-flex align-items-center gap-2">
                                <i class="ri-group-line"></i>
                                SS 1C
                                <span class="badge bg-success text-white ms-1">25</span>
                            </a>
                        </div>
                    </div>

                    <!-- SS 2 -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-medium mb-0">SS 2</h6>
                            <span class="badge bg-success-subtle text-success">2 Classes</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.students.promote.class', 'SS2A') }}" class="btn btn-outline-success d-flex align-items-center gap-2">
                                <i class="ri-group-line"></i>
                                SS 2A
                                <span class="badge bg-success text-white ms-1">18</span>
                            </a>
                            <a href="{{ route('admin.students.promote.class', 'SS2B') }}" class="btn btn-outline-success d-flex align-items-center gap-2">
                                <i class="ri-group-line"></i>
                                SS 2B
                                <span class="badge bg-success text-white ms-1">21</span>
                            </a>
                        </div>
                    </div>

                    <!-- SS 3 -->
                    <div class="mb-0">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-medium mb-0">SS 3</h6>
                            <span class="badge bg-danger-subtle text-danger">Final Year</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.students.promote.class', 'SS3A') }}" class="btn btn-danger d-flex align-items-center gap-2">
                                <i class="ri-group-line"></i>
                                SS 3A
                                <span class="badge bg-white text-danger ms-1">19</span>
                            </a>
                            <a href="{{ route('admin.students.promote.class', 'SS3B') }}" class="btn btn-outline-danger d-flex align-items-center gap-2">
                                <i class="ri-group-line"></i>
                                SS 3B
                                <span class="badge bg-danger text-white ms-1">17</span>
                            </a>
                            <a href="{{ route('admin.students.promote.class', 'SS3C') }}" class="btn btn-outline-danger d-flex align-items-center gap-2">
                                <i class="ri-group-line"></i>
                                SS 3C
                                <span class="badge bg-danger text-white ms-1">16</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mt-2">
        <div class="col-lg-3 col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 48px; height: 48px;">
                                <i class="ri-group-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">383</h6>
                            <p class="text-secondary mb-0 small">Total Students</p>
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
                                <i class="ri-arrow-up-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">356</h6>
                            <p class="text-secondary mb-0 small">Eligible for Promotion</p>
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
                                <i class="ri-repeat-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">15</h6>
                            <p class="text-secondary mb-0 small">Repeat Students</p>
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
                            <div class="d-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle" style="width: 48px; height: 48px;">
                                <i class="ri-graduation-cap-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">52</h6>
                            <p class="text-secondary mb-0 small">Graduating Students</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
