@extends('layouts.admin')

@section('title', 'Test/Exam Schedule')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Test/Exam Schedule</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.index') }}" class="text-muted">Academic Management</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Test/Exam Schedule</li>
            </ol>
        </nav>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="scheduleTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="test-tab" data-bs-toggle="tab" data-bs-target="#test" type="button" role="tab">Test Schedule</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="exam-tab" data-bs-toggle="tab" data-bs-target="#exam" type="button" role="tab">Exam Schedule</button>
        </li>
    </ul>

    <div class="tab-content" id="scheduleTabsContent">
        <!-- Test Schedule Tab -->
        <div class="tab-pane fade show active" id="test" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-4">Test Schedules</h5>
                    
                    <div class="row g-4">
                        @foreach(['Nursery', 'Primary', 'JSS', 'SS'] as $level)
                        <div class="col-12 col-lg-6">
                            <h6 class="fw-semibold mb-3">{{ $level }} Level</h6>
                            @php 
                                $levelClasses = \App\Models\ClassArm::whereHas('schoolClass', function($q) use ($level) { 
                                    $q->where('level', $level); 
                                })->with('schoolClass')->get()->groupBy('schoolClass.name');
                                $testTypes = ['first_ca', 'second_ca', 'third_ca'];
                            @endphp
                            @forelse($levelClasses as $className => $arms)
                                <div class="mb-3">
                                    <small class="text-muted fw-medium">{{ $className }}</small>
                                    @foreach($arms as $arm)
                                        @php 
                                            $testSchedules = \App\Models\AssessmentSchedule::where('class_id', $arm->id)
                                                ->whereIn('assessment_type', $testTypes)
                                                ->with('subject')
                                                ->get();
                                        @endphp
                                        @if($testSchedules->count() > 0)
                                            @foreach($testSchedules as $schedule)
                                                <a href="{{ route('admin.academic-management.test-exam-schedule.class', $arm->id) }}" class="d-block btn btn-soft text-start mt-1">
                                                    {{ $className }} {{ $arm->name }} - {{ $schedule->subject->name ?? '' }} ({{ ucfirst(str_replace('_', ' ', $schedule->assessment_type)) }})
                                                </a>
                                            @endforeach
                                        @else
                                            <a href="{{ route('admin.academic-management.test-exam-schedule.class', $arm->id) }}" class="d-block btn btn-soft text-start mt-1">
                                                {{ $className }} {{ $arm->name }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @empty
                                <p class="text-muted small mb-0">No classes available</p>
                            @endforelse
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Exam Schedule Tab -->
        <div class="tab-pane fade" id="exam" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-4">Exam Schedules</h5>
                    
                    <div class="row g-4">
                        @foreach(['Nursery', 'Primary', 'JSS', 'SS'] as $level)
                        <div class="col-12 col-lg-6">
                            <h6 class="fw-semibold mb-3">{{ $level }} Level</h6>
                            @php 
                                $levelClasses = \App\Models\ClassArm::whereHas('schoolClass', function($q) use ($level) { 
                                    $q->where('level', $level); 
                                })->with('schoolClass')->get()->groupBy('schoolClass.name');
                            @endphp
                            @forelse($levelClasses as $className => $arms)
                                <div class="mb-3">
                                    <small class="text-muted fw-medium">{{ $className }}</small>
                                    @foreach($arms as $arm)
                                        @php 
                                            $examSchedules = \App\Models\AssessmentSchedule::where('class_id', $arm->id)
                                                ->where('assessment_type', 'exam')
                                                ->with('subject')
                                                ->get();
                                        @endphp
                                        @if($examSchedules->count() > 0)
                                            @foreach($examSchedules as $schedule)
                                                <a href="{{ route('admin.academic-management.test-exam-schedule.class', $arm->id) }}" class="d-block btn btn-soft text-start mt-1">
                                                    {{ $className }} {{ $arm->name }} - {{ $schedule->subject->name ?? '' }} (Exam)
                                                </a>
                                            @endforeach
                                        @else
                                            <a href="{{ route('admin.academic-management.test-exam-schedule.class', $arm->id) }}" class="d-block btn btn-soft text-start mt-1">
                                                {{ $className }} {{ $arm->name }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @empty
                                <p class="text-muted small mb-0">No classes available</p>
                            @endforelse
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.btn-soft {
    background: #fff;
    border: 1px solid #dee2e6;
    padding: 8px 12px;
    font-size: 14px;
    transition: all 0.2s;
}
.btn-soft:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
}
</style>
@endpush
@endsection
