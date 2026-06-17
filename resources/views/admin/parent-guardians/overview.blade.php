@extends('layouts.admin')

@section('title', 'Parent/Guardian Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">Parent/Guardian Management</h1>
            <p class="text-muted mb-0">Manage parent and guardian accounts with portal access</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.parent-guardians.index') }}" class="btn btn-outline-primary">
                <i class="ri-list-check me-1"></i> View All
            </a>
            <a href="{{ route('admin.parent-guardians.create') }}" class="btn btn-primary">
                <i class="ri-add-line me-1"></i> Add Parent/Guardian
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2 small">Total Parents</p>
                            <h2 class="fw-bold mb-0">{{ number_format($stats['total_parents']) }}</h2>
                        </div>
                        <div class="text-primary">
                            <i class="ri-parent-line" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.parent-guardians.index') }}" class="text-decoration-none text-dark small mt-2 d-block">
                        View All <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2 small">Active (Last 30 Days)</p>
                            <h2 class="fw-bold mb-0">{{ number_format($stats['active_parents']) }}</h2>
                        </div>
                        <div class="text-success">
                            <i class="ri-user-follow-line" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <small class="text-muted">
                        {{ $stats['total_parents'] > 0 ? round(($stats['active_parents'] / $stats['total_parents']) * 100, 1) : 0 }}% activity rate
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2 small">Students Linked</p>
                            <h2 class="fw-bold mb-0">{{ number_format($stats['total_students_linked']) }}</h2>
                        </div>
                        <div class="text-info">
                            <i class="ri-links-line" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <small class="text-muted">Total dependents registered</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2 small">Pending Payments</p>
                            <h2 class="fw-bold mb-0">{{ number_format($stats['pending_payments']) }}</h2>
                        </div>
                        <div class="text-warning">
                            <i class="ri-money-dollar-circle-line" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.payments.fees-income') }}" class="text-decoration-none text-dark small mt-2 d-block">
                        View Payments <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Quick Actions</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('admin.parent-guardians.create') }}" class="text-decoration-none">
                                <div class="d-flex align-items-center p-3 bg-light rounded hover-effect">
                                    <i class="ri-user-add-line fs-3 text-primary me-3"></i>
                                    <div>
                                        <h6 class="mb-0">Add New Parent</h6>
                                        <small class="text-muted">Create portal account</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.parent-guardians.index') }}" class="text-decoration-none">
                                <div class="d-flex align-items-center p-3 bg-light rounded hover-effect">
                                    <i class="ri-list-check fs-3 text-success me-3"></i>
                                    <div>
                                        <h6 class="mb-0">View Directory</h6>
                                        <small class="text-muted">Browse all parents</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.students.index') }}" class="text-decoration-none">
                                <div class="d-flex align-items-center p-3 bg-light rounded hover-effect">
                                    <i class="ri-links-line fs-3 text-info me-3"></i>
                                    <div>
                                        <h6 class="mb-0">Link Students</h6>
                                        <small class="text-muted">Manage relationships</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Registrations -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="card-title mb-0 fw-bold">Recent Registrations</h5>
                    <a href="{{ route('admin.parent-guardians.index') }}" class="text-decoration-none small">
                        View All <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentParents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Parent/Guardian</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Dependents</th>
                                        <th>Registered</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentParents as $parent)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary text-white me-2">
                                                        {{ strtoupper(substr($parent->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $parent->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $parent->email }}</td>
                                            <td>{{ $parent->phone ?? '—' }}</td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">
                                                    {{ $parent->dependents_count }} {{ Str::plural('student', $parent->dependents_count) }}
                                                </span>
                                            </td>
                                            <td>{{ $parent->created_at->diffForHumans() }}</td>
                                            <td>
                                                @if($parent->last_login_at && $parent->last_login_at >= now()->subDays(30))
                                                    <span class="badge bg-success-subtle text-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.parent-guardians.show', $parent->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="ri-eye-line"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="ri-parent-line" style="font-size: 4rem;"></i>
                            <p class="mt-3">No parent registrations yet</p>
                            <a href="{{ route('admin.parent-guardians.create') }}" class="btn btn-primary">
                                <i class="ri-add-line me-1"></i> Add First Parent
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .hover-effect {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .hover-effect:hover {
            background-color: #e9ecef !important;
            transform: translateY(-2px);
        }
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
    </style>
    @endpush
@endsection
