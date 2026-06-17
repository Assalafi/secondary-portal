@extends('layouts.parent')

@section('title', 'Payments')
@section('page-title', 'Payment')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active text-muted">Payment</li>
            </ol>
        </nav>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Outstanding Balance (All Dependents)</p>
                        <h3 class="mb-0 fw-bold">₦{{ number_format($summary['total_pending'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Pending Payments</p>
                        <h3 class="mb-0 fw-bold">{{ $summary['pending_count'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Paid This Year</p>
                        <h3 class="mb-0 fw-bold">₦{{ number_format($summary['total_paid'], 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs mb-3" id="paymentTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="payments-tab" data-bs-toggle="tab" data-bs-target="#paymentsTab" type="button" role="tab">
                    Payments
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#historyTab" type="button" role="tab">
                    History
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="paymentTabContent">
            <!-- Payments Tab (Pending) -->
            <div class="tab-pane fade show active" id="paymentsTab" role="tabpanel">
                <!-- Action Buttons -->
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary w-100" id="paySelectedBtn" disabled>
                            <i class="ri-wallet-line me-2"></i>Pay Selected
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#schoolFeesModal">
                            <i class="ri-bank-card-line me-2"></i>Pay School Fees
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#otherServicesModal">
                            <i class="ri-service-line me-2"></i>Other Services
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="ri-filter-line me-2"></i>Filter Payments
                            @if($filterSession || $filterTerm || $filterStudent)
                                <span class="badge bg-primary ms-2">Active</span>
                            @endif
                        </button>
                    </div>
                    @if($filterSession || $filterTerm || $filterStudent)
                    <div class="col-md-3">
                        <a href="{{ route('parent.payments.index') }}" class="btn btn-outline-danger w-100">
                            <i class="ri-close-line me-2"></i>Clear Filters
                        </a>
                    </div>
                    @endif
                </div>

                @if($payments['pending']->isEmpty())
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="ri-check-double-line" style="font-size: 64px; color: #28a745;"></i>
                            <h5 class="mt-3 mb-2">All Payments Up to Date!</h5>
                            <p class="text-muted">You have no pending payments at this time.</p>
                        </div>
                    </div>
                @else
                    <!-- Pending Payments List Grouped by Student -->
                    @foreach($dependents as $dependent)
                        @php
                            $dependentInvoices = $payments['pending']->where('student_id', $dependent->id);
                        @endphp
                        
                        @if($dependentInvoices->count() > 0)
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body">
                                    <h6 class="mb-3 fw-bold">{{ $dependent->user->name }} - {{ $dependent->classArm ? (optional($dependent->classArm->schoolClass)->name . ' ' . $dependent->classArm->name) : 'N/A' }}</h6>
                                    
                                    @foreach($dependentInvoices as $invoice)
                                        @php
                                            $paymentTitle = 'Invoice #' . $invoice->invoice_number;
                                            if ($invoice->invoiceItems->count() > 0) {
                                                $firstItem = $invoice->invoiceItems->first();
                                                $paymentTitle = optional($firstItem->feeSetup)->payment_type ?? 'School Fees';
                                            }
                                        @endphp
                                        <div class="payment-item border-bottom py-3">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <div class="form-check">
                                                        <input class="form-check-input payment-checkbox" type="checkbox" 
                                                               value="{{ $invoice->id }}" 
                                                               data-amount="{{ $invoice->balance ?? $invoice->total_amount }}"
                                                               data-student="{{ $dependent->user->name }}"
                                                               data-title="{{ $paymentTitle }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <p class="mb-0 fw-bold">{{ $paymentTitle }}</p>
                                                </div>
                                                <div class="col-md-2">
                                                    <p class="text-muted small mb-0">Amount</p>
                                                    <p class="mb-0">₦{{ number_format($invoice->balance ?? $invoice->total_amount, 2) }}</p>
                                                </div>
                                                <div class="col-md-2">
                                                    <p class="text-muted small mb-0">Due Date</p>
                                                    <p class="mb-0">{{ $invoice->due_date ? $invoice->due_date->format('d/M/Y') : 'N/A' }}</p>
                                                </div>
                                                <div class="col-md-1">
                                                    <p class="text-muted small mb-0">Term</p>
                                                    <p class="mb-0">{{ optional($invoice->term)->name ?? 'N/A' }}</p>
                                                </div>
                                                <div class="col-md-1">
                                                    <span class="badge bg-warning text-dark">{{ $invoice->status }}</span>
                                                </div>
                                                <div class="col-md-2 text-end">
                                                    <button class="btn btn-sm btn-primary" 
                                                            onclick="payNow({{ $invoice->id }}, '{{ $invoice->invoice_number }}', {{ $invoice->balance ?? $invoice->total_amount }})">
                                                        <i class="ri-bank-card-line me-1"></i>Pay Now
                                                    </button>
                                                    @if($invoice->invoice_number && $invoice->invoice_number !== 'N/A')
                                                    <button class="btn btn-sm btn-outline-info mt-1" 
                                                            onclick="verifyPaymentStatus({{ $invoice->id }}, '{{ $invoice->invoice_number }}')"
                                                            title="Check if payment was made outside the portal">
                                                        <i class="ri-refresh-line me-1"></i>Verify Status
                                                    </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>

            <!-- History Tab (Paid) -->
            <div class="tab-pane fade" id="historyTab" role="tabpanel">
                <!-- Active Filters Display -->
                @if($filterSession || $filterTerm || $filterStudent)
                <div class="alert alert-info mb-3">
                    <strong><i class="ri-filter-line me-2"></i>Active Filters:</strong>
                    @if($filterSession)
                        <span class="badge bg-primary ms-2">Session: {{ $sessions->find($filterSession)->name ?? 'N/A' }}</span>
                    @endif
                    @if($filterTerm)
                        <span class="badge bg-primary ms-2">Term: {{ $terms->find($filterTerm)->name ?? 'N/A' }}</span>
                    @endif
                    @if($filterStudent)
                        <span class="badge bg-primary ms-2">Student: {{ $dependents->find($filterStudent)->user->name ?? 'N/A' }}</span>
                    @endif
                    <a href="{{ route('parent.payments.index') }}" class="btn btn-sm btn-outline-danger ms-3">Clear All</a>
                </div>
                @endif

                @if($payments['history']->isEmpty())
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="ri-file-list-line" style="font-size: 64px; color: #ccc;"></i>
                            <h5 class="mt-3 mb-2">No Payment History</h5>
                            <p class="text-muted">You haven't made any payments yet.</p>
                        </div>
                    </div>
                @else
                    <!-- Payment History List Grouped by Student -->
                    @foreach($dependents as $dependent)
                        @php
                            $dependentHistory = $payments['history']->where('student_id', $dependent->id);
                        @endphp
                        
                        @if($dependentHistory->count() > 0)
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body">
                                    <h6 class="mb-3 fw-bold">{{ $dependent->user->name }} - {{ $dependent->classArm ? (optional($dependent->classArm->schoolClass)->name . ' ' . $dependent->classArm->name) : 'N/A' }}</h6>
                                    
                                    @foreach($dependentHistory as $invoice)
                                        @php
                                            $paymentTitle = 'Invoice #' . $invoice->invoice_number;
                                            if ($invoice->invoiceItems->count() > 0) {
                                                $firstItem = $invoice->invoiceItems->first();
                                                $paymentTitle = optional($firstItem->feeSetup)->payment_type ?? 'School Fees';
                                            }
                                        @endphp
                                        <div class="payment-item border-bottom py-3">
                                            <div class="row align-items-center">
                                                <div class="col-md-2">
                                                    <p class="mb-0 fw-bold">{{ $paymentTitle }}</p>
                                                </div>
                                                <div class="col-md-2">
                                                    <p class="text-muted small mb-0">Amount</p>
                                                    <p class="mb-0">₦{{ number_format($invoice->amount_paid, 2) }}</p>
                                                </div>
                                                <div class="col-md-2">
                                                    <p class="text-muted small mb-0">Date Paid</p>
                                                    <p class="mb-0">{{ $invoice->updated_at->format('d/M/Y') }}</p>
                                                </div>
                                                <div class="col-md-2">
                                                    <p class="text-muted small mb-0">Term</p>
                                                    <p class="mb-0">{{ optional($invoice->term)->name ?? 'N/A' }}</p>
                                                </div>
                                                <div class="col-md-2">
                                                    <span class="badge bg-success">{{ $invoice->status }}</span>
                                                </div>
                                                <div class="col-md-2 text-end">
                                                    <a href="{{ route('parent.payments.download-receipt', $invoice->id) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       target="_blank">
                                                        <i class="ri-file-text-line me-1"></i>View Receipt
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
    </div>
</div>

<!-- School Fees Payment Modal -->
<div class="modal fade" id="schoolFeesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pay School Fees</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="schoolFeesForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Student(s)</label>
                        <div class="student-list">
                            @foreach($dependents as $dependent)
                                <div class="form-check">
                                    <input class="form-check-input student-select" type="checkbox" 
                                           value="{{ $dependent->id }}" 
                                           id="student{{ $dependent->id }}"
                                           data-name="{{ $dependent->user->name }}">
                                    <label class="form-check-label w-100" for="student{{ $dependent->id }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>{{ $dependent->user->name }}</span>
                                            <small class="text-muted">{{ $dependent->classArm ? (optional($dependent->classArm->schoolClass)->name . ' ' . $dependent->classArm->name) : 'N/A' }}</small>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Session(s)</label>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input session-select" type="checkbox" value="2024/2025" id="session1">
                                    <label class="form-check-label" for="session1">2024/2025</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input session-select" type="checkbox" value="2023/2024" id="session2">
                                    <label class="form-check-label" for="session2">2023/2024</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input session-select" type="checkbox" value="2022/2023" id="session3">
                                    <label class="form-check-label" for="session3">2022/2023</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Term(s)</label>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input term-select" type="checkbox" value="1st term" id="term1">
                                    <label class="form-check-label" for="term1">1st Term</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input term-select" type="checkbox" value="2nd term" id="term2">
                                    <label class="form-check-label" for="term2">2nd Term</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input term-select" type="checkbox" value="3rd term" id="term3">
                                    <label class="form-check-label" for="term3">3rd Term</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info" id="schoolFeesSummary" style="display: none;">
                        <h6 class="mb-2">Payment Summary:</h6>
                        <div id="summaryContent"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-secondary" id="proceedSchoolFees" disabled>Proceed to Payment</button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Confirmation Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="paymentSummary"></div>
                
                <div class="mt-3">
                    <h6>Payment Gateway</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gateway" id="remita" value="remita" checked>
                        <label class="form-check-label" for="remita">
                            <strong>Remita</strong> - Pay via Remita Payment Gateway
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmPayment">
                    <i class="ri-secure-payment-line me-2"></i>Pay with Remita
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Other Services Payment Modal -->
<div class="modal fade" id="otherServicesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pay for Other Services</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="otherServicesForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Students</label>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            @foreach($dependents as $dependent)
                                <div class="form-check mb-2">
                                    <input class="form-check-input other-student-checkbox" type="checkbox" 
                                           value="{{ $dependent->id }}" 
                                           id="otherStudent{{ $dependent->id }}">
                                    <label class="form-check-label" for="otherStudent{{ $dependent->id }}">
                                        {{ $dependent->user->name }} - {{ $dependent->classArm ? (optional($dependent->classArm->schoolClass)->name . ' ' . $dependent->classArm->name) : 'N/A' }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted">Select one or more students</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Service</label>
                        <select class="form-select" name="service" id="serviceSelect" required>
                            <option value="">Choose service...</option>
                            @foreach($otherServices as $service)
                                <option value="{{ $service->id }}" data-amount="{{ $service->amount }}" data-name="{{ $service->payment_type }}">
                                    {{ $service->payment_type }} - ₦{{ number_format($service->amount, 2) }}
                                </option>
                            @endforeach
                            <option value="custom" data-amount="" data-name="Other">Others (Custom Service)</option>
                        </select>
                        <small class="text-muted">Amount will be auto-populated based on selected service</small>
                    </div>

                    <div class="mb-3" id="customServiceDiv" style="display: none;">
                        <label class="form-label fw-bold">Custom Service Name</label>
                        <input type="text" class="form-control" id="customServiceName" placeholder="Enter service name (e.g., Extra Classes)">
                        <small class="text-muted">Specify the service you're paying for</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount per Student (₦)</label>
                        <input type="number" class="form-control" id="otherServiceAmount" name="amount" placeholder="0.00" step="0.01" required>
                        <small class="text-muted">This amount will be charged for each selected student</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Total Amount (₦)</label>
                        <input type="text" class="form-control fw-bold" id="otherServiceTotal" readonly value="₦0.00">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description (Optional)</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Add any additional details..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="proceedOtherServices">
                    <i class="ri-secure-payment-line me-2"></i>Proceed to Payment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="GET" action="{{ route('parent.payments.index') }}">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri-filter-line me-2"></i>Filter Payments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Academic Session</label>
                        <select class="form-select" name="session">
                            <option value="">All Sessions</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session->id }}" {{ $filterSession == $session->id ? 'selected' : '' }}>
                                    {{ $session->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Term</label>
                        <select class="form-select" name="term">
                            <option value="">All Terms</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ $filterTerm == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Student</label>
                        <select class="form-select" name="student">
                            <option value="">All Students</option>
                            @foreach($dependents as $dependent)
                                <option value="{{ $dependent->id }}" {{ $filterStudent == $dependent->id ? 'selected' : '' }}>
                                    {{ $dependent->user->name }} - {{ $dependent->classArm ? (optional($dependent->classArm->schoolClass)->name . ' ' . $dependent->classArm->name) : 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <i class="ri-information-line me-1"></i>
                            Filters will be applied to both Pending Payments and Payment History tabs.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="{{ route('parent.payments.index') }}" class="btn btn-outline-danger">Clear Filters</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-filter-line me-1"></i>Apply Filters
                    </button>
                </div>
            </form>
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
    .payment-item:last-child {
        border-bottom: none !important;
    }
    
    /* Mobile-First Optimizations */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0.5rem;
        }
        
        .form-check {
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .form-check:last-child {
            border-bottom: none;
        }
        
        .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
            margin-top: 0.25rem;
        }
        
        .form-check-label {
            font-size: 0.95rem;
            padding-left: 0.5rem;
        }
        
        .btn {
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }
        
        .modal-footer .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
        
        .modal-footer {
            flex-direction: column;
        }
    }
</style>
@endpush

@push('scripts')
<!-- Remita Payment Widget -->
<script src="{{ config('remita.widget_url') }}"></script>

<script>
let selectedPayments = [];
let paymentInitiated = false; // Track if payment was initiated
let remitaWidgetOpen = false; // Track if Remita widget is currently open
let widgetAboutToOpen = false; // Track if we're about to open the widget

document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.payment-checkbox');
    const paySelectedBtn = document.getElementById('paySelectedBtn');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedPayments();
        });
    });
    
    // Pay Selected button
    paySelectedBtn.addEventListener('click', function() {
        if (selectedPayments.length > 0) {
            showPaymentModal(selectedPayments);
        }
    });
    
    // Service select change
    document.getElementById('serviceSelect')?.addEventListener('change', function() {
        const customDiv = document.getElementById('customServiceDiv');
        if (this.value === 'other') {
            customDiv.style.display = 'block';
        } else {
            customDiv.style.display = 'none';
        }
    });
    
    // School fees form
    document.querySelectorAll('.student-select, .term-select, .session-select').forEach(input => {
        input.addEventListener('change', updateSchoolFeesSummary);
    });
    
    // Proceed buttons
    document.getElementById('proceedSchoolFees')?.addEventListener('click', function() {
        const students = Array.from(document.querySelectorAll('.student-select:checked'));
        const terms = Array.from(document.querySelectorAll('.term-select:checked'));
        const sessions = Array.from(document.querySelectorAll('.session-select:checked'));
        
        if (students.length === 0) {
            alert('Please select at least one student');
            return;
        }
        if (terms.length === 0) {
            alert('Please select at least one term');
            return;
        }
        if (sessions.length === 0) {
            alert('Please select at least one session');
            return;
        }
        
        initiateRemitaPayment('school_fees', {
            students: students.map(s => s.value),
            terms: terms.map(t => t.value),
            sessions: sessions.map(s => s.value)
        });
    });
    
    // Handle service selection - auto-populate amount
    document.getElementById('serviceSelect')?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const amount = selectedOption.getAttribute('data-amount');
        const amountField = document.getElementById('otherServiceAmount');
        const customServiceDiv = document.getElementById('customServiceDiv');
        const customServiceName = document.getElementById('customServiceName');
        
        // Check if "Others" (custom) is selected
        if (this.value === 'custom') {
            customServiceDiv.style.display = 'block';
            amountField.value = '';
            amountField.readOnly = false;
            customServiceName.required = true;
        } else {
            customServiceDiv.style.display = 'none';
            amountField.readOnly = true;
            customServiceName.required = false;
            
            if (amount) {
                amountField.value = parseFloat(amount).toFixed(2);
            } else {
                amountField.value = '';
            }
        }
        
        updateOtherServicesTotal();
    });
    
    // Update total when students are selected/deselected
    document.querySelectorAll('.other-student-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateOtherServicesTotal);
    });
    
    function updateOtherServicesTotal() {
        const selectedStudents = document.querySelectorAll('.other-student-checkbox:checked').length;
        const amountPerStudent = parseFloat(document.getElementById('otherServiceAmount').value) || 0;
        const total = selectedStudents * amountPerStudent;
        
        document.getElementById('otherServiceTotal').value = '₦' + total.toLocaleString('en-NG', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    
    document.getElementById('proceedOtherServices')?.addEventListener('click', function() {
        // Get selected students
        const selectedStudents = Array.from(document.querySelectorAll('.other-student-checkbox:checked'))
            .map(cb => cb.value);
        
        if (selectedStudents.length === 0) {
            alert('Please select at least one student');
            return;
        }
        
        const serviceSelect = document.getElementById('serviceSelect');
        if (!serviceSelect.value) {
            alert('Please select a service');
            return;
        }
        
        const amount = document.getElementById('otherServiceAmount').value;
        if (!amount || parseFloat(amount) <= 0) {
            alert('Please enter a valid amount');
            return;
        }
        
        const description = document.querySelector('#otherServicesForm textarea[name="description"]').value;
        let serviceId = serviceSelect.value;
        let serviceName;
        
        // Check if custom service
        if (serviceId === 'custom') {
            const customServiceName = document.getElementById('customServiceName').value.trim();
            if (!customServiceName) {
                alert('Please enter a service name');
                return;
            }
            serviceName = customServiceName;
            serviceId = 'custom'; // Keep as custom for backend handling
        } else {
            serviceName = serviceSelect.options[serviceSelect.selectedIndex].getAttribute('data-name');
        }
        
        const data = {
            students: selectedStudents,
            service_id: serviceId,
            service_name: serviceName,
            amount_per_student: parseFloat(amount),
            description: description,
            is_custom: serviceId === 'custom'
        };
        
        initiateRemitaPayment('other_services', data);
    });
    
    document.getElementById('confirmPayment')?.addEventListener('click', function() {
        initiateRemitaPayment('invoices', selectedPayments);
    });
    
    // Refresh page when payment modal closes (only if payment was initiated and Remita widget is not open)
    const paymentModal = document.getElementById('paymentModal');
    if (paymentModal) {
        paymentModal.addEventListener('hidden.bs.modal', function () {
            if (paymentInitiated && !widgetAboutToOpen) {
                // Wait 3 seconds, then check if widget is still open before refreshing
                console.log('Payment modal closed, waiting 3 seconds before checking widget status...');
                setTimeout(() => {
                    if (!remitaWidgetOpen && !widgetAboutToOpen) {
                        console.log('Remita widget confirmed closed, refreshing page...');
                        location.reload();
                    } else {
                        console.log('Remita widget still open or about to open, skipping refresh');
                    }
                }, 3000); // Wait 3 seconds
            } else if (widgetAboutToOpen) {
                console.log('Widget about to open, skipping refresh timer');
            }
        });
    }
});

function updateSelectedPayments() {
    const checkboxes = document.querySelectorAll('.payment-checkbox:checked');
    const paySelectedBtn = document.getElementById('paySelectedBtn');
    
    selectedPayments = Array.from(checkboxes).map(cb => ({
        id: cb.value,
        amount: parseFloat(cb.dataset.amount),
        student: cb.dataset.student,
        title: cb.dataset.title
    }));
    
    paySelectedBtn.disabled = selectedPayments.length === 0;
    paySelectedBtn.innerHTML = selectedPayments.length > 0 
        ? `<i class="ri-wallet-line me-2"></i>Pay Selected (${selectedPayments.length})`
        : `<i class="ri-wallet-line me-2"></i>Pay Selected`;
}

function showPaymentModal(payments) {
    const total = payments.reduce((sum, p) => sum + p.amount, 0);
    let html = '<h6>Selected Payments:</h6><ul class="list-unstyled">';
    
    payments.forEach(p => {
        html += `<li class="mb-2">
            <strong>${p.student}</strong> - ${p.title}<br>
            <span class="text-muted">Amount: ₦${p.amount.toLocaleString()}</span>
        </li>`;
    });
    
    html += `</ul><hr><h5>Total Amount: ₦${total.toLocaleString()}</h5>`;
    
    document.getElementById('paymentSummary').innerHTML = html;
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}

async function updateSchoolFeesSummary() {
    const students = document.querySelectorAll('.student-select:checked');
    const terms = document.querySelectorAll('.term-select:checked');
    const sessions = document.querySelectorAll('.session-select:checked');
    
    // Show summary if at least students are selected
    if (students.length > 0) {
        let html = '<ul class="mb-2">';
        html += `<li><strong>Students:</strong> ${students.length} selected`;
        
        // List selected students with their names
        if (students.length > 0) {
            html += '<ul class="mt-1 small">';
            students.forEach(s => {
                const label = document.querySelector(`label[for="${s.id}"]`);
                const name = s.dataset.name || 'Unknown';
                html += `<li>${name}</li>`;
            });
            html += '</ul>';
        }
        html += `</li>`;
        
        html += `<li><strong>Terms:</strong> ${terms.length} selected</li>`;
        html += `<li><strong>Sessions:</strong> ${sessions.length} selected</li>`;
        
        if (terms.length > 0 && sessions.length > 0) {
            html += `<li><strong>Total Payments:</strong> ${students.length * terms.length * sessions.length}</li>`;
        }
        html += '</ul>';
        
        document.getElementById('summaryContent').innerHTML = html;
        document.getElementById('schoolFeesSummary').style.display = 'block';
    }
    
    // Only calculate when all three are selected
    if (students.length > 0 && terms.length > 0 && sessions.length > 0) {
        // Fetch estimated amount from backend
        const studentsData = Array.from(students).map(s => s.value);
        const termsData = Array.from(terms).map(t => t.value);
        const sessionsData = Array.from(sessions).map(s => s.value);
        
        try {
            const response = await fetch('{{ route("parent.payments.calculate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    students: studentsData,
                    terms: termsData,
                    sessions: sessionsData
                })
            });
            
            const result = await response.json();
            
            let html = '<ul class="mb-2">';
            html += `<li><strong>Students:</strong> ${students.length} selected</li>`;
            html += `<li><strong>Terms:</strong> ${terms.length} selected</li>`;
            html += `<li><strong>Sessions:</strong> ${sessions.length} selected</li>`;
            html += `<li><strong>Total Payments:</strong> ${students.length * terms.length * sessions.length}</li>`;
            html += '</ul>';
            
            const proceedBtn = document.getElementById('proceedSchoolFees');
            
            if (result.success && result.amount) {
                // Show breakdown by student if available
                if (result.breakdown && result.breakdown.length > 0) {
                    html += `<hr class="my-2"><h6 class="mb-2">Breakdown by Student:</h6>`;
                    html += '<div class="small">';
                    result.breakdown.forEach(item => {
                        html += `<div class="d-flex justify-content-between mb-1">`;
                        html += `<span>${item.student} <span class="text-muted">(${item.class})</span></span>`;
                        html += `<span class="fw-bold">₦${parseFloat(item.amount).toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`;
                        html += `</div>`;
                    });
                    html += '</div>';
                }
                html += `<hr class="my-2"><h5 class="mb-0 text-primary">Total Amount: ₦${parseFloat(result.amount).toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h5>`;
                
                // Enable proceed button
                proceedBtn.disabled = false;
                proceedBtn.classList.remove('btn-secondary');
                proceedBtn.classList.add('btn-primary');
            } else if (result.existing_invoices && result.existing_invoices.length > 0) {
                // Show existing invoices error - clear previous summary and show prominent message
                html = '<div class="mb-3">';
                html += '<ul class="mb-2">';
                html += `<li><strong>Students:</strong> ${students.length} selected</li>`;
                html += `<li><strong>Terms:</strong> ${terms.length} selected</li>`;
                html += `<li><strong>Sessions:</strong> ${sessions.length} selected</li>`;
                html += '</ul>';
                html += '</div>';
                
                html += `<div class="alert alert-danger mb-0">`;
                html += `<h6 class="alert-heading mb-2"><i class="ri-error-warning-line"></i> Existing Invoices Found</h6>`;
                html += `<p class="mb-2 small">Cannot proceed - the following students already have invoices for the selected period:</p>`;
                html += `<div class="small">`;
                result.existing_invoices.forEach(invoice => {
                    html += `<div class="mb-2 p-2 bg-light border border-warning rounded">`;
                    html += `<div class="d-flex justify-content-between align-items-start">`;
                    html += `<div>`;
                    html += `<strong class="text-dark">${invoice.student}</strong><br>`;
                    html += `<small class="text-muted"><i class="ri-calendar-line"></i> ${invoice.session} - ${invoice.term}</small><br>`;
                    html += `<small><strong>Invoice:</strong> <code>${invoice.invoice_number}</code></small>`;
                    html += `</div>`;
                    html += `<span class="badge bg-${invoice.status === 'Paid' ? 'success' : 'warning'}">${invoice.status}</span>`;
                    html += `</div>`;
                    html += `</div>`;
                });
                html += `</div>`;
                html += `<hr class="my-2">`;
                html += `<p class="mb-0 small"><i class="ri-information-line"></i> <strong>What to do:</strong></p>`;
                html += `<ul class="mb-0 small">`;
                html += `<li>For <strong>Pending</strong> invoices: Go to the <strong>Payments</strong> tab and click "Pay Now"</li>`;
                html += `<li>For <strong>Paid</strong> invoices: Check the <strong>History</strong> tab for payment receipt</li>`;
                html += `<li>If you need assistance, contact the school administrator</li>`;
                html += `</ul>`;
                html += `</div>`;
                
                // Disable proceed button
                proceedBtn.disabled = true;
                proceedBtn.classList.remove('btn-primary');
                proceedBtn.classList.add('btn-secondary');
            } else if (result.message) {
                html += `<hr class="my-2">`;
                html += `<div class="alert alert-warning mb-0">`;
                html += `<i class="ri-alert-line"></i> ${result.message}`;
                html += `</div>`;
                
                // Disable proceed button
                proceedBtn.disabled = true;
                proceedBtn.classList.remove('btn-primary');
                proceedBtn.classList.add('btn-secondary');
            }
            
            document.getElementById('summaryContent').innerHTML = html;
            document.getElementById('schoolFeesSummary').style.display = 'block';
        } catch (error) {
            console.error('Error calculating amount:', error);
            
            const proceedBtn = document.getElementById('proceedSchoolFees');
            
            let html = '<ul class="mb-0">';
            html += `<li><strong>Students:</strong> ${students.length} selected</li>`;
            html += `<li><strong>Terms:</strong> ${terms.length} selected</li>`;
            html += `<li><strong>Sessions:</strong> ${sessions.length} selected</li>`;
            html += `<li><strong>Total Payments:</strong> ${students.length * terms.length * sessions.length}</li>`;
            html += '</ul>';
            html += `<hr class="my-2">`;
            html += `<div class="alert alert-danger mb-0">`;
            html += `<i class="ri-error-warning-line"></i> An error occurred while calculating fees. Please try again.`;
            html += `</div>`;
            
            document.getElementById('summaryContent').innerHTML = html;
            document.getElementById('schoolFeesSummary').style.display = 'block';
            
            // Disable proceed button on error
            proceedBtn.disabled = true;
            proceedBtn.classList.remove('btn-primary');
            proceedBtn.classList.add('btn-secondary');
        }
    } else {
        document.getElementById('schoolFeesSummary').style.display = 'none';
        // Reset button state when no selection
        const proceedBtn = document.getElementById('proceedSchoolFees');
        if (proceedBtn) {
            proceedBtn.disabled = true;
            proceedBtn.classList.remove('btn-primary');
            proceedBtn.classList.add('btn-secondary');
        }
    }
}

function payNow(invoiceId, invoiceNumber, amount) {
    // Check if invoice already has an RRR (invoice_number is the RRR)
    if (invoiceNumber && invoiceNumber.length > 0 && invoiceNumber !== 'N/A') {
        // Invoice is pending with existing RRR - open Remita widget directly
        console.log('Invoice already has RRR:', invoiceNumber, 'Amount:', amount);
        
        // Mark that payment was initiated
        paymentInitiated = true;
        widgetAboutToOpen = true; // Prevent refresh when modal closes
        
        // Show loading modal
        const loadingModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        document.getElementById('paymentSummary').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Opening payment widget...</p>
                <p class="text-muted small">RRR: ${invoiceNumber}</p>
            </div>
        `;
        document.querySelector('#paymentModal .modal-footer').style.display = 'none';
        loadingModal.show();
        
        // Close modal and open Remita widget
        setTimeout(() => {
            loadingModal.hide();
            openRemitaWidget(invoiceNumber, amount, invoiceId);
        }, 500);
    } else {
        // No RRR yet - need to generate new RRR
        const checkbox = document.querySelector(`.payment-checkbox[value="${invoiceId}"]`);
        if (checkbox) {
            const payment = [{
                id: invoiceId,
                amount: parseFloat(checkbox.dataset.amount),
                student: checkbox.dataset.student,
                title: checkbox.dataset.title
            }];
            
            initiateRemitaPayment('invoices', payment);
        }
    }
}

function openRemitaWidget(rrr, amount, invoiceId) {
    console.log('Opening Remita widget with RRR:', rrr, 'Amount:', amount);
    
    // Get merchant config
    const merchantId = '{{ config("remita.merchant_id") }}';
    const publicKey = '{{ config("remita.public_key") }}';
    
    // Check if RmPaymentEngine is loaded
    if (typeof RmPaymentEngine === 'undefined') {
        console.log('RmPaymentEngine not loaded yet, waiting...');
        
        let attempts = 0;
        const maxAttempts = 10;
        const checkInterval = setInterval(() => {
            attempts++;
            
            if (typeof RmPaymentEngine !== 'undefined') {
                clearInterval(checkInterval);
                console.log('RmPaymentEngine loaded after', attempts, 'attempts');
                initializePaymentWidget();
            } else if (attempts >= maxAttempts) {
                clearInterval(checkInterval);
                console.error('RmPaymentEngine failed to load after', attempts, 'attempts');
                alert('Payment system is loading. Please try again in a moment.');
            }
        }, 500); // Check every 500ms
        
        return;
    }
    
    initializePaymentWidget();
    
    function initializePaymentWidget() {
        try {
            console.log('Initializing Remita payment widget...');
            
            // Initialize Remita inline payment
            const paymentEngine = RmPaymentEngine.init({
                key: publicKey,
                processRrr: true,
                transactionId: Math.floor(Math.random() * 1101233),
                extendedData: {
                    customFields: [
                        {
                            name: "rrr",
                            value: rrr
                        }
                    ]
                },
                onSuccess: function(response) {
                    console.log('Payment successful', response);
                    remitaWidgetOpen = false;
                    verifyPayment(rrr, 'INV_' + invoiceId);
                },
                onError: function(response) {
                    console.log('Payment error', response);
                    remitaWidgetOpen = false;
                    alert('Payment failed. Please try again.');
                    // Refresh after error
                    setTimeout(() => location.reload(), 1000);
                },
                onClose: function() {
                    console.log('Payment window closed');
                    remitaWidgetOpen = false;
                    widgetAboutToOpen = false;
                    // Refresh when widget closes
                    setTimeout(() => location.reload(), 500);
                }
            });
            
            // Mark that Remita widget is opening
            remitaWidgetOpen = true;
            widgetAboutToOpen = false; // Widget is now open, clear the flag
            paymentEngine.showPaymentWidget();
        } catch (error) {
            console.error('Error opening Remita widget:', error);
            widgetAboutToOpen = false; // Clear flag on error
            alert('Could not open payment widget. Error: ' + error.message);
        }
    }
}

function initiateRemitaPayment(type, data) {
    // Show loading
    const loadingHtml = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Initializing payment...</p>
        </div>
    `;
    
    // Close all modals first
    document.querySelectorAll('.modal').forEach(modal => {
        const bsModal = bootstrap.Modal.getInstance(modal);
        if (bsModal) bsModal.hide();
    });
    
    // Show loading modal
    const loadingModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    document.getElementById('paymentSummary').innerHTML = loadingHtml;
    document.querySelector('#paymentModal .modal-footer').style.display = 'none';
    loadingModal.show();
    
    // Call backend to generate RRR
    fetch('/parent/payments/remita/initiate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ type, data })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Mark that payment was initiated
            paymentInitiated = true;
            
            loadingModal.hide();
            
            // Initialize Remita inline payment
            const paymentEngine = RmPaymentEngine.init({
                key: result.publicKey,
                processRrr: true,
                transactionId: result.orderId,
                extendedData: {
                    customFields: [
                        {
                            name: "rrr",
                            value: result.rrr
                        }
                    ]
                },
                onSuccess: function(response) {
                    console.log('Payment successful', response);
                    remitaWidgetOpen = false;
                    widgetAboutToOpen = false;
                    verifyPayment(result.rrr, result.orderId);
                },
                onError: function(response) {
                    console.log('Payment error', response);
                    remitaWidgetOpen = false;
                    widgetAboutToOpen = false;
                    alert('Payment failed. Please try again.');
                    // Refresh after error
                    setTimeout(() => location.reload(), 1000);
                },
                onClose: function() {
                    console.log('Payment window closed');
                    remitaWidgetOpen = false;
                    widgetAboutToOpen = false;
                    // Refresh when widget closes
                    setTimeout(() => location.reload(), 500);
                }
            });
            
            // Mark that Remita widget is opening
            remitaWidgetOpen = true;
            widgetAboutToOpen = false; // Widget is now open, clear the flag
            paymentEngine.showPaymentWidget();
        } else {
            // Show detailed error in modal instead of alert
            console.error('Payment initialization error:', result);
            
            const errorHtml = `
                <div class="alert alert-danger" role="alert">
                    <h5 class="alert-heading">
                        <i class="ri-error-warning-line me-2"></i>Payment Initialization Failed
                    </h5>
                    <hr>
                    <p class="mb-2"><strong>Error Details:</strong></p>
                    <p class="mb-0">${result.message || 'Unknown error occurred'}</p>
                    <hr class="mt-3 mb-2">
                    <small class="text-muted">
                        If this problem persists, please contact support or check your configuration.
                    </small>
                </div>
            `;
            
            document.getElementById('paymentSummary').innerHTML = errorHtml;
            document.querySelector('#paymentModal .modal-footer').style.display = 'flex';
            document.querySelector('#paymentModal .modal-footer').innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            `;
            loadingModal.show();
        }
    })
    .catch(error => {
        loadingModal.hide();
        console.error('Payment request error:', error);
        
        const errorHtml = `
            <div class="alert alert-danger" role="alert">
                <h5 class="alert-heading">
                    <i class="ri-wifi-off-line me-2"></i>Network Error
                </h5>
                <hr>
                <p class="mb-2"><strong>Could not connect to payment server</strong></p>
                <p class="mb-0">${error.message || 'Please check your internet connection and try again.'}</p>
            </div>
        `;
        
        const errorModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        document.getElementById('paymentSummary').innerHTML = errorHtml;
        document.querySelector('#paymentModal .modal-footer').style.display = 'flex';
        document.querySelector('#paymentModal .modal-footer').innerHTML = `
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        `;
        errorModal.show();
    });
}

function verifyPayment(rrr, orderId) {
    // Show verification loading
    const verifyModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    document.getElementById('paymentSummary').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Verifying payment...</p>
        </div>
    `;
    verifyModal.show();
    
    fetch('/parent/payments/remita/verify', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ rrr, orderId })
    })
    .then(response => response.json())
    .then(result => {
        verifyModal.hide();
        
        if (result.success) {
            // Show success message
            alert('Payment verified successfully!\n\nYour payment has been confirmed.');
            window.location.reload();
        } else {
            alert('Payment verification failed. Please contact support with RRR: ' + rrr);
        }
    })
    .catch(error => {
        verifyModal.hide();
        console.error('Verification error:', error);
        alert('Verification failed. Please contact support with RRR: ' + rrr);
    });
}

