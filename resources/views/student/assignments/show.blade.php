@extends('layouts.student')

@section('title', 'Assignment Details')
@section('page-title', 'Assignment Details')

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
                    <h6 class="fw-bold mb-0">{{ $assignment->title }}</h6>
                    <a href="{{ route('student.assignments.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ri-arrow-left-line me-1"></i>Back to Assignments
                    </a>
                </div>

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Assignment Details</h6>
                                
                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Question</label>
                                    <div class="p-3 bg-light rounded">
                                        {!! $assignment->question !!}
                                    </div>
                                </div>

                                @if($assignment->instructions)
                                    <div class="mb-3">
                                        <label class="text-muted small mb-1">Instructions</label>
                                        <div class="p-3 bg-light rounded">
                                            {!! $assignment->instructions !!}
                                        </div>
                                    </div>
                                @endif

                                @if($assignment->submission_info)
                                    <div class="mb-3">
                                        <label class="text-muted small mb-1">Submission Information</label>
                                        <div class="p-3 bg-light rounded">
                                            {!! $assignment->submission_info !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Assignment Info</h6>
                                
                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Subject</label>
                                    <p class="mb-0 fw-medium">{{ $assignment->subject->name ?? '-' }}</p>
                                </div>

                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Class</label>
                                    <p class="mb-0 fw-medium">
                                        {{ $assignment->class->name ?? 'All Classes' }}
                                        @if($assignment->classArm)
                                            {{ $assignment->classArm->name }}
                                        @endif
                                    </p>
                                </div>

                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Due Date</label>
                                    <p class="mb-0 fw-medium">{{ $assignment->due_date->format('M d, Y - h:i A') }}</p>
                                </div>

                                @if($assignment->total_marks)
                                    <div class="mb-3">
                                        <label class="text-muted small mb-1">Total Marks</label>
                                        <p class="mb-0 fw-medium">{{ $assignment->total_marks }}</p>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Teacher</label>
                                    <p class="mb-0 fw-medium">{{ $assignment->teacher->name ?? '-' }}</p>
                                </div>

                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Status</label>
                                    <span class="badge bg-{{ $assignment->status === 'Active' ? 'success' : ($assignment->status === 'Closed' ? 'danger' : 'warning') }}">
                                        {{ $assignment->status }}
                                    </span>
                                </div>

                                @if($assignment->published_at)
                                    <div class="mb-3">
                                        <label class="text-muted small mb-1">Published</label>
                                        <p class="mb-0 text-muted small">{{ $assignment->published_at->format('M d, Y - h:i A') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
