@extends('layouts.teacher')

@section('title', 'Create Assignment')
@section('page-title', 'Create Assignment')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="ri-add-circle-line me-2 text-success"></i>New Assignment</h6>
                    <a href="{{ route('teacher.assignments.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('teacher.assignments.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Enter assignment title" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Class & Subject <span class="text-danger">*</span></label>
                            <select name="class_subject" class="form-select @error('class_arm_id') is-invalid @enderror" id="classSubjectSelect" required>
                                <option value="">Select Class & Subject</option>
                                @foreach($mySubjects as $cs)
                                    <option value="{{ $cs->class_arm_id }}_{{ $cs->subject_id }}">
                                        {{ $cs->classArm->schoolClass->name ?? '' }} {{ $cs->classArm->name ?? '' }} - {{ $cs->subject->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="class_arm_id" id="classArmId">
                            <input type="hidden" name="subject_id" id="subjectId">
                            @error('class_arm_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}" min="{{ now()->addDay()->format('Y-m-d') }}" required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Question / Description <span class="text-danger">*</span></label>
                        <textarea name="question" class="form-control @error('question') is-invalid @enderror" rows="5" placeholder="Enter the assignment question or description" required>{{ old('question') }}</textarea>
                        @error('question')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Instructions <span class="text-muted">(Optional)</span></label>
                        <textarea name="instructions" class="form-control" rows="3" placeholder="Any specific instructions for students">{{ old('instructions') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Submission Info <span class="text-muted">(Optional)</span></label>
                        <textarea name="submission_info" class="form-control" rows="2" placeholder="How and where to submit">{{ old('submission_info') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('teacher.assignments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ri-send-plane-line me-1"></i>Publish Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="ri-information-line me-2 text-info"></i>Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-3">
                        <i class="ri-checkbox-circle-line text-success me-2 mt-1"></i>
                        <small>Write clear and concise questions</small>
                    </li>
                    <li class="d-flex mb-3">
                        <i class="ri-checkbox-circle-line text-success me-2 mt-1"></i>
                        <small>Set a reasonable due date giving students enough time</small>
                    </li>
                    <li class="d-flex mb-3">
                        <i class="ri-checkbox-circle-line text-success me-2 mt-1"></i>
                        <small>Include submission instructions if applicable</small>
                    </li>
                    <li class="d-flex">
                        <i class="ri-checkbox-circle-line text-success me-2 mt-1"></i>
                        <small>Students will see this immediately after publishing</small>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('classSubjectSelect').addEventListener('change', function() {
    const value = this.value;
    if (value) {
        const parts = value.split('_');
        document.getElementById('classArmId').value = parts[0];
        document.getElementById('subjectId').value = parts[1];
    } else {
        document.getElementById('classArmId').value = '';
        document.getElementById('subjectId').value = '';
    }
});
</script>
@endpush
