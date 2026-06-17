@extends('layouts.parent')

@section('title', 'My Dependents')
@section('page-title', 'My Dependents')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active text-muted">My Dependents</li>
            </ol>
        </nav>

        <!-- Apply for Admission Button -->
        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="text-white">
                        <h5 class="mb-2 fw-bold">Apply for New Admission</h5>
                        <p class="mb-0 opacity-90">Submit an admission application for a new student</p>
                    </div>
                    <a href="{{ route('parent.admission.create') }}" class="btn btn-light btn-lg px-4">
                        <i class="ri-add-line me-2"></i>Start New Application
                    </a>
                </div>
            </div>
        </div>

        @if($dependents->isEmpty())
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="ri-user-line" style="font-size: 64px; color: #ccc;"></i>
                    <h5 class="mt-3 mb-2">No Dependents Found</h5>
                    <p class="text-muted">You don't have any dependents linked to your account yet.</p>
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach($dependents as $dependent)
                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <!-- Student Header -->
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    @if($dependent['student']->user->photo_path)
                                        <img src="{{ asset('storage/' . $dependent['student']->user->photo_path) }}" 
                                             alt="{{ $dependent['student']->user->name }}"
                                             class="rounded-circle" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="ri-user-line text-white" style="font-size: 30px;"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h5 class="mb-1">{{ $dependent['student']->user->name }}</h5>
                                        <p class="mb-0 text-muted">Secondary Section</p>
                                    </div>
                                </div>

                                <!-- Class and Attendance Grid -->
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <p class="mb-1 text-muted small">Class</p>
                                        <p class="mb-0 fw-bold">{{ optional(optional($dependent['student']->classArm)->schoolClass)->name ?? 'N/A' }} {{ optional($dependent['student']->classArm)->name ?? '' }}</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-1 text-muted small">Attendance</p>
                                        <p class="mb-0 fw-bold">{{ $dependent['attendance'] }}%</p>
                                    </div>
                                </div>

                                <!-- View Details Link -->
                                <a href="{{ route('parent.dependents.show', $dependent['student']->id) }}" 
                                   class="text-decoration-none text-dark d-flex align-items-center">
                                    <span class="me-2">View Details</span>
                                    <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
