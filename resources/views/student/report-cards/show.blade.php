@extends('layouts.student')

@section('title', 'Report Card Details')
@section('page-title', 'Report Card')

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.report-cards') }}" class="text-decoration-none">Report Cards</a></li>
        <li class="breadcrumb-item active text-muted">{{ optional($reportCard->term)->name ?? '' }} {{ optional($reportCard->academicSession)->name ?? '' }}</li>
    </ol>
</nav>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="ri-file-text-line me-2 text-primary"></i>
                {{ optional($reportCard->term)->name ?? 'N/A' }} Report Card - {{ optional($reportCard->academicSession)->name ?? 'N/A' }}
            </h6>
            <span class="badge bg-success">Published</span>
        </div>
    </div>
    <div class="card-body">
        <!-- Student Info -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <small class="text-muted d-block">Student Name</small>
                <span class="fw-medium">{{ optional($reportCard->student)->full_name ?? Auth::user()->name }}</span>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Class</small>
                <span class="fw-medium">{{ optional($reportCard->class)->name ?? 'N/A' }}</span>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Session</small>
                <span class="fw-medium">{{ optional($reportCard->academicSession)->name ?? 'N/A' }}</span>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Term</small>
                <span class="fw-medium">{{ optional($reportCard->term)->name ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- Academic Performance -->
        @if($reportCard->items && $reportCard->items->count() > 0)
            <h6 class="fw-bold mb-3"><i class="ri-bar-chart-line me-1 text-primary"></i>Academic Performance</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Subject</th>
                            <th class="text-center">CA</th>
                            <th class="text-center">Exam</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Grade</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportCard->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ optional($item->subject)->name ?? 'N/A' }}</td>
                                <td class="text-center">{{ $item->ca_score ?? '-' }}</td>
                                <td class="text-center">{{ $item->exam_score ?? '-' }}</td>
                                <td class="text-center fw-bold">{{ $item->total ?? '-' }}</td>
                                <td class="text-center">
                                    @php
                                        $total = $item->total ?? 0;
                                        $gradeColor = $total >= 70 ? 'success' : ($total >= 50 ? 'warning' : 'danger');
                                    @endphp
                                    <span class="badge bg-{{ $gradeColor }}">{{ $item->grade ?? '-' }}</span>
                                </td>
                                <td>{{ $item->remark ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Summary Stats -->
        <div class="row g-3 mb-4">
            @if($reportCard->average_score)
                <div class="col-md-3">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center py-3">
                            <small class="text-muted d-block">Average Score</small>
                            <h4 class="mb-0 fw-bold">{{ $reportCard->average_score }}%</h4>
                        </div>
                    </div>
                </div>
            @endif
            @if($reportCard->position)
                <div class="col-md-3">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center py-3">
                            <small class="text-muted d-block">Class Position</small>
                            <h4 class="mb-0 fw-bold">{{ $reportCard->position }}</h4>
                        </div>
                    </div>
                </div>
            @endif
            @if($reportCard->class_average)
                <div class="col-md-3">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center py-3">
                            <small class="text-muted d-block">Class Average</small>
                            <h4 class="mb-0 fw-bold">{{ $reportCard->class_average }}%</h4>
                        </div>
                    </div>
                </div>
            @endif
            @if($reportCard->promotion_decision)
                <div class="col-md-3">
                    <div class="card bg-light border-0">
                        <div class="card-body text-center py-3">
                            <small class="text-muted d-block">Decision</small>
                            <h5 class="mb-0 fw-bold text-{{ $reportCard->promotion_decision === 'Promoted' ? 'success' : 'warning' }}">
                                {{ $reportCard->promotion_decision }}
                            </h5>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Affective Domain -->
        @if($reportCard->affectiveRatings && $reportCard->affectiveRatings->count() > 0)
            <h6 class="fw-bold mb-3"><i class="ri-heart-line me-1 text-danger"></i>Affective Domain</h6>
            <div class="row g-2 mb-4">
                @foreach($reportCard->affectiveRatings as $rating)
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between align-items-center py-2 px-3 bg-light rounded">
                            <span>{{ optional($rating->trait)->name ?? 'N/A' }}</span>
                            <span class="badge bg-primary">{{ $rating->rating ?? '-' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Psychomotor Domain -->
        @if($reportCard->psychomotorRatings && $reportCard->psychomotorRatings->count() > 0)
            <h6 class="fw-bold mb-3"><i class="ri-run-line me-1 text-success"></i>Psychomotor Domain</h6>
            <div class="row g-2 mb-4">
                @foreach($reportCard->psychomotorRatings as $rating)
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between align-items-center py-2 px-3 bg-light rounded">
                            <span>{{ optional($rating->trait)->name ?? 'N/A' }}</span>
                            <span class="badge bg-success">{{ $rating->rating ?? '-' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Comments -->
        <div class="row g-3 mb-4">
            @if($reportCard->teacher_comment)
                <div class="col-md-6">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <small class="text-muted d-block mb-1">Class Teacher's Comment</small>
                            <p class="mb-0">{{ $reportCard->teacher_comment }}</p>
                            @if($reportCard->classTeacher)
                                <small class="text-muted mt-1 d-block">- {{ $reportCard->classTeacher->name }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            @if($reportCard->principal_comment)
                <div class="col-md-6">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <small class="text-muted d-block mb-1">Principal's Comment</small>
                            <p class="mb-0">{{ $reportCard->principal_comment }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Back Button -->
        <div class="text-center">
            <a href="{{ route('student.report-cards') }}" class="btn btn-outline-primary">
                <i class="ri-arrow-left-line me-1"></i>Back to Report Cards
            </a>
        </div>
    </div>
</div>
@endsection
