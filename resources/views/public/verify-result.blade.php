<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result Verification - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .verification-header {
            text-align: center;
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .verification-header h1 {
            color: #2c3e50;
            margin: 0;
        }
        .verification-status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 10px;
        }
        .status-valid {
            background-color: #d4edda;
            color: #155724;
        }
        .report-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .school-header {
            text-align: center;
            border-bottom: 2px solid #3498db;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .school-header h2 {
            color: #2c3e50;
            margin: 0;
        }
        .student-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .student-info table {
            width: 100%;
        }
        .student-info td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        .student-info td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .grades-table th {
            background-color: #3498db;
            color: white;
            padding: 12px;
            text-align: center;
        }
        .grades-table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #dee2e6;
        }
        .grades-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            font-size: 12px;
        }
        .qr-section {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background-color: #e9ecef;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="verification-header">
        <h1>{{ config('app.name') }}</h1>
        <p>Official Result Verification Portal</p>
        <div class="verification-status status-valid">
            ✓ VERIFIED - This result is authentic
        </div>
    </div>

    <div class="report-card">
        <div class="school-header">
            <h2>{{ config('app.name') }}</h2>
            <p>{{ config('app.address', '') }}</p>
        </div>

        <div class="student-info">
            <h3>Student Information</h3>
            <table>
                <tr>
                    <td>Student Name:</td>
                    <td>{{ $reportCard->student->full_name }}</td>
                </tr>
                <tr>
                    <td>Admission Number:</td>
                    <td>{{ $reportCard->student->admission_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Class:</td>
                    <td>{{ $reportCard->class->name }}</td>
                </tr>
                <tr>
                    <td>Session:</td>
                    <td>{{ $reportCard->academicSession->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Term:</td>
                    <td>{{ $reportCard->term->name ?? 'Annual' }}</td>
                </tr>
                <tr>
                    <td>Position in Class:</td>
                    <td>{{ $reportCard->class_position ?? 'N/A' }} out of {{ $reportCard->number_in_class ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <h3>Academic Performance</h3>
        <table class="grades-table">
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
                    <td>{{ $item->subject_name }}</td>
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
                    <td colspan="2"><strong>Total/Grand Average</strong></td>
                    <td>{{ number_format($reportCard->items->sum('ca_score'), 2) }}</td>
                    <td>{{ number_format($reportCard->items->sum('exam_score'), 2) }}</td>
                    <td>{{ number_format($reportCard->total_score, 2) }}</td>
                    <td>{{ $reportCard->final_grade }}</td>
                    <td>{{ $reportCard->final_remark }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="student-info">
            <h3>Summary Statistics</h3>
            <table>
                <tr>
                    <td>Class Average:</td>
                    <td>{{ number_format($reportCard->class_average, 2) }}%</td>
                </tr>
                <tr>
                    <td>Highest in Class:</td>
                    <td>{{ number_format($reportCard->class_highest_average, 2) }}%</td>
                </tr>
                <tr>
                    <td>Lowest in Class:</td>
                    <td>{{ number_format($reportCard->class_lowest_average, 2) }}%</td>
                </tr>
                <tr>
                    <td>Attendance Percentage:</td>
                    <td>{{ number_format($reportCard->attendance_percentage, 2) }}%</td>
                </tr>
                <tr>
                    <td>Promotion Decision:</td>
                    <td>{{ $reportCard->promotion_decision ?? 'Pending' }}</td>
                </tr>
            </table>
        </div>

        <div class="qr-section">
            <p><strong>Verification Code:</strong> {{ $reportCard->verification_code }}</p>
            <p><strong>Status:</strong> Published</p>
            <p><strong>Verified On:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="footer">
        <p>This result is verified and authenticated by {{ config('app.name') }}</p>
        <p>For inquiries, please contact the school administration</p>
        <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
