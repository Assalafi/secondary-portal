@extends('layouts.admin')

@section('title', 'Report Cards')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Report Cards</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.index') }}" class="text-muted">Academic Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.results.index') }}" class="text-muted">Results & Grades</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Report Cards</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-semibold mb-0">All Report Cards</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.academic-management.results.index') }}" class="btn btn-outline-secondary">
                        <i class="ri-arrow-left-line me-2"></i>Back to Results
                    </a>
                </div>
            </div>

            @if($reportCards->isEmpty())
                <div class="text-center py-5">
                    <i class="ri-file-list-line" style="font-size: 48px; color: #ccc;"></i>
                    <h6 class="mt-3">No Report Cards Generated</h6>
                    <p class="text-muted small mb-0">Go to Results & Grades to generate report cards for students.</p>
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
                                <th>Type</th>
                                <th>Grade</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportCards as $reportCard)
                            <tr>
                                <td>{{ $reportCard->student->surname }}, {{ $reportCard->student->first_name }}</td>
                                <td>{{ $reportCard->class->name }}</td>
                                <td>{{ $reportCard->academicSession->name ?? 'N/A' }}</td>
                                <td>{{ $reportCard->term->name ?? 'Annual' }}</td>
                                <td>{{ ucfirst($reportCard->report_type) }}</td>
                                <td><span class="badge bg-{{ $reportCard->final_grade == 'A' ? 'success' : ($reportCard->final_grade == 'B' ? 'primary' : ($reportCard->final_grade == 'C' ? 'warning' : ($reportCard->final_grade == 'D' ? 'info' : 'danger'))) }}">{{ $reportCard->final_grade }}</span></td>
                                <td>{{ $reportCard->class_position ?? 'N/A' }}/{{ $reportCard->number_in_class ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $reportCard->status == 'published' ? 'success' : ($reportCard->status == 'approved' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst($reportCard->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.academic-management.report-cards.show', $reportCard->id) }}" class="btn btn-outline-primary" title="View">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        @if($reportCard->status == 'draft')
                                            <form action="{{ route('admin.academic-management.report-cards.approve', $reportCard->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success" title="Approve">
                                                    <i class="ri-check-line"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($reportCard->status == 'approved')
                                            <form action="{{ route('admin.academic-management.report-cards.publish', $reportCard->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success" title="Publish">
                                                    <i class="ri-send-plane-line"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.academic-management.report-cards.generate-pdf', $reportCard->id) }}" class="btn btn-outline-dark" title="Generate PDF">
                                            <i class="ri-file-pdf-line"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $reportCards->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