function verifyPaymentStatus(invoiceId, rrr) {
    if (!confirm('This will check if payment was made at the bank or through other channels.\n\nProceed with verification?')) {
        return;
    }
    
    // Show verification loading modal
    const verifyModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    document.getElementById('paymentSummary').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Checking payment status with Remita...</p>
            <p class="text-muted small">RRR: ${rrr}</p>
        </div>
    `;
    document.querySelector('#paymentModal .modal-footer').style.display = 'none';
    verifyModal.show();
    
    fetch('/parent/payments/verify-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ invoice_id: invoiceId, rrr: rrr })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Payment found and verified
            const successHtml = `
                <div class="alert alert-success" role="alert">
                    <h5 class="alert-heading">
                        <i class="ri-checkbox-circle-line me-2"></i>Payment Confirmed!
                    </h5>
                    <hr>
                    <p class="mb-2"><strong>Your payment has been verified and updated.</strong></p>
                    <ul class="mb-2">
                        <li>RRR: ${result.rrr || rrr}</li>
                        <li>Amount: ₦${parseFloat(result.amount || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})}</li>
                        <li>Status: <span class="badge bg-success">${result.status || 'Paid'}</span></li>
                    </ul>
                    <p class="mb-0 small text-muted">The page will refresh to show updated payment status.</p>
                </div>
            `;
            
            document.getElementById('paymentSummary').innerHTML = successHtml;
            document.querySelector('#paymentModal .modal-footer').style.display = 'flex';
            document.querySelector('#paymentModal .modal-footer').innerHTML = `
                <button type="button" class="btn btn-success" onclick="location.reload()">Refresh Page</button>
            `;
            
            // Auto refresh after 3 seconds
            setTimeout(() => location.reload(), 3000);
        } else {
            // Payment not found or still pending
            const infoHtml = `
                <div class="alert alert-warning" role="alert">
                    <h5 class="alert-heading">
                        <i class="ri-information-line me-2"></i>${result.message || 'Payment Not Confirmed'}
                    </h5>
                    <hr>
                    <p class="mb-2"><strong>Status from Remita:</strong> ${result.remita_status || 'Not Paid'}</p>
                    ${result.status_code ? `<p class="mb-2"><small class="text-muted">Status Code: ${result.status_code}</small></p>` : ''}
                    ${result.details ? `
                        <div class="alert alert-info mb-2 small">
                            <i class="ri-information-line me-1"></i>${result.details}
                        </div>
                    ` : ''}
                    ${result.error_details ? `<p class="mb-2 text-danger"><strong>Error:</strong> ${result.error_details}</p>` : ''}
                    ${result.status_code === '404' ? `
                        <p class="mb-2"><strong>What this means:</strong></p>
                        <p class="mb-2">This RRR (${rrr}) exists in the portal but has not been used for payment yet.</p>
                        <p class="mb-2"><strong>How to pay:</strong></p>
                        <ul class="mb-2">
                            <li><strong>Option 1:</strong> Click the "Pay Now" button to pay online</li>
                            <li><strong>Option 2:</strong> Go to any bank and quote this RRR: <strong>${rrr}</strong></li>
                            <li>After bank payment, wait 10-30 minutes then click "Verify Status" again</li>
                        </ul>
                    ` : `
                        <p class="mb-2">This could mean:</p>
                        <ul class="mb-2">
                            <li>Payment has not been made yet</li>
                            <li>Payment is still being processed by the bank</li>
                            <li>RRR was not used for payment</li>
                            ${result.error_details ? '<li>There was a connection issue with Remita</li>' : ''}
                        </ul>
                    `}
                    <p class="mb-0 small"><strong>Next Steps:</strong></p>
                    <ul class="mb-0 small">
                        <li>If you made payment at the bank, please wait 10-30 minutes and try again</li>
                        ${result.error_details ? '<li>Check your internet connection and try again</li>' : ''}
                        <li>If issue persists, contact support with RRR: ${rrr}</li>
                    </ul>
                </div>
            `;
            
            document.getElementById('paymentSummary').innerHTML = infoHtml;
            document.querySelector('#paymentModal .modal-footer').style.display = 'flex';
            document.querySelector('#paymentModal .modal-footer').innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            `;
        }
    })
    .catch(error => {
        console.error('Verification error:', error);
        
        const errorHtml = `
            <div class="alert alert-danger" role="alert">
                <h5 class="alert-heading">
                    <i class="ri-error-warning-line me-2"></i>Verification Error
                </h5>
                <hr>
                <p class="mb-2"><strong>Could not verify payment status</strong></p>
                <p class="mb-0">${error.message || 'Please try again later or contact support.'}</p>
            </div>
        `;
        
        document.getElementById('paymentSummary').innerHTML = errorHtml;
        document.querySelector('#paymentModal .modal-footer').style.display = 'flex';
        document.querySelector('#paymentModal .modal-footer').innerHTML = `
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        `;
    });
}
</script>
@endpush
@endsection
