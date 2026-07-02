@extends('layouts.teacher')

@section('title', 'Score Upload')
@section('page-title', 'Score Upload')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="ri-file-list-3-line me-2 text-primary"></i>Select Class & Subject to Upload Scores</h6>
            @if($currentSession && $currentTerm)
                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $currentSession->name ?? '' }} &bull; {{ $currentTerm->name ?? '' }}</span>
            @endif
        </div>
    </div>
    <div class="card-body p-0">
        @if($mySubjects->count() > 0)
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4">#</th>
                            <th class="border-0">Subject</th>
                            <th class="border-0">Class</th>
                            <th class="border-0">Level</th>
                            <th class="border-0 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mySubjects as $cs)
                            <tr>
                                <td class="px-4">{{ $loop->iteration }}</td>
                                <td><span class="fw-medium">{{ $cs->subject->name ?? 'N/A' }}</span></td>
                                <td>{{ $cs->classArm->schoolClass->name ?? '' }} {{ $cs->classArm->name ?? '' }}</td>
                                <td><span class="badge bg-info bg-opacity-10 text-info">{{ $cs->classArm->schoolClass->level ?? '' }}</span></td>
                                <td class="text-center">
                                    <a href="{{ route('teacher.scores.upload', [$cs->class_arm_id, $cs->subject_id]) }}" class="btn btn-sm btn-primary">
                                        <i class="ri-upload-2-line me-1"></i>Upload Scores
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="ri-file-list-3-line text-muted" style="font-size: 64px;"></i>
                <h5 class="text-muted mt-3">No Subjects Assigned</h5>
                <p class="text-muted">You don't have any subjects assigned for score upload.</p>
            </div>
        @endif
    </div>
</div>
@endsection
