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
            @foreach (['success' => 'success', 'error' => 'danger', 'info' => 'info'] as $key => $type)
                @if(session($key))
                    <div class="alert alert-{{ $type }} alert-dismissible fade show">
                        {{ session($key) }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endforeach

            @if(session('bulk_report_card_warnings') && count(session('bulk_report_card_warnings')))
                <div class="alert alert-warning alert-dismissible fade show">
                    <strong>Some report cards were skipped:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach(session('bulk_report_card_warnings') as $warning)
                            <li>{{ $warning }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-semibold mb-0">All Report Cards</h5>
                <div class="d-flex gap-2 flex-wrap">
                    @if(!$reportCards->isEmpty())
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="bulkActionBtn" data-bs-toggle="dropdown" disabled>
                                Bulk Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" data-bulk-action="approve">
                                        <i class="ri-check-line me-2"></i>Approve Selected
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" data-bulk-action="publish">
                                        <i class="ri-send-plane-line me-2"></i>Publish Selected
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" data-bulk-action="approve_publish">
                                        <i class="ri-checkbox-circle-line me-2"></i>Approve & Publish Selected
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif
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
                                <th style="width: 36px;">
                                    <input type="checkbox" class="form-check-input" id="selectAllReportCards" aria-label="Select all report cards on this page">
                                </th>
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
                                <td>
                                    <input type="checkbox"
                                           class="form-check-input report-card-checkbox"
                                           value="{{ $reportCard->id }}"
                                           aria-label="Select report card for {{ $reportCard->student->full_name ?? $reportCard->student->first_name }}">
                                </td>
                                <td>{{ $reportCard->student->surname }}, {{ $reportCard->student->first_name }}</td>
                                <td>{{ $reportCard->class->name }}</td>
                                <td>{{ $reportCard->session_name }}</td>
                                <td>{{ $reportCard->term_name }}</td>
                                <td>{{ ucfirst($reportCard->report_type) }}</td>
                                <td><span class="badge bg-{{ str_starts_with($reportCard->final_grade, 'A') ? 'success' : (str_starts_with($reportCard->final_grade, 'B') ? 'primary' : (str_starts_with($reportCard->final_grade, 'C') ? 'warning' : 'danger')) }}">{{ $reportCard->final_grade }}</span></td>
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

<form id="bulkReportCardForm" action="{{ route('admin.academic-management.report-cards.bulk-action') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="action" id="bulkActionInput">
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('selectAllReportCards');
        const checkboxes = Array.from(document.querySelectorAll('.report-card-checkbox'));
        const bulkButton = document.getElementById('bulkActionBtn');
        const bulkForm = document.getElementById('bulkReportCardForm');
        const bulkActionInput = document.getElementById('bulkActionInput');

        function selectedIds() {
            return checkboxes.filter(checkbox => checkbox.checked).map(checkbox => checkbox.value);
        }

        function refreshBulkState() {
            const ids = selectedIds();

            if (bulkButton) {
                bulkButton.disabled = ids.length === 0;
                bulkButton.textContent = ids.length > 0 ? `Bulk Actions (${ids.length})` : 'Bulk Actions';
            }

            if (selectAll) {
                selectAll.checked = ids.length > 0 && ids.length === checkboxes.length;
                selectAll.indeterminate = ids.length > 0 && ids.length < checkboxes.length;
            }
        }

        selectAll?.addEventListener('change', function () {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            refreshBulkState();
        });

        checkboxes.forEach(checkbox => checkbox.addEventListener('change', refreshBulkState));

        document.addEventListener('click', function (event) {
            const action = event.target.closest('[data-bulk-action]');

            if (!action) {
                return;
            }

            event.preventDefault();

            const ids = selectedIds();
            const actionName = action.getAttribute('data-bulk-action');
            const labels = {
                approve: 'approve',
                publish: 'publish',
                approve_publish: 'approve and publish',
            };

            if (ids.length === 0) {
                alert('Please select at least one report card.');
                return;
            }

            if (!confirm(`Are you sure you want to ${labels[actionName]} ${ids.length} selected report card${ids.length === 1 ? '' : 's'}?`)) {
                return;
            }

            bulkActionInput.value = actionName;
            bulkForm.querySelectorAll('input[name="report_card_ids[]"]').forEach(input => input.remove());

            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'report_card_ids[]';
                input.value = id;
                bulkForm.appendChild(input);
            });

            bulkForm.submit();
        });

        refreshBulkState();
    });
</script>
@endpush
