@extends('layouts.student')

@section('title', 'My Assignments')
@section('page-title', 'My Assignments')

@section('content')
    @if(!$student)
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="ri-error-warning-line text-warning" style="font-size: 64px;"></i>
                <h5 class="mt-3 mb-2">Student Profile Not Found</h5>
                <p class="text-muted">Your student profile has not been set up yet. Please contact the school administrator.</p>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0">My Assignments</h6>
                    <span class="badge bg-primary">{{ $assignments->count() }} Assignments</span>
                </div>

                @if($assignments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Subject</th>
                                    <th>Class</th>
                                    <th>Due Date</th>
                                    <th>Teacher</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignments as $assignment)
                                    @php
                                        $isOverdue = $assignment->due_date < now();
                                        $daysLeft = now()->diffInDays($assignment->due_date, false);
                                        $statusBadge = $isOverdue ? 'bg-danger' : ($daysLeft <= 2 ? 'bg-warning' : 'bg-success');
                                        $statusText = $isOverdue ? 'Overdue' : ($daysLeft <= 2 ? 'Due Soon' : 'Active');
                                    @endphp
                                    <tr onclick="window.location='{{ route('student.assignments.show', $assignment) }}'" style="cursor: pointer;">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <p class="mb-0 fw-medium">{{ $assignment->title }}</p>
                                            @if($assignment->instructions)
                                                <small class="text-muted">{{ Str::limit($assignment->instructions, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $assignment->subject->name ?? '-' }}</td>
                                        <td>
                                            {{ $assignment->class->name ?? 'All Classes' }}
                                            @if($assignment->classArm)
                                                {{ $assignment->classArm->name }}
                                            @endif
                                        </td>
                                        <td>
                                            <p class="mb-0">{{ $assignment->due_date->format('M d, Y') }}</p>
                                            <small class="text-muted">{{ $assignment->due_date->format('h:i A') }}</small>
                                        </td>
                                        <td>{{ $assignment->teacher->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ri-clipboard-line text-muted" style="font-size: 64px;"></i>
                        <h5 class="mt-3 mb-2">No Assignments</h5>
                        <p class="text-muted">You don't have any active assignments at the moment.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection
