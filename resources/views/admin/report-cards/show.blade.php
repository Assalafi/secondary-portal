@extends('layouts.admin')

@section('title', 'Report Card - ' . $reportCard->student->full_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nigerian Report Card - {{ $reportCard->report_type }}</h5>
                    <div>
                        @if($reportCard->status == 'draft')
                            <a href="{{ route('admin.academic-management.report-cards.edit-comments', $reportCard->id) }}" class="btn btn-sm btn-info">Edit Comments</a>
                            <a href="{{ route('admin.academic-management.report-cards.edit-domain-ratings', $reportCard->id) }}" class="btn btn-sm btn-warning">Edit Ratings</a>
                            <form action="{{ route('admin.academic-management.report-cards.approve', $reportCard->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            </form>
                        @elseif($reportCard->status == 'approved')
                            <form action="{{ route('admin.academic-management.report-cards.publish', $reportCard->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">Publish</button>
                            </form>
                        @endif
                        <button onclick="window.print()" class="btn btn-sm btn-secondary">Print</button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Printable Report Card -->
                    <div id="report-card-print" class="report-card-nigerian">
                        <!-- School Header -->
                        <div class="school-header text-center mb-4">
                            <h2>{{ config('app.name') }}</h2>
                            <p>{{ config('app.address', '') }}</p>
                            <p><strong>STUDENT REPORT CARD</strong></p>
                            <p>{{ $reportCard->report_type|upper }} - {{ $reportCard->academicSession->name ?? 'Annual' }} {{ $reportCard->term->name ?? '' }}</p>
                        </div>

                        <!-- Student Information -->
                        <table class="table table-bordered student-info-table">
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
                        <h6 class="mt-4 mb-3"><strong>ACADEMIC PERFORMANCE</strong></h6>
                        <table class="table table-bordered academic-table">
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

                        <!-- Class Statistics -->
                        <table class="table table-bordered class-stats-table mt-3">
                            <tr>
                                <td><strong>Class Average:</strong> {{ number_format($reportCard->class_average, 2) }}</td>
                                <td><strong>Highest in Class:</strong> {{ number_format($reportCard->class_highest_average, 2) }}</td>
                                <td><strong>Lowest in Class:</strong> {{ number_format($reportCard->class_lowest_average, 2) }}</td>
                            </tr>
                        </table>

                        <!-- Affective Domain -->
                        <h6 class="mt-4 mb-3"><strong>AFFECTIVE DOMAIN</strong></h6>
                        <table class="table table-bordered affective-table">
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
                                    <td>{{ $rating->trait->name }}</td>
                                    <td>{{ $rating->rating_value }}</td>
                                    <td>{{ $rating->rating_value >= 4 ? 'Excellent' : ($rating->rating_value == 3 ? 'Good' : 'Needs Improvement') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Psychomotor Skills -->
                        <h6 class="mt-4 mb-3"><strong>PSYCHOMOTOR SKILLS</strong></h6>
                        <table class="table table-bordered psychomotor-table">
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
                                    <td>{{ $rating->trait->name }}</td>
                                    <td>{{ $rating->rating_value }}</td>
                                    <td>{{ $rating->rating_value >= 4 ? 'Excellent' : ($rating->rating_value == 3 ? 'Good' : 'Needs Improvement') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Attendance Summary -->
                        <h6 class="mt-4 mb-3"><strong>ATTENDANCE SUMMARY</strong></h6>
                        <table class="table table-bordered attendance-table">
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
                        <h6 class="mt-4 mb-3"><strong>COMMENTS</strong></h6>
                        <table class="table table-bordered comments-table">
                            <tr>
                                <td><strong>Class Teacher's Comment:</strong></td>
                                <td>{{ $reportCard->class_teacher_comment ?: 'No comment provided' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Principal's Comment:</strong></td>
                                <td>{{ $reportCard->principal_comment ?: 'No comment provided' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Parent's Comment:</strong></td>
                                <td>{{ $reportCard->parent_comment ?: 'No comment provided' }}</td>
                            </tr>
                        </table>

                        <!-- Promotion Decision -->
                        <h6 class="mt-4 mb-3"><strong>PROMOTION DECISION</strong></h6>
                        <table class="table table-bordered promotion-table">
                            <tr>
                                <td><strong>Decision:</strong> {{ $reportCard->promotion_decision ?: 'Pending' }}</td>
                                <td><strong>Next Class:</strong> {{ $reportCard->nextClass->name ?? 'Pending' }}</td>
                            </tr>
                        </table>

                        <!-- Next Term Information -->
                        <h6 class="mt-4 mb-3"><strong>NEXT TERM INFORMATION</strong></h6>
                        <table class="table table-bordered next-term-table">
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
                        <div class="signatures-section mt-5">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="signature-box text-center">
                                        <p><strong>Class Teacher's Signature</strong></p>
                                        <div class="signature-line"></div>
                                        <p class="text-muted">{{ $reportCard->classTeacher->full_name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="signature-box text-center">
                                        <p><strong>Principal's Signature</strong></p>
                                        <div class="signature-line"></div>
                                        <p class="text-muted">Principal</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="signature-box text-center">
                                        <p><strong>Parent's Signature</strong></p>
                                        <div class="signature-line"></div>
                                        <p class="text-muted">Date: _____________</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Verification QR Code -->
                        @if($reportCard->verification_code && $reportCard->status == 'published')
                        <div class="verification-section mt-4 text-center">
                            <p><strong>Verification Code: {{ $reportCard->verification_code }}</strong></p>
                            <p>Verify this result at: {{ $reportCard->verification_url }}</p>
                            <div class="qr-code-placeholder">
                                <p>[QR Code Here]</p>
                            </div>
                        </div>
                        @endif

                        <!-- Footer -->
                        <div class="footer-section mt-5 text-center">
                            <p class="text-muted">This report card is computer-generated and requires no signature.</p>
                            <p class="text-muted">{{ now()->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.report-card-nigerian {
    font-family: 'Times New Roman', Times, serif;
    padding: 20px;
    max-width: 210mm;
    margin: 0 auto;
}

.school-header h2 {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 5px;
}

.school-header p {
    margin-bottom: 3px;
}

.student-info-table, .academic-table, .class-stats-table, 
.affective-table, .psychomotor-table, .attendance-table, 
.comments-table, .promotion-table, .next-term-table {
    font-size: 12px;
}

.academic-table th, .academic-table td {
    text-align: center;
    padding: 8px;
}

.academic-table th {
    background-color: #f0f0f0;
    font-weight: bold;
}

.signature-box {
    border: 1px solid #ddd;
    padding: 20px;
    margin: 10px;
}

.signature-line {
    border-bottom: 1px solid #000;
    height: 50px;
    margin: 20px 0;
}

.qr-code-placeholder {
    border: 1px dashed #ccc;
    padding: 20px;
    display: inline-block;
    margin-top: 10px;
}

@media print {
    .card-header {
        display: none;
    }
    .report-card-nigerian {
        padding: 0;
    }
}
</style>
@endsection
