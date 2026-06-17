@extends('layouts.admin')

@section('title', 'Assign Teacher')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-1 text-gray-800 fw-bold">Assign Teacher</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.classes-subjects.overview') }}">Classes & Subjects</a></li>
                <li class="breadcrumb-item active" aria-current="page">Assign Teacher</li>
            </ol>
        </nav>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.teachers.assign') }}" method="GET" class="mb-3">
        <div class="input-group filter-search-group">
            <!-- Level Dropdown -->
            <div class="btn-group">
                <button class="btn btn-filter dropdown-toggle" type="button" id="levelDropdown" data-bs-toggle="dropdown" aria-expanded="false">Level</button>
                <ul class="dropdown-menu" aria-labelledby="levelDropdown">
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['level' => null]) }}">All</a></li>
                    @foreach ($levels as $level)
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['level' => $level]) }}">{{ $level }}</a></li>
                    @endforeach
                </ul>
            </div>
            <!-- Class Dropdown -->
            <div class="btn-group">
                <button class="btn btn-filter dropdown-toggle" type="button" id="classDropdown" data-bs-toggle="dropdown" aria-expanded="false">Class</button>
                <ul class="dropdown-menu" aria-labelledby="classDropdown">
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['class' => null]) }}">All</a></li>
                    @foreach ($classNames as $className)
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['class' => $className]) }}">{{ $className }}</a></li>
                    @endforeach
                </ul>
            </div>
            <!-- Arm Dropdown -->
            <div class="btn-group">
                <button class="btn btn-filter dropdown-toggle" type="button" id="armDropdown" data-bs-toggle="dropdown" aria-expanded="false">Arm</button>
                <ul class="dropdown-menu" aria-labelledby="armDropdown">
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['arm' => null]) }}">All</a></li>
                    @foreach ($arms as $arm)
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['arm' => $arm]) }}">{{ $arm }}</a></li>
                    @endforeach
                </ul>
            </div>
            <!-- Group Dropdown -->
            <div class="btn-group">
                <button class="btn btn-filter dropdown-toggle" type="button" id="groupDropdown" data-bs-toggle="dropdown" aria-expanded="false">Group</button>
                <ul class="dropdown-menu" aria-labelledby="groupDropdown">
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['group' => null]) }}">All</a></li>
                    @foreach ($groups as $group)
                        <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['group' => $group]) }}">{{ $group }}</a></li>
                    @endforeach
                </ul>
            </div>
            <!-- Search Bar -->
            <span class="input-group-text search-icon"><i class="ri-search-line"></i></span>
            <input type="text" name="search" class="form-control search-input" placeholder="Search..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary d-none">Submit</button>
        </div>
    </form>

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:64px;">#</th>
                            <th>CLASS NAME</th>
                            <th>ASSIGNED TEACHER</th>
                            <th class="text-center" style="width:120px;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($classArms as $arm)
                            <tr>
                                <td>{{ $loop->iteration }}.</td>
                                <td>{{ $arm->schoolClass->level }} {{ $arm->schoolClass->name }}{{ $arm->schoolClass->group ? ' ' . $arm->schoolClass->group : '' }} {{ $arm->name }}</td>
                                <td class="{{ $arm->classTeacher ? '' : 'not-assigned' }}">{{ $arm->classTeacher->name ?? 'Not Assigned' }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-link text-secondary p-0" type="button" data-bs-toggle="dropdown">
                                            <i class="ri-more-2-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @if ($arm->classTeacher)
                                                <li><a class="dropdown-item assign-action" href="#" data-class-arm-id="{{ $arm->id }}" data-class-label="{{ $arm->schoolClass->level }} {{ $arm->schoolClass->name }}{{ $arm->schoolClass->group ? ' ' . $arm->schoolClass->group : '' }} {{ $arm->name }}" data-teacher-id="{{ $arm->classTeacher->id }}">Update</a></li>
                                                <li>
                                                    <form action="{{ route('admin.teachers.assign.remove', $arm->id) }}" method="POST" onsubmit="return confirm('Remove assigned teacher?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">Remove</button>
                                                    </form>
                                                </li>
                                            @else
                                                <li><a class="dropdown-item assign-action" href="#" data-class-arm-id="{{ $arm->id }}" data-class-label="{{ $arm->schoolClass->level }} {{ $arm->schoolClass->name }}{{ $arm->schoolClass->group ? ' ' . $arm->schoolClass->group : '' }} {{ $arm->name }}">Assign</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No class arms found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Assign/Update Teacher Modal -->
<div class="modal fade" id="assignTeacherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.teachers.assign.store') }}" method="POST">
                @csrf
                <input type="hidden" name="class_arm_id" id="modal_class_arm_id" value="{{ old('class_arm_id') }}">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Teacher to Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Class</label>
                        <input type="text" class="form-control" id="modal_class_label" value="" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="modal_teacher_id" class="form-label">Select Teacher</label>
                        <select class="form-select @error('teacher_id') is-invalid @enderror" name="teacher_id" id="modal_teacher_id" required>
                            <option value="" disabled selected>Choose Teacher</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
    </div>

@push('styles')
<style>
    .filter-search-group { border: 1px solid #e3e6f0; border-radius: 0.6rem; overflow: hidden; background-color: #f8f9fc; }
    .filter-search-group .btn-filter, .filter-search-group .search-icon, .filter-search-group .search-input { border: none; background-color: transparent; box-shadow: none !important; }
    .filter-search-group .btn-filter { border-right: 1px solid #e3e6f0; border-radius: 0; color: #5a5c69; padding: 0.5rem 1rem; }
    .filter-search-group .search-icon { padding-left: 1rem; }
    .filter-search-group .search-input { padding-left: 0.5rem; }
    thead th { background-color: #f8f9fc; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid #e3e6f0; border-top: 0; }
    .not-assigned { color: #858796; }
    .modal-content { border-radius: 0.8rem; }
    .modal-header { border-bottom: none; padding: 1.25rem 1.25rem 0; }
    .modal-footer { border-top: none; padding: 0 1.25rem 1.25rem; }
    .form-select { background-color: #f8f9fc; }
    .btn-link { text-decoration: none; }
    .dropdown-menu { box-shadow: 0 8px 24px rgba(17, 24, 39, 0.08); border: 0; }
    .dropdown-item.text-danger { color: #dc3545 !important; }
    .dropdown-item.text-danger:hover { background-color: #fde7ea; }
    .table > :not(caption) > * > * { padding: 1rem 1rem; }
    .table tbody tr + tr { border-top: 1px solid #f1f3f5; }
    .table { margin-bottom: 0; }
    .card { border-radius: 0.8rem; }
    .rounded-lg { border-radius: 0.8rem !important; }
    .text-gray-800 { color: #1f2937; }
    .fs-5 { font-size: 1.1rem; }
    .ri-more-2-fill { font-size: 20px; }
    .ri-search-line { font-size: 18px; color: #6b7280; }
    .btn-light { background: #fff; border: 1px solid #e5e7eb; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Open modal with data
        document.querySelectorAll('.assign-action').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                const classArmId = this.dataset.classArmId;
                const classLabel = this.dataset.classLabel || '';
                const teacherId = this.dataset.teacherId || '';

                document.getElementById('modal_class_arm_id').value = classArmId;
                document.getElementById('modal_class_label').value = classLabel;
                const teacherSelect = document.getElementById('modal_teacher_id');
                if (teacherId) { teacherSelect.value = teacherId; } else { teacherSelect.value = ''; }

                const modal = new bootstrap.Modal(document.getElementById('assignTeacherModal'));
                modal.show();
            });
        });

        // Reopen modal if validation failed
        @if ($errors->any())
            const modal = new bootstrap.Modal(document.getElementById('assignTeacherModal'));
            modal.show();
        @endif
    });
</script>
@endpush
@endsection
