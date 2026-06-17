<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
    <style>
        @page { margin: 20mm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; margin: 0; padding: 0; color: #333; line-height: 1.4; font-size: 12px; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #28a745; padding-bottom: 15px; }
        .header h1 { margin: 0 0 8px 0; color: #28a745; font-size: 22px; font-weight: bold; }
        .header p { margin: 3px 0; color: #666; font-size: 11px; }
        .school-info h2 { margin: 0; color: #333; font-size: 16px; font-weight: bold; }
        .receipt-info { background-color: #f5f5f5; padding: 12px; border: 1px solid #ddd; margin: 15px 0; border-left: 3px solid #28a745; }
        .receipt-info h3 { margin-top: 0; margin-bottom: 8px; color: #28a745; font-size: 13px; font-weight: bold; }
        .receipt-info p { margin: 3px 0; font-size: 11px; }
        .receipt-details { margin: 15px 0; }
        .detail-row { width: 100%; margin: 8px 0; padding: 6px 0; border-bottom: 1px dotted #ccc; }
        .detail-row:after { content: ""; display: table; clear: both; }
        .detail-label { float: left; font-weight: bold; width: 40%; }
        .detail-value { float: right; width: 58%; text-align: right; }
        .amount-section { background-color: #e8f5e8; padding: 15px; border: 1px solid #c3e6cb; margin: 15px 0; text-align: center; }
        .amount { font-size: 20px; font-weight: bold; color: #28a745; }
        .footer { margin-top: 25px; text-align: center; color: #666; font-size: 9px; border-top: 1px solid #ddd; padding-top: 12px; }
        .qr-section { text-align: center; margin: 15px 0; padding: 12px; border: 1px dashed #ccc; }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-info">
            <h2>SECONDARY SCHOOL MANAGEMENT PORTAL</h2>
            <p>Official Payment Receipt</p>
        </div>
        <h1>PAYMENT RECEIPT</h1>
        <p>Generated on: {{ date('F j, Y \a\t g:i A') }}</p>
    </div>
    
    <div class="receipt-info">
        <h3>Transaction Details</h3>
        <p><strong>Receipt #:</strong> {{ $transaction->reference_number }}</p>
        <p><strong>Payment Date:</strong> {{ $transaction->payment_date->format('F j, Y') }}</p>
        <p><strong>Status:</strong> {{ $transaction->status }}</p>
    </div>
    
    <div class="receipt-details">
        <div class="detail-row">
            <div class="detail-label">Student Name:</div>
            <div class="detail-value">{{ $transaction->student->user->name ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Student ID:</div>
            <div class="detail-value">{{ $transaction->student->student_id ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Payment Type:</div>
            <div class="detail-value">{{ $transaction->payment_type }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Level:</div>
            <div class="detail-value">{{ $transaction->level ?: 'All' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Term:</div>
            <div class="detail-value">{{ $transaction->term ?: 'All' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Payment Method:</div>
            <div class="detail-value">{{ $transaction->payment_method ?: 'Cash' }}</div>
        </div>
    </div>
    
    <div class="amount-section">
        <p style="margin: 0 0 10px 0; font-size: 16px; color: #666;">Amount Paid</p>
        <div class="amount">₦{{ number_format($transaction->amount) }}</div>
    </div>
    
    <div class="qr-section">
        <p style="margin: 0; font-size: 12px; color: #666;">
            Reference: {{ $transaction->reference_number }}<br>
            This receipt is valid and can be verified with the reference number above.
        </p>
    </div>
    
    <div class="footer">
        <p><strong>Thank you for your payment!</strong></p>
        <p>This is a computer-generated receipt and is valid without signature.</p>
        <p>For inquiries, please contact the school finance office.</p>
        <p>© {{ date('Y') }} Secondary School Management Portal. All rights reserved.</p>
    </div>
</body>
</html>
