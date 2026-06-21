@extends('layouts.student')

@section('title', 'My Report Cards')
@section('page-title', 'My Report Cards')

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item active text-muted">Report Cards</li>
    </ol>
</nav>

@if($reportCards->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="ri-file-text-line text-muted" style="font-size: 64px;"></i>
            <h5 class="mt-3 mb-2">No Report Cards Yet</h5>
            <p class="text-muted">Your published report cards will appear here.</p>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach($reportCards as $report)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="fw-bold mb-1">{{ optional($report->academicSession)->name ?? 'N/A' }}</h6>
                                <span class="badge bg-primary">{{ optional($report->term)->name ?? 'N/A' }}</span>
                            </div>
                            <span class="badge bg-success">Published</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Class</small>
                            <span>{{ optional($report->class)->name ?? 'N/A' }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Report Type</small>
                            <span class="text-capitalize">{{ $report->report_type ?? 'Termly' }}</span>
                        </div>
                        @if($report->average_score)
                            <div class="mb-3">
                                <small class="text-muted d-block">Average Score</small>
                                @php
                                    $avg = $report->average_score;
                                    $avgColor = $avg >= 70 ? 'success' : ($avg >= 50 ? 'warning' : 'danger');
                                @endphp
                                <span class="fw-bold text-{{ $avgColor }}">{{ $avg }}%</span>
                                <div class="progress mt-1" style="height: 5px;">
                                    <div class="progress-bar bg-{{ $avgColor }}" style="width: {{ $avg }}%"></div>
                                </div>
                            </div>
                        @endif
                        <a href="{{ route('student.report-cards.show', $report->id) }}" class="btn btn-outline-primary w-100 btn-sm">
                            <i class="ri-eye-line me-1"></i>View Report Card
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
