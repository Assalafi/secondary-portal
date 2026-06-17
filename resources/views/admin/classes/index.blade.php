@extends('layouts.admin')

@section('title', 'Classes Management')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800 fw-bold">Classes Management</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.classes-subjects.overview') }}">Classes &
                                Subjects</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Classes Management</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-body p-4">
                <form action="{{ route('admin.classes.index') }}" method="GET">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-dark action-btn" data-bs-toggle="modal"
                                data-bs-target="#createClassModal2">Create New Class</button>
                            <button type="button" class="btn btn-outline-secondary action-btn">Export List</button>
                        </div>
                        <div class="input-group filter-search-group">
                            <!-- Level Dropdown -->
                            <div class="btn-group">
                                <button class="btn btn-filter dropdown-toggle" type="button" id="levelDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">Level</button>
                                <ul class="dropdown-menu" aria-labelledby="levelDropdown">
                                    <li><a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['level' => null]) }}">All</a></li>
                                    @foreach ($levels as $level)
                                        <li><a class="dropdown-item"
                                                href="{{ request()->fullUrlWithQuery(['level' => $level]) }}">{{ $level }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <!-- Class Dropdown -->
                            <div class="btn-group">
                                <button class="btn btn-filter dropdown-toggle" type="button" id="classDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">Class</button>
                                <ul class="dropdown-menu" aria-labelledby="classDropdown">
                                    <li><a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['class' => null]) }}">All</a></li>
                                    @foreach ($classNames as $className)
                                        <li><a class="dropdown-item"
                                                href="{{ request()->fullUrlWithQuery(['class' => $className]) }}">{{ $className }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <!-- Arm Dropdown -->
                            <div class="btn-group">
                                <button class="btn btn-filter dropdown-toggle" type="button" id="armDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">Arm</button>
                                <ul class="dropdown-menu" aria-labelledby="armDropdown">
                                    <li><a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['arm' => null]) }}">All</a></li>
                                    @foreach ($arms as $arm)
                                        <li><a class="dropdown-item"
                                                href="{{ request()->fullUrlWithQuery(['arm' => $arm]) }}">{{ $arm }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <!-- Group Dropdown -->
                            <div class="btn-group">
                                <button class="btn btn-filter dropdown-toggle" type="button" id="groupDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">Group</button>
                                <ul class="dropdown-menu" aria-labelledby="groupDropdown">
                                    <li><a class="dropdown-item"
                                            href="{{ request()->fullUrlWithQuery(['group' => null]) }}">All</a></li>
                                    @foreach ($groups as $group)
                                        <li><a class="dropdown-item"
                                                href="{{ request()->fullUrlWithQuery(['group' => $group]) }}">{{ $group }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <!-- Search Bar -->
                            <span class="input-group-text search-icon"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control search-input" placeholder="Search..."
                                value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary d-none">Submit</button> <!-- Hidden submit -->
                        </div>
                    </div>
                </form>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>LEVEL</th>
                                <th>CLASS</th>
                                <th>ARM</th>
                                <th>GROUP</th>
                                <th>ASSIGNED TEACHER</th>
                                <th>STUDENTS</th>
                                <th class="text-center">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classArms as $arm)
                                <tr>
                                    <td>{{ $loop->iteration }}.</td>
                                    <td>{{ $arm->schoolClass->level }}</td>
                                    <td>{{ $arm->schoolClass->name }}</td>
                                    <td>{{ $arm->name }}</td>
                                    <td>{{ $arm->schoolClass->group ?? '-' }}</td>
                                    <td class="{{ $arm->classTeacher ? '' : 'not-assigned' }}">
                                        {{ $arm->classTeacher->name ?? 'Not Assigned' }}</td>
                                    <td>{{ $arm->students->count() }}</td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-secondary p-0" type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="ri-more-2-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item"
                                                        href="{{ route('admin.classes.show', ['class' => $arm->school_class_id, 'arm' => $arm->id]) }}">View</a>
                                                </li>
                                                <li><a class="dropdown-item"
                                                        href="{{ route('admin.classes.edit', $arm->school_class_id) }}">Edit</a>
                                                </li>
                                                <li>
                                                    <form
                                                        action="{{ route('admin.classes.destroy', $arm->school_class_id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete the entire class? This will delete all its arms.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="dropdown-item text-danger">Delete</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No classes found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .rounded-lg {
            border-radius: 0.75rem !important;
        }

        .action-btn {
            border-radius: 0.6rem !important;
        }

        .btn-dark {
            background-color: #212529 !important;
            border-color: #212529 !important;
            padding: 0.5rem 1rem;
        }

        .btn-outline-secondary.action-btn {
            background-color: #fff;
            border: 1px solid #e3e6f0;
            padding: 0.5rem 1rem;
        }

        .table thead th {
            background-color: #f8f9fc;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            border-bottom: 1px solid #e3e6f0;
            border-top: 0;
        }

        .filter-search-group {
            border: 1px solid #e3e6f0;
            border-radius: 0.6rem;
            overflow: hidden;
            background-color: #f8f9fc;
        }

        .filter-search-group .btn-filter,
        .filter-search-group .search-icon,
        .filter-search-group .search-input {
            border: none;
            background-color: transparent;
            box-shadow: none !important;
        }

        .filter-search-group .btn-filter {
            border-right: 1px solid #e3e6f0;
            border-radius: 0;
            color: #5a5c69;
            padding: 0.5rem 1rem;
        }

        .filter-search-group .search-icon {
            padding-left: 1rem;
        }

        .filter-search-group .search-input {
            padding-left: 0.5rem;
        }

        .not-assigned {
            color: #858796;
        }

        .modal-content {
            border-radius: 0.8rem;
        }

        .modal-header {
            border-bottom: none;
            padding: 1.5rem 1.5rem 0;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: none;
            padding: 0 1.5rem 1.5rem;
        }

        .form-select {
            background-color: #f8f9fc;
        }
    </style>
