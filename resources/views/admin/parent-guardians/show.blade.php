@extends('layouts.admin')

@section('title', 'Parent Profile - ' . $parentGuardian->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.parent-guardians.overview') }}">Parent/Guardians</a></li>
                    <li class="breadcrumb-item active">{{ $parentGuardian->name }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 fw-bold mt-2">Parent/Guardian Profile</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.parent-guardians.index') }}" class="btn btn-outline-secondary">
                <i class="ri-arrow-left-line me-1"></i> Back to List
            </a>
            <a href="{{ route('admin.parent-guardians.edit', $parentGuardian->id) }}" class="btn btn-primary">
                <i class="ri-edit-line me-1"></i> Edit
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Column - Main Content -->
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="ri-user-3-line me-2 text-primary"></i>Personal Information
                    </h5>
                    <a href="{{ route('admin.parent-guardians.edit', $parentGuardian->id) }}" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="ri-edit-line"></i> Edit
                    </a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Full Name</label>
                            <p class="mb-0 fw-semibold">{{ $parentGuardian->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Email Address</label>
                            <p class="mb-0">
                                <i class="ri-mail-line me-1"></i>{{ $parentGuardian->email }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Phone Number</label>
                            <p class="mb-0">
                                <i class="ri-phone-line me-1"></i>{{ $parentGuardian->phone ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Occupation</label>
                            <p class="mb-0">{{ $parentGuardian->occupation ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Address</label>
                            <p class="mb-0">{{ $parentGuardian->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Linked Students (Dependents) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="ri-group-line me-2 text-primary"></i>
                        Linked Students ({{ $parentGuardian->dependents->count() }} Dependents)
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($parentGuardian->dependents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Admission No</th>
                                        <th>Class</th>
                                        <th>Relationship</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($parentGuardian->dependents as $dependent)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($dependent->photo_path)
                                                        <img src="{{ Storage::url($dependent->photo_path) }}" 
                                                             class="rounded-circle me-2" width="32" height="32" alt="">
                                                    @else
                                                        <div class="avatar-circle bg-primary text-white me-2" style="width: 32px; height: 32px; font-size: 0.7rem;">
                                                            {{ strtoupper(substr($dependent->full_name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-semibold">{{ $dependent->full_name }}</div>
                                                        <small class="text-muted">{{ $dependent->user->email ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $dependent->admission_no }}</td>
                                            <td>{{ optional($dependent->classArm->schoolClass)->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">
                                                    {{ $dependent->pivot->relationship ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $dependent->status === 'Active' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $dependent->status }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.students.show', $dependent->id) }}" 
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
                            <i class="ri-links-line" style="font-size: 3rem;"></i>
                            <p class="mt-2">No students linked</p>
                            <a href="{{ route('admin.parent-guardians.edit', $parentGuardian->id) }}" 
                               class="btn btn-sm btn-primary">
                                <i class="ri-add-line me-1"></i> Link Students
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="ri-money-dollar-circle-line me-2 text-success"></i>Recent Payments
                    </h5>
                    <a href="{{ route('admin.payments.fees-income') }}" class="text-decoration-none small">
                        View All <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentPayments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Student</th>
                                        <th>Reference</th>
                                        <th class="text-end">Amount</th>
                                        <th>Method</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPayments as $payment)
                                        <tr>
                                            <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                            <td>{{ optional($payment->invoice->student->user)->name ?? 'N/A' }}</td>
                                            <td>
                                                <small class="font-monospace">{{ $payment->payment_reference }}</small>
                                            </td>
                                            <td class="text-end fw-semibold">₦{{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                <span class="badge bg-secondary-subtle text-secondary">
                                                    {{ $payment->payment_method ?? 'N/A' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="ri-money-dollar-circle-line" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">No payment records</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Invoices -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="ri-file-list-3-line me-2 text-warning"></i>Recent Invoices
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($recentInvoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Student</th>
                                        <th>Date</th>
                                        <th class="text-end">Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentInvoices as $invoice)
                                        <tr>
                                            <td>
                                                <small class="font-monospace">{{ $invoice->invoice_number }}</small>
                                            </td>
                                            <td>{{ optional($invoice->student->user)->name ?? 'N/A' }}</td>
                                            <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                                            <td class="text-end">₦{{ number_format($invoice->total_amount, 2) }}</td>
                                            <td>
                                                @php
                                                    $badgeClass = match($invoice->status) {
                                                        'Paid' => 'bg-success',
                                                        'Partial' => 'bg-warning',
                                                        'Overdue' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $invoice->status }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="ri-file-list-3-line" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">No invoice records</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-bold">Overview</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Total Paid</span>
                            <span class="fw-bold text-success">₦{{ number_format($paymentStats['total_paid'], 2) }}</span>
                        </div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Pending</span>
                            <span class="fw-bold text-warning">₦{{ number_format($paymentStats['total_pending'], 2) }}</span>
                        </div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Total Invoices</span>
                            <span class="fw-bold">{{ $paymentStats['total_invoices'] }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Dependents</span>
                            <span class="fw-bold">{{ $parentGuardian->dependents->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Summary -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-bold">Attendance (This Month)</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Present</span>
                            <span class="text-success fw-bold">{{ $attendanceStats['present'] }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" 
                                 style="width: {{ $attendanceStats['present'] + $attendanceStats['absent'] > 0 ? ($attendanceStats['present'] / ($attendanceStats['present'] + $attendanceStats['absent'])) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Absent</span>
                            <span class="text-danger fw-bold">{{ $attendanceStats['absent'] }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" 
                                 style="width: {{ $attendanceStats['present'] + $attendanceStats['absent'] > 0 ? ($attendanceStats['absent'] / ($attendanceStats['present'] + $attendanceStats['absent'])) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Portal Access -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-bold">Portal Access</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Username (Email)</label>
                        <p class="mb-0">{{ $parentGuardian->email }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Last Login</label>
                        <p class="mb-0">
                            @if($parentGuardian->last_login_at)
                                {{ $parentGuardian->last_login_at->format('M d, Y H:i') }}
                                <br>
                                <small class="text-muted">({{ $parentGuardian->last_login_at->diffForHumans() }})</small>
                            @else
                                <span class="text-muted">Never logged in</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Account Status</label>
                        <p class="mb-0">
                            @if($parentGuardian->last_login_at && $parentGuardian->last_login_at >= now()->subDays(30))
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </p>
                    </div>
                    <form action="{{ route('admin.parent-guardians.reset-password', $parentGuardian->id) }}" 
                          method="POST" class="d-grid">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning btn-sm" 
                                onclick="return confirm('Reset password to default?')">
                            <i class="ri-key-line me-1"></i> Reset Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- Account Actions -->
            <div class="card border-0 shadow-sm mb-4 border-danger">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-bold text-danger">Danger Zone</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Deleting this parent/guardian will remove their portal access and unlink all students.
                    </p>
                    <form action="{{ route('admin.parent-guardians.destroy', $parentGuardian->id) }}" 
                          method="POST" class="d-grid">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" 
                                onclick="return confirm('Are you sure you want to delete this parent/guardian? This action cannot be undone.')">
                            <i class="ri-delete-bin-line me-1"></i> Delete Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .avatar-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: bold;
        }
    </style>
    @endpush
@endsection
