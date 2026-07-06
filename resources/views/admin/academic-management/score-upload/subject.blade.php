@extends('layouts.admin')

@section('title', 'Upload Scores')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Upload Scores</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.index') }}" class="text-muted">Academic Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.score-upload.index') }}" class="text-muted">Score Upload</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Upload Scores</li>
            </ol>
        </nav>
    </div>

    @php
        $classArm = \App\Models\ClassArm::with(['schoolClass', 'students.user'])->findOrFail($classId);
        $subject = \App\Models\Subject::findOrFail($subjectId);
        $currentSession = \App\Models\AcademicSession::where('is_current', true)->first();
        $currentTerm = \App\Models\Term::first();
    @endphp

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
                <h5 class="fw-semibold mb-0">Upload Scores - {{ $subject->name }}</h5>
                <a href="{{ route('admin.academic-management.score-upload.class', $classId) }}" class="btn btn-outline-secondary btn-sm w-100 w-md-auto">Back</a>
            </div>

            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <p class="mb-1"><strong>Class:</strong> {{ $classArm->schoolClass->name }} {{ $classArm->name }}</p>
                    <p class="mb-1"><strong>Subject:</strong> {{ $subject->name }}</p>
                </div>
                <div class="col-12 col-md-6">
                    <p class="mb-1"><strong>Session:</strong> {{ $currentSession->name ?? 'Not set' }}</p>
                    <p class="mb-1"><strong>Term:</strong> {{ $currentTerm->name ?? 'Not set' }}</p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-light border-0">
                        <div class="card-body p-3">
                            <h6 class="fw-semibold mb-3">Configure Maximum Marks (Total must be 100)</h6>
                            <div class="row g-3">
                                <div class="col-6 col-md-3">
                                    <label class="form-label small">1st CA Max</label>
                                    <input type="number" id="first_ca_max" class="form-control form-control-sm" min="0" max="100" step="1" value="{{ $scoreBatch->first_ca_max ?? 10 }}" oninput="updateMaxMarks()">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label small">2nd CA Max</label>
                                    <input type="number" id="second_ca_max" class="form-control form-control-sm" min="0" max="100" step="1" value="{{ $scoreBatch->second_ca_max ?? 10 }}" oninput="updateMaxMarks()">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label small">3rd CA Max</label>
                                    <input type="number" id="third_ca_max" class="form-control form-control-sm" min="0" max="100" step="1" value="{{ $scoreBatch->third_ca_max ?? 10 }}" oninput="updateMaxMarks()">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label small">Exam Max</label>
                                    <input type="number" id="exam_max" class="form-control form-control-sm" min="0" max="100" step="1" value="{{ $scoreBatch->exam_max ?? 70 }}" oninput="updateMaxMarks()">
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">Total: <span id="total_max" class="fw-bold">100</span>/100</small>
                                <span id="max_error" class="text-danger small ms-2 d-none">Total must not exceed 100</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.academic-management.score-upload.store') }}">
                @csrf
                <input type="hidden" name="class_id" value="{{ $classArm->id }}">
                <input type="hidden" name="subject_id" value="{{ $subjectId }}">
                <input type="hidden" name="academic_session_id" value="{{ $currentSession->id ?? '' }}">
                <input type="hidden" name="term_id" value="{{ $currentTerm->id ?? '' }}">
                <input type="hidden" name="first_ca_max" id="hidden_first_ca_max" value="10">
                <input type="hidden" name="second_ca_max" id="hidden_second_ca_max" value="10">
                <input type="hidden" name="third_ca_max" id="hidden_third_ca_max" value="10">
                <input type="hidden" name="exam_max" id="hidden_exam_max" value="70">

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Admission No</th>
                                <th>1st CA (<span id="header_first_ca_max">10</span>)</th>
                                <th>2nd CA (<span id="header_second_ca_max">10</span>)</th>
                                <th>3rd CA (<span id="header_third_ca_max">10</span>)</th>
                                <th>Exam (<span id="header_exam_max">70</span>)</th>
                                <th>Total</th>
                                <th>Grade</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classArm->students as $student)
                                @php
                                    $existingScore = $existingScores[$student->id] ?? null;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $student->full_name ?? $student->user->name ?? '-' }}</td>
                                    <td>{{ $student->admission_no ?? '-' }}</td>
                                    <td><input type="number" name="scores[{{ $student->id }}][first_ca]" class="form-control form-control-sm score-input" min="0" step="0.5" value="{{ $existingScore->first_ca ?? 0 }}" oninput="calculateTotal(this)" data-type="first_ca"></td>
                                    <td><input type="number" name="scores[{{ $student->id }}][second_ca]" class="form-control form-control-sm score-input" min="0" step="0.5" value="{{ $existingScore->second_ca ?? 0 }}" oninput="calculateTotal(this)" data-type="second_ca"></td>
                                    <td><input type="number" name="scores[{{ $student->id }}][third_ca]" class="form-control form-control-sm score-input" min="0" step="0.5" value="{{ $existingScore->third_ca ?? 0 }}" oninput="calculateTotal(this)" data-type="third_ca"></td>
                                    <td><input type="number" name="scores[{{ $student->id }}][exam]" class="form-control form-control-sm score-input" min="0" step="0.5" value="{{ $existingScore->exam ?? 0 }}" oninput="calculateTotal(this)" data-type="exam"></td>
                                    <td><input type="text" name="scores[{{ $student->id }}][total]" class="form-control form-control-sm bg-light" readonly value="{{ $existingScore->total ?? '' }}"></td>
                                    <td><input type="text" name="scores[{{ $student->id }}][grade]" class="form-control form-control-sm bg-light" readonly value="{{ $existingScore->grade ?? '' }}"></td>
                                    <td><input type="text" name="scores[{{ $student->id }}][remark]" class="form-control form-control-sm bg-light" readonly value="{{ $existingScore->remark ?? '' }}"></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">No students found in this class</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex flex-column flex-md-row gap-2">
                    <button type="submit" class="btn btn-primary w-100 w-md-auto">Save Scores</button>
                    <button type="button" class="btn btn-success w-100 w-md-auto" onclick="publishScores()">Save & Publish</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateMaxMarks() {
    const firstCaMax = parseFloat(document.getElementById('first_ca_max').value) || 0;
    const secondCaMax = parseFloat(document.getElementById('second_ca_max').value) || 0;
    const thirdCaMax = parseFloat(document.getElementById('third_ca_max').value) || 0;
    const examMax = parseFloat(document.getElementById('exam_max').value) || 0;
    
    const total = firstCaMax + secondCaMax + thirdCaMax + examMax;
    
    document.getElementById('total_max').textContent = total;
    document.getElementById('header_first_ca_max').textContent = firstCaMax;
    document.getElementById('header_second_ca_max').textContent = secondCaMax;
    document.getElementById('header_third_ca_max').textContent = thirdCaMax;
    document.getElementById('header_exam_max').textContent = examMax;
    
    document.getElementById('hidden_first_ca_max').value = firstCaMax;
    document.getElementById('hidden_second_ca_max').value = secondCaMax;
    document.getElementById('hidden_third_ca_max').value = thirdCaMax;
    document.getElementById('hidden_exam_max').value = examMax;
    
    const errorSpan = document.getElementById('max_error');
    const submitButton = document.querySelector('button[type="submit"]');
    
    if (total > 100) {
        errorSpan.classList.remove('d-none');
        submitButton.disabled = true;
        document.querySelectorAll('.score-input').forEach(input => {
            input.disabled = true;
        });
    } else {
        errorSpan.classList.add('d-none');
        submitButton.disabled = false;
        document.querySelectorAll('.score-input').forEach(input => {
            input.disabled = false;
        });
    }
    
    // Update max attributes on score inputs
    document.querySelectorAll('[data-type="first_ca"]').forEach(input => {
        input.max = firstCaMax;
    });
    document.querySelectorAll('[data-type="second_ca"]').forEach(input => {
        input.max = secondCaMax;
    });
    document.querySelectorAll('[data-type="third_ca"]').forEach(input => {
        input.max = thirdCaMax;
    });
    document.querySelectorAll('[data-type="exam"]').forEach(input => {
        input.max = examMax;
    });
}

