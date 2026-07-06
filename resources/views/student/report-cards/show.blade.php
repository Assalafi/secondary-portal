@extends('layouts.student')

@section('title', 'Report Card')
@section('page-title', 'Report Card')

@section('content')
<div class="d-flex justify-content-between align-items-center gap-2 mb-3 no-print">
    <a href="{{ route('student.report-cards') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ri-arrow-left-line me-1"></i>Report Cards
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('student.report-cards.download', $reportCard) }}" class="btn btn-dark btn-sm">
            <i class="ri-download-line me-1"></i>Download PDF
        </a>
        <button type="button" onclick="window.print()" class="btn btn-outline-dark btn-sm">Print</button>
    </div>
</div>

@include('report-cards.partials.card')
@endsection
