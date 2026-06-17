@extends('layouts.admin')

@section('title', 'Fees & Income')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-1">Fees & Income</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="text-muted text-decoration-none">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.payments.overview') }}"
                                        class="text-muted text-decoration-none">Payment & Finance</a>
                                </li>
                                <li class="breadcrumb-item active text-muted" aria-current="page">Fees & Income</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" id="manageStatusBtn">
                            <i class="ri-upload-2-line"></i> Manage Payment Status
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                            <i class="ri-add-line"></i> Record Payment
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card custom-shadow rounded-3 bg-white border">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Revenue</p>
                                <h4 class="fw-bold mb-0 text-success">₦{{ number_format($stats['total_revenue'] ?? 0) }}
                                </h4>
                                <small class="text-success">
                                    <i class="ri-arrow-up-line"></i> Total collected
                                </small>
                            </div>
                            <div class="icon-wrapper bg-success-subtle text-success">
                                <i class="ri-money-dollar-circle-line fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card custom-shadow rounded-3 bg-white border">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Total Students</p>
                                <h4 class="fw-bold mb-0 text-primary">{{ number_format($stats['total_students'] ?? 0) }}
                                </h4>
                                <small class="text-muted">Students enrolled</small>
                            </div>
                            <div class="icon-wrapper bg-primary-subtle text-primary">
                                <i class="ri-graduation-cap-line fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card custom-shadow rounded-3 bg-white border">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Paid</p>
                                <h4 class="fw-bold mb-0 text-success">{{ number_format($stats['paid_count'] ?? 0) }}</h4>
                                <small class="text-success">Students paid</small>
                            </div>
                            <div class="icon-wrapper bg-success-subtle text-success">
                                <i class="ri-check-double-line fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card custom-shadow rounded-3 bg-white border">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Outstanding</p>
                                <h4 class="fw-bold mb-0 text-warning">{{ number_format($stats['outstanding_count'] ?? 0) }}
                                </h4>
                                <small class="text-warning">Students owing</small>
                            </div>
                            <div class="icon-wrapper bg-warning-subtle text-warning">
                                <i class="ri-time-line fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Student Payments -->
        <div class="row">
            <div class="col-12">
                <div class="card custom-shadow rounded-3 bg-white border">
                    <div class="card-header bg-transparent border-0">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <h6 class="fw-semibold mb-0">Student Payments</h6>

                            <!-- Filters -->
                            <div class="d-flex gap-2 flex-wrap">
                                <select class="form-select form-select-sm" id="levelFilter" style="width: auto;">
                                    <option value="">All Levels</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level }}">{{ $level }}</option>
                                    @endforeach
                                </select>

                                <select class="form-select form-select-sm" id="termFilter" style="width: auto;">
                                    <option value="">All Terms</option>
                                    @foreach ($terms as $term)
                                        <option value="{{ $term->id }}">{{ $term->name }}</option>
                                    @endforeach
                                </select>

                                <select class="form-select form-select-sm" id="sessionFilter" style="width: auto;">
                                    <option value="">All Sessions</option>
                                    @foreach ($sessions as $session)
                                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                                    @endforeach
                                </select>

                                <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
                                    <option value="">All Status</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Partial">Partial</option>
                                    <option value="Overdue">Overdue</option>
                                </select>

                                <button class="btn btn-sm btn-primary" id="filterBtn">
                                    <i class="ri-search-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>NAME</th>
                                        <th>TYPE</th>
                                        <th>SESSION/TERM</th>
                                        <th>CLASS</th>
                                        <th>AMOUNT</th>
                                        <th>BALANCE</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $invoice)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        $studentName = $invoice->student?->user?->name ?? 'Student';
                                                        $photoUrl =
                                                            $invoice->student &&
                                                            $invoice->student->user &&
                                                            $invoice->student->user->photo_path
                                                                ? Storage::url($invoice->student->user->photo_path)
                                                                : 'https://ui-avatars.com/api/?name=' .
                                                                    urlencode($studentName) .
                                                                    '&background=4f46e5&color=fff&size=32&rounded=true';
                                                        $metadata = is_string($invoice->metadata)
                                                            ? json_decode($invoice->metadata, true)
                                                            : $invoice->metadata ?? [];

                                                        // Get service name from metadata, or from invoice items
                                                        $serviceName = $metadata['service_name'] ?? null;
                                                        if (
                                                            !$serviceName &&
                                                            $invoice->items &&
                                                            $invoice->items->isNotEmpty()
                                                        ) {
                                                            $firstItem = $invoice->items->first();
                                                            $serviceName =
                                                                $firstItem->paymentSetup->payment_type ?? 'Payment';
                                                        }
                                                        $serviceName = $serviceName ?? 'School Fees';
                                                    @endphp
                                                    <img src="{{ $photoUrl }}" alt="Student"
                                                        class="rounded-circle me-2" width="32" height="32">
                                                    <div>
                                                        <div class="fw-semibold">
                                                            {{ $invoice->student?->user?->name ?? 'N/A' }}</div>
                                                        <small
                                                            class="text-muted">{{ $invoice->student?->admission_no ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $serviceName }}</td>
                                            <td>
                                                <div>{{ $invoice->academicSession->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $invoice->term->name ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary-subtle text-secondary">
                                                    {{ $invoice->student?->classArm?->schoolClass?->level ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="fw-bold text-primary">
                                                ₦{{ number_format($invoice->total_amount, 2) }}</td>
                                            <td
                                                class="fw-bold {{ $invoice->balance > 0 ? 'text-danger' : 'text-success' }}">
                                                ₦{{ number_format($invoice->balance, 2) }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $invoice->status === 'Paid' ? 'success' : ($invoice->status === 'Pending' ? 'warning' : ($invoice->status === 'Partial' ? 'info' : 'danger')) }}-subtle text-{{ $invoice->status === 'Paid' ? 'success' : ($invoice->status === 'Pending' ? 'warning' : ($invoice->status === 'Partial' ? 'info' : 'danger')) }}">
                                                    {{ $invoice->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                        <i class="ri-more-line"></i>
                                                    </button>
                                                    @php
                                                        $meta = is_string($invoice->metadata)
                                                            ? json_decode($invoice->metadata, true)
                                                            : $invoice->metadata ?? [];
                                                        $isRemita =
                                                            isset($meta['payment_method']) &&
                                                            $meta['payment_method'] === 'Remita';
                                                    @endphp
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('admin.payments.invoice.show', $invoice->id) }}"
                                                                target="_blank"><i class="ri-eye-line me-2"></i>View
                                                                Details</a></li>
                                                        @if ($isRemita && $invoice->status !== 'Paid')
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li><a class="dropdown-item pay-now-remita-btn" href="#"
                                                                    data-id="{{ $invoice->id }}"
                                                                    data-rrr="{{ $invoice->invoice_number }}"
                                                                    data-order-id="{{ $meta['remita_order_id'] ?? '' }}"
                                                                    data-amount="{{ $invoice->balance }}"
                                                                    data-student="{{ $invoice->student?->user?->name ?? 'N/A' }}"><i
                                                                        class="ri-bank-card-line me-2"></i>Pay Now
                                                                    (Remita)</a></li>
                                                            <li><a class="dropdown-item verify-remita-btn" href="#"
                                                                    data-id="{{ $invoice->id }}"
                                                                    data-rrr="{{ $invoice->invoice_number }}"
                                                                    data-student="{{ $invoice->student?->user?->name ?? 'N/A' }}"><i
                                                                        class="ri-refresh-line me-2"></i>Verify Payment
                                                                    Status</a></li>
                                                        @elseif($isRemita && $invoice->status === 'Paid')
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li><a class="dropdown-item text-success" href="#"><i
                                                                        class="ri-check-line me-2"></i>Paid via Remita</a>
                                                            </li>
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="{{ route('admin.payments.invoice.receipt', $invoice->id) }}"
                                                                    target="_blank"><i class="ri-printer-line me-2"></i>Print
                                                                    Receipt</a></li>
                                                        @else
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li><a class="dropdown-item update-status-btn" href="#"
                                                                    data-id="{{ $invoice->id }}"
                                                                    data-status="{{ $invoice->status }}"
                                                                    data-student="{{ $invoice->student?->user?->name ?? 'N/A' }}"><i
                                                                        class="ri-edit-line me-2"></i>Update Status</a>
                                                            </li>
                                                            @if ($invoice->status === 'Paid')
                                                                <li>
                                                                    <hr class="dropdown-divider">
                                                                </li>
                                                                <li><a class="dropdown-item"
                                                                        href="{{ route('admin.payments.invoice.receipt', $invoice->id) }}"
                                                                        target="_blank"><i
                                                                            class="ri-printer-line me-2"></i>Print
                                                                        Receipt</a></li>
                                                            @endif
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="ri-money-dollar-box-line display-6"></i>
                                                    <p class="mt-2">No payment records found</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($transactions->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $transactions->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Record Payment Modal -->
    <div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Record New Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="recordPaymentForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Student <span class="text-danger">*</span></label>
                                <select class="form-select" name="student_id" id="studentSelect" required>
                                    <option value="">Choose student...</option>
                                    <!-- This will be populated via AJAX -->
                                </select>
                                <small class="text-muted">Search and select student</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Payment Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="payment_setup_id" id="paymentSetupSelect" required>
                                    <option value="">Choose payment type...</option>
                                    @php
                                        $paymentSetups = \App\Models\PaymentSetup::where('status', 'Active')->get();
                                    @endphp
                                    @foreach ($paymentSetups as $setup)
                                        <option value="{{ $setup->id }}" data-amount="{{ $setup->amount }}"
                                            data-payment-type="{{ $setup->payment_type }}"
                                            data-level="{{ $setup->level }}" data-term="{{ $setup->term }}">
                                            {{ $setup->payment_type }}
                                            @if ($setup->payment_type === 'School Fees')
                                                ({{ $setup->level }})
                                            @endif
                                            - ₦{{ number_format($setup->amount, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted" id="paymentTypeHint">Select student first to auto-calculate
                                    school fees</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" class="form-control" name="amount" id="amountInput"
                                        step="0.01" min="0" required>
                                </div>
                                <small class="text-muted">Auto-filled from payment type</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Academic Session <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="session_id" required>
                                    <option value="">Choose session...</option>
                                    @foreach ($sessions as $session)
                                        <option value="{{ $session->id }}" {{ $session->is_current ? 'selected' : '' }}>
                                            {{ $session->name }} {{ $session->is_current ? '(Current)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Term <span class="text-danger">*</span></label>
                                <select class="form-select" name="term_id" required>
                                    <option value="">Choose term...</option>
                                    @foreach ($terms as $term)
                                        <option value="{{ $term->id }}" {{ $term->is_current ? 'selected' : '' }}>
                                            {{ $term->name }} {{ $term->is_current ? '(Current)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Payment Method <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="payment_method" id="paymentMethodSelect" required>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="POS">POS</option>
                                    <option value="Mobile Money">Mobile Money</option>
                                    <option value="Remita">Online Payment (Remita)</option>
                                </select>
                                <small class="text-muted" id="paymentMethodHint">Remita will open payment gateway</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Payment Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="payment_date" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Description/Notes</label>
                                <textarea class="form-control" name="description" rows="3" placeholder="Optional notes about this payment..."></textarea>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3 mb-0">
                            <i class="ri-information-line me-2"></i>
                            <strong>Note:</strong> This will create a paid invoice and payment record for the selected
                            student.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Record Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Payment Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="updateStatusForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="updateTransactionId" name="transaction_id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Student</label>
                                <input type="text" class="form-control" id="updateStudentName" readonly>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select" id="updateStatus" name="status" required>
                                    <option value="Paid">Paid</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Remarks</label>
                                <textarea class="form-control" name="remarks" rows="3" placeholder="Optional remarks about status change"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Student Name</label>
                            <input type="text" class="form-control" id="detailStudentName" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Type</label>
                            <input type="text" class="form-control" id="detailPaymentType" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Amount</label>
                            <input type="text" class="form-control" id="detailAmount" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <input type="text" class="form-control" id="detailStatus" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Date</label>
                            <input type="text" class="form-control" id="detailPaymentDate" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Reference Number</label>
                            <input type="text" class="form-control" id="detailReference" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content text-center">
                <div class="modal-body p-4">
                    <div class="text-success mb-3">
                        <i class="ri-check-double-line display-4"></i>
                    </div>
                    <h6 class="fw-semibold mb-2" id="successMessage">Operation completed successfully!</h6>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Remita Payment Widget -->
    <script src="{{ config('remita.widget_url') }}"></script>

    <script>
        let remitaWidgetOpen = false;
        let widgetAboutToOpen = false;
        // Filter functionality
        document.getElementById('filterBtn').addEventListener('click', function() {
            const level = document.getElementById('levelFilter').value;
            const term = document.getElementById('termFilter').value;
            const session = document.getElementById('sessionFilter').value;
            const status = document.getElementById('statusFilter').value;

            const params = new URLSearchParams();
            if (level) params.append('level', level);
            if (term) params.append('term', term);
            if (session) params.append('session', session);
            if (status) params.append('status', status);

            const queryString = params.toString();
            const url = '{{ route('admin.payments.fees-income') }}' + (queryString ? '?' + queryString : '');
            window.location.href = url;
        });

        // Auto-filter on select change
        document.querySelectorAll('#levelFilter, #termFilter, #sessionFilter, #statusFilter').forEach(select => {
            select.addEventListener('change', function() {
                document.getElementById('filterBtn').click();
            });
        });

        // Set filter values from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('level')) {
            document.getElementById('levelFilter').value = urlParams.get('level');
        }
        if (urlParams.get('term')) {
            document.getElementById('termFilter').value = urlParams.get('term');
        }
        if (urlParams.get('session')) {
            document.getElementById('sessionFilter').value = urlParams.get('session');
        }
        if (urlParams.get('status')) {
            document.getElementById('statusFilter').value = urlParams.get('status');
        }

        // Set default payment date to today
        document.querySelector('input[name="payment_date"]').value = new Date().toISOString().split('T')[0];

        // Store selected student's level
        let selectedStudentLevel = null;

        // Auto-populate amount when payment type is selected
        document.getElementById('paymentSetupSelect').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const amount = selectedOption.getAttribute('data-amount');
            const paymentType = selectedOption.getAttribute('data-payment-type');
            const setupLevel = selectedOption.getAttribute('data-level');

            // If School Fees is selected and student level is known
            if (paymentType === 'School Fees') {
                if (!selectedStudentLevel) {
                    // No student selected yet
                    document.getElementById('paymentTypeHint').innerHTML =
                        `<span class="text-warning"><i class="ri-alert-line"></i> Please select a student first</span>`;
                    document.getElementById('amountInput').value = '';
                    this.selectedIndex = 0; // Reset selection
                    return;
                }

                // Find the correct School Fees option for this student's level
                const paymentSetupSelect = document.getElementById('paymentSetupSelect');
                let correctOption = null;
                let correctAmount = null;

                for (let i = 0; i < paymentSetupSelect.options.length; i++) {
                    const opt = paymentSetupSelect.options[i];
                    if (opt.getAttribute('data-payment-type') === 'School Fees' &&
                        opt.getAttribute('data-level') === selectedStudentLevel) {
                        correctAmount = opt.getAttribute('data-amount');
                        correctOption = opt;
                        // Silently select the correct option if not already selected
                        if (paymentSetupSelect.selectedIndex !== i) {
                            paymentSetupSelect.selectedIndex = i;
                        }
                        break;
                    }
                }

                if (correctAmount) {
                    document.getElementById('amountInput').value = parseFloat(correctAmount).toFixed(2);
                    document.getElementById('paymentTypeHint').innerHTML =
                        `<span class="text-success"><i class="ri-check-line"></i> ✓ Auto-calculated for ${selectedStudentLevel}: ₦${parseFloat(correctAmount).toLocaleString()}</span>`;
                } else {
                    // No specific setup for this level, use the selected one or show warning
                    if (amount) {
                        document.getElementById('amountInput').value = parseFloat(amount).toFixed(2);
                        document.getElementById('paymentTypeHint').innerHTML =
                            `<span class="text-warning"><i class="ri-alert-line"></i> No school fees setup found for ${selectedStudentLevel}. Using ${setupLevel} amount.</span>`;
                    } else {
                        document.getElementById('paymentTypeHint').innerHTML =
                            `<span class="text-danger"><i class="ri-close-line"></i> No school fees setup found for ${selectedStudentLevel}</span>`;
                    }
                }
            } else {
                // For non-School Fees payments, just use the amount
                if (amount) {
                    document.getElementById('amountInput').value = parseFloat(amount).toFixed(2);
                    document.getElementById('paymentTypeHint').innerHTML =
                        `<span class="text-success"><i class="ri-check-line"></i> Amount: ₦${parseFloat(amount).toLocaleString()}</span>`;
                }
            }
        });

        // Load students when record payment modal opens
        document.getElementById('recordPaymentModal').addEventListener('show.bs.modal', function() {
            const studentSelect = document.getElementById('studentSelect');

            // Show loading
            studentSelect.innerHTML = '<option value="">Loading students...</option>';

            // Reset student level
            selectedStudentLevel = null;

            // Fetch all students
            fetch('/api/students/all', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    studentSelect.innerHTML = '<option value="">Choose student...</option>';

                    if (data.students && data.students.length > 0) {
                        data.students.forEach(student => {
                            const option = document.createElement('option');
                            option.value = student.id;
                            option.setAttribute('data-level', student.level || '');
                            option.textContent =
                                `${student.name} (${student.student_id}) - ${student.class || 'No Class'}`;
                            studentSelect.appendChild(option);
                        });
                    } else {
                        studentSelect.innerHTML = '<option value="">No students found</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading students:', error);
                    studentSelect.innerHTML = '<option value="">Error loading students</option>';
                });
        });

        // Capture student selection to get their level
        document.getElementById('studentSelect').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            selectedStudentLevel = selectedOption.getAttribute('data-level');

            if (selectedStudentLevel) {
                document.getElementById('paymentTypeHint').innerHTML =
                    `<span class="text-info"><i class="ri-information-line"></i> Student level: ${selectedStudentLevel} - Select School Fees to auto-calculate</span>`;
            } else {
                document.getElementById('paymentTypeHint').textContent = 'Select payment type';
            }

            // Reset payment type and amount when student changes
            document.getElementById('paymentSetupSelect').selectedIndex = 0;
            document.getElementById('amountInput').value = '';
        });

        // Monitor payment method selection
        document.getElementById('paymentMethodSelect').addEventListener('change', function() {
            const selectedMethod = this.value;
            const hint = document.getElementById('paymentMethodHint');

            if (selectedMethod === 'Remita') {
                hint.innerHTML =
                    '<span class="text-info"><i class="ri-shield-check-line"></i> Remita payment gateway will open</span>';
            } else {
                hint.innerHTML = '<span class="text-muted">Manual payment recording</span>';
            }
        });

        // Record Payment Form Submission
        document.getElementById('recordPaymentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const paymentMethod = document.getElementById('paymentMethodSelect').value;

            // Check if Remita is selected
            if (paymentMethod === 'Remita') {
                initiateAdminRemitaPayment();
            } else {
                recordManualPayment();
            }
        });

        function recordManualPayment() {
            const form = document.getElementById('recordPaymentForm');
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Recording...';
            submitBtn.disabled = true;

            const formData = new FormData(form);

            fetch('{{ route('admin.payments.fees-income.record') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;

                    if (data.success) {
                        // Hide record modal
                        const recordModal = bootstrap.Modal.getInstance(document.getElementById('recordPaymentModal'));
                        recordModal.hide();

                        // Show success message
                        alert('✅ Payment recorded successfully!');

                        // Reload page to show new payment
                        location.reload();
                    } else {
                        alert(`❌ Error: ${data.message || 'Failed to record payment'}`);
                    }
                })
                .catch(error => {
                    console.error('Record payment error:', error);
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    alert(`❌ Error: Unable to record payment. Please try again.`);
                });
        }

        function initiateAdminRemitaPayment() {
            const form = document.getElementById('recordPaymentForm');
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            submitBtn.disabled = true;

            // Gather form data
            const formData = {
                student_id: document.getElementById('studentSelect').value,
                payment_setup_id: document.getElementById('paymentSetupSelect').value,
                session_id: document.querySelector('select[name="session_id"]').value,
                term_id: document.querySelector('select[name="term_id"]').value,
                amount: document.getElementById('amountInput').value,
                description: document.querySelector('textarea[name="description"]').value
            };

            // Call backend to initiate Remita payment
            fetch('{{ route('admin.payments.remita.initiate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(result => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;

                    if (result.success) {
                        // Hide record modal
                        const recordModal = bootstrap.Modal.getInstance(document.getElementById('recordPaymentModal'));
                        recordModal.hide();

                        // Open Remita widget
                        openAdminRemitaWidget(result.rrr, result.orderId, result.amount, result.invoiceId);
                    } else {
                        alert(`❌ Error: ${result.message || 'Failed to initiate payment'}`);
                    }
                })
                .catch(error => {
                    console.error('Remita initiation error:', error);
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    alert(`❌ Error: Unable to initiate Remita payment. Please try again.`);
                });
        }

        function openAdminRemitaWidget(rrr, orderId, amount, invoiceId) {
            console.log('Opening Remita widget - RRR:', rrr, 'OrderID:', orderId, 'Amount:', amount);

            const merchantId = '{{ config('remita.merchant_id') }}';
            const publicKey = '{{ config('remita.public_key') }}';

            if (typeof RmPaymentEngine === 'undefined') {
                console.error('Remita Payment Engine not loaded');
                alert('Payment gateway not available. Please refresh and try again.');
                return;
            }

            widgetAboutToOpen = true;

            try {
                const paymentEngine = RmPaymentEngine.init({
                    key: publicKey,
                    processRrr: true,
                    transactionId: orderId,
                    extendedData: {
                        customFields: [{
                            name: "rrr",
                            value: rrr
                        }]
                    },
                    onSuccess: function(response) {
                        console.log('Payment successful', response);
                        remitaWidgetOpen = false;
                        widgetAboutToOpen = false;
                        verifyAdminPayment(rrr, invoiceId);
                    },
                    onError: function(response) {
                        console.log('Payment error', response);
                        remitaWidgetOpen = false;
                        widgetAboutToOpen = false;
                        alert('Payment failed. Please try again.');
                        setTimeout(() => location.reload(), 1000);
                    },
                    onClose: function() {
                        console.log('Payment window closed');
                        remitaWidgetOpen = false;
                        widgetAboutToOpen = false;
                        setTimeout(() => location.reload(), 500);
                    }
                });

                remitaWidgetOpen = true;
                widgetAboutToOpen = false;
                paymentEngine.showPaymentWidget();
            } catch (error) {
                console.error('Error opening Remita widget:', error);
                widgetAboutToOpen = false;
                alert('Could not open payment widget. Error: ' + error.message);
            }
        }

        function verifyAdminPayment(rrr, invoiceId) {
            console.log('Verifying payment - RRR:', rrr);

            fetch('{{ route('admin.payments.remita.verify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        rrr: rrr,
                        invoice_id: invoiceId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Payment verified successfully!');
                        location.reload();
                    } else {
                        alert('⚠️ Payment verification pending. Status: ' + (data.status || 'Unknown'));
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Verification error:', error);
                    alert('⚠️ Could not verify payment status. Please check payment history.');
                    location.reload();
                });
        }

        // View Details functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('view-details-btn') || e.target.closest('.view-details-btn')) {
                const btn = e.target.classList.contains('view-details-btn') ? e.target : e.target.closest(
                    '.view-details-btn');
                e.preventDefault();

                // Get data from button attributes
                const student = btn.getAttribute('data-student');
                const type = btn.getAttribute('data-type');
                const amount = btn.getAttribute('data-amount');
                const status = btn.getAttribute('data-status');
                const date = btn.getAttribute('data-date');
                const reference = btn.getAttribute('data-reference');

                // Populate view details modal
                document.getElementById('detailStudentName').value = student;
                document.getElementById('detailPaymentType').value = type;
                document.getElementById('detailAmount').value = '₦' + parseFloat(amount).toLocaleString();
                document.getElementById('detailStatus').value = status;
                document.getElementById('detailPaymentDate').value = date;
                document.getElementById('detailReference').value = reference;

                // Show view details modal
                const viewModal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
                viewModal.show();
            }
        });

        // Update Status functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('update-status-btn') || e.target.closest('.update-status-btn')) {
                const btn = e.target.classList.contains('update-status-btn') ? e.target : e.target.closest(
                    '.update-status-btn');
                e.preventDefault();

                // Get data from button attributes
                const id = btn.getAttribute('data-id');
                const status = btn.getAttribute('data-status');
                const student = btn.getAttribute('data-student');

                // Populate update status modal
                document.getElementById('updateTransactionId').value = id;
                document.getElementById('updateStudentName').value = student;
                document.getElementById('updateStatus').value = status;

                // Show update status modal
                const updateModal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
                updateModal.show();
            }
        });

        // Pay Now - Reopen Remita Widget
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('pay-now-remita-btn') || e.target.closest('.pay-now-remita-btn')) {
                const btn = e.target.classList.contains('pay-now-remita-btn') ? e.target : e.target.closest(
                    '.pay-now-remita-btn');
                e.preventDefault();

                const invoiceId = btn.getAttribute('data-id');
                const rrr = btn.getAttribute('data-rrr');
                const orderId = btn.getAttribute('data-order-id');
                const amount = btn.getAttribute('data-amount');
                const studentName = btn.getAttribute('data-student');

                if (confirm(
                        `Open Remita payment for ${studentName}?\n\nAmount: ₦${parseFloat(amount).toLocaleString()}`
                        )) {
                    openAdminRemitaWidget(rrr, orderId, amount, invoiceId);
                }
            }
        });

        // Verify Remita Payment
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('verify-remita-btn') || e.target.closest('.verify-remita-btn')) {
                const btn = e.target.classList.contains('verify-remita-btn') ? e.target : e.target.closest(
                    '.verify-remita-btn');
                e.preventDefault();

                const invoiceId = btn.getAttribute('data-id');
                const rrr = btn.getAttribute('data-rrr');
                const studentName = btn.getAttribute('data-student');

                if (confirm(
                        `Verify Remita payment for ${studentName}?\n\nThis will check the payment status with Remita.`
                        )) {
                    // Show loading
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying...';
                    btn.disabled = true;

                    verifyAdminPayment(rrr, invoiceId);
                }
            }
        });

        // Update Status Form Submission
        document.getElementById('updateStatusForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const transactionId = document.getElementById('updateTransactionId').value;

            fetch(`{{ url('admin/payments/fees-income') }}/${transactionId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-HTTP-Method-Override': 'PUT'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Hide update modal
                        const updateModal = bootstrap.Modal.getInstance(document.getElementById(
                            'updateStatusModal'));
                        updateModal.hide();

                        // Show success modal
                        document.getElementById('successMessage').textContent = data.message;
                        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();

                        // Reload page after success modal is closed
                        document.getElementById('successModal').addEventListener('hidden.bs.modal', function() {
                            location.reload();
                        });
                    } else {
                        alert(`❌ Error: ${data.message || 'Failed to update status'}`);
                    }
                })
                .catch(error => {
                    console.error('Update status error:', error);
                    alert(
                        `❌ Network Error: Unable to update status. Please check your connection and try again.`);
                });
        });

        // Print Receipt functionality - Generate PDF
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('print-receipt-btn') || e.target.closest('.print-receipt-btn')) {
                const btn = e.target.classList.contains('print-receipt-btn') ? e.target : e.target.closest(
                    '.print-receipt-btn');
                e.preventDefault();

                // Get transaction ID from button attributes
                const transactionId = btn.getAttribute('data-id');

                // Build PDF URL
                const pdfUrl = '{{ url('admin/payments/receipt') }}/' + transactionId + '/pdf';

                // Open PDF in new tab
                window.open(pdfUrl, '_blank');
            }
        });

        // Manage Payment Status functionality (Bulk operations)
        document.getElementById('manageStatusBtn').addEventListener('click', function() {
            alert(
                '📋 Bulk Payment Status Management\n\nThis feature allows you to:\n• Update multiple payment statuses at once\n• Export payment reports\n• Send payment reminders\n• Mark payments as verified\n\nFeature coming soon!');
        });

        // Reset forms when modals are closed
        document.getElementById('recordPaymentModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('recordPaymentForm').reset();
            document.querySelector('input[name="payment_date"]').value = new Date().toISOString().split('T')[0];
        });

        document.getElementById('updateStatusModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('updateStatusForm').reset();
        });
    </script>
@endpush

@push('styles')
    <style>
        .custom-shadow {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(79, 70, 229, 0.05);
        }

        .icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .bg-primary-subtle {
            background-color: rgba(13, 110, 253, 0.1) !important;
        }

        .bg-success-subtle {
            background-color: rgba(25, 135, 84, 0.1) !important;
        }

        .bg-info-subtle {
            background-color: rgba(13, 202, 240, 0.1) !important;
        }

        .bg-warning-subtle {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }

        .bg-danger-subtle {
            background-color: rgba(220, 53, 69, 0.1) !important;
        }

        .bg-secondary-subtle {
            background-color: rgba(108, 117, 125, 0.1) !important;
        }
    </style>
@endpush
