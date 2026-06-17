<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        @page { margin: 20mm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; margin: 0; padding: 0; color: #333; line-height: 1.4; font-size: 11px; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #0d6efd; padding-bottom: 15px; }
        .header h1 { margin: 0 0 8px 0; color: #0d6efd; font-size: 24px; font-weight: bold; }
        .header p { margin: 3px 0; color: #666; font-size: 12px; }
        .school-info { text-align: center; margin-bottom: 15px; }
        .school-info h2 { margin: 0; color: #333; font-size: 18px; font-weight: bold; }
        .report-info { background-color: #f5f5f5; padding: 12px; border: 1px solid #ddd; margin: 15px 0; border-left: 3px solid #0d6efd; }
        .report-info h3 { margin-top: 0; margin-bottom: 8px; color: #0d6efd; font-size: 14px; font-weight: bold; }
        .report-info p { margin: 3px 0; font-size: 11px; }
        .summary-section { margin: 20px 0; }
        .summary-card { display: inline-block; width: 32%; margin: 0 0.5%; padding: 12px; text-align: center; vertical-align: top; border: 1px solid #ddd; }
        .summary-card.success { background-color: #e8f5e8; border-color: #c3e6cb; }
        .summary-card.warning { background-color: #fff8e1; border-color: #ffeaa7; }
        .summary-card.danger { background-color: #fdeaea; border-color: #f5b7b1; }
        .summary-card h4 { margin: 0 0 4px 0; font-size: 16px; font-weight: bold; color: #333; }
        .summary-card p { margin: 0; color: #666; font-size: 10px; }
        .section-title { color: #0d6efd; font-size: 16px; font-weight: bold; margin: 25px 0 12px 0; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; font-size: 10px; }
        th { background-color: #f5f5f5; font-weight: bold; color: #333; }
        tbody tr:nth-child(even) { background-color: #fafafa; }
        .amount { font-weight: bold; color: #28a745; text-align: right; }
        .total-row { background-color: #e9ecef !important; font-weight: bold; }
        .footer { margin-top: 25px; text-align: center; color: #666; font-size: 9px; border-top: 1px solid #ddd; padding-top: 12px; }
        .no-data { text-align: center; color: #666; padding: 20px; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-info">
            <h2>SECONDARY SCHOOL MANAGEMENT PORTAL</h2>
            <p>Financial Management System</p>
        </div>
        <h1>Financial Report</h1>
        <p>Generated on: {{ date('F j, Y \a\t g:i A') }}</p>
    </div>
    
    <div class="report-info">
        <h3>Report Parameters</h3>
        <p><strong>Report Type:</strong> {{ $reportTypeText }}</p>
        <p><strong>Level:</strong> {{ $levelText }}</p>
        <p><strong>Date Range:</strong> {{ $dateRangeText }}</p>
    </div>
    
    <div class="section-title">Financial Summary</div>
    <div class="summary-section">
        <div class="summary-card success">
            <h4>₦{{ number_format($summary['total_collected']) }}</h4>
            <p>Total Collected</p>
        </div>
        <div class="summary-card warning">
            <h4>₦{{ number_format($summary['outstanding_fees']) }}</h4>
            <p>Outstanding Fees</p>
        </div>
        <div class="summary-card danger">
            <h4>₦{{ number_format($summary['payroll_expense']) }}</h4>
            <p>Payroll Expense</p>
        </div>
    </div>
    
    <div class="section-title">Detailed Report</div>
    <table>
        <thead>
            <tr>
                <th>Payment Type</th>
                <th>Level</th>
                <th>Amount (₦)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; @endphp
            @forelse($reportData as $data)
                @php $totalAmount += $data->amount; @endphp
                <tr>
                    <td>{{ $data->payment_type }}</td>
                    <td>{{ $data->level ?: 'All' }}</td>
                    <td class="amount">{{ number_format($data->amount) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="no-data">No data available for the selected criteria</td>
                </tr>
            @endforelse
            
            @if($reportData->count() > 0)
                <tr class="total-row">
                    <td colspan="2"><strong>TOTAL ({{ $reportData->count() }} entries)</strong></td>
                    <td class="amount"><strong>{{ number_format($totalAmount) }}</strong></td>
                </tr>
            @endif
        </tbody>
    </table>
    
    <div class="footer">
        <p>This report was generated automatically by the School Management System</p>
        <p>For questions about this report, please contact the finance department</p>
        <p>© {{ date('Y') }} Secondary School Management Portal. All rights reserved.</p>
    </div>
</body>
</html>
