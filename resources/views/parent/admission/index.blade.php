@extends('layouts.parent')

@section('title', 'My Applications')
@section('page-title', 'Admission Applications')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active text-muted">Applications</li>
            </ol>
        </nav>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Admission Applications</h4>
                <p class="text-muted small mb-0">Track and manage your admission applications</p>
            </div>
            <a href="{{ route('parent.admission.create') }}" class="btn btn-primary">
                <i class="ri-add-line me-2"></i>New Application
            </a>
        </div>

        @if($applications->isEmpty())
            <!-- Empty State -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="ri-file-list-line" style="font-size: 64px; color: #ccc;"></i>
                    </div>
                    <h5 class="mb-2">No Applications Yet</h5>
                    <p class="text-muted mb-4">You haven't submitted any admission applications</p>
                    <a href="{{ route('parent.admission.create') }}" class="btn btn-primary">
                        <i class="ri-add-line me-2"></i>Start New Application
                    </a>
                </div>
            </div>
        @else
            <!-- Applications Grid -->
            <div class="row g-4">
                @foreach($applications as $application)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <!-- Status Badge -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge {{ $application->getStatusBadgeClass() }} px-3 py-2">
                                        {{ $application->status }}
                                    </span>
                                    @if($application->canEdit())
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                                <i class="ri-more-2-line"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @if($application->status === 'Pending Payment')
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('parent.admission.payment', $application->id) }}">
                                                            <i class="ri-money-dollar-circle-line me-2"></i>Continue Payment
                                                        </a>
                                                    </li>
                                                @elseif($application->status === 'Draft')
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('parent.admission.form', $application->id) }}">
                                                            <i class="ri-edit-line me-2"></i>Continue Application
                                                        </a>
                                                    </li>
                                                @endif
                                                <li>
                                                    <form action="{{ route('parent.admission.destroy', $application->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" 
                                                                onclick="return confirm('Delete this application?')">
                                                            <i class="ri-delete-bin-line me-2"></i>Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>

                                <!-- Application Info -->
                                <h6 class="mb-3 fw-bold">{{ $application->full_name }}</h6>
                                
                                <div class="mb-3">
                                    <div class="d-flex align-items-center text-muted small mb-2">
                                        <i class="ri-hashtag me-2"></i>
                                        <span>{{ $application->application_number }}</span>
                                    </div>
                                    <div class="d-flex align-items-center text-muted small mb-2">
                                        <i class="ri-book-line me-2"></i>
                                        <span>{{ $application->proposedClass->name ?? 'Not specified' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center text-muted small mb-2">
                                        <i class="ri-calendar-line me-2"></i>
                                        <span>{{ $application->academicSession->name ?? 'Not specified' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="ri-time-line me-2"></i>
                                        <span>{{ $application->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>

                                <!-- Progress -->
                                @if($application->status === 'Pending Payment')
                                    <div class="alert alert-warning small mb-3 py-2">
                                        <i class="ri-alert-line me-1"></i>Payment pending
                                    </div>
                                @elseif($application->status === 'Draft')
                                    <div class="alert alert-info small mb-3 py-2">
                                        <i class="ri-information-line me-1"></i>Complete your application
                                    </div>
                                @elseif($application->status === 'Submitted')
                                    <div class="alert alert-primary small mb-3 py-2">
                                        <i class="ri-hourglass-line me-1"></i>Awaiting review
                                    </div>
                                @elseif($application->status === 'Under Review')
                                    <div class="alert alert-info small mb-3 py-2">
                                        <i class="ri-search-line me-1"></i>Under review
                                    </div>
                                @elseif($application->status === 'Approved')
                                    <div class="alert alert-success small mb-3 py-2">
                                        <i class="ri-check-line me-1"></i>Approved!
                                    </div>
                                @elseif($application->status === 'Rejected')
                                    <div class="alert alert-danger small mb-3 py-2">
                                        <i class="ri-close-line me-1"></i>Not approved
                                    </div>
                                @endif

                                <!-- Action Button -->
                                <a href="{{ route('parent.admission.show', $application->id) }}" class="btn btn-outline-primary w-100">
                                    <i class="ri-eye-line me-2"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($applications->hasPages())
                <div class="mt-4">
                    {{ $applications->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
