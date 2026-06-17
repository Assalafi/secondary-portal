@extends('layouts.admin')

@section('title', 'Admission Applications')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Admission Applications</h4>
            <p class="text-muted small mb-0">Review and manage student admission applications</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.admissions.index') }}">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold">Search</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Application number, name, email..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="Submitted" {{ request('status') === 'Submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="Under Review" {{ request('status') === 'Under Review' ? 'selected' : '' }}>Under Review</option>
                            <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="Pending Payment" {{ request('status') === 'Pending Payment' ? 'selected' : '' }}>Pending Payment</option>
                            <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-search-line me-1"></i> Search
                        </button>
                        <a href="{{ route('admin.admissions.index') }}" class="btn btn-outline-secondary">
                            <i class="ri-refresh-line me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($applications->isEmpty())
                <div class="text-center py-5">
                    <i class="ri-file-list-line" style="font-size: 64px; color: #ccc;"></i>
                    <h5 class="mt-3 mb-2">No Applications Found</h5>
                    <p class="text-muted">No admission applications match your search criteria</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3">Application No.</th>
                                <th class="py-3">Applicant Name</th>
                                <th class="py-3">Proposed Class</th>
                                <th class="py-3">Parent</th>
                                <th class="py-3">Date</th>
                                <th class="py-3">Status</th>
                                <th class="py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $application)
                                <tr>
                                    <td class="px-4 py-3">
                                        <span class="fw-semibold">{{ $application->application_number }}</span>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-semibold">{{ $application->full_name }}</div>
                                        <small class="text-muted">{{ $application->gender }} • {{ $application->date_of_birth?->format('M d, Y') }}</small>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-primary-subtle text-primary">
                                            {{ $application->proposedClass->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-semibold">{{ $application->parent->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $application->guardian_phone }}</small>
                                    </td>
                                    <td class="py-3">
                                        <small>{{ $application->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge {{ $application->getStatusBadgeClass() }}">
                                            {{ $application->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.admissions.show', $application->id) }}">
                                                        <i class="ri-eye-line me-2"></i>View Details
                                                    </a>
                                                </li>
                                                @if(in_array($application->status, ['Submitted', 'Under Review']))
                                                    <li>
                                                        <a class="dropdown-item text-success" href="{{ route('admin.admissions.review', $application->id) }}">
                                                            <i class="ri-check-line me-2"></i>Review
                                                        </a>
                                                    </li>
                                                @endif
                                                @if(in_array($application->status, ['Draft', 'Pending Payment', 'Rejected']))
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('admin.admissions.destroy', $application->id) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" 
                                                                    onclick="return confirm('Delete this application?')">
                                                                <i class="ri-delete-bin-line me-2"></i>Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($applications->hasPages())
                    <div class="card-footer bg-white border-top py-3">
                        {{ $applications->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
