@extends('layouts.parent')

@section('title', 'Test & Exam Schedule')
@section('page-title', 'Test & Exam Schedule')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.dependents.index') }}" class="text-decoration-none">My Dependents</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.dependents.show', $student->id) }}" class="text-decoration-none">{{ $student->user->name }}</a></li>
                <li class="breadcrumb-item active text-muted">Test & Exam schedule</li>
            </ol>
        </nav>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="scheduleTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="test-tab" data-bs-toggle="tab" data-bs-target="#test" type="button" role="tab">
                    Test
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="exam-tab" data-bs-toggle="tab" data-bs-target="#exam" type="button" role="tab">
                    Exam
                </button>
            </li>
        </ul>

        <div class="tab-content" id="scheduleTabContent">
            <!-- Test Tab -->
            <div class="tab-pane fade show active" id="test" role="tabpanel">
                <!-- Class Assessment Tabs -->
                <div class="d-flex gap-2 mb-4 flex-wrap">
                    <button class="btn btn-sm btn-dark rounded-pill ca-filter-btn active" data-ca="1st">1st C.A</button>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill ca-filter-btn" data-ca="2nd">2nd C.A</button>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill ca-filter-btn" data-ca="3rd">3rd C.A</button>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill ca-filter-btn" data-ca="all">All</button>
                </div>

                @php
                    $testSchedule = $schedule->where('type', 'Test');
                @endphp

                @if($testSchedule->isEmpty())
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="ri-calendar-line" style="font-size: 48px; color: #ccc;"></i>
                            <h6 class="mt-3">No Test Schedule</h6>
                            <p class="text-muted small mb-0">There are no tests scheduled at this time.</p>
                        </div>
                    </div>
                @else
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>SUBJECTS</th>
                                            <th>DATE</th>
                                            <th>TIME</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($testSchedule as $index => $test)
                                            @php
                                                // Determine C.A based on title or default to 1st
                                                $caType = '1st';
                                                if (str_contains(strtolower($test->title), '2nd')) {
                                                    $caType = '2nd';
                                                } elseif (str_contains(strtolower($test->title), '3rd')) {
                                                    $caType = '3rd';
                                                }
                                            @endphp
                                            <tr class="test-row" data-ca="{{ $caType }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $test->subject->name ?? 'Subject' }}</td>
                                                <td>{{ $test->assessment_date ? \Carbon\Carbon::parse($test->assessment_date)->format('n/d/Y') : 'TBA' }}</td>
                                                <td>{{ $test->assessment_date ? \Carbon\Carbon::parse($test->assessment_date)->format('h:i a') : '08:00 am' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- View Results Link -->
                <div class="mt-4">
                    <a href="{{ route('parent.dependents.results', $student->id) }}" class="btn btn-outline-dark d-flex align-items-center justify-content-center gap-2">
                        <i class="ri-bar-chart-line"></i>
                        <span>View Results & Grades</span>
                        <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            </div>

            <!-- Exam Tab -->
            <div class="tab-pane fade" id="exam" role="tabpanel">
                @php
                    $examSchedule = $schedule->where('type', 'Exam');
                @endphp

                @if($examSchedule->isEmpty())
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="ri-calendar-line" style="font-size: 48px; color: #ccc;"></i>
                            <h6 class="mt-3">No Exam Schedule</h6>
                            <p class="text-muted small mb-0">There are no exams scheduled at this time.</p>
                        </div>
                    </div>
                @else
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>SUBJECTS</th>
                                            <th>DATE</th>
                                            <th>TIME</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($examSchedule as $index => $exam)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $exam->subject->name ?? 'Subject' }}</td>
                                                <td>{{ $exam->assessment_date ? \Carbon\Carbon::parse($exam->assessment_date)->format('n/d/Y') : 'TBA' }}</td>
                                                <td>{{ $exam->assessment_date ? \Carbon\Carbon::parse($exam->assessment_date)->format('h:i a') : '08:00 am' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- View Results Link -->
                <div class="mt-4">
                    <a href="{{ route('parent.dependents.results', $student->id) }}" class="btn btn-outline-dark d-flex align-items-center justify-content-center gap-2">
                        <i class="ri-bar-chart-line"></i>
                        <span>View Results & Grades</span>
                        <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 2px solid transparent;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        color: #000;
        background: transparent;
        border-bottom: 2px solid #000;
    }
    .table th {
        font-size: 13px;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table td {
        font-size: 14px;
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script>
    // C.A Filter for Tests only
    document.querySelectorAll('.ca-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const selectedCA = this.getAttribute('data-ca');
            
            // Update button styles
            document.querySelectorAll('.ca-filter-btn').forEach(b => {
                b.classList.remove('btn-dark', 'active');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-dark', 'active');
            
            // Filter test rows
            document.querySelectorAll('.test-row').forEach(row => {
                const rowCA = row.getAttribute('data-ca');
                if (selectedCA === 'all' || rowCA === selectedCA) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update row numbers
            let visibleIndex = 1;
            document.querySelectorAll('.test-row').forEach(row => {
                if (row.style.display !== 'none') {
                    row.querySelector('td:first-child').textContent = visibleIndex++;
                }
            });
        });
    });
</script>
@endpush
@endsection
