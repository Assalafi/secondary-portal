@php
    $schoolName = $schoolSettings?->school_name ?: config('app.name', 'School');
    $ratingRemark = static fn ($value) => match ((int) $value) {
        5 => 'Excellent',
        4 => 'Very Good',
        3 => 'Good',
        2 => 'Fair',
        default => 'Needs Improvement',
    };
    $isSeniorSecondary = $reportCard->class?->level === 'SS';
@endphp

<div class="nigerian-report-card">
    <header class="report-school-header">
        @if($schoolSettings?->school_logo)
            <img class="school-logo" src="{{ asset('storage/' . $schoolSettings->school_logo) }}" alt="{{ $schoolName }} logo">
        @endif
        <div>
            <h1>{{ $schoolName }}</h1>
            @if($schoolSettings?->school_address)<p>{{ $schoolSettings->school_address }}</p>@endif
            <p>
                @if($schoolSettings?->phone_number)Tel: {{ $schoolSettings->phone_number }}@endif
                @if($schoolSettings?->phone_number && $schoolSettings?->email) &middot; @endif
                @if($schoolSettings?->email)Email: {{ $schoolSettings->email }}@endif
            </p>
        </div>
    </header>

    <div class="report-heading">
        <h2>{{ $reportCard->report_type === 'annual' ? 'Annual Student Report Card' : 'Termly Student Report Card' }}</h2>
        <p>{{ $reportCard->session_name }} Academic Session &middot; {{ $reportCard->term_name }}</p>
    </div>

    <table class="report-table report-details">
        <tr>
            <th>Name of Student</th>
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
        <tr>
            <th>Position</th>
            <td>{{ $reportCard->class_position ?: 'N/A' }} / {{ $reportCard->number_in_class ?: 'N/A' }}</td>
            <th>No. of Subjects</th>
            <td>{{ $reportCard->items->count() }}</td>
        </tr>
    </table>

    <h3 class="report-section-title">Cognitive Domain / Academic Performance</h3>
    <div class="report-table-wrap">
        <table class="report-table performance-table">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th class="text-left">Subject</th>
                    <th>CA<br>({{ $reportSettings->ca_max_score }})</th>
                    <th>Exam<br>({{ $reportSettings->exam_max_score }})</th>
                    <th>Total<br>(100)</th>
                    @if($reportSettings->show_class_average)<th>Class Avg.</th>@endif
                    @if($reportSettings->show_highest_lowest)
                        <th>Highest</th>
                        <th>Lowest</th>
                    @endif
                    @if($reportSettings->show_subject_position)<th>Pos.</th>@endif
                    <th>Grade</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportCard->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-left">{{ $item->subject_name }}</td>
                        <td>{{ number_format((float) $item->ca_score, 1) }}</td>
                        <td>{{ number_format((float) $item->exam_score, 1) }}</td>
                        <td><strong>{{ number_format((float) $item->total_score, 1) }}</strong></td>
                        @if($reportSettings->show_class_average)<td>{{ number_format((float) $item->class_average, 1) }}</td>@endif
                        @if($reportSettings->show_highest_lowest)
                            <td>{{ number_format((float) $item->highest_score, 1) }}</td>
                            <td>{{ number_format((float) $item->lowest_score, 1) }}</td>
                        @endif
                        @if($reportSettings->show_subject_position)<td>{{ $item->subject_position ?: '—' }}</td>@endif
                        <td><strong>{{ $item->grade }}</strong></td>
                        <td>{{ $item->remark }}</td>
                    </tr>
                @empty
                    <tr><td colspan="11">No subject scores are available.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="text-left">Total / Overall</th>
                    <th>{{ number_format((float) $reportCard->items->sum('ca_score'), 1) }}</th>
                    <th>{{ number_format((float) $reportCard->items->sum('exam_score'), 1) }}</th>
                    <th>{{ number_format((float) $reportCard->total_score, 1) }}</th>
                    @if($reportSettings->show_class_average)<th>{{ number_format((float) $reportCard->class_average, 1) }}%</th>@endif
                    @if($reportSettings->show_highest_lowest)
                        <th>{{ number_format((float) $reportCard->class_highest_average, 1) }}%</th>
                        <th>{{ number_format((float) $reportCard->class_lowest_average, 1) }}%</th>
                    @endif
                    @if($reportSettings->show_subject_position)<th>{{ $reportCard->class_position ?: '—' }}</th>@endif
                    <th>{{ $reportCard->final_grade }}</th>
                    <th>{{ $reportCard->final_remark }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <table class="report-table summary-table">
        <tr>
            <th>Overall Average</th>
            <td>{{ number_format((float) $reportCard->average_score, 1) }}%</td>
            <th>Class Average</th>
            <td>{{ number_format((float) $reportCard->class_average, 1) }}%</td>
            <th>Class Highest</th>
            <td>{{ number_format((float) $reportCard->class_highest_average, 1) }}%</td>
            <th>Class Lowest</th>
            <td>{{ number_format((float) $reportCard->class_lowest_average, 1) }}%</td>
        </tr>
    </table>

    <div class="report-columns">
        <div class="report-column">
            <h3 class="report-section-title">Grading Key</h3>
            <table class="report-table compact-table">
                @if($isSeniorSecondary)
                    <tr><th>A1</th><td>75–100</td><td>Excellent</td></tr>
                    <tr><th>B2–B3</th><td>65–74</td><td>Very Good / Good</td></tr>
                    <tr><th>C4–C6</th><td>50–64</td><td>Credit</td></tr>
                    <tr><th>D7–E8</th><td>40–49</td><td>Pass</td></tr>
                    <tr><th>F9</th><td>0–39</td><td>Fail</td></tr>
                @else
                    <tr><th>A</th><td>70–100</td><td>Excellent</td></tr>
                    <tr><th>B</th><td>60–69</td><td>Very Good</td></tr>
                    <tr><th>C</th><td>50–59</td><td>Good</td></tr>
                    <tr><th>D–E</th><td>40–49</td><td>Fair / Pass</td></tr>
                    <tr><th>F</th><td>0–39</td><td>Fail</td></tr>
                @endif
            </table>
        </div>

        @if($reportSettings->show_attendance)
            <div class="report-column">
                <h3 class="report-section-title">Attendance</h3>
                <table class="report-table compact-table">
                    <tr><th>School Opened</th><td>{{ $reportCard->attendance_opened }}</td></tr>
                    <tr><th>Present</th><td>{{ $reportCard->attendance_present }}</td></tr>
                    <tr><th>Absent</th><td>{{ $reportCard->attendance_absent }}</td></tr>
                    <tr><th>Late</th><td>{{ $reportCard->attendance_late }}</td></tr>
                    <tr><th>Attendance</th><td>{{ number_format((float) $reportCard->attendance_percentage, 1) }}%</td></tr>
                </table>
            </div>
        @endif
    </div>

    @if($reportSettings->show_affective_domain || $reportSettings->show_psychomotor_domain)
        <div class="report-columns">
            @if($reportSettings->show_affective_domain)
                <div class="report-column">
                    <h3 class="report-section-title">Affective Domain</h3>
                    <table class="report-table compact-table">
                        <thead><tr><th class="text-left">Trait</th><th>Rating</th><th>Remark</th></tr></thead>
                        <tbody>
                            @forelse($reportCard->affectiveRatings as $rating)
                                <tr>
                                    <td class="text-left">{{ $rating->trait?->name }}</td>
                                    <td>{{ $rating->rating_value }}/5</td>
                                    <td>{{ $ratingRemark($rating->rating_value) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3">Not rated</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
            @if($reportSettings->show_psychomotor_domain)
                <div class="report-column">
                    <h3 class="report-section-title">Psychomotor Domain</h3>
                    <table class="report-table compact-table">
                        <thead><tr><th class="text-left">Skill</th><th>Rating</th><th>Remark</th></tr></thead>
                        <tbody>
                            @forelse($reportCard->psychomotorRatings as $rating)
                                <tr>
                                    <td class="text-left">{{ $rating->trait?->name }}</td>
                                    <td>{{ $rating->rating_value }}/5</td>
                                    <td>{{ $ratingRemark($rating->rating_value) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3">Not rated</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        <p class="rating-key"><strong>Rating key:</strong> 5 – Excellent, 4 – Very Good, 3 – Good, 2 – Fair, 1 – Needs Improvement.</p>
    @endif

    <h3 class="report-section-title">Comments and Decision</h3>
    <table class="report-table comments-table">
        <tr><th>Class Teacher's Comment</th><td>{{ $reportCard->class_teacher_comment ?: '—' }}</td></tr>
        <tr><th>Principal's Comment</th><td>{{ $reportCard->principal_comment ?: '—' }}</td></tr>
        <tr><th>Promotion Decision</th><td>{{ $reportCard->promotion_decision ?: 'Pending' }}</td></tr>
        <tr><th>Next Class</th><td>{{ $reportCard->nextClass?->name ?: '—' }}</td></tr>
    </table>

    <table class="report-table next-term-table">
        <tr>
            <th>Vacation Date</th>
            <td>{{ $reportCard->vacation_date?->format('d M Y') ?: '—' }}</td>
            <th>Next Term Begins</th>
            <td>{{ $reportCard->next_term_begins?->format('d M Y') ?: '—' }}</td>
        </tr>
        @if($reportSettings->show_next_term_fee || $reportSettings->show_outstanding_balance)
            <tr>
                @if($reportSettings->show_next_term_fee)
                    <th>Next Term Fee</th><td>₦{{ number_format((float) $reportCard->next_term_fee, 2) }}</td>
                @endif
                @if($reportSettings->show_outstanding_balance)
                    <th>Outstanding Balance</th><td>₦{{ number_format((float) $reportCard->outstanding_balance, 2) }}</td>
                @endif
            </tr>
        @endif
    </table>

    <table class="signature-table">
        <tr>
            <td><span></span><strong>Class Teacher</strong><small>{{ $reportCard->classTeacher?->user?->name ?: '' }}</small></td>
            <td><span></span><strong>Principal</strong><small>School Authority</small></td>
            @if($reportSettings->show_parent_signature)
                <td><span></span><strong>Parent / Guardian</strong><small>Date: ______________</small></td>
            @endif
        </tr>
    </table>

    @if($reportCard->status === 'published' && $reportCard->verification_code)
        <div class="verification-box">
            <strong>Verification code:</strong> {{ $reportCard->verification_code }}
            @if($reportCard->verification_url)<br><small>{{ $reportCard->verification_url }}</small>@endif
        </div>
    @endif

    <footer class="report-footer">
        Generated {{ now()->format('d M Y, h:i A') }} &middot; Status: {{ ucfirst($reportCard->status) }}
    </footer>
</div>

<style>
    .nigerian-report-card{max-width:1100px;margin:0 auto;padding:22px;background:#fff;color:#17202a;border:2px solid #1f4d3b;font-family:"Times New Roman",serif}
    .report-school-header{display:table;width:100%;text-align:center;border-bottom:4px double #1f4d3b;padding-bottom:12px}
    .report-school-header>div{display:table-cell;vertical-align:middle}.school-logo{width:78px;height:78px;object-fit:contain;display:table-cell;float:left}
    .report-school-header h1{margin:0;text-transform:uppercase;color:#173f31;font-size:27px}.report-school-header p{margin:3px 0;font-size:13px}
    .report-heading{text-align:center;margin:12px 0}.report-heading h2{font-size:18px;text-transform:uppercase;margin:0 0 4px}.report-heading p{margin:0;font-weight:bold}
    .report-table{width:100%;border-collapse:collapse;margin:8px 0;font-size:12px}.report-table th,.report-table td{border:1px solid #34495e;padding:5px;text-align:center;vertical-align:middle}
    .report-table th{background:#eaf3ee;font-weight:700}.report-details th,.comments-table th,.next-term-table th{width:16%;text-align:left}.report-details td,.comments-table td,.next-term-table td{text-align:left}
    .report-section-title{text-align:center;text-transform:uppercase;font-size:13px;margin:14px 0 5px;padding:5px;background:#1f4d3b;color:#fff;letter-spacing:.4px}
    .performance-table{font-size:11px}.performance-table tfoot th{background:#dcebe3}.text-left{text-align:left!important}
    .report-columns{display:table;width:100%;table-layout:fixed}.report-column{display:table-cell;width:50%;vertical-align:top;padding:0 5px}.compact-table{font-size:11px}
    .summary-table th{white-space:nowrap}.rating-key{font-size:10px;text-align:center;margin:4px 0 10px}
    .signature-table{width:100%;border-collapse:separate;border-spacing:22px 8px;margin-top:26px;text-align:center}.signature-table td{width:33%}.signature-table span{display:block;border-bottom:1px solid #222;height:26px}.signature-table strong,.signature-table small{display:block;margin-top:4px}
    .verification-box{border:1px dashed #1f4d3b;text-align:center;padding:7px;margin:14px auto 0;font-size:11px;max-width:520px;overflow-wrap:anywhere}
    .report-footer{text-align:center;border-top:1px solid #aaa;margin-top:12px;padding-top:7px;font-size:9px;color:#555}
    @media(max-width:768px){.nigerian-report-card{padding:10px;border-width:1px}.report-table-wrap{overflow-x:auto}.report-columns,.report-column{display:block;width:100%;padding:0}.report-school-header h1{font-size:21px}}
    @media print{body{background:#fff!important}.nigerian-report-card{border:0;max-width:none;padding:0}.no-print{display:none!important}.report-table{page-break-inside:auto}.report-table tr{page-break-inside:avoid}.report-section-title{print-color-adjust:exact;-webkit-print-color-adjust:exact}}
</style>
