@extends('layouts.admin')

@section('title', 'Payment & Finance')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Payment & Finance</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active text-muted" aria-current="page">Payment & Finance</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary">
                        <i class="ri-download-line"></i> Export Report
                    </button>
                    <button class="btn btn-primary">
                        <i class="ri-add-line"></i> New Transaction
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Collected Amount</p>
                            <h4 class="fw-bold mb-0 text-success">₦{{ number_format($stats['total_collected'] ?? 0) }}</h4>
                            <small class="text-success">
                                <i class="ri-arrow-up-line"></i> +2.5% from last month
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
                            <p class="text-muted mb-1">Outstanding Fees</p>
                            <h4 class="fw-bold mb-0 text-warning">₦{{ number_format($stats['outstanding_fees'] ?? 0) }}</h4>
                            <small class="text-warning">
                                <i class="ri-arrow-down-line"></i> -1.2% from last month
                            </small>
                        </div>
                        <div class="icon-wrapper bg-warning-subtle text-warning">
                            <i class="ri-time-line fs-2"></i>
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
                            <p class="text-muted mb-1">Total Payroll Expense</p>
                            <h4 class="fw-bold mb-0 text-danger">₦{{ number_format($stats['payroll_expense'] ?? 0) }}</h4>
                            <small class="text-muted">
                                <i class="ri-arrow-right-line"></i> Monthly expense
                            </small>
                        </div>
                        <div class="icon-wrapper bg-danger-subtle text-danger">
                            <i class="ri-user-settings-line fs-2"></i>
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
                            <p class="text-muted mb-1">Net Income</p>
                            <h4 class="fw-bold mb-0 text-info">₦{{ number_format(($stats['total_collected'] ?? 0) - ($stats['payroll_expense'] ?? 0)) }}</h4>
                            <small class="text-info">
                                <i class="ri-arrow-up-line"></i> +5.7% from last month
                            </small>
                        </div>
                        <div class="icon-wrapper bg-info-subtle text-info">
                            <i class="ri-line-chart-line fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Financial Summary -->
    <div class="row g-3">
        <!-- Quick Actions -->
        <div class="col-xl-8">
            <div class="card custom-shadow rounded-3 bg-white border h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="fw-semibold mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Generate Payroll -->
                        <div class="col-md-6">
                            <a href="{{ route('admin.payments.payroll') }}" class="text-decoration-none">
                                <div class="card bg-primary-subtle border-primary border h-100 hover-card">
                                    <div class="card-body p-4 text-center">
                                        <div class="icon-wrapper mb-3">
                                            <i class="ri-user-star-line fs-1 text-primary"></i>
                                        </div>
                                        <h6 class="fw-semibold mb-2 text-primary">Generate Payroll</h6>
                                        <p class="text-muted mb-0 small">Process staff salaries and generate payroll records</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Payment Setup -->
                        <div class="col-md-6">
                            <a href="{{ route('admin.payments.setup') }}" class="text-decoration-none">
                                <div class="card bg-success-subtle border-success border h-100 hover-card">
                                    <div class="card-body p-4 text-center">
                                        <div class="icon-wrapper mb-3">
                                            <i class="ri-settings-2-line fs-1 text-success"></i>
                                        </div>
                                        <h6 class="fw-semibold mb-2 text-success">Payment Setup</h6>
                                        <p class="text-muted mb-0 small">Configure school fees and payment structures</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Fees & Income -->
                        <div class="col-md-6">
                            <a href="{{ route('admin.payments.fees-income') }}" class="text-decoration-none">
                                <div class="card bg-warning-subtle border-warning border h-100 hover-card">
                                    <div class="card-body p-4 text-center">
                                        <div class="icon-wrapper mb-3">
                                            <i class="ri-money-cny-box-line fs-1 text-warning"></i>
                                        </div>
                                        <h6 class="fw-semibold mb-2 text-warning">Fees & Income</h6>
                                        <p class="text-muted mb-0 small">Track student payments and revenue</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Financial Reports -->
                        <div class="col-md-6">
                            <a href="{{ route('admin.payments.reports') }}" class="text-decoration-none">
                                <div class="card bg-info-subtle border-info border h-100 hover-card">
                                    <div class="card-body p-4 text-center">
                                        <div class="icon-wrapper mb-3">
                                            <i class="ri-bar-chart-box-line fs-1 text-info"></i>
                                        </div>
                                        <h6 class="fw-semibold mb-2 text-info">Financial Reports</h6>
                                        <p class="text-muted mb-0 small">Generate detailed financial reports and analytics</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary Chart -->
        <div class="col-xl-4">
            <div class="card custom-shadow rounded-3 bg-white border h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="fw-semibold mb-0">Income vs Expenses</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <canvas id="financialChart" width="300" height="300"></canvas>
                    </div>
                    <div class="d-flex justify-content-center gap-4">
                        <div class="text-center">
                            <div class="d-flex align-items-center justify-content-center mb-1">
                                <div class="bg-primary rounded-circle" style="width: 12px; height: 12px;"></div>
                                <small class="text-muted ms-2">Income</small>
                            </div>
                            <div class="fw-semibold">75.0%</div>
                        </div>
                        <div class="text-center">
                            <div class="d-flex align-items-center justify-content-center mb-1">
                                <div class="bg-warning rounded-circle" style="width: 12px; height: 12px;"></div>
                                <small class="text-muted ms-2">Expenses</small>
                            </div>
                            <div class="fw-semibold">25.0%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-header bg-transparent border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-semibold mb-0">Recent Transactions</h6>
                        <a href="{{ route('admin.payments.fees-income') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>DATE</th>
                                    <th>STUDENT</th>
                                    <th>TYPE</th>
                                    <th>SESSION/TERM</th>
                                    <th>AMOUNT</th>
                                    <th>METHOD</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <i class="ri-user-line"></i>
                                                </div>
                                                <div>
                                                    <div>{{ $payment->invoice?->student?->user?->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $payment->invoice?->student?->classArm?->schoolClass?->level ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $metadata = $payment->invoice && is_string($payment->invoice->metadata) ? json_decode($payment->invoice->metadata, true) : ($payment->invoice?->metadata ?? []);
                                                $serviceName = $metadata['service_name'] ?? 'School Fees';
                                            @endphp
                                            {{ $serviceName }}
                                        </td>
                                        <td>
                                            <div>{{ $payment->invoice?->academicSession?->name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $payment->invoice?->term?->name ?? 'N/A' }}</small>
                                        </td>
                                        <td class="fw-semibold">₦{{ number_format($payment->amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info">
                                                {{ $payment->payment_method }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ri-money-dollar-box-line display-6"></i>
                                                <p class="mt-2">No recent transactions found</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Financial Chart
    const ctx = document.getElementById('financialChart').getContext('2d');
    const financialChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Income', 'Expenses'],
            datasets: [{
                data: [{{ $stats['total_collected'] ?? 0 }}, {{ $stats['payroll_expense'] ?? 0 }}],
                backgroundColor: ['#0d6efd', '#ffc107'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endpush

@push('styles')
<style>
    .custom-shadow { 
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); 
    }
    .hover-card {
        transition: all 0.3s ease;
    }
    .hover-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
    .bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1) !important; }
</style>
@endpush
