<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fee Statement - {{ $student->full_name ?? 'Student' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 11px;
            color: #666;
        }
        .student-info {
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 5px;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            margin: 0 0 10px;
            font-size: 14px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            background: #f5f5f5;
            padding: 5px 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background: #f5f5f5;
            font-weight: bold;
        }
        .summary {
            margin-top: 20px;
            padding: 10px;
            background: #f5f5f5;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 5px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Fee Statement</h1>
        <p>Generated on: {{ now()->format('jS F Y, g:i A') }}</p>
    </div>

    @php
        $cls = data_get($student, 'classArm.schoolClass.name');
        $arm = data_get($student, 'classArm.name');
        $className = trim(($cls ?: '-') . ' ' . ($arm ?: ''));
        $invoices = $student->invoices ?? collect();
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

    <div class="student-info">
        <h3>Student Information</h3>
        <div class="info-grid">
            <div class="label">Full Name:</div>
            <div>{{ $student->full_name ?? '-' }}</div>
            <div class="label">Admission No:</div>
            <div>{{ $student->admission_no ?? '-' }}</div>
            <div class="label">Class:</div>
            <div>{{ $className }}</div>
            <div class="label">Academic Year:</div>
            <div>{{ data_get($student, 'academicSession.name', '—') }}</div>
        </div>
    </div>

    <div class="section">
        <h3>Payment History</h3>
        @if($invoices->isEmpty())
            <p style="text-align: center; padding: 20px;">No payment records found.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Term</th>
                        <th>Payment Type</th>
                        <th>Amount</th>
                        <th>Payment Date</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $inv)
                        @php
                            $termName = data_get($inv, 'term.name');
                            $status = data_get($inv, 'status', '—');
                            $amount = (float)($inv->total_amount ?? 0);
                            $dateVal = data_get($inv, 'due_date') ?: data_get($inv, 'created_at');
                            $dateText = $dateVal ? \Carbon\Carbon::parse($dateVal)->format('jS M Y') : '—';
                            $type = data_get($inv, 'invoice_number', 'Invoice');
                        @endphp
                        <tr>
                            <td>{{ $termName ?? '—' }}</td>
                            <td>{{ $type }}</td>
                            <td>₦{{ number_format($amount, 0) }}</td>
                            <td>{{ $dateText }}</td>
                            <td>—</td>
                            <td>{{ ucfirst($status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="summary">
        <h3>Fee Summary</h3>
        <div class="summary-grid">
            <div class="label">Total Fees:</div>
            <div>₦{{ number_format($totalFees, 0) }}</div>
            <div class="label">Total Paid:</div>
            <div>₦{{ number_format($totalPaid, 0) }}</div>
            <div class="label">Outstanding:</div>
            <div>₦{{ number_format($outstanding, 0) }}</div>
            <div class="label">Payment Rate:</div>
            <div>{{ $paymentRate !== null ? $paymentRate.'%' : 'N/A' }}</div>
        </div>
    </div>

    <div class="footer">
        <p>This document is computer-generated and does not require a signature.</p>
    </div>
</body>
</html>
