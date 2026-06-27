<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance History - {{ $classArm->schoolClass->name }} {{ $classArm->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #333;
        }
        .header h2 {
            font-size: 14px;
            margin: 5px 0;
            color: #666;
        }
        .info-row {
            margin-bottom: 15px;
        }
        .info-row strong {
            display: inline-block;
            width: 100px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .status-present {
            background-color: #d4edda;
            color: #155724;
        }
        .status-absent {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-late {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-excused {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Attendance History Report</h1>
        <h2>{{ $classArm->schoolClass->name }} {{ $classArm->name }}</h2>
    </div>

    <div class="info-row">
        <strong>Class:</strong> {{ $classArm->schoolClass->name }} {{ $classArm->name }}
    </div>

    @if($request->filled('from_date') || $request->filled('to_date'))
    <div class="info-row">
        <strong>Date Range:</strong>
        @if($request->filled('from_date')){{ \Carbon\Carbon::parse($request->from_date)->format('M d, Y') }}@endif
        @if($request->filled('from_date') && $request->filled('to_date')) to @endif
        @if($request->filled('to_date')){{ \Carbon\Carbon::parse($request->to_date)->format('M d, Y') }}@endif
    </div>
    @endif

    <div class="info-row">
        <strong>Total Records:</strong> {{ $attendances->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th style="width: 25%;">Student</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 30%;">Remarks</th>
                <th style="width: 20%;">Marked By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $attendance)
                @php
                    $statusClass = match(strtolower($attendance->status)) {
                        'present' => 'status-present',
                        'absent' => 'status-absent',
                        'late' => 'status-late',
                        'excused' => 'status-excused',
                        default => ''
                    };
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}</td>
                    <td>{{ $attendance->student->full_name ?? $attendance->student->user->name ?? '-' }}</td>
                    <td class="{{ $statusClass }}">{{ ucfirst($attendance->status) }}</td>
                    <td>{{ $attendance->remarks ?? '-' }}</td>
                    <td>{{ $attendance->markedBy->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">No attendance records found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ \Carbon\Carbon::now()->format('M d, Y H:i:s') }}</p>
        <p>Attendance History Report - {{ $classArm->schoolClass->name }} {{ $classArm->name }}</p>
    </div>
</body>
</html>
