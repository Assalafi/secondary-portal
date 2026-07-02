@extends('layouts.teacher')

@section('title', 'My Classes')
@section('page-title', 'My Classes')

@section('content')
<!-- Classes as Class Teacher -->
@if($classTeacherArms->count() > 0)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold"><i class="ri-user-star-line me-2 text-primary"></i>Class Teacher Assignments</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 px-4">#</th>
                        <th class="border-0">Class</th>
                        <th class="border-0">Level</th>
                        <th class="border-0">Students</th>
                        <th class="border-0 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classTeacherArms as $arm)
                        <tr>
                            <td class="px-4">{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-medium">{{ $arm->schoolClass->name ?? '' }} {{ $arm->name }}</span>
                            </td>
                            <td><span class="badge bg-info bg-opacity-10 text-info">{{ $arm->schoolClass->level ?? 'N/A' }}</span></td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $arm->students->count() }}</span></td>
                            <td class="text-center">
                                <a href="{{ route('teacher.attendance.mark', $arm->id) }}" class="btn btn-sm btn-outline-warning" title="Mark Attendance">
                                    <i class="ri-checkbox-multiple-line me-1"></i>Attendance
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Subject Teaching Assignments -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold"><i class="ri-book-open-line me-2 text-success"></i>Subject Teaching Assignments</h6>
    </div>
    <div class="card-body p-0">
        @if($subjectClasses->count() > 0)
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4">#</th>
                            <th class="border-0">Class</th>
                            <th class="border-0">Subjects</th>
                            <th class="border-0">Students</th>
                            <th class="border-0 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjectClasses as $classArmId => $subjects)
                            @php
                                $classArm = $subjects->first()->classArm;
                            @endphp
                            <tr>
                                <td class="px-4">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="fw-medium">{{ $classArm->schoolClass->level ?? '' }} {{ $classArm->schoolClass->name ?? '' }} {{ $classArm->name }}</span>
                                </td>
                                <td>
                                    @foreach($subjects as $cs)
                                        <span class="badge bg-success bg-opacity-10 text-success me-1 mb-1">{{ $cs->subject->name ?? 'N/A' }}</span>
                                    @endforeach
                                </td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $classArm->students->count() ?? 0 }}</span></td>
                                <td class="text-center">
                                    @foreach($subjects as $cs)
                                        <a href="{{ route('teacher.scores.upload', [$classArmId, $cs->subject_id]) }}" class="btn btn-sm btn-outline-primary mb-1" title="Upload scores for {{ $cs->subject->name }}">
                                            <i class="ri-file-list-3-line me-1"></i>{{ $cs->subject->name }}
                                        </a>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="ri-book-line text-muted" style="font-size: 48px;"></i>
                <p class="text-muted mt-2 mb-0">No subjects assigned yet</p>
            </div>
        @endif
    </div>
</div>
@endsection
