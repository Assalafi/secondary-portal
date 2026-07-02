@extends('layouts.teacher')

@section('title', 'My Subjects')
@section('page-title', 'My Subjects')

@section('content')
@if($mySubjects->count() > 0)
    <div class="row">
        @foreach($mySubjects as $subjectName => $classSubjects)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: rgba(99, 102, 241, 0.1);">
                                <i class="ri-book-2-line text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $subjectName }}</h6>
                                <small class="text-muted">{{ $classSubjects->count() }} class{{ $classSubjects->count() > 1 ? 'es' : '' }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($classSubjects as $cs)
                                <div class="list-group-item border-0 py-2 px-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-medium small">{{ $cs->classArm->schoolClass->level ?? '' }} {{ $cs->classArm->schoolClass->name ?? '' }} {{ $cs->classArm->name ?? '' }}</span>
                                    </div>
                                    <a href="{{ route('teacher.scores.upload', [$cs->class_arm_id, $cs->subject_id]) }}" class="btn btn-sm btn-outline-primary py-0 px-2">
                                        <small><i class="ri-upload-line me-1"></i>Scores</small>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="ri-book-open-line text-muted" style="font-size: 64px;"></i>
            <h5 class="text-muted mt-3">No Subjects Assigned</h5>
            <p class="text-muted">You have not been assigned any subjects yet. Please contact the administrator.</p>
        </div>
    </div>
@endif
@endsection
