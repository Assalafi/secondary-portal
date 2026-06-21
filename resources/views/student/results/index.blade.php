@extends('layouts.student')

@section('title', 'My Results')
@section('page-title', 'My Results')

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item active text-muted">My Results</li>
    </ol>
</nav>

@if(!$student)
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="ri-error-warning-line text-warning" style="font-size: 64px;"></i>
            <h5 class="mt-3">Student profile not found</h5>
            <p class="text-muted">Please contact the administrator.</p>
        </div>
    </div>
@elseif($results->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="ri-file-list-line text-muted" style="font-size: 64px;"></i>
            <h5 class="mt-3 mb-2">No Results Yet</h5>
            <p class="text-muted">Your results will appear here once they are published.</p>
        </div>
    </div>
@else
    <!-- Student Info Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                @if(Auth::user()->photo_path)
                    <img src="{{ asset('storage/' . Auth::user()->photo_path) }}" alt="photo" class="rounded-circle me-3" style="width:50px;height:50px;object-fit:cover;">
                @else
                    <div class="rounded-circle me-3 d-flex align-items-center justify-content-center" style="width:50px;height:50px;background:linear-gradient(135deg,#667eea,#764ba2);">
                        <span class="text-white fw-bold">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                    </div>
                @endif
                <div>
                    <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                    <small class="text-muted">{{ $student->classArm->schoolClass->name ?? '' }} {{ $student->classArm->name ?? '' }} &bull; {{ $student->admission_no }}</small>
                </div>
            </div>
        </div>
    </div>

    @foreach($results as $groupLabel => $scores)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="ri-calendar-line me-2 text-primary"></i>{{ $groupLabel }}
                    </h6>
                    <span class="badge bg-primary">{{ $scores->count() }} Scores</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Subject</th>
                                <th class="text-center">1st CA</th>
                                <th class="text-center">2nd CA</th>
                                <th class="text-center">3rd CA</th>
                                <th class="text-center">Exam</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scores as $score)
                                @php
                                    $subjectName = optional(optional($score->scoreBatch)->subject)->name ?? 'N/A';
                                    $totalScore = $score->total ?? 0;
                                    $pct = min(round($totalScore), 100);
                                    $grade = $score->grade ?? ($pct >= 70 ? 'A' : ($pct >= 60 ? 'B' : ($pct >= 50 ? 'C' : ($pct >= 40 ? 'D' : 'F'))));
                                    $color = $pct >= 70 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
                                @endphp
                                <tr>
                                    <td>
                                        <i class="ri-book-line me-1 text-primary"></i>
                                        {{ $subjectName }}
                                    </td>
                                    <td class="text-center">{{ $score->first_ca ?? '-' }}</td>
                                    <td class="text-center">{{ $score->second_ca ?? '-' }}</td>
                                    <td class="text-center">{{ $score->third_ca ?? '-' }}</td>
                                    <td class="text-center">{{ $score->exam ?? '-' }}</td>
                                    <td class="text-center fw-bold">{{ $totalScore }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $color }}">{{ $grade }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            @php
                                $avgTotal = $scores->count() > 0 ? round($scores->avg('total'), 1) : 0;
                            @endphp
                            <tr class="fw-bold">
                                <td colspan="5">Overall Average</td>
                                <td class="text-center">{{ $avgTotal }}%</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
