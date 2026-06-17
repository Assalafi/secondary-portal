@extends('layouts.admin')

@section('title', 'Financial Reports')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Report</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.payments.overview') }}" class="text-muted text-decoration-none">Payment & Finance</a>
                            </li>
                            <li class="breadcrumb-item active text-muted" aria-current="page">Report</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" id="exportReportBtn">
                        <i class="ri-download-line"></i> Export Report
                    </button>
                    <button class="btn btn-outline-secondary" id="printReportBtn">
                        <i class="ri-printer-line"></i> Print Report
                    </button>
                    <button class="btn btn-primary" id="generateReportBtn">
                        <i class="ri-file-chart-line"></i> Generate Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body p-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Date</label>
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control form-control-sm" id="dateFrom" placeholder="From">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold text-white">.</label>
                            <input type="date" class="form-control form-control-sm" id="dateTo" placeholder="To">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Report Type</label>
                            <select class="form-select form-select-sm" id="reportTypeFilter">
                                <option value="all">All</option>
                                <option value="fees_collected">Fees Collected</option>
                                <option value="outstanding_fees">Outstanding Fees</option>
                                <option value="payroll">Payroll</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Level</label>
                            <select class="form-select form-select-sm" id="levelFilter">
                                <option value="all">All</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level }}">{{ $level }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-semibold mb-0">Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-center p-4 bg-success-subtle rounded">
                                <h4 class="fw-bold text-success mb-1">₦{{ number_format($summary['total_collected'] ?? 0) }}</h4>
                                <p class="text-muted mb-0">Total Collected Amount</p>
                                <small class="text-success">
                                    <i class="ri-arrow-up-line"></i> vs last month
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-4 bg-warning-subtle rounded">
                                <h4 class="fw-bold text-warning mb-1">₦{{ number_format($summary['outstanding_fees'] ?? 0) }}</h4>
                                <p class="text-muted mb-0">Outstanding Fees</p>
                                <small class="text-warning">
                                    <i class="ri-arrow-down-line"></i> vs last month
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-4 bg-danger-subtle rounded">
                                <h4 class="fw-bold text-danger mb-1">₦{{ number_format($summary['payroll_expense'] ?? 0) }}</h4>
                                <p class="text-muted mb-0">Total Payroll Expense</p>
                                <small class="text-muted">
                                    <i class="ri-arrow-right-line"></i> vs last month
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics -->
    <div class="row mb-4">
        <!-- Income vs Expenses Chart -->
        <div class="col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border h-100">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-semibold mb-0">Income vs Expenses</h6>
                </div>
                <div class="card-body">
                    <canvas id="incomeExpenseChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Fees Collection vs Outstanding -->
        <div class="col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border h-100">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-semibold mb-0">Fees Collected vs Outstanding</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <canvas id="feesChart" width="300" height="300"></canvas>
                    </div>
                    <div class="d-flex justify-content-center gap-4 mt-3">
                        <div class="text-center">
                            <div class="d-flex align-items-center justify-content-center mb-1">
                                <div class="bg-primary rounded-circle" style="width: 12px; height: 12px;"></div>
                                <small class="text-muted ms-2">Collected Fees</small>
                            </div>
                            <div class="fw-semibold">75.0%</div>
                        </div>
                        <div class="text-center">
                            <div class="d-flex align-items-center justify-content-center mb-1">
                                <div class="bg-warning rounded-circle" style="width: 12px; height: 12px;"></div>
                                <small class="text-muted ms-2">Outstanding Fees</small>
                            </div>
                            <div class="fw-semibold">25.0%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="row">
        <div class="col-12">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-header bg-transparent border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-semibold mb-0">Report Table</h6>
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary-subtle text-primary">Fees Collected</span>
                            <span class="badge bg-warning-subtle text-warning">Outstanding Fees</span>
                            <span class="badge bg-danger-subtle text-danger">Payroll</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>PAYMENT TYPE</th>
                                    <th>LEVEL</th>
                                    <th>AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportData as $data)
                                    <tr>
                                        <td class="fw-semibold">{{ $data->payment_type }}</td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary">{{ $data->level ?? 'All' }}</span>
                                        </td>
                                        <td class="fw-bold text-success">₦{{ number_format($data->amount) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ri-file-chart-line display-6"></i>
                                                <p class="mt-2">No report data found</p>
                                                <p class="small">Adjust your filters or date range</p>
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
    // Income vs Expenses Line Chart
    const incomeCtx = document.getElementById('incomeExpenseChart').getContext('2d');
    const incomeExpenseChart = new Chart(incomeCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Income',
                data: [120000, 190000, 300000, 500000, 200000, 300000, 450000, 600000, 400000, 350000, 280000, 500000],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Expenses',
                data: [80000, 120000, 180000, 250000, 150000, 200000, 280000, 350000, 220000, 180000, 160000, 300000],
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₦' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Fees Collection Pie Chart
    const feesCtx = document.getElementById('feesChart').getContext('2d');
    const feesChart = new Chart(feesCtx, {
        type: 'doughnut',
        data: {
            labels: ['Collected Fees', 'Outstanding Fees'],
            datasets: [{
                data: [{{ $summary['total_collected'] ?? 1 }}, {{ $summary['outstanding_fees'] ?? 1 }}],
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

    // Generate Report functionality
    document.getElementById('generateReportBtn').addEventListener('click', function() {
        const reportType = document.getElementById('reportTypeFilter').value;
        const level = document.getElementById('levelFilter').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        
        const params = new URLSearchParams();
        if (reportType !== 'all') params.append('report_type', reportType);
        if (level !== 'all') params.append('level', level);
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);
        
        const queryString = params.toString();
        const url = '{{ route("admin.payments.reports") }}' + (queryString ? '?' + queryString : '');
        window.location.href = url;
    });

    // Auto-filter on select change
    document.querySelectorAll('#reportTypeFilter, #levelFilter').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('generateReportBtn').click();
        });
    });

    // Set filter values from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('report_type')) {
        document.getElementById('reportTypeFilter').value = urlParams.get('report_type');
    }
    if (urlParams.get('level')) {
        document.getElementById('levelFilter').value = urlParams.get('level');
    }
    if (urlParams.get('date_from')) {
        document.getElementById('dateFrom').value = urlParams.get('date_from');
    }
    if (urlParams.get('date_to')) {
        document.getElementById('dateTo').value = urlParams.get('date_to');
    }

    // Export Report functionality
    document.getElementById('exportReportBtn').addEventListener('click', function() {
        const reportType = document.getElementById('reportTypeFilter').value;
        const level = document.getElementById('levelFilter').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        
        // Create CSV content
        let csvContent = "Financial Report\n";
        csvContent += "Generated on: " + new Date().toLocaleDateString() + "\n";
        csvContent += "Report Type: " + (reportType === 'all' ? 'All' : reportType.replace('_', ' ').toUpperCase()) + "\n";
        csvContent += "Level: " + (level === 'all' ? 'All' : level) + "\n";
        csvContent += "Date Range: " + (dateFrom || 'All') + " to " + (dateTo || 'All') + "\n\n";
        
        // Add summary section
        csvContent += "SUMMARY\n";
        csvContent += "Total Collected,₦{{ number_format($summary['total_collected'] ?? 0) }}\n";
        csvContent += "Outstanding Fees,₦{{ number_format($summary['outstanding_fees'] ?? 0) }}\n";
        csvContent += "Payroll Expense,₦{{ number_format($summary['payroll_expense'] ?? 0) }}\n\n";
        
        // Add detailed data
        csvContent += "DETAILED REPORT\n";
        csvContent += "Payment Type,Level,Amount\n";
        
        // Get table data
        const table = document.querySelector('.table tbody');
        const rows = table.querySelectorAll('tr');
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length >= 3) {
                const paymentType = cells[0].textContent.trim();
                const level = cells[1].querySelector('.badge')?.textContent?.trim() || '';
                const amount = cells[2].textContent.trim().replace('₦', '').replace(/,/g, '');
                
                csvContent += `"${paymentType}","${level}","${amount}"\n`;
            }
        });
        
        // Download CSV file
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `financial_report_${new Date().toISOString().split('T')[0]}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        console.log('Financial report exported successfully');
    });

    // Print Report functionality - Generate PDF
    document.getElementById('printReportBtn').addEventListener('click', function() {
        const reportType = document.getElementById('reportTypeFilter').value;
        const level = document.getElementById('levelFilter').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        
        // Build PDF URL with current filter parameters
        const params = new URLSearchParams();
        if (reportType !== 'all') params.append('report_type', reportType);
        if (level !== 'all') params.append('level', level);
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);
        
        const queryString = params.toString();
        const pdfUrl = '{{ route("admin.payments.reports.pdf") }}' + (queryString ? '?' + queryString : '');
        
        // Open PDF in new tab
        window.open(pdfUrl, '_blank');
    });

    // Enhanced date range functionality
    document.getElementById('dateFrom').addEventListener('change', function() {
        const fromDate = this.value;
        const toDateInput = document.getElementById('dateTo');
        
        if (fromDate && !toDateInput.value) {
            // Auto-set "to" date to end of month if only "from" date is selected
            const date = new Date(fromDate);
            const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
            toDateInput.value = lastDay.toISOString().split('T')[0];
        }
        
        // Set minimum date for "to" field
        if (fromDate) {
            toDateInput.min = fromDate;
        }
    });

    // Auto-generate report when date range changes
    document.getElementById('dateTo').addEventListener('change', function() {
        const fromDate = document.getElementById('dateFrom').value;
        const toDate = this.value;
        
        if (fromDate && toDate) {
            setTimeout(() => {
                document.getElementById('generateReportBtn').click();
            }, 500); // Small delay for better UX
        }
    });

    // Initialize date inputs with current month range
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        
        if (!document.getElementById('dateFrom').value) {
            document.getElementById('dateFrom').value = firstDay.toISOString().split('T')[0];
        }
        if (!document.getElementById('dateTo').value) {
            document.getElementById('dateTo').value = lastDay.toISOString().split('T')[0];
        }
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
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
    .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
    .bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1) !important; }
</style>
@endpush
