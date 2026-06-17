<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Details - {{ $application->application_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #000;
            padding: 0;
            margin: 0;
        }
        .page {
            padding: 15px;
        }
        /* Letterhead */
        .letterhead {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            border-bottom: 3px solid #000;
        }
        .school-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 10px;
            background: #003366;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 36px;
            font-weight: bold;
        }
        .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #003366;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .school-address {
            font-size: 9px;
            color: #666;
            margin-bottom: 2px;
        }
        .school-contact {
            font-size: 9px;
            color: #666;
        }
        .document-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #000;
            text-decoration: underline;
            margin: 20px 0 10px;
            text-transform: uppercase;
        }
        /* Application Info Box */
        .app-info-box {
            background: #f8f9fa;
            border: 2px solid #003366;
            padding: 10px;
            margin-bottom: 15px;
            position: relative;
        }
        .student-photo {
            position: absolute;
            right: 10px;
            top: 10px;
            width: 100px;
            height: 120px;
            border: 2px solid #003366;
            overflow: hidden;
        }
        .student-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .app-details {
            width: 65%;
        }
        .app-number {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        .status-submitted {
            background-color: #0066cc;
            color: white;
        }
        .status-approved {
            background-color: #00aa00;
            color: white;
        }
        .status-rejected {
            background-color: #cc0000;
            color: white;
        }
        .status-draft {
            background-color: #666666;
            color: white;
        }
        /* Sections */
        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #003366;
            color: white;
            padding: 5px 8px;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 35%;
            color: #333;
        }
        .info-table td:last-child {
            width: 65%;
            color: #000;
        }
        /* Payment Box */
        .payment-box {
            background-color: #e8f5e9;
            border: 1px solid #00aa00;
            padding: 8px;
            margin-top: 5px;
        }
        .payment-amount {
            font-size: 14px;
            font-weight: bold;
            color: #00aa00;
        }
        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #000;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Letterhead -->
        <div class="letterhead">
            @if(config('app.school_logo'))
                <img src="{{ public_path(config('app.school_logo')) }}" alt="Logo" style="width: 80px; height: 80px; margin: 0 auto 10px;">
            @else
                <div class="school-logo">S</div>
            @endif
            <div class="school-name">{{ config('app.school_name', 'SCHOOL NAME') }}</div>
            <div class="school-address">{{ config('app.school_address', 'School Address Here') }}</div>
            <div class="school-contact">
                Tel: {{ config('app.school_phone', '+234 XXX XXX XXXX') }} | 
                Email: {{ config('app.school_email', 'info@school.com') }}
            </div>
        </div>

        <!-- Document Title -->
        <div class="document-title">Admission Application Form</div>

        <!-- Application Info Box with Photo -->
        <div class="app-info-box">
            @if($application->passport_photo_path)
                <div class="student-photo">
                    <img src="{{ public_path('storage/' . $application->passport_photo_path) }}" alt="Photo">
                </div>
            @endif
            <div class="app-details">
                <div class="app-number">Application No: {{ $application->application_number }}</div>
                <div style="margin-bottom: 5px;">Submitted: {{ $application->submitted_at?->format('F d, Y h:i A') ?? 'Not Submitted' }}</div>
                <div>Status: <span class="status-badge status-{{ strtolower($application->status) }}">{{ $application->status }}</span></div>
                <div style="margin-top: 5px;">Date Generated: {{ date('F d, Y h:i A') }}</div>
            </div>
        </div>

        <!-- Student Information -->
        <div class="section">
            <div class="section-title">Student Information</div>
            <table class="info-table">
                <tr>
                    <td>Full Name:</td>
                    <td>{{ $application->full_name }}</td>
                </tr>
                <tr>
                    <td>Date of Birth:</td>
                    <td>{{ $application->date_of_birth?->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <td>Gender:</td>
                    <td>{{ $application->gender }}</td>
                </tr>
                <tr>
                    <td>Nationality:</td>
                    <td>{{ $application->nationality }}</td>
                </tr>
                <tr>
                    <td>State of Origin:</td>
                    <td>{{ $application->state_of_origin ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>LGA:</td>
                    <td>{{ $application->lga ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Religion:</td>
                    <td>{{ $application->religion ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Blood Group:</td>
                    <td>{{ $application->blood_group ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Place of Birth:</td>
                    <td>{{ $application->place_of_birth_town ?? '' }}{{ $application->place_of_birth_lga ? ', ' . $application->place_of_birth_lga : '' }}{{ $application->place_of_birth_state ? ', ' . $application->place_of_birth_state : '' }}</td>
                </tr>
                <tr>
                    <td>Health Status:</td>
                    <td>{{ $application->health_status ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Medical Conditions:</td>
                    <td>{{ $application->medical_conditions ?? 'None' }}</td>
                </tr>
                <tr>
                    <td>Disability Details:</td>
                    <td>{{ $application->disability_details ?? 'None' }}</td>
                </tr>
                <tr>
                    <td>Home Address:</td>
                    <td>{{ $application->home_address ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- Academic Information -->
        <div class="section">
            <div class="section-title">Academic Information</div>
            <table class="info-table">
                <tr>
                    <td>Proposed Class:</td>
                    <td>{{ $application->proposedClass->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Class Arm:</td>
                    <td>{{ $application->proposedClassArm->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Academic Session:</td>
                    <td>{{ $application->academicSession->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Previous School:</td>
                    <td>{{ $application->previous_school ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Reason for Admission:</td>
                    <td>{{ $application->reason_for_admission ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- Guardian Information -->
        <div class="section">
            <div class="section-title">Guardian Information</div>
            <table class="info-table">
                <tr>
                    <td>Guardian Name:</td>
                    <td>{{ $application->guardian_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Relationship:</td>
                    <td>{{ $application->guardian_relationship ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Phone Number:</td>
                    <td>{{ $application->guardian_phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>{{ $application->guardian_email ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Occupation:</td>
                    <td>{{ $application->guardian_occupation ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Address:</td>
                    <td>{{ $application->guardian_address ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>City:</td>
                    <td>{{ $application->guardian_city ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>State:</td>
                    <td>{{ $application->guardian_state ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- Emergency Contact -->
        <div class="section">
            <div class="section-title">Emergency Contact</div>
            <table class="info-table">
                <tr>
                    <td>Contact Name:</td>
                    <td>{{ $application->emergency_contact_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Contact Phone:</td>
                    <td>{{ $application->emergency_contact_phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Relationship:</td>
                    <td>{{ $application->emergency_contact_relationship ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment Information -->
        @if($application->invoice)
            <div class="section">
                <div class="section-title">Payment Information</div>
                <div class="payment-box">
                    <table class="info-table" style="border: none;">
                        <tr>
                            <td style="border: none;">Invoice Number:</td>
                            <td style="border: none;">{{ $application->invoice->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td style="border: none;">Total Amount:</td>
                            <td style="border: none;"><span class="payment-amount">₦{{ number_format($application->invoice->total_amount, 2) }}</span></td>
                        </tr>
                        <tr>
                            <td style="border: none;">Amount Paid:</td>
                            <td style="border: none;">₦{{ number_format($application->invoice->amount_paid, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="border: none;">Status:</td>
                            <td style="border: none;"><strong>{{ $application->invoice->status }}</strong></td>
                        </tr>
                        @if($application->invoice->metadata)
                            @php $metadata = json_decode($application->invoice->metadata, true); @endphp
                            @if(isset($metadata['RRR']))
                                <tr>
                                    <td style="border: none;">RRR Reference:</td>
                                    <td style="border: none;">{{ $metadata['RRR'] }}</td>
                                </tr>
                            @endif
                        @endif
                    </table>
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p><strong>This is a computer-generated document. No signature is required.</strong></p>
            <p>Generated on {{ date('F d, Y h:i A') }}</p>
            <p>For inquiries, please contact the school administration.</p>
        </div>
    </div>
</body>
</html>
