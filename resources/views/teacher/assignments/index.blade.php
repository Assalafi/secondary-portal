@extends('layouts.teacher')

@section('title', 'Assignments')
@section('page-title', 'Assignments')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="ri-task-line me-2 text-primary"></i>My Assignments</h6>
            <a href="{{ route('teacher.assignments.create') }}" class="btn btn-sm btn-primary">
                <i class="ri-add-line me-1"></i>Create Assignment
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        @if($assignments->count() > 0)
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4">#</th>
                            <th class="border-0">Title</th>
                            <th class="border-0">Subject</th>
                            <th class="border-0">Class</th>
                            <th class="border-0">Due Date</th>
                            <th class="border-0">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignments as $assignment)
                            <tr>
                                <td class="px-4">{{ $loop->iteration + ($assignments->currentPage() - 1) * $assignments->perPage() }}</td>
                                <td><span class="fw-medium">{{ $assignment->title }}</span></td>
                                <td>{{ $assignment->subject->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info">
                                        {{ $assignment->classArm->schoolClass->name ?? '' }} {{ $assignment->classArm->name ?? '' }}
                                    </span>
                                </td>
                                <td>
                                    @if($assignment->due_date)
                                        @if($assignment->due_date->isPast())
                                            <span class="text-danger small">{{ $assignment->due_date->format('M d, Y') }}</span>
                                        @else
                                            <span class="text-muted small">{{ $assignment->due_date->format('M d, Y') }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColor = match($assignment->status) {
                                            'Published' => 'success',
                                            'Draft' => 'secondary',
                                            'Closed' => 'danger',
                                            default => 'warning'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}">{{ $assignment->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $assignments->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="ri-task-line text-muted" style="font-size: 64px;"></i>
                <h5 class="text-muted mt-3">No Assignments Yet</h5>
                <p class="text-muted mb-3">Create your first assignment for your students.</p>
                <a href="{{ route('teacher.assignments.create') }}" class="btn btn-primary">
                    <i class="ri-add-line me-1"></i>Create Assignment
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
