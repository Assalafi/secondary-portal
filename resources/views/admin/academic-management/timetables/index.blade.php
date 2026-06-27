@extends('layouts.admin')

@section('title', 'Timetables')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Timetables</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.index') }}" class="text-muted">Academic Management</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Timetables</li>
            </ol>
        </nav>
    </div>

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

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
                <h5 class="fw-semibold mb-0">All Timetables</h5>
                <a href="{{ route('admin.academic-management.timetables.create') }}" class="btn btn-primary btn-sm w-100 w-md-auto">Create New Timetable Entry</a>
            </div>

            <!-- Filter by Class -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <select id="classFilter" class="form-select form-select-sm">
                        <option value="">All Classes</option>
                        @foreach($classArms as $classArm)
                            <option value="{{ $classArm->id }}">{{ $classArm->schoolClass->name ?? '' }} {{ $classArm->name ?? '' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Room</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($timetables as $timetable)
                            <tr class="timetable-row" data-class-arm="{{ $timetable->class_arm_id }}">
                                <td>
                                    {{ $timetable->classArm->schoolClass->name ?? 'N/A' }} {{ $timetable->classArm->name ?? '' }}
                                </td>
                                <td>{{ $timetable->subject->name ?? 'N/A' }}</td>
                                <td>{{ $timetable->teacher->name ?? '-' }}</td>
                                <td>{{ $timetable->day }}</td>
                                <td>{{ $timetable->start_time->format('H:i') }} - {{ $timetable->end_time->format('H:i') }}</td>
                                <td>{{ $timetable->room ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $timetable->status === 'Active' ? 'success' : 'secondary' }}">
                                        {{ $timetable->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.academic-management.timetables.show', $timetable) }}" class="btn btn-outline-secondary">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        <a href="{{ route('admin.academic-management.timetables.edit', $timetable) }}" class="btn btn-outline-primary">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <form action="{{ route('admin.academic-management.timetables.destroy', $timetable) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this timetable entry?')">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('classFilter').addEventListener('change', function() {
    const classArmId = this.value;
    const rows = document.querySelectorAll('.timetable-row');
    
    rows.forEach(row => {
        if (classArmId === '' || row.dataset.classArm === classArmId) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
@endsection
