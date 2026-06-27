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
    @php
        $currentSession = \App\Models\AcademicSession::where('is_current', true)->first();
        $currentTerm = \App\Models\Term::where('is_current', true)->first();
    @endphp
    <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
        <i class="ri-information-line me-2 fs-5"></i>
        <div>
            <strong>Current Academic Session:</strong> {{ $currentSession->name ?? 'Not Set' }} | <strong>Term:</strong> {{ $currentTerm->name ?? 'Not Set' }}
            <br>
            <small>Select a class below to view students eligible for promotion or transfer</small>
        </div>
    </div>

    <!-- Class Selection Grid -->
    <div class="row g-4">
        @php
            $jssClasses = $schoolClasses->filter(function($class) {
                return str_starts_with($class->name, 'JSS');
            });
            $ssClasses = $schoolClasses->filter(function($class) {
                return str_starts_with($class->name, 'SS');
            });
        @endphp

        <!-- Junior Secondary Section -->
        @if($jssClasses->isNotEmpty())
            <div class="col-lg-6">
                <div class="card custom-shadow rounded-3 bg-white border">
                    <div class="card-header bg-transparent border-0">
                        <h6 class="fw-semibold mb-0">
                            <i class="ri-graduation-cap-line me-2 text-primary"></i>Junior Secondary School (JSS)
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($jssClasses as $schoolClass)
                            @php
                                $isGraduationYear = str_contains($schoolClass->name, 'JSS3');
                                $badgeClass = $isGraduationYear ? 'bg-warning-subtle text-warning' : 'bg-primary-subtle text-primary';
                                $badgeText = $isGraduationYear ? 'Graduation Year' : $schoolClass->classArms->count() . ' Classes';
                            @endphp
                            <div class="mb-4 {{ $loop->last ? 'mb-0' : '' }}">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="fw-medium mb-0">{{ $schoolClass->name }}</h6>
                                    <span class="badge {{ $badgeClass }}">{{ $badgeText }}</span>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($schoolClass->classArms as $classArm)
                                        @php
                                            $studentCount = $classArm->students->where('status', 'Active')->count();
                                            $btnClass = $isGraduationYear ? 'btn-outline-warning' : 'btn-outline-primary';
                                        @endphp
                                        <a href="{{ route('admin.students.promote.class', $classArm->id) }}" class="btn {{ $btnClass }} d-flex align-items-center gap-2">
                                            <i class="ri-group-line"></i>
                                            {{ $classArm->name }}
                                            <span class="badge {{ $isGraduationYear ? 'bg-warning' : 'bg-primary' }} text-white ms-1">{{ $studentCount }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Senior Secondary Section -->
        @if($ssClasses->isNotEmpty())
            <div class="col-lg-6">
                <div class="card custom-shadow rounded-3 bg-white border">
                    <div class="card-header bg-transparent border-0">
                        <h6 class="fw-semibold mb-0">
                            <i class="ri-graduation-cap-fill me-2 text-success"></i>Senior Secondary School (SS)
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($ssClasses as $schoolClass)
                            @php
                                $isFinalYear = str_contains($schoolClass->name, 'SS3');
                                $badgeClass = $isFinalYear ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success';
                                $badgeText = $isFinalYear ? 'Final Year' : $schoolClass->classArms->count() . ' Classes';
                            @endphp
                            <div class="mb-4 {{ $loop->last ? 'mb-0' : '' }}">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="fw-medium mb-0">{{ $schoolClass->name }}</h6>
                                    <span class="badge {{ $badgeClass }}">{{ $badgeText }}</span>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($schoolClass->classArms as $classArm)
                                        @php
                                            $studentCount = $classArm->students->where('status', 'Active')->count();
                                            $btnClass = $isFinalYear ? 'btn-outline-danger' : 'btn-outline-success';
                                        @endphp
                                        <a href="{{ route('admin.students.promote.class', $classArm->id) }}" class="btn {{ $btnClass }} d-flex align-items-center gap-2">
                                            <i class="ri-group-line"></i>
                                            {{ $classArm->name }}
                                            <span class="badge {{ $isFinalYear ? 'bg-danger' : 'bg-success' }} text-white ms-1">{{ $studentCount }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Stats -->
    @php
        $eligibleForPromotion = $totalStudents - $graduatingStudents;
        $repeatStudents = 0; // This would need to be calculated based on academic performance
    @endphp
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
                            <h6 class="mb-0 fw-semibold">{{ $totalStudents }}</h6>
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
                            <h6 class="mb-0 fw-semibold">{{ $eligibleForPromotion }}</h6>
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
                            <h6 class="mb-0 fw-semibold">{{ $repeatStudents }}</h6>
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
                            <h6 class="mb-0 fw-semibold">{{ $graduatingStudents }}</h6>
                            <p class="text-secondary mb-0 small">Graduating Students</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
