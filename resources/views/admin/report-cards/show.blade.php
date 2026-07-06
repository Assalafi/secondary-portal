@extends('layouts.admin')

@section('title', 'Report Card - ' . $reportCard->student->full_name)

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 no-print">
        <div>
            <h1 class="h4 fw-bold mb-1">Report Card</h1>
            <p class="text-muted mb-0">{{ $reportCard->student->full_name }} · {{ $reportCard->session_name }} · {{ $reportCard->term_name }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.academic-management.report-cards.edit-comments', $reportCard) }}" class="btn btn-outline-info btn-sm">Comments</a>
            <a href="{{ route('admin.academic-management.report-cards.edit-domain-ratings', $reportCard) }}" class="btn btn-outline-warning btn-sm">Domain Ratings</a>
            <a href="{{ route('admin.academic-management.report-cards.edit-attendance', $reportCard) }}" class="btn btn-outline-secondary btn-sm">Attendance</a>
            <a href="{{ route('admin.academic-management.report-cards.edit-promotion', $reportCard) }}" class="btn btn-outline-secondary btn-sm">Decision / Next Term</a>
            @if($reportCard->status === 'draft')
                <form action="{{ route('admin.academic-management.report-cards.approve', $reportCard) }}" method="POST">@csrf
                    <button class="btn btn-success btn-sm">Approve</button>
                </form>
            @elseif($reportCard->status === 'approved')
                <form action="{{ route('admin.academic-management.report-cards.publish', $reportCard) }}" method="POST">@csrf
                    <button class="btn btn-primary btn-sm">Publish</button>
                </form>
            @endif
            <a href="{{ route('admin.academic-management.report-cards.generate-pdf', $reportCard) }}" class="btn btn-dark btn-sm">Download PDF</a>
            <button type="button" onclick="window.print()" class="btn btn-outline-dark btn-sm">Print</button>
        </div>
    </div>

    @include('report-cards.partials.card')
</div>
@endsection
