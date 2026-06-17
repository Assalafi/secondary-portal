@extends('layouts.admin')

@section('title', 'Invoice Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Invoice Details</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.payments.fees-income') }}" class="text-muted text-decoration-none">Fees & Income</a>
                            </li>
                            <li class="breadcrumb-item active text-muted" aria-current="page">Invoice Details</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.payments.fees-income') }}" class="btn btn-outline-secondary">
                        <i class="ri-arrow-left-line"></i> Back
                    </a>
                    @if($invoice->status === 'Paid')
                        <a href="{{ route('admin.payments.invoice.receipt', $invoice->id) }}" class="btn btn-primary" target="_blank">
                            <i class="ri-printer-line"></i> Print Receipt
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Invoice Details -->
        <div class="col-lg-8">
            <div class="card custom-shadow rounded-3 bg-white border mb-4">
                <div class="card-header bg-transparent border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-semibold mb-0">Invoice Information</h6>
                        <span class="badge bg-{{ $invoice->status === 'Paid' ? 'success' : ($invoice->status === 'Pending' ? 'warning' : ($invoice->status === 'Partial' ? 'info' : 'danger')) }}-subtle text-{{ $invoice->status === 'Paid' ? 'success' : ($invoice->status === 'Pending' ? 'warning' : ($invoice->status === 'Partial' ? 'info' : 'danger')) }} px-3 py-2">
                            {{ $invoice->status }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Invoice Number</label>
                            <p class="fw-semibold mb-0">{{ $invoice->invoice_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Issue Date</label>
                            <p class="fw-semibold mb-0">{{ $invoice->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Academic Session</label>
                            <p class="fw-semibold mb-0">{{ $invoice->academicSession->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Term</label>
                            <p class="fw-semibold mb-0">{{ $invoice->term->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Due Date</label>
                            <p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</p>
                        </div>
                        @if($invoice->notes)
                            <div class="col-12">
                                <label class="text-muted small mb-1">Notes</label>
                                <p class="fw-semibold mb-0">{{ $invoice->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Invoice Items -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $metadata = is_string($invoice->metadata) ? json_decode($invoice->metadata, true) : ($invoice->metadata ?? []);
                                    $serviceName = $metadata['service_name'] ?? null;
                                    
                                    // Fallback to invoice items
                                    if (!$serviceName && $invoice->items && $invoice->items->isNotEmpty()) {
                                        $firstItem = $invoice->items->first();
                                        $serviceName = $firstItem->paymentSetup->payment_type ?? null;
                                    }
                                    
                                    // Final fallback
                                    $serviceName = $serviceName ?? 'Payment';
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $serviceName }}</div>
                                        <small class="text-muted">{{ $invoice->academicSession->name ?? 'N/A' }} - {{ $invoice->term->name ?? 'N/A' }}</small>
                                    </td>
                                    <td class="text-end fw-semibold">₦{{ number_format($invoice->total_amount, 2) }}</td>
                                </tr>
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <th>Total Amount</th>
                                    <th class="text-end">₦{{ number_format($invoice->total_amount, 2) }}</th>
                                </tr>
                                <tr>
                                    <th>Amount Paid</th>
                                    <th class="text-end text-success">₦{{ number_format($invoice->amount_paid, 2) }}</th>
                                </tr>
                                <tr class="fw-bold">
                                    <th>Balance Due</th>
                                    <th class="text-end {{ $invoice->balance > 0 ? 'text-danger' : 'text-success' }}">
                                        ₦{{ number_format($invoice->balance, 2) }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            @if($invoice->payments && $invoice->payments->count() > 0)
                <div class="card custom-shadow rounded-3 bg-white border">
                    <div class="card-header bg-transparent border-bottom">
                        <h6 class="fw-semibold mb-0">Payment History</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Reference</th>
                                        <th>Method</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y h:i A') : 'N/A' }}</td>
                                            <td>
                                                <span class="font-monospace text-muted small">{{ $payment->payment_reference }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary">{{ $payment->payment_method }}</span>
                                            </td>
                                            <td class="text-end fw-semibold text-success">₦{{ number_format($payment->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Student Info Sidebar -->
        <div class="col-lg-4">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-header bg-transparent border-bottom">
                    <h6 class="fw-semibold mb-0">Student Information</h6>
                </div>
                <div class="card-body p-4">
                    @php
                        $photoUrl = $invoice->student && $invoice->student->user && $invoice->student->user->photo_path
                            ? Storage::url($invoice->student->user->photo_path) 
                            : 'https://ui-avatars.com/api/?name='.urlencode($invoice->student->user->name ?? 'Student').'&background=4f46e5&color=fff&size=128&rounded=true';
                    @endphp
                    <div class="text-center mb-4">
                        <img src="{{ $photoUrl }}" alt="Student" class="rounded-circle border border-3 border-light shadow" width="120" height="120">
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small mb-1">Student Name</label>
                        <p class="fw-semibold mb-0">{{ $invoice->student->user->name ?? 'N/A' }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small mb-1">Student ID</label>
                        <p class="fw-semibold mb-0">{{ $invoice->student->student_id ?? 'N/A' }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small mb-1">Class</label>
                        <p class="fw-semibold mb-0">
                            {{ optional($invoice->student->classArm->schoolClass)->level ?? 'N/A' }}
                            @if($invoice->student->classArm)
                                - {{ $invoice->student->classArm->name }}
                            @endif
                        </p>
                    </div>

                    <div class="mb-0">
                        <label class="text-muted small mb-1">Email</label>
                        <p class="fw-semibold mb-0">{{ $invoice->student->user->email ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <style>
        .bg-success-subtle {
            background-color: rgba(16, 185, 129, 0.1) !important;
        }

        .bg-primary-subtle {
            background-color: rgba(79, 70, 229, 0.1) !important;
        }

        .bg-info-subtle {
            background-color: rgba(59, 130, 246, 0.1) !important;
        }

        .bg-warning-subtle {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }

        .bg-danger-subtle {
            background-color: rgba(220, 53, 69, 0.1) !important;
        }
    </style>
@endpush
