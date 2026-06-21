@extends('layouts.student')

@section('title', 'Payments')
@section('page-title', 'My Payments')

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item active text-muted">Payments</li>
    </ol>
</nav>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:45px;height:45px;background:rgba(220,53,69,0.1);">
                        <i class="ri-money-dollar-circle-line text-danger" style="font-size:22px;"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Outstanding Balance</p>
                        <h4 class="mb-0 fw-bold">&#8358;{{ number_format($summary['total_pending'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:45px;height:45px;background:rgba(255,193,7,0.1);">
                        <i class="ri-time-line text-warning" style="font-size:22px;"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Pending Invoices</p>
                        <h4 class="mb-0 fw-bold">{{ $summary['pending_count'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:45px;height:45px;background:rgba(40,167,69,0.1);">
                        <i class="ri-checkbox-circle-line text-success" style="font-size:22px;"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Total Paid</p>
                        <h4 class="mb-0 fw-bold">&#8358;{{ number_format($summary['total_paid'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('student.payments.index') }}" class="row align-items-end g-3">
            <div class="col-md-4">
                <label class="form-label">Academic Session</label>
                <select class="form-select" name="session">
                    <option value="">All Sessions</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}" {{ $filterSession == $session->id ? 'selected' : '' }}>{{ $session->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Term</label>
                <select class="form-select" name="term">
                    <option value="">All Terms</option>
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" {{ $filterTerm == $term->id ? 'selected' : '' }}>{{ $term->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill"><i class="ri-filter-line me-1"></i>Filter</button>
                    @if($filterSession || $filterTerm)
                        <a href="{{ route('student.payments.index') }}" class="btn btn-outline-danger"><i class="ri-close-line"></i></a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs mb-3" id="paymentTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pendingTab" type="button" role="tab">
            <i class="ri-time-line me-1"></i>Pending
            @if($summary['pending_count'] > 0)
                <span class="badge bg-danger ms-1">{{ $summary['pending_count'] }}</span>
            @endif
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#historyTab" type="button" role="tab">
            <i class="ri-history-line me-1"></i>Payment History
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="paymentTabContent">
    <!-- Pending Payments Tab -->
    <div class="tab-pane fade show active" id="pendingTab" role="tabpanel">
        @if($payments['pending']->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="ri-check-double-line text-success" style="font-size: 64px;"></i>
                    <h5 class="mt-3 mb-2">All Payments Up to Date!</h5>
                    <p class="text-muted">You have no pending payments at this time.</p>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th>Description</th>
                                    <th>Term</th>
                                    <th>Due Date</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments['pending'] as $invoice)
                                    @php
                                        $paymentTitle = 'Invoice #' . $invoice->invoice_number;
                                        if ($invoice->invoiceItems && $invoice->invoiceItems->count() > 0) {
                                            $paymentTitle = optional($invoice->invoiceItems->first()->feeSetup)->payment_type ?? 'School Fees';
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input payment-checkbox" type="checkbox"
                                                       value="{{ $invoice->id }}"
                                                       data-amount="{{ $invoice->balance ?? $invoice->total_amount }}"
                                                       data-title="{{ $paymentTitle }}">
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-medium">{{ $paymentTitle }}</span>
                                            <br><small class="text-muted">{{ $invoice->invoice_number }}</small>
                                        </td>
                                        <td>{{ optional($invoice->term)->name ?? 'N/A' }}</td>
                                        <td>{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'N/A' }}</td>
                                        <td class="text-end fw-bold">&#8358;{{ number_format($invoice->balance ?? $invoice->total_amount, 2) }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-warning text-dark">{{ $invoice->status }}</span>
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-primary"
                                                    onclick="payNow({{ $invoice->id }}, '{{ $invoice->invoice_number }}', {{ $invoice->balance ?? $invoice->total_amount }})">
                                                <i class="ri-bank-card-line me-1"></i>Pay
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-primary" id="paySelectedBtn" disabled>
                            <i class="ri-wallet-line me-2"></i>Pay Selected (<span id="selectedCount">0</span>)
                        </button>
                        <span class="fw-bold">Selected Total: &#8358;<span id="selectedTotal">0.00</span></span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Payment History Tab -->
    <div class="tab-pane fade" id="historyTab" role="tabpanel">
        @if($payments['history']->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="ri-file-list-line text-muted" style="font-size: 64px;"></i>
                    <h5 class="mt-3 mb-2">No Payment History</h5>
                    <p class="text-muted">Your paid invoices will appear here.</p>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th>Term</th>
                                    <th>Date Paid</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments['history'] as $invoice)
                                    @php
                                        $paymentTitle = 'Invoice #' . $invoice->invoice_number;
                                        if ($invoice->invoiceItems && $invoice->invoiceItems->count() > 0) {
                                            $paymentTitle = optional($invoice->invoiceItems->first()->feeSetup)->payment_type ?? 'School Fees';
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="fw-medium">{{ $paymentTitle }}</span>
                                            <br><small class="text-muted">{{ $invoice->invoice_number }}</small>
                                        </td>
                                        <td>{{ optional($invoice->term)->name ?? 'N/A' }}</td>
                                        <td>{{ $invoice->updated_at->format('d M Y') }}</td>
                                        <td class="text-end fw-bold">&#8358;{{ number_format($invoice->amount_paid, 2) }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-success">Paid</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('student.payments.receipt', $invoice->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="ri-file-text-line me-1"></i>Receipt
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

<!-- Payment Confirmation Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-secure-payment-line me-2"></i>Confirm Payment</h5>
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

@push('styles')
<style>
    .nav-tabs .nav-link { color: #6c757d; border: none; border-bottom: 2px solid transparent; font-weight: 500; }
    .nav-tabs .nav-link.active { color: #000; background: transparent; border-bottom: 2px solid #667eea; }
</style>
@endpush

@push('scripts')
<script src="{{ config('remita.widget_url') }}"></script>
<script>
let selectedPayments = [];

document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.payment-checkbox');
    const selectAll = document.getElementById('selectAll');
    const paySelectedBtn = document.getElementById('paySelectedBtn');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => { cb.checked = this.checked; });
            updateSelectedPayments();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectedPayments);
    });

    if (paySelectedBtn) {
        paySelectedBtn.addEventListener('click', function() {
            if (selectedPayments.length > 0) showPaymentModal(selectedPayments);
        });
    }

    document.getElementById('confirmPayment')?.addEventListener('click', function() {
        initiatePayment();
    });
});

