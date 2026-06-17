<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #000;
            padding: 15px 20px;
        }
        
        .container {
            width: 100%;
            max-width: 100%;
        }
        
        /* Header Section */
        .header {
            text-align: center;
            padding-bottom: 10px;
            margin-bottom: 15px;
            border-bottom: 3px solid #1e40af;
        }
        
        .header h1 {
            color: #1e40af;
            font-size: 20px;
            margin-bottom: 3px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .header .subtitle {
            color: #64748b;
            font-size: 11px;
            margin-bottom: 5px;
        }
        
        .receipt-badge {
            background: #10b981;
            color: white;
            padding: 5px 20px;
            display: inline-block;
            font-weight: bold;
            font-size: 11px;
            margin-top: 5px;
            border: 2px solid #059669;
        }
        
        /* Two Column Layout */
        .two-column {
            width: 100%;
            margin-bottom: 10px;
        }
        
        .two-column table {
            width: 100%;
        }
        
        .two-column td {
            width: 50%;
            vertical-align: top;
            padding: 0 5px;
        }
        
        /* Info Box Styles */
        .info-box {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            margin-bottom: 10px;
        }
        
        .info-box table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-box td {
            padding: 4px 0;
            border-bottom: 1px dotted #e2e8f0;
        }
        
        .info-box tr:last-child td {
            border-bottom: none;
        }
        
        .info-box td:first-child {
            font-weight: bold;
            width: 40%;
            color: #475569;
            font-size: 9px;
        }
        
        .info-box td:last-child {
            color: #000;
            font-size: 10px;
        }
        
        /* Section Titles */
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1e40af;
            margin: 10px 0 8px 0;
            padding: 4px 0;
            border-bottom: 2px solid #1e40af;
            text-transform: uppercase;
        }
        
        /* Payment Details Table */
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            border: 1px solid #1e40af;
        }
        
        .payment-table th {
            background: #1e40af;
            color: white;
            padding: 6px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }
        
        .payment-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        
        .payment-table tr:last-child td {
            border-bottom: none;
        }
        
        .payment-table .amount-col {
            text-align: right;
            font-weight: bold;
        }
        
        /* Total Section */
        .total-section {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            margin: 10px 0;
        }
        
        .total-row {
            padding: 3px 0;
            border-bottom: 1px dotted #e2e8f0;
        }
        
        .total-row:last-child {
            border-bottom: none;
        }
        
        .total-row table {
            width: 100%;
        }
        
        .total-row td {
            padding: 2px 0;
            font-size: 10px;
        }
        
        .total-row td:last-child {
            text-align: right;
            font-weight: bold;
        }
        
        .grand-total {
            background: #1e40af;
            color: white;
            padding: 8px 10px;
            margin-top: 5px;
        }
        
        .grand-total table {
            width: 100%;
            color: white;
        }
        
        .grand-total td {
            font-size: 12px;
            font-weight: bold;
        }
        
        /* Signature Section */
        .signature-section {
            margin-top: 15px;
            padding-top: 10px;
        }
        
        .signature-section table {
            width: 100%;
        }
        
        .signature-section td {
            width: 50%;
            padding: 0 5px;
        }
        
        .signature-box {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #000;
        }
        
        .signature-label {
            font-weight: bold;
            color: #475569;
            margin-top: 5px;
            font-size: 9px;
        }
        
        /* Footer */
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #cbd5e1;
            text-align: center;
        }
        
        .footer p {
            font-size: 8px;
            color: #64748b;
            margin: 3px 0;
            line-height: 1.2;
        }
        
        .footer .bold {
            font-weight: bold;
            color: #000;
            font-size: 9px;
        }
        
        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            font-weight: bold;
            color: rgba(30, 64, 175, 0.02);
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="watermark">PAID</div>
    
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ strtoupper($schoolSettings->school_name ?? 'SECONDARY SCHOOL PORTAL') }}</h1>
            @if($schoolSettings->school_address || $schoolSettings->phone_number || $schoolSettings->email)
            <p class="subtitle">
                @if($schoolSettings->school_address){{ $schoolSettings->school_address }}@endif
                @if($schoolSettings->phone_number) | Tel: {{ $schoolSettings->phone_number }}@endif
                @if($schoolSettings->email) | {{ $schoolSettings->email }}@endif
            </p>
            @endif
            <p class="subtitle">Official Payment Receipt</p>
            <div class="receipt-badge">PAID</div>
        </div>
        
        <!-- Receipt & Student Information (Two Columns) -->
        <div class="two-column">
            <table>
                <tr>
                    <td>
                        <div class="info-box">
                            <table>
                                <tr>
                                    <td>Receipt No:</td>
                                    <td>{{ $invoice->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td>Date:</td>
                                    <td>{{ $invoice->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td>Session:</td>
                                    <td>{{ $invoice->academicSession->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td>Term:</td>
                                    <td>{{ $invoice->term->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td>
                        <div class="info-box">
                            <table>
                                <tr>
                                    <td>Student:</td>
                                    <td>{{ $invoice->student->user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td>Student ID:</td>
                                    <td>{{ $invoice->student->student_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td>Class:</td>
                                    <td>{{ optional($invoice->student->classArm->schoolClass)->level ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Payment Details -->
        <h2 class="section-title">Payment Details</h2>
        <table class="payment-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="amount-col">Amount (NGN)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $metadata = is_string($invoice->metadata) ? json_decode($invoice->metadata, true) : ($invoice->metadata ?? []);
                    $serviceName = $metadata['service_name'] ?? null;
                    
                    if (!$serviceName && $invoice->items && $invoice->items->isNotEmpty()) {
                        $firstItem = $invoice->items->first();
                        $serviceName = $firstItem->paymentSetup->payment_type ?? null;
                    }
                    
                    $serviceName = $serviceName ?? 'School Fees';
                @endphp
                <tr>
                    <td>{{ $serviceName }}</td>
                    <td class="amount-col">{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <table>
                    <tr>
                        <td>Total Amount:</td>
                        <td>NGN {{ number_format($invoice->total_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
            <div class="total-row">
                <table>
                    <tr>
                        <td>Amount Paid:</td>
                        <td>NGN {{ number_format($invoice->amount_paid, 2) }}</td>
                    </tr>
                </table>
            </div>
            <div class="total-row">
                <table>
                    <tr>
                        <td>Balance:</td>
                        <td>NGN {{ number_format($invoice->balance, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="grand-total">
            <table>
                <tr>
                    <td>TOTAL PAID AMOUNT:</td>
                    <td style="text-align: right;">NGN {{ number_format($invoice->amount_paid, 2) }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Payment Information -->
        @if($payment)
        <h2 class="section-title">Payment Information</h2>
        <div class="info-box">
            <table>
                <tr>
                    <td>Method:</td>
                    <td>{{ $payment->payment_method ?? 'Remita' }}</td>
                </tr>
                <tr>
                    <td>Reference:</td>
                    <td>{{ $payment->payment_reference ?? $invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td>{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y H:i') : $invoice->updated_at->format('d/m/Y H:i') }}</td>
                </tr>
                @if($payment->transaction_id)
                <tr>
                    <td>Trans ID:</td>
                    <td>{{ $payment->transaction_id }}</td>
                </tr>
                @endif
            </table>
        </div>
        @endif
        
        <!-- Signature Section -->
        <div class="signature-section">
            <table>
                <tr>
                    <td>
                        <div class="signature-box">
                            <div class="signature-label">Student's Signature</div>
                        </div>
                    </td>
                    <td>
                        <div class="signature-box">
                            <div class="signature-label">School Official</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p class="bold">{{ strtoupper($schoolSettings->school_name ?? 'SECONDARY SCHOOL PORTAL') }}</p>
            <p>Computer-generated receipt. No signature required. For queries, contact school administration.</p>
            <p>Generated: {{ now()->format('d/m/Y H:i') }} | Thank you for your payment</p>
        </div>
    </div>
</body>
</html>
