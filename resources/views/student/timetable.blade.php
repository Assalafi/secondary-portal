@extends('layouts.student')

@section('title', 'My Timetable')
@section('page-title', 'Class Timetable')

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item active text-muted">Timetable</li>
    </ol>
</nav>

@if(!$student || !$student->classArm)
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="ri-calendar-todo-line text-muted" style="font-size: 64px;"></i>
            <h5 class="mt-3 mb-2">No Timetable Available</h5>
            <p class="text-muted">Your class timetable will appear here once it is set up.</p>
        </div>
    </div>
@else
    <!-- Class Info -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-1 fw-bold">{{ $student->classArm->schoolClass->name ?? '' }} {{ $student->classArm->name ?? '' }}</h6>
                    <small class="text-muted">{{ $subjects->count() }} subjects registered</small>
                </div>
                <span class="badge bg-primary px-3 py-2">{{ $globalSettings['current_term'] }}</span>
            </div>
        </div>
    </div>

    <!-- Subjects List as Timetable -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="mb-0 fw-bold">
                <i class="ri-book-open-line me-2 text-primary"></i>Registered Subjects & Teachers
            </h6>
        </div>
        <div class="card-body p-0">
            @if($subjects->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:50px;">#</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th class="text-center">Code</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjects as $subject)
                                <tr>
                                    <td>
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:rgba(102,126,234,0.1);">
                                            <span class="text-primary fw-bold" style="font-size:12px;">{{ $loop->iteration }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ $subject->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width:28px;height:28px;background:rgba(40,167,69,0.1);">
                                                <i class="ri-user-line text-success" style="font-size:14px;"></i>
                                            </div>
                                            @php $teacher = $subject->pivot->teacher_id ? \App\Models\User::find($subject->pivot->teacher_id) : null; @endphp
                                            <span>{{ $teacher->name ?? 'TBA' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">{{ $subject->code ?? '-' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="ri-book-line text-muted" style="font-size: 48px;"></i>
                    <p class="mt-2 text-muted mb-0">No subjects assigned to your class yet.</p>
                </div>
            @endif
        </div>
    </div>
@endif
@endsection
