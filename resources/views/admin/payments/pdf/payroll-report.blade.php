<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payroll Report</title>
    <style>
        @page { margin: 15mm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; margin: 0; padding: 0; color: #333; line-height: 1.3; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0d6efd; padding-bottom: 15px; }
        .header h1 { margin: 0 0 8px 0; color: #0d6efd; font-size: 20px; font-weight: bold; }
        .header p { margin: 2px 0; color: #666; font-size: 10px; }
        .school-info h2 { margin: 0; color: #333; font-size: 16px; font-weight: bold; }
        .payroll-info { background-color: #f5f5f5; padding: 10px; border: 1px solid #ddd; margin: 15px 0; border-left: 3px solid #28a745; }
        .payroll-info h3 { margin-top: 0; margin-bottom: 6px; color: #28a745; font-size: 12px; font-weight: bold; }
        .payroll-info p { margin: 2px 0; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 8px; }
        th, td { border: 1px solid #ddd; padding: 4px 6px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; color: #333; }
        tbody tr:nth-child(even) { background-color: #fafafa; }
        .amount { text-align: right; font-weight: bold; }
        .total-row { background-color: #e9ecef !important; font-weight: bold; }
        .status-generated { color: #28a745; font-weight: bold; font-size: 7px; }
        .status-pending { color: #ffc107; font-weight: bold; font-size: 7px; }
        .footer { margin-top: 20px; text-align: center; color: #666; font-size: 8px; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-info">
            <h2>SECONDARY SCHOOL MANAGEMENT PORTAL</h2>
            <p>Human Resources & Payroll System</p>
        </div>
        <h1>Staff Payroll Report</h1>
        <p>{{ $month }} {{ $year }}{{ $department ? ' - ' . $department . ' Department' : '' }}</p>
        <p>Generated on: {{ date('F j, Y \a\t g:i A') }}</p>
    </div>
    
    <div class="payroll-info">
        <h3>Payroll Summary</h3>
        <p><strong>Period:</strong> {{ $month }} {{ $year }}</p>
        <p><strong>Department:</strong> {{ $department ?: 'All Departments' }}</p>
        <p><strong>Total Staff:</strong> {{ $staff->count() }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Staff Name</th>
                <th>Role</th>
                <th>Base Pay</th>
                <th>Allowances</th>
                <th>Deductions</th>
                <th>Net Pay</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalBasePay = 0;
                $totalAllowances = 0;
                $totalDeductions = 0;
                $totalNetPay = 0;
                $generatedCount = 0;
            @endphp
            
            @foreach($staff as $member)
                @php
                    $existingRecord = $existingPayroll->get($member->id);
                    $roleName = $member->user->role->name ?? 'Default';
                    $salaryStructure = $salaryStructures->get($roleName);
                    
                    // Apply fallback mappings if needed
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
                    
                    if ($existingRecord) {
                        $basePay = $existingRecord->base_pay;
                        $allowances = $existingRecord->allowances;
                        $deductions = $existingRecord->deductions;
                        $netPay = $existingRecord->net_pay;
                        $status = 'Generated';
                        $generatedCount++;
                    } else {
                        $basePay = $salaryStructure ? $salaryStructure->base_salary : 0;
                        $allowances = $salaryStructure ? $salaryStructure->allowance : 0;
                        $deductions = $salaryStructure ? $salaryStructure->deduction : 0;
                        $netPay = $basePay + $allowances - $deductions;
                        $status = 'Pending';
                    }
                    
                    $totalBasePay += $basePay;
                    $totalAllowances += $allowances;
                    $totalDeductions += $deductions;
                    $totalNetPay += $netPay;
                @endphp
                
                <tr>
                    <td>{{ $member->user->name }}</td>
                    <td>{{ $roleName }}</td>
                    <td class="amount">₦{{ number_format($basePay) }}</td>
                    <td class="amount">₦{{ number_format($allowances) }}</td>
                    <td class="amount">₦{{ number_format($deductions) }}</td>
                    <td class="amount">₦{{ number_format($netPay) }}</td>
                    <td>
                        <span class="status-{{ strtolower($status) }}">{{ $status }}</span>
                    </td>
                </tr>
            @endforeach
            
            <tr class="total-row">
                <td colspan="2"><strong>TOTAL ({{ $staff->count() }} staff, {{ $generatedCount }} generated)</strong></td>
                <td class="amount"><strong>₦{{ number_format($totalBasePay) }}</strong></td>
                <td class="amount"><strong>₦{{ number_format($totalAllowances) }}</strong></td>
                <td class="amount"><strong>₦{{ number_format($totalDeductions) }}</strong></td>
                <td class="amount"><strong>₦{{ number_format($totalNetPay) }}</strong></td>
                <td><strong>-</strong></td>
            </tr>
        </tbody>
    </table>
    
    <div class="footer">
        <p>This payroll report was generated automatically by the School Management System</p>
        <p>For questions about payroll, please contact the Human Resources department</p>
        <p>© {{ date('Y') }} Secondary School Management Portal. All rights reserved.</p>
    </div>
</body>
</html>
