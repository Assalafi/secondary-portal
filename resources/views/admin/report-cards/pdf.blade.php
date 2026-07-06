<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $reportCard->student->surname }} {{ $reportCard->student->first_name }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }
        
        .report-card {
            max-width: 210mm;
            margin: 0 auto;
            padding: 15mm;
            border: 2px solid #000;
            background-color: #fff;
        }
        
        .school-header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
        }
        
        .school-header h1 {
            font-size: 20pt;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .school-header h2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }
        
        .school-header p {
            margin: 3px 0;
            font-size: 11pt;
        }
        
        .report-title {
            text-align: center;
            font-weight: bold;
            font-size: 16pt;
            margin: 15px 0;
            text-decoration: underline;
            text-transform: uppercase;
        }
        
        .term-info {
            text-align: center;
            font-size: 12pt;
            margin: 10px 0;
            font-weight: bold;
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
            background-color: #e8e8e8;
            font-weight: bold;
            text-align: center;
            padding: 8px 5px;
            text-transform: uppercase;
            font-size: 10pt;
        }
        
        td {
            padding: 6px 5px;
            text-align: center;
        }
        
        td:first-child {
            text-align: left;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 13pt;
            margin: 20px 0 10px 0;
            text-decoration: underline;
            text-transform: uppercase;
            text-align: center;
        }
        
        .signature-section {
            margin-top: 40px;
            clear: both;
        }
        
        .signature-box {
            width: 30%;
            float: left;
            text-align: center;
            margin: 10px 5px;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            height: 50px;
            margin: 20px 0 5px 0;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10pt;
            font-style: italic;
            border-top: 1px solid #000;
            padding-top: 15px;
            clear: both;
        }
        
        .grade-excellent { background-color: #d4edda; }
        .grade-very-good { background-color: #e2e6ea; }
        .grade-good { background-color: #fff3cd; }
        .grade-fair { background-color: #f8d7da; }
        .grade-fail { background-color: #f5c6cb; }
        
        .naira-symbol {
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <div class="report-card">
        <!-- School Header -->
        <div class="school-header">
            <h1>{{ config('app.name', 'SECONDARY SCHOOL') }}</h1>
            <h2>{{ config('app.motto', 'Excellence in Education') }}</h2>
            <p>{{ config('app.address', 'Lagos, Nigeria') }}</p>
            <p>Tel: {{ config('app.phone', '+234 XXX XXX XXXX') }} | Email: {{ config('app.email', 'info@school.com') }}</p>
        </div>

        <div class="report-title">Student Report Card</div>
        <div class="term-info">
            {{ strtoupper($reportCard->report_type) }} - {{ strtoupper($reportCard->academicSession->name ?? 'Annual') }} {{ strtoupper($reportCard->term->name ?? '') }}
        </div>

        <!-- Student Information -->
        <table>
            <tr>
                <td><strong>Name of Student:</strong> {{ $reportCard->student->surname }}, {{ $reportCard->student->first_name }} {{ $reportCard->student->middle_name }}</td>
                <td><strong>Admission No:</strong> {{ $reportCard->student->admission_no ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Class:</strong> {{ $reportCard->class->name }}</td>
                <td><strong>Sex:</strong> {{ $reportCard->student->gender ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Position in Class:</strong> {{ $reportCard->class_position ?? 'N/A' }}{{ $ordinal ?? '' }} out of {{ $reportCard->number_in_class ?? 'N/A' }}</td>
                <td><strong>Next Term Begins:</strong> {{ $reportCard->next_term_begins ? $reportCard->next_term_begins->format('d/m/Y') : 'N/A' }}</td>
            </tr>
        </table>

        <!-- Academic Performance -->
        <div class="section-title">Academic Performance</div>
        <table>
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Subject</th>
                    <th>1st CA (10)</th>
                    <th>2nd CA (10)</th>
                    <th>3rd CA (10)</th>
                    <th>Total CA (30)</th>
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
                    <td>{{ number_format($item->ca_score / 3, 1) }}</td>
                    <td>{{ number_format($item->ca_score / 3, 1) }}</td>
                    <td>{{ number_format($item->ca_score / 3, 1) }}</td>
                    <td>{{ number_format($item->ca_score, 1) }}</td>
                    <td>{{ number_format($item->exam_score, 1) }}</td>
                    <td><strong>{{ number_format($item->total_score, 1) }}</strong></td>
                    <td class="{{ $gradeClasses[$item->grade] ?? '' }}"><strong>{{ $item->grade }}</strong></td>
                    <td>{{ $item->remark }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <td colspan="2" style="text-align: center;">Total/Grand Average</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>{{ number_format($reportCard->items->sum('ca_score'), 1) }}</td>
                    <td>{{ number_format($reportCard->items->sum('exam_score'), 1) }}</td>
                    <td>{{ number_format($reportCard->total_score, 1) }}</td>
                    <td class="{{ $gradeClasses[$reportCard->final_grade] ?? '' }}">{{ $reportCard->final_grade }}</td>
                    <td>{{ $reportCard->final_remark }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Class Statistics -->
        <table>
            <tr>
                <td><strong>Class Average:</strong> {{ number_format($reportCard->class_average, 1) }}%</td>
                <td><strong>Highest in Class:</strong> {{ number_format($reportCard->class_highest_average, 1) }}%</td>
                <td><strong>Lowest in Class:</strong> {{ number_format($reportCard->class_lowest_average, 1) }}%</td>
            </tr>
        </table>

        <!-- Grading Scale -->
        <div class="section-title">Grading Scale</div>
        <table>
            <tr>
                <th>Grade</th>
                <th>Score Range</th>
                <th>Remark</th>
            </tr>
            <tr>
                <td class="grade-excellent"><strong>A</strong></td>
                <td>75 - 100</td>
                <td>Excellent</td>
            </tr>
            <tr>
                <td class="grade-very-good"><strong>B</strong></td>
                <td>65 - 74</td>
                <td>Very Good</td>
            </tr>
            <tr>
                <td class="grade-good"><strong>C</strong></td>
                <td>55 - 64</td>
                <td>Good</td>
            </tr>
            <tr>
                <td class="grade-fair"><strong>D</strong></td>
                <td>45 - 54</td>
                <td>Fair</td>
            </tr>
            <tr>
                <td class="grade-fail"><strong>F</strong></td>
                <td>0 - 44</td>
                <td>Fail</td>
            </tr>
        </table>

        <!-- Affective Domain -->
        <div class="section-title">Affective Domain</div>
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
                @php
                    $affectiveTraits = ['Punctuality', 'Attendance', 'Neatness', 'Politeness', 'Attentiveness', 'Honesty', 'Obedience', 'Respect', 'Cooperation', 'Initiative'];
                @endphp
                @foreach($affectiveTraits as $index => $trait)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left;">{{ $trait }}</td>
                    <td>{{ $reportCard->affectiveRatings[$index] ?? 3 }}</td>
                    <td>{{ ($reportCard->affectiveRatings[$index] ?? 3) >= 4 ? 'Excellent' : (($reportCard->affectiveRatings[$index] ?? 3) == 3 ? 'Good' : 'Needs Improvement') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Psychomotor Skills -->
        <div class="section-title">Psychomotor Skills</div>
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
                @php
                    $psychomotorSkills = ['Handwriting', 'Games', 'Sports', 'Drawing & Painting', 'Crafts', 'Music', 'Drama', 'Debate'];
                @endphp
                @foreach($psychomotorSkills as $index => $skill)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left;">{{ $skill }}</td>
                    <td>{{ $reportCard->psychomotorRatings[$index] ?? 3 }}</td>
                    <td>{{ ($reportCard->psychomotorRatings[$index] ?? 3) >= 4 ? 'Excellent' : (($reportCard->psychomotorRatings[$index] ?? 3) == 3 ? 'Good' : 'Needs Improvement') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Attendance Summary -->
        <div class="section-title">Attendance Summary</div>
        <table>
            <tr>
                <td><strong>Times School Opened:</strong> {{ $reportCard->attendance_opened ?? 0 }}</td>
                <td><strong>Times Present:</strong> {{ $reportCard->attendance_present ?? 0 }}</td>
                <td><strong>Times Absent:</strong> {{ $reportCard->attendance_absent ?? 0 }}</td>
            </tr>
            <tr>
                <td><strong>Times Late:</strong> {{ $reportCard->attendance_late ?? 0 }}</td>
                <td colspan="2"><strong>Attendance Percentage:</strong> {{ number_format($reportCard->attendance_percentage ?? 0, 1) }}%</td>
            </tr>
        </table>

        <!-- Comments -->
        <div class="section-title">Comments</div>
        <table>
            <tr>
                <td style="text-align: left; width: 30%;"><strong>Class Teacher's Comment:</strong></td>
                <td style="text-align: left;">{{ $reportCard->class_teacher_comment ?: 'Good performance. Keep it up!' }}</td>
            </tr>
            <tr>
                <td style="text-align: left;"><strong>Principal's Comment:</strong></td>
                <td style="text-align: left;">{{ $reportCard->principal_comment ?: 'Satisfactory academic performance.' }}</td>
            </tr>
            <tr>
                <td style="text-align: left;"><strong>Parent's Comment:</strong></td>
                <td style="text-align: left;">{{ $reportCard->parent_comment ?: '___________________________' }}</td>
            </tr>
        </table>

        <!-- Promotion Decision -->
        <div class="section-title">Promotion Decision</div>
        <table>
            <tr>
                <td><strong>Decision:</strong> {{ $reportCard->promotion_decision ?: 'Pending' }}</td>
                <td><strong>Next Class:</strong> {{ $reportCard->nextClass->name ?? 'Pending' }}</td>
            </tr>
        </table>

        <!-- Next Term Information -->
        <div class="section-title">Next Term Information</div>
        <table>
            <tr>
                <td><strong>Vacation Date:</strong> {{ $reportCard->vacation_date ? $reportCard->vacation_date->format('d/m/Y') : 'N/A' }}</td>
                <td><strong>Next Term Begins:</strong> {{ $reportCard->next_term_begins ? $reportCard->next_term_begins->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Next Term Fee:</strong> <span class="naira-symbol">₦</span>{{ number_format($reportCard->next_term_fee ?? 0, 2) }}</td>
                <td><strong>Outstanding Balance:</strong> <span class="naira-symbol">₦</span>{{ number_format($reportCard->outstanding_balance ?? 0, 2) }}</td>
            </tr>
        </table>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <p><strong>Class Teacher's Signature</strong></p>
                <div class="signature-line"></div>
                <p class="small">{{ $reportCard->classTeacher->name ?? '________________________' }}</p>
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
        <div style="text-align: center; margin-top: 25px; border-top: 1px solid #000; padding-top: 15px;">
            <p><strong>Verification Code: {{ $reportCard->verification_code }}</strong></p>
            <p style="font-size: 9pt;">Verify this result at: {{ $reportCard->verification_url }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>This report card is computer-generated and officially signed by the school authority.</p>
            <p>Generated on: {{ now()->format('d/m/Y H:i') }} | Ministry of Education Approved</p>
        </div>
    </div>
</body>
</html>