function calculateTotal(input) {
    const row = input.closest('tr');
    const firstCa = parseFloat(row.querySelector('[name$="[first_ca]"]')?.value) || 0;
    const secondCa = parseFloat(row.querySelector('[name$="[second_ca]"]')?.value) || 0;
    const thirdCa = parseFloat(row.querySelector('[name$="[third_ca]"]')?.value) || 0;
    const exam = parseFloat(row.querySelector('[name$="[exam]"]')?.value) || 0;
    
    const firstCaMax = parseFloat(document.getElementById('first_ca_max').value) || 0;
    const secondCaMax = parseFloat(document.getElementById('second_ca_max').value) || 0;
    const thirdCaMax = parseFloat(document.getElementById('third_ca_max').value) || 0;
    const examMax = parseFloat(document.getElementById('exam_max').value) || 0;
    
    const total = firstCa + secondCa + thirdCa + exam;
    row.querySelector('[name$="[total]"]').value = total.toFixed(2);
    
    // Auto-calculate grade based on total percentage using grading system
    const maxTotal = firstCaMax + secondCaMax + thirdCaMax + examMax;
    const percentage = maxTotal > 0 ? (total / maxTotal) * 100 : 0;
    
    // Find grade from grading system
    let grade = 'F';
    let remark = 'Fail';
    
    console.log('Percentage:', percentage);
    console.log('Grading Systems:', window.gradingSystems);
    
    if (window.gradingSystems && window.gradingSystems.length > 0) {
        for (const gs of window.gradingSystems) {
            console.log('Checking grade:', gs.grade, 'range:', gs.min_score, '-', gs.max_score);
            if (percentage >= gs.min_score && percentage <= gs.max_score) {
                grade = gs.grade;
                remark = gs.remark;
                console.log('Match found:', grade, remark);
                break;
            }
        }
    } else {
        // Fallback to hardcoded grades if grading system not available
        console.log('Using fallback grades');
        if (percentage >= 70) { grade = 'A'; remark = 'Excellent'; }
        else if (percentage >= 60) { grade = 'B'; remark = 'Very Good'; }
        else if (percentage >= 50) { grade = 'C'; remark = 'Good'; }
        else if (percentage >= 45) { grade = 'D'; remark = 'Fair'; }
        else if (percentage >= 40) { grade = 'E'; remark = 'Pass'; }
        else { grade = 'F'; remark = 'Fail'; }
    }
    
    console.log('Final grade:', grade, 'remark:', remark);
    row.querySelector('[name$="[grade]"]').value = grade;
    row.querySelector('[name$="[remark]"]').value = remark;
}

function publishScores() {
    if (confirm('Are you sure you want to publish these scores? This will make them visible to parents and students.')) {
        document.querySelector('form').submit();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateMaxMarks();
    
    // Pass grading systems to JavaScript
    window.gradingSystems = @json($gradingSystems ?? []);
    console.log('Grading Systems loaded:', window.gradingSystems);
});
</script>
@endpush
@endsection
