@extends('layouts.student')

@section('title', 'Invoice Details')
@section('page-title', 'Invoice Details')

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.payments.index') }}" class="text-decoration-none">Payments</a></li>
        <li class="breadcrumb-item active text-muted">{{ $invoice->invoice_number }}</li>
    </ol>
</nav>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Invoice #{{ $invoice->invoice_number }}</h6>
                    <span class="badge bg-{{ $invoice->status === 'Paid' ? 'success' : ($invoice->status === 'Overdue' ? 'danger' : 'warning') }} text-{{ $invoice->status === 'Paid' ? 'white' : 'dark' }}">
                        {{ $invoice->status }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                @if($invoice->invoiceItems && $invoice->invoiceItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->invoiceItems as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ optional($item->feeSetup)->payment_type ?? 'Fee Item' }}</td>
                                        <td class="text-end">&#8358;{{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td colspan="2">Total</td>
                                    <td class="text-end">&#8358;{{ number_format($invoice->total_amount, 2) }}</td>
                                </tr>
                                @if($invoice->amount_paid > 0)
                                    <tr>
                                        <td colspan="2">Amount Paid</td>
                                        <td class="text-end text-success">&#8358;{{ number_format($invoice->amount_paid, 2) }}</td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <td colspan="2">Balance</td>
                                        <td class="text-end text-danger">&#8358;{{ number_format($invoice->balance ?? ($invoice->total_amount - $invoice->amount_paid), 2) }}</td>
                                    </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No invoice items found.</p>
                @endif
            </div>
        </div>

        <!-- Payment History -->
        @if($invoice->payments && $invoice->payments->count() > 0)
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold">Payment History</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Reference</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : $payment->created_at->format('d M Y') }}</td>
                                        <td>{{ $payment->payment_method }}</td>
                                        <td><code>{{ $payment->reference ?? '-' }}</code></td>
                                        <td class="text-end">&#8358;{{ number_format($payment->amount, 2) }}</td>
                                        <td class="text-center"><span class="badge bg-success">{{ $payment->status }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Invoice Summary</h6>
                <div class="mb-2">
                    <small class="text-muted d-block">Session</small>
                    <span>{{ optional($invoice->academicSession)->name ?? 'N/A' }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Term</small>
                    <span>{{ optional($invoice->term)->name ?? 'N/A' }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Due Date</small>
                    <span>{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'N/A' }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Created</small>
                    <span>{{ $invoice->created_at->format('d M Y') }}</span>
                </div>

                @if($invoice->status === 'Paid')
                    <a href="{{ route('student.payments.receipt', $invoice->id) }}" class="btn btn-success w-100" target="_blank">
                        <i class="ri-download-line me-1"></i>Download Receipt
                    </a>
                @else
                    <a href="{{ route('student.payments.index') }}" class="btn btn-primary w-100">
                        <i class="ri-bank-card-line me-1"></i>Make Payment
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
