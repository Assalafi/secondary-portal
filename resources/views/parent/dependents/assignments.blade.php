@extends('layouts.parent')

@section('title', 'Assignments')
@section('page-title', 'Assignments')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.dependents.index') }}" class="text-decoration-none">My Dependents</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.dependents.show', $student->id) }}" class="text-decoration-none">{{ $student->user->name }}</a></li>
                <li class="breadcrumb-item active text-muted">Assignments</li>
            </ol>
        </nav>

        @if($assignments->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="ri-file-list-line" style="font-size: 48px; color: #ccc;"></i>
                    <h6 class="mt-3">No Assignments Found</h6>
                    <p class="text-muted small mb-0">There are no assignments available at the moment.</p>
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach($assignments as $assignment)
                    <div class="col-md-6 col-lg-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        {{ $assignment->subject->name ?? 'General' }}
                                    </span>
                                    <span class="badge {{ $assignment->assessment_date && \Carbon\Carbon::parse($assignment->assessment_date)->isPast() ? 'bg-danger' : 'bg-success' }}">
                                        {{ $assignment->assessment_date && \Carbon\Carbon::parse($assignment->assessment_date)->isPast() ? 'Closed' : 'Active' }}
                                    </span>
                                </div>
                                
                                <h6 class="mb-3">{{ $assignment->title }}</h6>
                                
                                <div class="mb-3">
                                    <p class="small text-muted mb-1">Assessment Date:</p>
                                    <p class="mb-0">
                                        <i class="ri-calendar-line me-1"></i>
                                        {{ $assignment->assessment_date ? \Carbon\Carbon::parse($assignment->assessment_date)->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                </div>

                                @if($assignment->description)
                                    <div class="mb-3">
                                        <p class="small text-muted mb-1">Description:</p>
                                    </div>
                                @endif

                                <hr>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="small mb-0 text-muted">Status: <strong class="text-warning">Pending</strong></p>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#assignmentModal{{ $assignment->id }}">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assignment Detail Modal -->
                    <div class="modal fade" id="assignmentModal{{ $assignment->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div>
                                        <h5 class="modal-title">{{ $assignment->subject->name ?? 'General' }} - {{ $assignment->title }}</h5>
                                        <p class="text-muted small mb-0">
                                            My Dependents > {{ $student->user->name }} > Assignment > {{ $assignment->subject->name ?? 'General' }} Assignment
                                        </p>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-4">
                                        <p class="small text-muted mb-1">Status:</p>
                                        <p class="mb-0"><strong class="text-warning">Pending</strong> (Due date: {{ $assignment->assessment_date ? \Carbon\Carbon::parse($assignment->assessment_date)->format('M d, Y') : 'N/A' }})</p>
                                    </div>

                                    @if($assignment->description)
                                        <div class="mb-4">
                                            <h6>Description:</h6>
                                            <p>{{ $assignment->description }}</p>
                                        </div>
                                    @endif

                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <h6 class="mb-3">Submission Info</h6>
                                            <p class="small mb-0"><i class="ri-information-line me-2"></i>Submit to your teacher in class</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