@endpush

@include('admin.classes._modals')
@include('admin.classes._create_class_modal')

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const createClassModalEl = document.getElementById('createClassModal2');
            if (!createClassModalEl) return; // Safety guard

            // Re-open modal on validation errors or flash error
            @if ($errors->any() || session('error'))
                const createClassModal = new bootstrap.Modal(createClassModalEl);
                createClassModal.show();
            @endif

            const levelSelect = createClassModalEl.querySelector('#level');
            const classNameSelect = createClassModalEl.querySelector('#class_name');
            const groupSelect = createClassModalEl.querySelector('#group');
            if (!levelSelect || !classNameSelect || !groupSelect) return; // Safety guard

            function updateClassDetails(level) {
                if (!level) {
                    classNameSelect.innerHTML = '<option value="" disabled selected>Choose class</option>';
                    groupSelect.innerHTML = '<option value="" disabled selected>Choose group</option>';
                    return;
                }

                const url = `{{ route('admin.classes.getDetails') }}?level=${encodeURIComponent(level)}`;
                // Show loading state
                classNameSelect.innerHTML = '<option value="" disabled selected>Loading classes...</option>';
                groupSelect.innerHTML = '<option value="" disabled selected>Loading groups...</option>';
                classNameSelect.disabled = true;
                groupSelect.disabled = true;

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok: ' + response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.debug('Class details fetched:', data);
                        // Populate Class Name dropdown
                        let classNameOptions = '<option value="" disabled selected>Choose class</option>';
                        if (data.classNames && data.classNames.length > 0) {
                            data.classNames.forEach(function (className) {
                                const isSelected = `{{ old('class_name') }}` === className ? 'selected' : '';
                                classNameOptions += `<option value="${className}" ${isSelected}>${className}</option>`;
                            });
                        }
                        classNameSelect.innerHTML = classNameOptions;
                        classNameSelect.disabled = false;

                        // Populate Group dropdown
                        let groupOptions = '<option value="" disabled selected>Choose group (optional)</option>';
                        if (data.groups && data.groups.length > 0) {
                            groupSelect.disabled = false;
                            data.groups.forEach(function (group) {
                                const isSelected = `{{ old('group') }}` === group ? 'selected' : '';
                                groupOptions += `<option value="${group}" ${isSelected}>${group}</option>`;
                            });
                        } else {
                            groupSelect.disabled = true;
                        }
                        groupSelect.innerHTML = groupOptions;
                    })
                    .catch(error => {
                        console.error('Error fetching class details:', error);
                        classNameSelect.innerHTML = '<option value="" disabled selected>Unable to load classes</option>';
                        groupSelect.innerHTML = '<option value="" disabled selected>Unable to load groups</option>';
                    });
            }

            levelSelect.addEventListener('change', function () {
                updateClassDetails(this.value);
            });

            // If there's an old level value (due to validation failure), trigger the change event to populate dependent dropdowns
            if ('{{ old('level') }}') {
                updateClassDetails('{{ old('level') }}');
            }
        });
    </script>
@endpush