function updateSelectedPayments() {
    selectedPayments = [];
    let total = 0;
    document.querySelectorAll('.payment-checkbox:checked').forEach(cb => {
        selectedPayments.push({ id: cb.value, amount: parseFloat(cb.dataset.amount), title: cb.dataset.title });
        total += parseFloat(cb.dataset.amount);
    });

    const btn = document.getElementById('paySelectedBtn');
    const countEl = document.getElementById('selectedCount');
    const totalEl = document.getElementById('selectedTotal');

    if (btn) btn.disabled = selectedPayments.length === 0;
    if (countEl) countEl.textContent = selectedPayments.length;
    if (totalEl) totalEl.textContent = total.toLocaleString('en-NG', {minimumFractionDigits: 2});
}

function payNow(invoiceId, invoiceNumber, amount) {
    selectedPayments = [{ id: invoiceId, amount: amount, title: invoiceNumber }];
    showPaymentModal(selectedPayments);
}

function showPaymentModal(payments) {
    let html = '<div class="mb-3"><h6>Payment Summary</h6>';
    let total = 0;
    payments.forEach(p => {
        html += `<div class="d-flex justify-content-between py-1 border-bottom"><span>${p.title}</span><strong>&#8358;${parseFloat(p.amount).toLocaleString('en-NG', {minimumFractionDigits:2})}</strong></div>`;
        total += parseFloat(p.amount);
    });
    html += `<div class="d-flex justify-content-between py-2 mt-2 fw-bold fs-5"><span>Total</span><span class="text-success">&#8358;${total.toLocaleString('en-NG', {minimumFractionDigits:2})}</span></div></div>`;
    document.getElementById('paymentSummary').innerHTML = html;
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}

function initiatePayment() {
    const invoiceIds = selectedPayments.map(p => p.id);
    const btn = document.getElementById('confirmPayment');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

    fetch('{{ route("student.payments.remita.initiate") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ invoice_ids: invoiceIds })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            var paymentEngine = RmPaymentEngine.init({
                key: data.data.merchantId,
                processRrr: false,
                transactionId: data.data.orderId,
                channel: '',
                extendedData: { customFields: [{ name: "merchant_id", value: data.data.merchantId }] },
                onSuccess: function(response) { verifyPayment(data.data.orderId, response.paymentReference); },
                onError: function(response) { alert('Payment failed. Please try again.'); resetBtn(); },
                onClose: function() { resetBtn(); }
            });
            paymentEngine.showPaymentWidget();
        } else {
            alert(data.message || 'Failed to initiate payment.');
            resetBtn();
        }
    })
    .catch(err => { alert('An error occurred.'); resetBtn(); });

    function resetBtn() {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-secure-payment-line me-2"></i>Pay with Remita';
    }
}

function verifyPayment(orderId, rrr) {
    fetch('{{ route("student.payments.remita.verify") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ orderId: orderId, rrr: rrr })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Payment successful!');
            window.location.reload();
        } else {
            alert(data.message || 'Verification failed.');
        }
    });
}
</script>
@endpush
@endsection
