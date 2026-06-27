<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Academic Report - {{ $student->full_name ?? 'Student' }}</title>
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
        <h1>Academic Report</h1>
        <p>Generated on: {{ now()->format('jS F Y, g:i A') }}</p>
    </div>

    @php
        $cls = data_get($student, 'classArm.schoolClass.name');
        $arm = data_get($student, 'classArm.name');
        $className = trim(($cls ?: '-') . ' ' . ($arm ?: ''));
        $avgScore = $student->scores->isNotEmpty() ? $student->scores->avg('total') : 0;
        $totalSubjects = $student->scores->count();
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
        <h3>Subject Performance</h3>
        @if($student->scores->isEmpty())
            <p style="text-align: center; padding: 20px;">No academic records available.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>1st CA</th>
                        <th>2nd CA</th>
                        <th>3rd CA</th>
                        <th>Exam</th>
                        <th>Total</th>
                        <th>Grade</th>
                        <th>Term</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($student->scores as $score)
                        <tr>
                            <td>{{ $score->scoreBatch->subject->name ?? 'N/A' }}</td>
                            <td>{{ number_format($score->first_ca ?? 0, 1) }}</td>
                            <td>{{ number_format($score->second_ca ?? 0, 1) }}</td>
                            <td>{{ number_format($score->third_ca ?? 0, 1) }}</td>
                            <td>{{ number_format($score->exam ?? 0, 1) }}</td>
                            <td><strong>{{ number_format($score->total ?? 0, 1) }}</strong></td>
                            <td>{{ $score->grade ?? 'N/A' }}</td>
                            <td>{{ $score->scoreBatch->term->name ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if($student->scores->isNotEmpty())
        <div class="summary">
            <h3>Performance Summary</h3>
            <div class="info-grid">
                <div class="label">Total Subjects:</div>
                <div>{{ $totalSubjects }}</div>
                <div class="label">Average Score:</div>
                <div>{{ number_format($avgScore, 1) }}%</div>
            </div>
        </div>
    @endif

    <div class="footer">
        <p>This document is computer-generated and does not require a signature.</p>
    </div>
</body>
</html>
