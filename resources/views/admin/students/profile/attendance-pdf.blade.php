<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report - {{ $student->full_name ?? 'Student' }}</title>
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
        <h1>Attendance Report</h1>
        <p>Generated on: {{ now()->format('jS F Y, g:i A') }}</p>
    </div>

    @php
        $cls = data_get($student, 'classArm.schoolClass.name');
        $arm = data_get($student, 'classArm.name');
        $className = trim(($cls ?: '-') . ' ' . ($arm ?: ''));
        $att = $student->attendances ?? collect();
        $presentCount = $att->where('status', 'Present')->count();
        $absentCount = $att->where('status', 'Absent')->count();
        $lateCount = $att->where('status', 'Late')->count();
        $totalCount = $att->count();
        $attendanceRate = $totalCount > 0 ? round(($presentCount / $totalCount) * 100, 1) : null;
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
        <h3>Attendance Records</h3>
        @if($att->isEmpty())
            <p style="text-align: center; padding: 20px;">No attendance records found.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($att as $rec)
                        @php
                            $dateVal = $rec->date ?? $rec->created_at;
                            $dateText = $dateVal ? \Carbon\Carbon::parse($dateVal)->format('jS M Y') : '—';
                            $dayText = $dateVal ? \Carbon\Carbon::parse($dateVal)->format('l') : '—';
                            $status = $rec->status ?? '—';
                        @endphp
                        <tr>
                            <td>{{ $dateText }}</td>
                            <td>{{ $dayText }}</td>
                            <td>{{ $rec->check_in ?? '—' }}</td>
                            <td>{{ $rec->check_out ?? '—' }}</td>
                            <td>{{ ucfirst($status) }}</td>
                            <td>{{ $rec->remarks ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if($att->isNotEmpty())
        <div class="summary">
            <h3>Attendance Summary</h3>
            <div class="summary-grid">
                <div class="label">Total Days:</div>
                <div>{{ $totalCount }}</div>
                <div class="label">Days Present:</div>
                <div>{{ $presentCount }}</div>
                <div class="label">Days Absent:</div>
                <div>{{ $absentCount }}</div>
                <div class="label">Late Arrivals:</div>
                <div>{{ $lateCount }}</div>
                <div class="label">Attendance Rate:</div>
                <div>{{ $attendanceRate !== null ? $attendanceRate.'%' : 'N/A' }}</div>
            </div>
        </div>
    @endif

    <div class="footer">
        <p>This document is computer-generated and does not require a signature.</p>
    </div>
</body>
</html>
