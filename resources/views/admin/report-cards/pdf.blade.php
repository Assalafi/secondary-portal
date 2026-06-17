<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $reportCard->student->full_name }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        
        .report-card {
            max-width: 210mm;
            margin: 0 auto;
            padding: 15mm;
            border: 1px solid #000;
        }
        
        .school-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .school-header h2 {
            font-size: 18pt;
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        
        .school-header p {
            margin: 2px 0;
            font-size: 11pt;
        }
        
        .report-title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin: 10px 0;
            text-decoration: underline;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 11pt;
        }
        
        table, th, td {
            border: 1px solid #000;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            padding: 8px;
        }
        
        td {
            padding: 6px;
            text-align: center;
        }
        
        td:first-child {
            text-align: left;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 12pt;
            margin: 15px 0 10px 0;
            text-decoration: underline;
        }
        
        .signature-section {
            margin-top: 30px;
        }
        
        .signature-box {
            width: 30%;
            float: left;
            text-align: center;
            margin: 10px;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            height: 40px;
            margin: 15px 0;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10pt;
            font-style: italic;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="report-card">
        <!-- School Header -->
        <div class="school-header">
            <h2>{{ config('app.name') }}</h2>
            <p>{{ config('app.address', '') }}</p>
            <p>{{ config('app.phone', '') }}</p>
        </div>

        <div class="report-title">STUDENT REPORT CARD</div>
        <p style="text-align: center; margin: 5px 0;">
            <strong>{{ $reportCard->report_type|upper }} - {{ $reportCard->academicSession->name ?? 'Annual' }} {{ $reportCard->term->name ?? '' }}</strong>
        </p>

        <!-- Student Information -->
        <table>
            <tr>
                <td><strong>Student Name:</strong> {{ $reportCard->student->full_name }}</td>
                <td><strong>Admission No:</strong> {{ $reportCard->student->admission_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Class:</strong> {{ $reportCard->class->name }}</td>
                <td><strong>Sex:</strong> {{ $reportCard->student->gender ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Position in Class:</strong> {{ $reportCard->class_position ?? 'N/A' }} out of {{ $reportCard->number_in_class ?? 'N/A' }}</td>
                <td><strong>Next Term Begins:</strong> {{ $reportCard->next_term_begins ? $reportCard->next_term_begins->format('d/m/Y') : 'N/A' }}</td>
            </tr>
        </table>

        <!-- Academic Performance -->
        <div class="section-title">ACADEMIC PERFORMANCE</div>
        <table>
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Subject</th>
                    <th>CA (30)</th>
                    <th>Exam (70)</th>
                    <th>Total (100)</th>
                    <th>Grade</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportCard->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left;">{{ $item->subject_name }}</td>
                    <td>{{ number_format($item->ca_score, 2) }}</td>
                    <td>{{ number_format($item->exam_score, 2) }}</td>
                    <td>{{ number_format($item->total_score, 2) }}</td>
                    <td>{{ $item->grade }}</td>
                    <td>{{ $item->remark }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align: center;"><strong>Total/Grand Average</strong></td>
                    <td>{{ number_format($reportCard->items->sum('ca_score'), 2) }}</td>
                    <td>{{ number_format($reportCard->items->sum('exam_score'), 2) }}</td>
                    <td>{{ number_format($reportCard->total_score, 2) }}</td>
                    <td>{{ $reportCard->final_grade }}</td>
                    <td>{{ $reportCard->final_remark }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Class Statistics -->
        <table>
            <tr>
                <td><strong>Class Average:</strong> {{ number_format($reportCard->class_average, 2) }}</td>
                <td><strong>Highest in Class:</strong> {{ number_format($reportCard->class_highest_average, 2) }}</td>
                <td><strong>Lowest in Class:</strong> {{ number_format($reportCard->class_lowest_average, 2) }}</td>
            </tr>
        </table>

        <!-- Affective Domain -->
        <div class="section-title">AFFECTIVE DOMAIN</div>
        <table>
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Attribute</th>
                    <th>Rating (1-5)</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportCard->affectiveRatings as $index => $rating)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left;">{{ $rating->trait->name }}</td>
                    <td>{{ $rating->rating_value }}</td>
                    <td>{{ $rating->rating_value >= 4 ? 'Excellent' : ($rating->rating_value == 3 ? 'Good' : 'Needs Improvement') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Psychomotor Skills -->
        <div class="section-title">PSYCHOMOTOR SKILLS</div>
        <table>
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Skill</th>
                    <th>Rating (1-5)</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportCard->psychomotorRatings as $index => $rating)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left;">{{ $rating->trait->name }}</td>
                    <td>{{ $rating->rating_value }}</td>
                    <td>{{ $rating->rating_value >= 4 ? 'Excellent' : ($rating->rating_value == 3 ? 'Good' : 'Needs Improvement') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Attendance Summary -->
        <div class="section-title">ATTENDANCE SUMMARY</div>
        <table>
            <tr>
                <td><strong>Times School Opened:</strong> {{ $reportCard->attendance_opened }}</td>
                <td><strong>Times Present:</strong> {{ $reportCard->attendance_present }}</td>
                <td><strong>Times Absent:</strong> {{ $reportCard->attendance_absent }}</td>
            </tr>
            <tr>
                <td><strong>Times Late:</strong> {{ $reportCard->attendance_late }}</td>
                <td colspan="2"><strong>Attendance Percentage:</strong> {{ number_format($reportCard->attendance_percentage, 2) }}%</td>
            </tr>
        </table>

        <!-- Comments -->
        <div class="section-title">COMMENTS</div>
        <table>
            <tr>
                <td style="text-align: left;"><strong>Class Teacher's Comment:</strong></td>
                <td style="text-align: left;">{{ $reportCard->class_teacher_comment ?: 'No comment provided' }}</td>
            </tr>
            <tr>
                <td style="text-align: left;"><strong>Principal's Comment:</strong></td>
                <td style="text-align: left;">{{ $reportCard->principal_comment ?: 'No comment provided' }}</td>
            </tr>
            <tr>
                <td style="text-align: left;"><strong>Parent's Comment:</strong></td>
                <td style="text-align: left;">{{ $reportCard->parent_comment ?: 'No comment provided' }}</td>
            </tr>
        </table>

        <!-- Promotion Decision -->
        <div class="section-title">PROMOTION DECISION</div>
        <table>
            <tr>
                <td><strong>Decision:</strong> {{ $reportCard->promotion_decision ?: 'Pending' }}</td>
                <td><strong>Next Class:</strong> {{ $reportCard->nextClass->name ?? 'Pending' }}</td>
            </tr>
        </table>

        <!-- Next Term Information -->
        <div class="section-title">NEXT TERM INFORMATION</div>
        <table>
            <tr>
                <td><strong>Vacation Date:</strong> {{ $reportCard->vacation_date ? $reportCard->vacation_date->format('d/m/Y') : 'N/A' }}</td>
                <td><strong>Next Term Begins:</strong> {{ $reportCard->next_term_begins ? $reportCard->next_term_begins->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Next Term Fee:</strong> ₦{{ number_format($reportCard->next_term_fee, 2) }}</td>
                <td><strong>Outstanding Balance:</strong> ₦{{ number_format($reportCard->outstanding_balance, 2) }}</td>
            </tr>
        </table>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <p><strong>Class Teacher's Signature</strong></p>
                <div class="signature-line"></div>
                <p class="small">{{ $reportCard->classTeacher->full_name ?? 'N/A' }}</p>
            </div>
            <div class="signature-box">
                <p><strong>Principal's Signature</strong></p>
                <div class="signature-line"></div>
                <p class="small">Principal</p>
            </div>
            <div class="signature-box">
                <p><strong>Parent's Signature</strong></p>
                <div class="signature-line"></div>
                <p class="small">Date: _____________</p>
            </div>
        </div>

        <!-- Verification -->
        @if($reportCard->verification_code && $reportCard->status == 'published')
        <div style="text-align: center; margin-top: 20px; border-top: 1px solid #000; padding-top: 15px;">
            <p><strong>Verification Code: {{ $reportCard->verification_code }}</strong></p>
            <p>Verify this result at: {{ $reportCard->verification_url }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>This report card is computer-generated and requires no signature.</p>
            <p>Generated on: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
