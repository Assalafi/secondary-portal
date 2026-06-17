@extends('layouts.admin')

@section('title', 'Staff Overview')

@section('content')
<div class="main-content-container overflow-hidden">
    <!-- Staff Overview Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="fs-20 fw-semibold mb-1">Staff</h3>
                    <p class="text-secondary mb-0">Manage school staff, assignments, and performance</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Management Cards -->
    <div class="row g-4">
        <!-- Staff Directory -->
        <div class="col-md-6">
            <a href="{{ route('admin.staff.index') }}" class="text-decoration-none">
                <div class="card custom-shadow rounded-3 bg-white border h-100 hover-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper me-3">
                                    <i class="ri-group-line fs-1 text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="fw-semibold mb-1 text-dark">Staff Directory</h5>
                                    <p class="text-muted mb-0">View and manage all staff members</p>
                                </div>
                            </div>
                            <div>
                                <i class="ri-arrow-right-line fs-4 text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Role & Class Assignment -->
        <div class="col-md-6">
            <a href="{{ route('admin.staff.assignments') }}" class="text-decoration-none">
                <div class="card custom-shadow rounded-3 bg-white border h-100 hover-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper me-3">
                                    <i class="ri-file-list-line fs-1 text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="fw-semibold mb-1 text-dark">Role & Class Assignment</h5>
                                    <p class="text-muted mb-0">Assign staff to classes and subjects</p>
                                </div>
                            </div>
                            <div>
                                <i class="ri-arrow-right-line fs-4 text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Performance Log -->
        <div class="col-md-6">
            <a href="#" class="text-decoration-none">
                <div class="card custom-shadow rounded-3 bg-white border h-100 hover-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper me-3">
                                    <i class="ri-bar-chart-line fs-1 text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="fw-semibold mb-1 text-dark">Performance Log</h5>
                                    <p class="text-muted mb-0">Track staff performance and evaluations</p>
                                </div>
                            </div>
                            <div>
                                <i class="ri-arrow-right-line fs-4 text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Quick Stats -->
        <div class="col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border h-100">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-semibold mb-0">
                        <i class="ri-dashboard-line me-2 text-primary"></i>Quick Stats
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-primary-subtle rounded">
                                <h4 class="fw-bold mb-1 text-primary">{{ $stats['total_staff'] ?? 0 }}</h4>
                                <small class="text-muted">Total Staff</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-success-subtle rounded">
                                <h4 class="fw-bold mb-1 text-success">{{ $stats['active_staff'] ?? 0 }}</h4>
                                <small class="text-muted">Active Staff</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-info-subtle rounded">
                                <h4 class="fw-bold mb-1 text-info">{{ $stats['departments'] ?? 0 }}</h4>
                                <small class="text-muted">Departments</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-warning-subtle rounded">
                                <h4 class="fw-bold mb-1 text-warning">{{ $stats['new_this_month'] ?? 0 }}</h4>
                                <small class="text-muted">New This Month</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0">
                        <i class="ri-time-line me-2 text-primary"></i>Recent Activities
                    </h6>
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-outline-primary">
                        View All Staff
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($recent_staff) && $recent_staff->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recent_staff as $staff)
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex align-items-center">
                                        @php
                                            $photoUrl = $staff->user->photo_path 
                                                ? Storage::url($staff->user->photo_path) 
                                                : 'https://ui-avatars.com/api/?name='.urlencode($staff->user->name ?? 'Staff').'&background=4f46e5&color=fff&size=40&rounded=true';
                                        @endphp
                                        <img src="{{ $photoUrl }}" alt="Staff" class="rounded-circle me-3" width="40" height="40">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-medium">{{ $staff->user->name ?? 'N/A' }}</h6>
                                            <small class="text-muted">{{ $staff->designation ?? 'N/A' }} • {{ $staff->department ?? 'N/A' }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success-subtle text-success">{{ $staff->status }}</span>
                                            <br>
                                            <small class="text-muted">{{ $staff->created_at ? $staff->created_at->diffForHumans() : 'N/A' }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ri-user-line display-6 text-muted"></i>
                            <h6 class="mt-3 mb-1">No Staff Members Yet</h6>
                            <p class="text-muted mb-3">Start by adding your first staff member.</p>
                            <a href="{{ route('admin.staff.enroll.step1') }}" class="btn btn-primary">
                                <i class="ri-add-line me-1"></i>Add First Staff Member
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .custom-shadow { 
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); 
        transition: all 0.3s ease;
    }
    .hover-card:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .icon-wrapper {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(79, 70, 229, 0.1);
        border-radius: 12px;
    }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1); }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1); }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1); }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1); }
</style>
@endpush
