@extends('layouts.parent')

@section('title', 'Result')
@section('page-title', 'Result')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.dependents.index') }}" class="text-decoration-none">My Dependents</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.dependents.show', $student->id) }}" class="text-decoration-none">{{ $student->user->name }}</a></li>
                <li class="breadcrumb-item active text-muted">Result</li>
            </ol>
        </nav>

        <!-- Filters -->
        <form method="GET" action="{{ route('parent.dependents.results', $student->id) }}" id="filterForm">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Session:</label>
                    <select class="form-select" name="session" onchange="document.getElementById('filterForm').submit()">
                        <option value="">All Sessions</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session->name }}" {{ request('session') == $session->name ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Term:</label>
                    <select class="form-select" name="term" onchange="document.getElementById('filterForm').submit()">
                        <option value="">All Terms</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->name }}" {{ request('term') == $term->name ? 'selected' : '' }}>
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        @if($results->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="ri-file-list-line" style="font-size: 48px; color: #ccc;"></i>
                    <h6 class="mt-3">No Results Available</h6>
                    <p class="text-muted small mb-0">There are no results for this session/term yet.</p>
                </div>
            </div>
        @else
            <!-- Result Summary Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-md-3">
                            <p class="text-muted small mb-1">Term:</p>
                            <p class="mb-0 fw-bold">{{ request('term') ?: 'All Terms' }}</p>
                        </div>
                        <div class="col-6 col-md-3">
                            <p class="text-muted small mb-1">Total Score:</p>
                            <p class="mb-0 fw-bold">{{ number_format($totalScore, 0) }}</p>
                        </div>
                        <div class="col-6 col-md-3">
                            <p class="text-muted small mb-1">Session:</p>
                            <p class="mb-0 fw-bold">{{ request('session') ?: 'All' }}</p>
                        </div>
                        <div class="col-6 col-md-3">
                            <p class="text-muted small mb-1">Final Grade:</p>
                            @php
                                $grade = $averageScore >= 80 ? 'A' : ($averageScore >= 70 ? 'B' : ($averageScore >= 60 ? 'C' : ($averageScore >= 50 ? 'D' : 'F')));
                            @endphp
                            <p class="mb-0 fw-bold">{{ $grade }}</p>
                        </div>
                        <div class="col-6 col-md-3">
                            <p class="text-muted small mb-1">Class:</p>
                            <p class="mb-0 fw-bold">{{ optional(optional($student->classArm)->schoolClass)->name ?? 'N/A' }} {{ optional($student->classArm)->name ?? '' }}</p>
                        </div>
                        <div class="col-6 col-md-3">
                            <p class="text-muted small mb-1">Final Average:</p>
                            <p class="mb-0 fw-bold">{{ $averageScore ? number_format($averageScore, 1) : '0' }}%</p>
                        </div>
                        <div class="col-6 col-md-3">
                            <p class="text-muted small mb-1">No. in Class:</p>
                            <p class="mb-0 fw-bold">{{ optional($student->classArm)->students ? $student->classArm->students->count() : 0 }} students</p>
                        </div>
                        <div class="col-6 col-md-3">
                            <p class="text-muted small mb-1">Total Subjects:</p>
                            <p class="mb-0 fw-bold">{{ $totalSubjects }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Table -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>SUBJECTS</th>
                                    <th class="text-center">C.A TEST (30)</th>
                                    <th class="text-center">EXAM (70)</th>
                                    <th class="text-center">Total (100)</th>
                                    <th class="text-center">GRADE</th>
                                    <th>REMARK</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $index = 1;
                                @endphp
                                @foreach($subjectResults as $subjectName => $subjectResultList)
                                    @php
                                        // Get the latest score for this subject
                                        $score = $subjectResultList->first();
                                        $caScore = $score ? ($score->first_ca + $score->second_ca + $score->third_ca) : 0;
                                        $examScore = $score ? $score->exam : 0;
                                        $totalScore = $score ? $score->total : 0;
                                        $subjectGrade = $score ? $score->grade : 'N/A';
                                        $remark = $score ? $score->remark : 'N/A';
                                    @endphp
                                    <tr>
                                        <td>{{ $index++ }}</td>
                                        <td>{{ $subjectName }}</td>
                                        <td class="text-center">{{ number_format($caScore, 1) }}</td>
                                        <td class="text-center">{{ number_format($examScore, 1) }}</td>
                                        <td class="text-center fw-bold">{{ number_format($totalScore, 1) }}</td>
                                        <td class="text-center fw-bold">{{ $subjectGrade }}</td>
                                        <td>{{ $remark }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 bg-light border-top">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="small text-muted">
                                <strong>Grade Details:</strong> F=0-50, D= 50-60, C = 60-70, B = 70-80, A = 80-100
                            </div>
                            <div class="small">
                                <strong>No. of Subjects:</strong> {{ $totalSubjects }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Character Traits & Extracurricular Activities -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="mb-3">Character Traits</h6>
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Rating</th>
                                        <th class="text-end">Rating</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td>PUNTUALITY</td><td class="text-end">5</td></tr>
                                    <tr><td>ATTENDANCE</td><td class="text-end">4</td></tr>
                                    <tr><td>NEATNESS</td><td class="text-end">5</td></tr>
                                    <tr><td>POLITENESS</td><td class="text-end">4</td></tr>
                                    <tr><td>ATTENTIVENESS</td><td class="text-end">5</td></tr>
                                    <tr><td>HONESTY</td><td class="text-end">4</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="mb-3">Extracurricular Activities</h6>
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Rating</th>
                                        <th class="text-end">Rating</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td>HANDWRITING</td><td class="text-end">5</td></tr>
                                    <tr><td>GAMES</td><td class="text-end">4</td></tr>
                                    <tr><td>SPORTS</td><td class="text-end">5</td></tr>
                                    <tr><td>DRAWING & PAINTING</td><td class="text-end">4</td></tr>
                                    <tr><td>CRAFTS</td><td class="text-end">5</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="mb-2">Class Teacher: <span class="fw-normal">Mr. Adam Umar</span></h6>
                        <p class="mb-2"><strong>Class Teacher Comment:</strong></p>
                        <p class="text-muted small">Lorem ipsum eget condimentum eget odio lactus purus vitae eget massa ut purus nisi placerat sem.</p>
                    </div>
                    <hr>
                    <div class="mb-4">
                        <h6 class="mb-2">Principal: <span class="fw-normal">Mr. Adam Umar</span></h6>
                        <p class="mb-2"><strong>Principal Comment:</strong></p>
                        <p class="text-muted small mb-2">Lorem ipsum eget condimentum eget odio lactus purus</p>
                        <p class="mb-0"><strong>Principal Signature:</strong> <span class="fst-italic">Lorem Ipsum</span></p>
                    </div>
                    <hr>
                    <div>
                        <p class="mb-0"><strong>Next Term Resumption Date:</strong> 03/12/2025</p>
                    </div>
                </div>
            </div>

            <!-- Download Button -->
            <div class="text-center mb-4">
                <a href="{{ route('parent.report-cards') }}" class="btn btn-dark btn-lg px-5">
                    <i class="ri-download-line me-2"></i>View Report Cards
                </a>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .table th {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .table td {
        font-size: 14px;
        vertical-align: middle;
    }
</style>
@endpush
@endsection
