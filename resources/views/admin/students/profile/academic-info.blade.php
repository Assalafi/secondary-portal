@extends('layouts.admin')

@section('title', 'Student Profile - Academic Info')

@section('content')
<div class="main-content-container overflow-hidden">
    <!-- Student Profile Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary me-3 d-flex align-items-center gap-2">
                    <i class="ri-arrow-left-line"></i>
                    Back to Students
                </a>
                <div>
                    <h3 class="fs-20 fw-semibold mb-1">Student Profile</h3>
                    <p class="text-secondary mb-0">{{ $student->full_name ?? '-' }} - {{ $student->admission_no ?? '-' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="d-flex gap-2 justify-content-md-end">
                <button class="btn btn-outline-primary d-flex align-items-center gap-2" onclick="exportAcademic()">
                    <i class="ri-download-line"></i>
                    Export Report
                </button>
                <button class="btn btn-primary d-flex align-items-center gap-2" onclick="printAcademic()">
                    <i class="ri-printer-line"></i>
                    Print Report
                </button>
            </div>
        </div>
    </div>
    
    <!-- Tab Navigation -->
    <div class="card custom-shadow rounded-3 bg-white border mb-4">
        <div class="card-body p-0">
            <ul class="nav nav-tabs border-0 px-4 pt-3" id="studentProfileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium" href="{{ route('admin.students.profile.overview', $student->id) }}">
                        <i class="ri-user-line me-2"></i>Overview
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link active fw-medium" href="{{ route('admin.students.profile.academic', $student->id) }}">
                        <i class="ri-graduation-cap-line me-2"></i>Academic Info
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium" href="{{ route('admin.students.profile.fees', $student->id) }}">
                        <i class="ri-money-dollar-circle-line me-2"></i>Fees & Payments
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium" href="{{ route('admin.students.profile.attendance', $student->id) }}">
                        <i class="ri-calendar-check-line me-2"></i>Attendance
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium" href="{{ route('admin.students.profile.documents', $student->id) }}">
                        <i class="ri-file-text-line me-2"></i>Documents
                    </a>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- Academic Content -->
    <div class="row g-4">
        <!-- Academic Status Cards -->
        <div class="col-lg-4">
            <div class="card custom-shadow rounded-3 bg-white border mb-4">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-semibold mb-0">
                        <i class="ri-graduation-cap-line me-2 text-primary"></i>Current Academic Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            @php
                                $cls = data_get($student, 'classArm.schoolClass.name');
                                $arm = data_get($student, 'classArm.name');
                                $className = trim(($cls ?: '-') . ' ' . ($arm ?: ''));
                                $currentTerm = null; // Not tracked; can be passed later
                            @endphp
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium text-secondary">Current Class:</span>
                                <span class="badge bg-primary-subtle text-primary">{{ $className }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium text-secondary">Academic Level:</span>
                                <span>{{ data_get($student, 'classArm.schoolClass.level', '—') }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium text-secondary">Shift:</span>
                                <span>—</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium text-secondary">Academic Year:</span>
                                <span>{{ data_get($student, 'academicSession.name', '—') }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium text-secondary">Current Term:</span>
                                <span class="badge bg-info-subtle text-info">{{ $student->academicSession->name ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium text-secondary">Class Teacher:</span>
                                <span>Mrs. Sarah Johnson</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Summary -->
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-semibold mb-0">
                        <i class="ri-trophy-line me-2 text-warning"></i>Performance Summary
                    </h6>
                </div>
                <div class="card-body">
                    @if($student->scores->isEmpty())
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">No data available</p>
                        </div>
                    @else
                        @php
                            $avgScore = $student->scores->avg('total');
                            $totalResults = $student->scores->count();
                            $gradeCounts = $student->scores->groupBy('grade')->map->count();
                            $bestGrade = $gradeCounts->sortKeysDesc()->keys()->first();
                        @endphp
                        <div class="text-center mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle mb-2" style="width: 60px; height: 60px;">
                                <span class="fw-bold fs-5">{{ number_format($avgScore, 1) }}</span>
                            </div>
                            <p class="mb-0 fw-medium">Average Score</p>
                            <small class="text-secondary">{{ $totalResults }} subjects</small>
                        </div>
                        <hr>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-medium text-secondary">Total Subjects:</span>
                                    <span>{{ $totalResults }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-medium text-secondary">Average Grade:</span>
                                    <span class="badge @if($bestGrade == 'A') bg-success @elseif($bestGrade == 'B') bg-primary @elseif($bestGrade == 'C') bg-warning @else bg-danger @endif">
                                        {{ $bestGrade ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-medium text-secondary">A Grades:</span>
                                    <span>{{ $gradeCounts->get('A', 0) }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-medium text-secondary">B Grades:</span>
                                    <span>{{ $gradeCounts->get('B', 0) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Subject Performance Table -->
        <div class="col-lg-8">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0">
                        <i class="ri-file-list-3-line me-2 text-primary"></i>Subject Performance
                    </h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="ri-filter-line me-1"></i>Filter Term
                        </button>
                        <ul class="dropdown-menu">
                            @foreach($terms as $term)
                                <li><a class="dropdown-item" href="#">{{ $term->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($student->scores->isEmpty())
                        <div class="text-center py-5">
                            <i class="ri-file-list-line text-muted" style="font-size: 48px;"></i>
                            <p class="text-muted mt-3">No scores recorded yet.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold">Subject</th>
                                        <th class="fw-semibold text-center">1st CA</th>
                                        <th class="fw-semibold text-center">2nd CA</th>
                                        <th class="fw-semibold text-center">3rd CA</th>
                                        <th class="fw-semibold text-center">Exam</th>
                                        <th class="fw-semibold text-center">Total</th>
                                        <th class="fw-semibold text-center">Grade</th>
                                        <th class="fw-semibold text-center">Term</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($student->scores as $score)
                                        <tr>
                                            <td class="fw-medium">{{ $score->scoreBatch->subject->name ?? 'N/A' }}</td>
                                            <td class="text-center">{{ number_format($score->first_ca ?? 0, 1) }}</td>
                                            <td class="text-center">{{ number_format($score->second_ca ?? 0, 1) }}</td>
                                            <td class="text-center">{{ number_format($score->third_ca ?? 0, 1) }}</td>
                                            <td class="text-center">{{ number_format($score->exam ?? 0, 1) }}</td>
                                            <td class="text-center fw-bold">{{ number_format($score->total ?? 0, 1) }}</td>
                                            <td class="text-center">
                                                <span class="badge @if($score->grade == 'A') bg-success @elseif($score->grade == 'B') bg-primary @elseif($score->grade == 'C') bg-warning @else bg-danger @endif">
                                                    {{ $score->grade ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ $score->scoreBatch->term->name ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Performance Chart -->
            <div class="card custom-shadow rounded-3 bg-white border mt-4">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-semibold mb-0">
                        <i class="ri-bar-chart-line me-2 text-primary"></i>Performance Trend by Term
                    </h6>
                </div>
                <div class="card-body">
                    @if($student->scores->isEmpty())
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">No data available</p>
                        </div>
                    @else
                        @php
                            $termAverages = $student->scores->groupBy(function($score) {
                                return $score->scoreBatch->term->name ?? 'Unknown';
                            })->map(function($scores) {
                                return $scores->avg('total');
                            });
                        @endphp
                        <div class="row g-3">
                            @foreach($terms as $term)
                                @php
                                    $avg = $termAverages->get($term->name, 0);
                                    $isHighest = $termAverages->max() == $avg && $avg > 0;
                                @endphp
                                <div class="col-md-4 text-center">
                                    <div class="border rounded p-3 @if($isHighest) bg-success-subtle @endif">
                                        <h5 class="@if($isHighest) text-success @else text-primary @endif mb-1">{{ number_format($avg, 1) }}%</h5>
                                        <p class="mb-0 text-secondary">{{ $term->name }} Average</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($termAverages->count() > 1)
                            @php
                                $firstTerm = $termAverages->first();
                                $lastTerm = $termAverages->last();
                                $trend = $lastTerm - $firstTerm;
                            @endphp
                            <div class="mt-3">
                                @if($trend > 0)
                                    <div class="d-flex align-items-center">
                                        <i class="ri-arrow-up-line text-success me-2"></i>
                                        <span class="text-success fw-medium">Improved by {{ number_format($trend, 1) }} points</span>
                                    </div>
                                @elseif($trend < 0)
                                    <div class="d-flex align-items-center">
                                        <i class="ri-arrow-down-line text-danger me-2"></i>
                                        <span class="text-danger fw-medium">Declined by {{ number_format(abs($trend), 1) }} points</span>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center">
                                        <i class="ri-subtract-line text-secondary me-2"></i>
                                        <span class="text-secondary fw-medium">Consistent performance</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        function printAcademic() {
            window.open('{{ route('admin.students.profile.academic.pdf', $student->id) }}', '_blank');
        }

        function exportAcademic() {
            const academicData = {
                student_name: "{{ $student->full_name ?? '' }}",
                admission_no: "{{ $student->admission_no ?? '' }}",
                class: "{{ trim((data_get($student, 'classArm.schoolClass.name') ?: '') . ' ' . (data_get($student, 'classArm.name') ?: '')) }}",
                academic_year: "{{ data_get($student, 'academicSession.name', '') }}",
                total_subjects: "{{ $student->scores->count() }}",
                average_score: "{{ $student->scores->isNotEmpty() ? number_format($student->scores->avg('total'), 1) : 'N/A' }}"
            };

            const scores = [];
            @foreach($student->scores as $score)
                scores.push({
                    subject: "{{ $score->scoreBatch->subject->name ?? 'N/A' }}",
                    first_ca: "{{ number_format($score->first_ca ?? 0, 1) }}",
                    second_ca: "{{ number_format($score->second_ca ?? 0, 1) }}",
                    third_ca: "{{ number_format($score->third_ca ?? 0, 1) }}",
                    exam: "{{ number_format($score->exam ?? 0, 1) }}",
                    total: "{{ number_format($score->total ?? 0, 1) }}",
                    grade: "{{ $score->grade ?? 'N/A' }}",
                    term: "{{ $score->scoreBatch->term->name ?? 'N/A' }}"
                });
            @endforeach

            let csvContent = '"FIELD","VALUE"\n';
            Object.entries(academicData).forEach(([key, value]) => {
                csvContent += `"${key.replace(/_/g, ' ').toUpperCase()}","${value}"\n`;
            });

            csvContent += '\n"SUBJECT","1ST CA","2ND CA","3RD CA","EXAM","TOTAL","GRADE","TERM"\n';
            scores.forEach(s => {
                csvContent += `"${s.subject}","${s.first_ca}","${s.second_ca}","${s.third_ca}","${s.exam}","${s.total}","${s.grade}","${s.term}"\n`;
            });

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `academic_${academicData.admission_no}_report.csv`;
            link.click();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Export Successful',
                    text: 'Academic report has been exported to CSV file.',
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                alert('Academic report exported successfully!');
            }
        }
    </script>
@endpush
