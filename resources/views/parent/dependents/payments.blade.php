@extends('layouts.parent')

@section('title', 'Payment')
@section('page-title', 'Payment')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.dependents.index') }}" class="text-decoration-none">My Dependents</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.dependents.show', $student->id) }}" class="text-decoration-none">{{ $student->user->name }}</a></li>
                <li class="breadcrumb-item active text-muted">Payment</li>
            </ol>
        </nav>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="paymentTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">
                    Payments
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                    History
                </button>
            </li>
        </ul>

        <div class="tab-content" id="paymentTabContent">
            <!-- Payments Tab (Pending) -->
            <div class="tab-pane fade show active" id="payments" role="tabpanel">
                @php
                    $pendingInvoices = $invoices->whereIn('status', ['Pending', 'Partial', 'Overdue']);
                @endphp

                @if($pendingInvoices->isEmpty())
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="ri-file-list-line" style="font-size: 48px; color: #ccc;"></i>
                            <h6 class="mt-3">No Pending Payments</h6>
                            <p class="text-muted small mb-0">All payments are up to date.</p>
                        </div>
                    </div>
                @else
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th>PAYMENTS</th>
                                            <th>AMOUNT</th>
                                            <th>STATUS</th>
                                            <th>ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingInvoices as $index => $invoice)
                                            @php
                                                $paymentTitle = 'Invoice #' . $invoice->invoice_number;
                                                if ($invoice->invoiceItems->count() > 0) {
                                                    $firstItem = $invoice->invoiceItems->first();
                                                    $paymentTitle = optional($firstItem->feeSetup)->payment_type ?? 'School Fees';
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $paymentTitle }}</td>
                                                <td>₦{{ number_format($invoice->balance ?? $invoice->total_amount, 2) }}</td>
                                                <td><span class="badge bg-warning text-dark">{{ $invoice->status }}</span></td>
                                                <td>
                                                    <a href="#" class="text-decoration-none">
                                                        Pay Now <i class="ri-arrow-right-line"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- History Tab (Paid) -->
            <div class="tab-pane fade" id="history" role="tabpanel">
                @php
                    $paidInvoices = $invoices->where('status', 'Paid');
                @endphp

                @if($paidInvoices->isEmpty())
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="ri-file-list-line" style="font-size: 48px; color: #ccc;"></i>
                            <h6 class="mt-3">No Payment History</h6>
                            <p class="text-muted small mb-0">No completed payments yet.</p>
                        </div>
                    </div>
                @else
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>DATE</th>
                                            <th>PAYMENTS</th>
                                            <th>AMOUNT</th>
                                            <th>REF NO</th>
                                            <th>ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paidInvoices as $invoice)
                                            @php
                                                $paymentTitle = 'Invoice #' . $invoice->invoice_number;
                                                if ($invoice->invoiceItems->count() > 0) {
                                                    $firstItem = $invoice->invoiceItems->first();
                                                    $paymentTitle = optional($firstItem->feeSetup)->payment_type ?? 'School Fees';
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $invoice->updated_at ? $invoice->updated_at->format('m/d/Y') : 'N/A' }}</td>
                                                <td>{{ $paymentTitle }}</td>
                                                <td>₦{{ number_format($invoice->amount_paid, 2) }}</td>
                                                <td>#{{ $invoice->invoice_number }}</td>
                                                <td>
                                                    <a href="#" class="text-decoration-none">
                                                        Receipt <i class="ri-download-line"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 2px solid transparent;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        color: #000;
        background: transparent;
        border-bottom: 2px solid #000;
    }
    .table th {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .table td {
        font-size: 14px;
        vertical-align: middle;
    }
</style>
@endpush
@endsection
