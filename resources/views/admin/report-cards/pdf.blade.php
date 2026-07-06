<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $reportCard->student->full_name }}</title>
    <style>
        @page { size: A4 portrait; margin: 7mm; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: #18241f;
            font-family: "DejaVu Sans", sans-serif;
            font-size: {{ $reportCard->items->count() > 12 ? '7.5pt' : '8.5pt' }};
            line-height: 1.18;
        }
        .report-card {
            width: 100%;
            border: 1.5px solid #174c38;
            padding: 5mm;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: .6px solid #52655d; padding: 2.2mm 1.5mm; vertical-align: middle; }
        th { background: #e8f1ed; font-weight: 700; }
        .school-header td { border: 0; padding: 0; text-align: center; }
        .school-logo { width: 18mm; height: 18mm; object-fit: contain; }
        .school-name {
            margin: 0;
            color: #174c38;
            font-size: 16pt;
            line-height: 1.05;
            text-transform: uppercase;
        }
        .school-contact { margin: 1mm 0 0; font-size: 7.5pt; }
        .heading {
            margin: 2.5mm 0 2mm;
            padding: 1.5mm;
            border-top: 2px double #174c38;
            border-bottom: .8px solid #174c38;
            text-align: center;
        }
        .heading h2 { margin: 0; font-size: 11pt; text-transform: uppercase; }
        .heading p { margin: .7mm 0 0; font-size: 8pt; font-weight: 700; }
        .student-info { margin-bottom: 2mm; }
        .student-info th { width: 15%; text-align: left; }
        .student-info td { width: 35%; }
        .section-title {
            margin: 2mm 0 0;
            padding: 1.2mm;
            background: #174c38;
            color: #fff;
            font-size: 8pt;
            letter-spacing: .3px;
            text-align: center;
            text-transform: uppercase;
        }
        .scores { table-layout: fixed; }
        .scores th, .scores td {
            padding: {{ $reportCard->items->count() > 12 ? '1.15mm' : '1.55mm' }} 1mm;
            text-align: center;
        }
        .scores .subject { width: 30%; text-align: left; }
        .scores .score { width: 9%; }
        .scores .remark { width: 17%; }
        .scores tfoot th { background: #d8e8e1; }
        .summary { margin-top: 2mm; table-layout: fixed; }
        .summary th, .summary td { padding: 1.5mm 1mm; text-align: center; }
        .comments { margin-top: 2mm; }
        .comments th { width: 22%; text-align: left; }
        .comments td { text-align: left; }
        .decision { margin-top: 2mm; table-layout: fixed; }
        .decision th { width: 18%; text-align: left; }
        .signature-table { margin-top: 5mm; table-layout: fixed; }
        .signature-table td { width: 33.33%; border: 0; padding: 0 5mm; text-align: center; }
        .signature-line { display: block; height: 5mm; border-bottom: .7px solid #222; }
        .signature-label { display: block; margin-top: 1mm; font-size: 7pt; font-weight: 700; }
        .verification {
            margin-top: 3mm;
            padding-top: 1.5mm;
            border-top: .7px solid #85968f;
            color: #4b5d55;
            font-size: 6.5pt;
            text-align: center;
        }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>
@php
    $schoolName = $schoolSettings?->school_name ?: config('app.name', 'School');
    $logoData = null;
    if ($schoolSettings?->school_logo) {
        $logoPath = storage_path('app/public/' . ltrim($schoolSettings->school_logo, '/'));
        if (is_file($logoPath)) {
            $mime = mime_content_type($logoPath) ?: 'image/png';
            $logoData = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
    }
@endphp
<div class="report-card">
    <table class="school-header">
        <tr>
            <td style="width: 20%;">
                @if($logoData)<img class="school-logo" src="{{ $logoData }}" alt="School logo">@endif
            </td>
            <td style="width: 60%;">
                <h1 class="school-name">{{ $schoolName }}</h1>
                <p class="school-contact">
                    {{ $schoolSettings?->school_address }}
                    @if($schoolSettings?->phone_number) &nbsp; | &nbsp; {{ $schoolSettings->phone_number }} @endif
                    @if($schoolSettings?->email) &nbsp; | &nbsp; {{ $schoolSettings->email }} @endif
                </p>
            </td>
            <td style="width: 20%;"></td>
        </tr>
    </table>

    <div class="heading">
        <h2>{{ $reportCard->report_type === 'annual' ? 'Annual Report Card' : 'Termly Report Card' }}</h2>
        <p>{{ $reportCard->session_name }} Academic Session &nbsp; | &nbsp; {{ $reportCard->term_name }}</p>
    </div>

    <table class="student-info">
        <tr>
            <th>Student</th>
            <td>{{ $reportCard->student->surname }}, {{ $reportCard->student->first_name }} {{ $reportCard->student->middle_name }}</td>
            <th>Admission No.</th>
            <td>{{ $reportCard->student->admission_no ?: 'N/A' }}</td>
        </tr>
        <tr>
            <th>Class</th>
            <td>{{ $reportCard->class->name }}</td>
            <th>Gender</th>
            <td>{{ $reportCard->student->gender ?: 'N/A' }}</td>
        </tr>
    </table>

    <div class="section-title">Academic Performance</div>
    <table class="scores">
        <thead>
            <tr>
                <th class="subject">Subject</th>
                <th class="score">CA<br>({{ $reportSettings->ca_max_score }})</th>
                <th class="score">Exam<br>({{ $reportSettings->exam_max_score }})</th>
                <th class="score">Total<br>(100)</th>
                <th class="score">Class<br>Avg.</th>
                <th class="score">Grade</th>
                <th class="remark">Remark</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportCard->items as $item)
                <tr>
                    <td class="subject">{{ $item->subject_name }}</td>
                    <td>{{ number_format((float) $item->ca_score, 1) }}</td>
                    <td>{{ number_format((float) $item->exam_score, 1) }}</td>
                    <td><strong>{{ number_format((float) $item->total_score, 1) }}</strong></td>
                    <td>{{ number_format((float) $item->class_average, 1) }}</td>
                    <td><strong>{{ $item->grade }}</strong></td>
                    <td>{{ $item->remark }}</td>
                </tr>
            @empty
                <tr><td colspan="7">No subject scores available.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th class="subject">Overall</th>
                <th>{{ number_format((float) $reportCard->items->sum('ca_score'), 1) }}</th>
                <th>{{ number_format((float) $reportCard->items->sum('exam_score'), 1) }}</th>
                <th>{{ number_format((float) $reportCard->total_score, 1) }}</th>
                <th>{{ number_format((float) $reportCard->class_average, 1) }}%</th>
                <th>{{ $reportCard->final_grade }}</th>
                <th>{{ $reportCard->final_remark }}</th>
            </tr>
        </tfoot>
    </table>

    <table class="summary">
        <tr>
            <th>Average</th>
            <td><strong>{{ number_format((float) $reportCard->average_score, 1) }}%</strong></td>
            <th>Position</th>
            <td><strong>{{ $reportCard->class_position ?: 'N/A' }} / {{ $reportCard->number_in_class ?: 'N/A' }}</strong></td>
            <th>Attendance</th>
            <td><strong>{{ number_format((float) $reportCard->attendance_percentage, 1) }}%</strong></td>
            <th>Subjects</th>
            <td><strong>{{ $reportCard->items->count() }}</strong></td>
        </tr>
    </table>

    <table class="comments">
        <tr>
            <th>Class Teacher's Comment</th>
            <td>{{ $reportCard->class_teacher_comment ?: '—' }}</td>
        </tr>
        <tr>
            <th>Principal's Comment</th>
            <td>{{ $reportCard->principal_comment ?: '—' }}</td>
        </tr>
    </table>

    <table class="decision">
        <tr>
            <th>Decision</th>
            <td>{{ $reportCard->promotion_decision ?: 'Pending' }}</td>
            <th>Next Class</th>
            <td>{{ $reportCard->nextClass?->name ?: '—' }}</td>
            <th>Next Term</th>
            <td>{{ $reportCard->next_term_begins?->format('d M Y') ?: '—' }}</td>
        </tr>
    </table>

    <table class="signature-table">
        <tr>
            <td><span class="signature-line"></span><span class="signature-label">Class Teacher</span></td>
            <td><span class="signature-line"></span><span class="signature-label">Principal</span></td>
            <td><span class="signature-line"></span><span class="signature-label">Parent / Guardian</span></td>
        </tr>
    </table>

    <div class="verification">
        @if($reportCard->verification_code)
            Verification code: <strong>{{ $reportCard->verification_code }}</strong>
            @if($reportCard->verification_url) &nbsp; | &nbsp; {{ $reportCard->verification_url }} @endif
        @else
            Official student report card
        @endif
    </div>
</div>
</body>
</html>
