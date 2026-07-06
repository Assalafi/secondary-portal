@extends('layouts.parent')

@section('title', 'Report Card')
@section('page-title', 'Report Card')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.report-cards') }}" class="text-decoration-none">Report Cards</a></li>
                <li class="breadcrumb-item active text-muted">View Report Card</li>
            </ol>
        </nav>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="mb-1">{{ $reportCard->student->surname }}, {{ $reportCard->student->first_name }}</h5>
                        <p class="text-muted small mb-0">{{ $reportCard->class->name }} | {{ $reportCard->academicSession->name ?? 'Annual' }} {{ $reportCard->term->name ?? '' }}</p>
                    </div>
                    <a href="{{ route('parent.report-cards.download', $reportCard->id) }}" class="btn btn-dark">
                        <i class="ri-download-line me-2"></i>Download PDF
                    </a>
                </div>

                <!-- Academic Performance -->
                <h6 class="mb-3">Academic Performance</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
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
                                <td>{{ $item->subject_name }}</td>
                                <td>{{ number_format($item->ca_score / 3, 1) }}</td>
                                <td>{{ number_format($item->ca_score / 3, 1) }}</td>
                                <td>{{ number_format($item->ca_score / 3, 1) }}</td>
                                <td>{{ number_format($item->ca_score, 1) }}</td>
                                <td>{{ number_format($item->exam_score, 1) }}</td>
                                <td><strong>{{ number_format($item->total_score, 1) }}</strong></td>
                                <td><strong>{{ $item->grade }}</strong></td>
                                <td>{{ $item->remark }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #f0f0f0; font-weight: bold;">
                                <td colspan="2" class="text-center">Total/Grand Average</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>{{ number_format($reportCard->items->sum('ca_score'), 1) }}</td>
                                <td>{{ number_format($reportCard->items->sum('exam_score'), 1) }}</td>
                                <td>{{ number_format($reportCard->total_score, 1) }}</td>
                                <td>{{ $reportCard->final_grade }}</td>
                                <td>{{ $reportCard->final_remark }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Summary -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="small text-muted mb-1">Position in Class</h6>
                                <h4 class="mb-0">{{ $reportCard->class_position ?? 'N/A' }}/{{ $reportCard->number_in_class ?? 'N/A' }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="small text-muted mb-1">Class Average</h6>
                                <h4 class="mb-0">{{ number_format($reportCard->class_average ?? 0, 1) }}%</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="small text-muted mb-1">Highest in Class</h6>
                                <h4 class="mb-0">{{ number_format($reportCard->class_highest_average ?? 0, 1) }}%</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="small text-muted mb-1">Lowest in Class</h6>
                                <h4 class="mb-0">{{ number_format($reportCard->class_lowest_average ?? 0, 1) }}%</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comments -->
                <h6 class="mb-3">Comments</h6>
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Class Teacher's Comment:</strong>
                            <p class="mb-0">{{ $reportCard->class_teacher_comment ?: 'No comment provided' }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Principal's Comment:</strong>
                            <p class="mb-0">{{ $reportCard->principal_comment ?: 'No comment provided' }}</p>
                        </div>
                        <div>
                            <strong>Promotion Decision:</strong>
                            <span class="badge bg-{{ $reportCard->promotion_decision == 'Promoted' ? 'success' : ($reportCard->promotion_decision == 'Repeated' ? 'danger' : 'warning') }}">
                                {{ $reportCard->promotion_decision ?: 'Pending' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
