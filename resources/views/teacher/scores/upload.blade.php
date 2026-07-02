@extends('layouts.teacher')

@section('title', 'Upload Scores - ' . ($subject->name ?? ''))
@section('page-title', 'Upload Scores')

@push('styles')
<style>
    .score-input {
        width: 60px;
        text-align: center;
        padding: 4px 6px;
        font-size: 13px;
    }
    .score-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 0.15rem rgba(99, 102, 241, 0.15);
    }
    .total-cell { font-weight: 600; color: #1e40af; }
    .grade-cell { font-weight: 600; }
</style>
@endpush

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="mb-0 fw-bold">
                    <i class="ri-file-list-3-line me-2 text-primary"></i>
                    {{ $subject->name }} - {{ $classArm->schoolClass->name ?? '' }} {{ $classArm->name }}
                </h6>
                <small class="text-muted">{{ $currentSession->name ?? '' }} &bull; {{ $currentTerm->name ?? '' }}</small>
            </div>
            <a href="{{ route('teacher.scores.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="ri-arrow-left-line me-1"></i>Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('teacher.scores.store') }}" method="POST" id="scoreForm">
            @csrf
            <input type="hidden" name="class_id" value="{{ $classArm->id }}">
            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
            <input type="hidden" name="academic_session_id" value="{{ $currentSession->id ?? '' }}">
            <input type="hidden" name="term_id" value="{{ $currentTerm->id ?? '' }}">

            <!-- Max Scores Configuration -->
            <div class="row mb-4 g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-medium">1st CA Max</label>
                    <input type="number" name="first_ca_max" class="form-control" value="{{ $scoreBatch->first_ca_max ?? 10 }}" min="0" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">2nd CA Max</label>
                    <input type="number" name="second_ca_max" class="form-control" value="{{ $scoreBatch->second_ca_max ?? 10 }}" min="0" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">3rd CA Max</label>
                    <input type="number" name="third_ca_max" class="form-control" value="{{ $scoreBatch->third_ca_max ?? 10 }}" min="0" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-medium">Exam Max</label>
                    <input type="number" name="exam_max" class="form-control" value="{{ $scoreBatch->exam_max ?? 70 }}" min="0" required>
                </div>
            </div>

            <!-- Scores Table -->
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0" style="width: 40px;">#</th>
                            <th class="border-0">Student Name</th>
                            <th class="border-0 text-center">1st CA</th>
                            <th class="border-0 text-center">2nd CA</th>
                            <th class="border-0 text-center">3rd CA</th>
                            <th class="border-0 text-center">Exam</th>
                            <th class="border-0 text-center">Total</th>
                            <th class="border-0 text-center">Grade</th>
                            <th class="border-0 text-center">Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classArm->students->sortBy('user.name') as $student)
                            @php
                                $existing = $existingScores[$student->id] ?? null;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-medium">{{ $student->user->name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <input type="number" name="scores[{{ $student->id }}][first_ca]" class="form-control form-control-sm score-input ca-input" value="{{ $existing->first_ca ?? '' }}" min="0" data-student="{{ $student->id }}">
                                </td>
                                <td class="text-center">
                                    <input type="number" name="scores[{{ $student->id }}][second_ca]" class="form-control form-control-sm score-input ca-input" value="{{ $existing->second_ca ?? '' }}" min="0" data-student="{{ $student->id }}">
                                </td>
                                <td class="text-center">
                                    <input type="number" name="scores[{{ $student->id }}][third_ca]" class="form-control form-control-sm score-input ca-input" value="{{ $existing->third_ca ?? '' }}" min="0" data-student="{{ $student->id }}">
                                </td>
                                <td class="text-center">
                                    <input type="number" name="scores[{{ $student->id }}][exam]" class="form-control form-control-sm score-input ca-input" value="{{ $existing->exam ?? '' }}" min="0" data-student="{{ $student->id }}">
                                </td>
                                <td class="text-center total-cell">
                                    <input type="hidden" name="scores[{{ $student->id }}][total]" id="total-{{ $student->id }}" value="{{ $existing->total ?? 0 }}">
                                    <span id="total-display-{{ $student->id }}">{{ $existing->total ?? 0 }}</span>
                                </td>
                                <td class="text-center grade-cell">
                                    <input type="hidden" name="scores[{{ $student->id }}][grade]" id="grade-{{ $student->id }}" value="{{ $existing->grade ?? '' }}">
                                    <span id="grade-display-{{ $student->id }}">{{ $existing->grade ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <input type="hidden" name="scores[{{ $student->id }}][remark]" id="remark-{{ $student->id }}" value="{{ $existing->remark ?? '' }}">
                                    <span id="remark-display-{{ $student->id }}" class="small">{{ $existing->remark ?? '-' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="ri-save-line me-1"></i>Save Scores
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const gradingSystems = @json($gradingSystems);

document.querySelectorAll('.ca-input').forEach(input => {
    input.addEventListener('input', function() {
        const studentId = this.dataset.student;
        calculateTotal(studentId);
    });
});

function calculateTotal(studentId) {
    const inputs = document.querySelectorAll(`input[data-student="${studentId}"]`);
    let total = 0;
    inputs.forEach(input => {
        total += parseFloat(input.value) || 0;
    });

    document.getElementById(`total-${studentId}`).value = total;
    document.getElementById(`total-display-${studentId}`).textContent = total;

    // Calculate grade
    const maxTotal = parseFloat(document.querySelector('[name="first_ca_max"]').value || 0) +
                     parseFloat(document.querySelector('[name="second_ca_max"]').value || 0) +
                     parseFloat(document.querySelector('[name="third_ca_max"]').value || 0) +
                     parseFloat(document.querySelector('[name="exam_max"]').value || 0);

    const percentage = maxTotal > 0 ? (total / maxTotal) * 100 : 0;

    let grade = 'F';
    let remark = 'Fail';
    for (let g of gradingSystems) {
        if (percentage >= g.min_score && percentage <= g.max_score) {
            grade = g.grade;
            remark = g.description || (percentage >= 50 ? 'Pass' : 'Fail');
            break;
        }
    }

    document.getElementById(`grade-${studentId}`).value = grade;
    document.getElementById(`grade-display-${studentId}`).textContent = grade;
    document.getElementById(`remark-${studentId}`).value = remark;
    document.getElementById(`remark-display-${studentId}`).textContent = remark;
}
</script>
@endpush
