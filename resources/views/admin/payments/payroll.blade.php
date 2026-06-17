@extends('layouts.admin')

@section('title', 'Generate Payroll')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Generate Payroll</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.payments.overview') }}" class="text-muted text-decoration-none">Payment & Finance</a>
                            </li>
                            <li class="breadcrumb-item active text-muted" aria-current="page">Payroll</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.payments.salary-setup') }}" class="btn btn-outline-primary">
                        <i class="ri-settings-line"></i> Salary Setup
                    </a>
                    <button class="btn btn-success" id="generatePayrollBtn">
                        <i class="ri-play-line"></i> Generate Payroll
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Month</label>
                            <select class="form-select" id="monthFilter">
                                @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $monthName)
                                    <option value="{{ $monthName }}" {{ $month === $monthName ? 'selected' : '' }}>{{ $monthName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Year</label>
                            <select class="form-select" id="yearFilter">
                                @for($i = now()->year; $i >= now()->year - 5; $i--)
                                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Department</label>
                            <select class="form-select" id="departmentFilter">
                                <option value="">All Departments</option>
                                <option value="Academic">Academic</option>
                                <option value="Administration">Administration</option>
                                <option value="Finance">Finance</option>
                                <option value="ICT">ICT</option>
                                <option value="Library">Library</option>
                                <option value="Security">Security</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary w-100" id="filterBtn">
                                <i class="ri-search-line"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Payroll Table -->
    <div class="row">
        <div class="col-12">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-header bg-transparent border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-semibold mb-0">Staff Payroll - {{ $month }} {{ $year }}</h6>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary" id="exportBtn">
                                <i class="ri-download-line"></i> Export CSV
                            </button>
                            <button class="btn btn-sm btn-outline-info" id="printBtn">
                                <i class="ri-printer-line"></i> Print PDF
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>STAFF NAME</th>
                                    <th>ROLE</th>
                                    <th>BASE PAY</th>
                                    <th>ALLOWANCES</th>
                                    <th>DEDUCTION</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staff as $member)
                                    @php
                                        $existingRecord = $existingPayroll->get($member->id);
                                        $roleName = $member->user->role->name ?? 'Default';
                                        $salaryStructure = $salaryStructures->get($roleName);
                                        
                                        // If exact match not found, try fallback mappings
                                        if (!$salaryStructure) {
                                            $roleMappings = [
                                                'Teacher' => 'Subject Teacher',
                                                'Admin' => 'Admin Staff',
                                                'Administrator' => 'Admin Staff',
                                                'Default' => 'Subject Teacher'
                                            ];
                                            
                                            $fallbackRole = $roleMappings[$roleName] ?? null;
                                            if ($fallbackRole) {
                                                $salaryStructure = $salaryStructures->get($fallbackRole);
                                            }
                                        }
                                        
                                        $basePay = $salaryStructure ? $salaryStructure->base_salary : 0;
                                        $allowances = $salaryStructure ? $salaryStructure->allowance : 0;
                                        $deductions = $salaryStructure ? $salaryStructure->deduction : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $photoUrl = $member->user->photo_path 
                                                        ? Storage::url($member->user->photo_path) 
                                                        : 'https://ui-avatars.com/api/?name='.urlencode($member->user->name ?? 'Staff').'&background=4f46e5&color=fff&size=32&rounded=true';
                                                @endphp
                                                <img src="{{ $photoUrl }}" alt="Staff" class="rounded-circle me-2" width="32" height="32">
                                                <div>
                                                    <div class="fw-semibold">{{ $member->user->name }}</div>
                                                    <small class="text-muted">{{ $member->staff_id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary">{{ $roleName }}</span>
                                        </td>
                                        <td class="fw-semibold">₦{{ number_format($existingRecord ? $existingRecord->base_pay : $basePay) }}</td>
                                        <td class="text-success">₦{{ number_format($existingRecord ? $existingRecord->allowances : $allowances) }}</td>
                                        <td class="text-danger">₦{{ number_format($existingRecord ? $existingRecord->deductions : $deductions) }}</td>
                                        <td>
                                            @if($existingRecord)
                                                <span class="badge bg-success-subtle text-success">
                                                    <i class="ri-check-line"></i> Generated
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning">
                                                    <i class="ri-time-line"></i> Pending
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ri-user-line display-6"></i>
                                                <p class="mt-2">No staff members found</p>
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

    <!-- Payroll Summary -->
    @if($existingPayroll->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card custom-shadow rounded-3 bg-white border">
                    <div class="card-header bg-transparent border-0">
                        <h6 class="fw-semibold mb-0">Payroll Summary - {{ $month }} {{ $year }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-primary-subtle rounded">
                                    <div class="fw-bold text-primary fs-5">{{ $existingPayroll->count() }}</div>
                                    <small class="text-muted">Staff Paid</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-success-subtle rounded">
                                    <div class="fw-bold text-success fs-5">₦{{ number_format($existingPayroll->sum('base_pay')) }}</div>
                                    <small class="text-muted">Total Base Pay</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-info-subtle rounded">
                                    <div class="fw-bold text-info fs-5">₦{{ number_format($existingPayroll->sum('allowances')) }}</div>
                                    <small class="text-muted">Total Allowances</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center p-3 bg-warning-subtle rounded">
                                    <div class="fw-bold text-warning fs-5">₦{{ number_format($existingPayroll->sum('net_pay')) }}</div>
                                    <small class="text-muted">Total Net Pay</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Filter functionality
    document.getElementById('filterBtn').addEventListener('click', function() {
        const month = document.getElementById('monthFilter').value;
        const year = document.getElementById('yearFilter').value;
        const department = document.getElementById('departmentFilter').value;
        
        const params = new URLSearchParams();
        params.append('month', month);
        params.append('year', year);
        if (department) params.append('department', department);
        
        window.location.href = '{{ route("admin.payments.payroll") }}?' + params.toString();
    });

    // Generate payroll functionality
    document.getElementById('generatePayrollBtn').addEventListener('click', function() {
        const month = document.getElementById('monthFilter').value;
        const year = document.getElementById('yearFilter').value;
        const department = document.getElementById('departmentFilter').value;

        if (confirm(`Are you sure you want to generate payroll for ${month} ${year}?`)) {
            const formData = new FormData();
            formData.append('month', month);
            formData.append('year', year);
            if (department) formData.append('department', department);

            fetch('{{ route("admin.payments.generate-payroll") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
                    alert(`✅ Success: ${data.message}`);
                    location.reload();
                } else {
                    alert(`❌ Error: ${data.message || 'Failed to generate payroll'}`);
                }
            })
            .catch(error => {
                console.error('Payroll generation error:', error);
                alert(`❌ Network Error: Unable to generate payroll. Please check your connection and try again.`);
            });
        }
    });

    // Export functionality
    document.getElementById('exportBtn').addEventListener('click', function() {
        const month = document.getElementById('monthFilter').value;
        const year = document.getElementById('yearFilter').value;
        const department = document.getElementById('departmentFilter').value;
        
        // Create CSV content
        let csvContent = "Staff Name,Role,Base Pay,Allowances,Deductions,Net Pay,Status\n";
        
        // Get table data
        const table = document.querySelector('.table tbody');
        const rows = table.querySelectorAll('tr');
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length > 0) {
                const staffName = cells[0].querySelector('.fw-semibold')?.textContent?.trim() || '';
                const role = cells[1].querySelector('.badge')?.textContent?.trim() || '';
                const basePay = cells[2].textContent?.trim().replace('₦', '').replace(/,/g, '') || '0';
                const allowances = cells[3].textContent?.trim().replace('₦', '').replace(/,/g, '') || '0';
                const deductions = cells[4].textContent?.trim().replace('₦', '').replace(/,/g, '') || '0';
                const status = cells[5].querySelector('.badge')?.textContent?.trim() || '';
                
                // Calculate net pay
                const netPay = (parseFloat(basePay) + parseFloat(allowances) - parseFloat(deductions)).toFixed(2);
                
                csvContent += `"${staffName}","${role}","${basePay}","${allowances}","${deductions}","${netPay}","${status}"\n`;
            }
        });
        
        // Download CSV file
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `payroll_${month}_${year}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        console.log('Payroll exported successfully');
    });

    // Print functionality - Generate PDF
    document.getElementById('printBtn').addEventListener('click', function() {
        const month = document.getElementById('monthFilter').value;
        const year = document.getElementById('yearFilter').value;
        const department = document.getElementById('departmentFilter').value;
        
        // Build PDF URL with current filter parameters
        const params = new URLSearchParams();
        if (month) params.append('month', month);
        if (year) params.append('year', year);
        if (department) params.append('department', department);
        
        const queryString = params.toString();
        const pdfUrl = '{{ route("admin.payments.payroll.pdf") }}' + (queryString ? '?' + queryString : '');
        
        // Open PDF in new tab
        window.open(pdfUrl, '_blank');
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
