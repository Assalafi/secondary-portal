@extends('layouts.admin')

@section('title', 'Attendance Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Attendance Management</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.index') }}" class="text-muted">Academic Management</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Attendance</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-4">
            <h5 class="mb-4 fw-semibold">Select Class to Mark Attendance</h5>
            
            <div class="row g-4">
                @foreach(['Nursery', 'Primary', 'JSS', 'SS'] as $level)
                <div class="col-12 col-lg-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">{{ $level }} Level</h6>
                            @php $levelClasses = \App\Models\ClassArm::whereHas('schoolClass', function($q) use ($level) { $q->where('level', $level); })->with('schoolClass')->get()->groupBy('schoolClass.name'); @endphp
                            @forelse($levelClasses as $className => $arms)
                                <div class="mb-3">
                                    <small class="text-muted fw-medium">{{ $className }}</small>
                                    @foreach($arms as $arm)
                                        <a href="{{ route('admin.academic-management.attendance.take', $arm->id) }}" class="d-block btn btn-soft text-start mt-1">
                                            {{ $className }} {{ $arm->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @empty
                                <p class="text-muted small mb-0">No classes available</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Quick Stats -->
                <div class="col-lg-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">Today's Summary</h6>
                            @php
                                $today = \Carbon\Carbon::today();
                                $totalPresent = \App\Models\Attendance::whereDate('date', $today)->where('status', 'Present')->count();
                                $totalAbsent = \App\Models\Attendance::whereDate('date', $today)->where('status', 'Absent')->count();
                            @endphp
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Present Today:</span>
                                <span class="fw-bold text-success">{{ $totalPresent }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Absent Today:</span>
                                <span class="fw-bold text-danger">{{ $totalAbsent }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Date:</span>
                                <span class="fw-bold">{{ $today->format('M d, Y') }}</span>
                            </div>
                        </div>
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
