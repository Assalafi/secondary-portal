@extends('layouts.admin')

@section('title', 'Timetable Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Timetable Details</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.index') }}" class="text-muted">Academic Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.timetables.index') }}" class="text-muted">Timetables</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Details</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3">Class Information</h6>
                    <p class="mb-1"><strong>Class:</strong> {{ $timetable->classArm->schoolClass->name ?? 'N/A' }} {{ $timetable->classArm->name ?? '' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3">Subject Information</h6>
                    <p class="mb-1"><strong>Subject:</strong> {{ $timetable->subject->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Teacher:</strong> {{ $timetable->teacher->name ?? '-' }}</p>
                </div>
            </div>

            <hr class="my-4">

            <div class="row">
                <div class="col-md-4">
                    <p class="mb-1"><strong>Day:</strong> {{ $timetable->day }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-1"><strong>Time:</strong> {{ $timetable->start_time->format('H:i') }} - {{ $timetable->end_time->format('H:i') }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-1"><strong>Room:</strong> {{ $timetable->room ?? '-' }}</p>
                </div>
            </div>

            <hr class="my-4">

            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Status:</strong>
                        <span class="badge bg-{{ $timetable->status === 'Active' ? 'success' : 'secondary' }}">
                            {{ $timetable->status }}
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Created:</strong> {{ $timetable->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.academic-management.timetables.index') }}" class="btn btn-secondary">Back to List</a>
                <a href="{{ route('admin.academic-management.timetables.edit', $timetable) }}" class="btn btn-primary">Edit</a>
            </div>
        </div>
    </div>
</div>
@endsection
