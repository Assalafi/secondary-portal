@extends('layouts.parent')

@section('title', 'Report Cards')
@section('page-title', 'Report Cards')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active text-muted">Report Cards</li>
            </ol>
        </nav>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                @if($reportCards->isEmpty())
                    <div class="text-center py-5">
                        <i class="ri-file-list-line" style="font-size: 48px; color: #ccc;"></i>
                        <h6 class="mt-3">No Report Cards Available</h6>
                        <p class="text-muted small mb-0">Report cards will appear here once they are published by the school.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Class</th>
                                    <th>Session</th>
                                    <th>Term</th>
                                    <th>Grade</th>
                                    <th>Position</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportCards as $reportCard)
                                <tr>
                                    <td>{{ $reportCard->student->surname }}, {{ $reportCard->student->first_name }}</td>
                                    <td>{{ $reportCard->class->name }}</td>
                                    <td>{{ $reportCard->session_name }}</td>
                                    <td>{{ $reportCard->term_name }}</td>
                                    <td><span class="badge bg-{{ str_starts_with($reportCard->final_grade, 'A') ? 'success' : (str_starts_with($reportCard->final_grade, 'B') ? 'primary' : (str_starts_with($reportCard->final_grade, 'C') ? 'warning' : 'danger')) }}">{{ $reportCard->final_grade }}</span></td>
                                    <td>{{ $reportCard->class_position ?? 'N/A' }}/{{ $reportCard->number_in_class ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('parent.report-cards.show', $reportCard->id) }}" class="btn btn-sm btn-primary">
                                            <i class="ri-eye-line"></i> View
                                        </a>
                                        @if($reportSettings->allow_parent_download)
                                            <a href="{{ route('parent.report-cards.download', $reportCard->id) }}" class="btn btn-sm btn-dark">
                                                <i class="ri-download-line"></i> PDF
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
