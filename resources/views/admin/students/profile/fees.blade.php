@extends('layouts.admin')

@section('title', 'Student Profile - Fees & Payments')

@section('content')
<div class="main-content-container overflow-hidden">
    <!-- Student Profile Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary me-3 d-flex align-items-center gap-2">
                    <i class="ri-arrow-left-line"></i>
                    Back to Students
                </a>
                <div>
                    <h3 class="fs-20 fw-semibold mb-1">Student Profile</h3>
                    <p class="text-secondary mb-0">{{ $student->full_name ?? '-' }} - {{ $student->admission_no ?? '-' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="d-flex gap-2 justify-content-md-end">
                <button class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <i class="ri-add-line"></i>
                    Record Payment
                </button>
                <button class="btn btn-primary d-flex align-items-center gap-2" onclick="printFees()">
                    <i class="ri-printer-line"></i>
                    Print Statement
                </button>
            </div>
        </div>
    </div>
    
    <!-- Tab Navigation -->
    <div class="card custom-shadow rounded-3 bg-white border mb-4">
        <div class="card-body p-0">
            <ul class="nav nav-tabs border-0 px-4 pt-3" id="studentProfileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium" href="{{ route('admin.students.profile.overview', $student->id) }}">
                        <i class="ri-user-line me-2"></i>Overview
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium" href="{{ route('admin.students.profile.academic', $student->id) }}">
                        <i class="ri-graduation-cap-line me-2"></i>Academic Info
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link active fw-medium" href="{{ route('admin.students.profile.fees', $student->id) }}">
                        <i class="ri-money-dollar-circle-line me-2"></i>Fees & Payments
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium" href="{{ route('admin.students.profile.attendance', $student->id) }}">
                        <i class="ri-calendar-check-line me-2"></i>Attendance
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium" href="{{ route('admin.students.profile.documents', $student->id) }}">
                        <i class="ri-file-text-line me-2"></i>Documents
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Fee Summary Cards -->
    @php
        $invoices = isset($student->invoices) ? $student->invoices : collect();
        $totalFees = $invoices->sum(function($inv){ return (float)($inv->total_amount ?? 0); });
        $totalPaid = $invoices->sum(function($inv){ return (float)($inv->amount_paid ?? 0); });
        $outstanding = $invoices->sum(function($inv){
            $total = (float)($inv->total_amount ?? 0);
            $paid = (float)($inv->amount_paid ?? 0);
            $balance = $inv->balance !== null ? (float)$inv->balance : max(0, $total - $paid);
            return $balance;
        });
        $paymentRate = $totalFees > 0 ? round(($totalPaid / $totalFees) * 100, 1) : null;
    @endphp
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 48px; height: 48px;">
                                <i class="ri-money-dollar-circle-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">₦{{ number_format($totalPaid, 0) }}</h6>
                            <p class="text-secondary mb-0 small">Total Paid</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle" style="width: 48px; height: 48px;">
                                <i class="ri-time-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">₦{{ number_format($outstanding, 0) }}</h6>
                            <p class="text-secondary mb-0 small">Outstanding</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 48px; height: 48px;">
                                <i class="ri-calculator-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">₦{{ number_format($totalFees, 0) }}</h6>
                            <p class="text-secondary mb-0 small">Total Fees</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center bg-info-subtle text-info rounded-circle" style="width: 48px; height: 48px;">
                                <i class="ri-percent-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">{{ $paymentRate !== null ? $paymentRate.'%' : '—' }}</h6>
                            <p class="text-secondary mb-0 small">Payment Rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <div class="card custom-shadow rounded-3 bg-white border">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-semibold mb-0">
                <i class="ri-history-line me-2 text-primary"></i>Payment History - Academic Session {{ $student->academicSession->name ?? '—' }}
            </h6>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="ri-filter-line me-1"></i>Filter Term
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">All Terms</a></li>
                        <li><a class="dropdown-item" href="#">1st Term</a></li>
                        <li><a class="dropdown-item active" href="#">2nd Term</a></li>
                        <li><a class="dropdown-item" href="#">3rd Term</a></li>
                    </ul>
                </div>
                <button class="btn btn-sm btn-outline-primary" onclick="exportFees()">
                    <i class="ri-download-line me-1"></i>Export
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">Term</th>
                            <th class="fw-semibold">Payment Type</th>
                            <th class="fw-semibold">Amount</th>
                            <th class="fw-semibold">Payment Date</th>
                            <th class="fw-semibold">Payment Method</th>
                            <th class="fw-semibold">Status</th>
                            <th class="fw-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($invoices ?? collect()) as $inv)
                            @php
                                $termName = data_get($inv, 'term.name');
                                $badgeClass = 'bg-secondary';
                                $status = data_get($inv, 'status', '—');
                                if (strtolower((string)$status) === 'paid') $badgeClass = 'bg-success';
                                elseif (strtolower((string)$status) === 'pending') $badgeClass = 'bg-warning';
                                $amount = (float)($inv->total_amount ?? 0);
                                $method = '—';
                                $dateVal = data_get($inv, 'due_date') ?: data_get($inv, 'created_at');
                                $dateText = $dateVal ? \Carbon\Carbon::parse($dateVal)->format('jS M Y') : '—';
                                $type = data_get($inv, 'invoice_number', 'Invoice');
                            @endphp
                            <tr>
                                <td>
                                    @if($termName)
                                        <span class="badge bg-primary-subtle text-primary">{{ $termName }}</span>
                                    @else
                                        <span class="badge bg-light text-secondary">—</span>
                                    @endif
                                </td>
                                <td class="fw-medium">{{ $type }}</td>
                                <td class="fw-bold {{ strtolower((string)$status) === 'paid' ? 'text-success' : 'text-secondary' }}">₦{{ number_format($amount, 0) }}</td>
                                <td>{{ $dateText }}</td>
                                <td>{{ $method }}</td>
                                <td><span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#"><i class="ri-download-line me-2"></i>Download Receipt</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="ri-eye-line me-2"></i>View Details</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-secondary">No invoices found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Schedule -->
    <div class="card custom-shadow rounded-3 bg-white border mt-4">
        <div class="card-header bg-transparent border-0">
            <h6 class="fw-semibold mb-0">
                <i class="ri-calendar-schedule-line me-2 text-primary"></i>Payment Schedule 2024/2025
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-success-subtle text-success me-2">1st Term</span>
                            <small class="text-secondary">Sep - Dec 2024</small>
                        </div>
                        <h6 class="fw-semibold text-success mb-2">₦170,000 <small class="text-secondary">(Paid)</small></h6>
                        <ul class="list-unstyled small mb-0">
                            <li class="d-flex justify-content-between">
                                <span>School Fees:</span>
                                <span class="text-success">₦150,000 ✓</span>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span>PTA Levy:</span>
                                <span class="text-success">₦10,000 ✓</span>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span>Uniform Fee:</span>
                                <span class="text-success">₦10,000 ✓</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100 bg-warning-subtle">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-info-subtle text-info me-2">2nd Term</span>
                            <small class="text-secondary">Jan - Apr 2025</small>
                        </div>
                        <h6 class="fw-semibold text-warning mb-2">₦165,000 <small class="text-secondary">(Partial)</small></h6>
                        <ul class="list-unstyled small mb-0">
                            <li class="d-flex justify-content-between">
                                <span>School Fees:</span>
                                <span class="text-secondary">₦150,000 (Not Due)</span>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span>Excursion Fee:</span>
                                <span class="text-warning">₦15,000 (Pending)</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-secondary-subtle text-secondary me-2">3rd Term</span>
                            <small class="text-secondary">May - Jul 2025</small>
                        </div>
                        <h6 class="fw-semibold text-secondary mb-2">₦150,000 <small class="text-secondary">(Upcoming)</small></h6>
                        <ul class="list-unstyled small mb-0">
                            <li class="d-flex justify-content-between">
                                <span>School Fees:</span>
                                <span class="text-secondary">₦150,000</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        function printFees() {
            window.open('{{ route('admin.students.profile.fees.pdf', $student->id) }}', '_blank');
        }

        function exportFees() {
            const feeData = {
                student_name: "{{ $student->full_name ?? '' }}",
                admission_no: "{{ $student->admission_no ?? '' }}",
                class: "{{ trim((data_get($student, 'classArm.schoolClass.name') ?: '') . ' ' . (data_get($student, 'classArm.name') ?: '')) }}",
                academic_year: "{{ data_get($student, 'academicSession.name', '') }}",
                total_fees: "{{ number_format($totalFees, 0) }}",
                total_paid: "{{ number_format($totalPaid, 0) }}",
                outstanding: "{{ number_format($outstanding, 0) }}",
                payment_rate: "{{ $paymentRate !== null ? $paymentRate.'%' : 'N/A' }}"
            };

            const invoices = [];
            @foreach($invoices as $inv)
                invoices.push({
                    term: "{{ data_get($inv, 'term.name', 'N/A') }}",
                    payment_type: "{{ data_get($inv, 'invoice_number', 'Invoice') }}",
                    amount: "{{ number_format((float)($inv->total_amount ?? 0), 0) }}",
                    payment_date: "{{ (data_get($inv, 'due_date') ?: data_get($inv, 'created_at')) ? \Carbon\Carbon::parse(data_get($inv, 'due_date') ?: data_get($inv, 'created_at'))->format('jS M Y') : '—' }}",
                    payment_method: "—",
                    status: "{{ ucfirst(data_get($inv, 'status', '—')) }}"
                });
            @endforeach

            let csvContent = '"FIELD","VALUE"\n';
            Object.entries(feeData).forEach(([key, value]) => {
                csvContent += `"${key.replace(/_/g, ' ').toUpperCase()}","${value}"\n`;
            });

            csvContent += '\n"TERM","PAYMENT TYPE","AMOUNT","PAYMENT DATE","PAYMENT METHOD","STATUS"\n';
            invoices.forEach(inv => {
                csvContent += `"${inv.term}","${inv.payment_type}","${inv.amount}","${inv.payment_date}","${inv.payment_method}","${inv.status}"\n`;
            });

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `fees_${feeData.admission_no}_statement.csv`;
            link.click();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Export Successful',
                    text: 'Fee statement has been exported to CSV file.',
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                alert('Fee statement exported successfully!');
            }
        }
    </script>
@endpush
