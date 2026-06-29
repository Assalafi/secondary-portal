@extends('layouts.admin')

@section('title', 'Subjects')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-1 text-gray-800 fw-bold">Subjects</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 breadcrumb-soft">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.classes-subjects.overview') }}">Classes & Subjects</a></li>
                <li class="breadcrumb-item active" aria-current="page">Subjects</li>
            </ol>
        </nav>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Actions Row -->
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-pill-dark" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                    <i class="ri-add-line me-1"></i> Add New Subject
                </button>
                <a href="{{ route('admin.subjects.import') }}" class="btn btn-success">
                    <i class="ri-file-excel-2-line me-1"></i> Import Excel
                </a>
                <button class="btn btn-soft">
                    <i class="ri-download-2-line me-1"></i> Export List
                </button>
            </div>

            <!-- Filters Row -->
            <form action="{{ route('admin.subjects.index') }}" method="GET" class="mb-3">
                <div class="row g-2">
                    <div class="col-auto">
                        <div class="btn-group">
                            <button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown">ID</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['code' => null]) }}">All</a></li>
                                @foreach($codes as $code)
                                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['code' => $code]) }}">{{ $code }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown">Subject Name</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['subject_name' => null]) }}">All</a></li>
                                @foreach($subjectNames as $name)
                                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['subject_name' => $name]) }}">{{ $name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown">Class</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['class' => null]) }}">All</a></li>
                                @foreach($classNames as $cn)
                                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['class' => $cn]) }}">{{ $cn }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown">Level</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['level' => null]) }}">All</a></li>
                                @foreach($levels as $lvl)
                                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['level' => $lvl]) }}">{{ $lvl }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown">Group</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['group' => null]) }}">All</a></li>
                                @foreach($groups as $grp)
                                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['group' => $grp]) }}">{{ $grp }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group search-soft">
                            <span class="input-group-text"><i class="ri-search-line"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                        </div>
                    </div>
                </div>
            </form>
            
            <!-- Subjects Table -->
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>SUBJECT</th>
                            <th>LEVEL</th>
                            <th>CLASS</th>
                            <th>GROUP</th>
                            <th>ASSIGNED TEACHER</th>
                            <th class="text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $index => $subject)
                            <tr>
                                <td>{{ $loop->iteration }}.</td>
                                <td>{{ $subject->code }}</td>
                                <td>{{ $subject->name }}</td>
                                <td>{{ $subject->level ?? '-' }}</td>
                                <td>{{ $subject->class_name ?? '-' }}</td>
                                <td>{{ $subject->group ?? '-' }}</td>
                                <td><span class="{{ $subject->teacher_name ? '' : 'not-assigned' }}">{{ $subject->teacher_name ?? 'Not Assigned' }}</span></td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-link text-secondary p-0" type="button" data-bs-toggle="dropdown">
                                            <i class="ri-more-2-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('admin.subjects.show', $subject->id) }}">View Details</a></li>
                                            <li>
                                                <a class="dropdown-item edit-subject-trigger" href="#"
                                                   data-bs-toggle="modal" data-bs-target="#editSubjectModal"
                                                   data-id="{{ $subject->id }}"
                                                   data-name="{{ $subject->name }}"
                                                   data-level="{{ $subject->level }}"
                                                   data-class-name="{{ $subject->class_name }}"
                                                   data-group="{{ $subject->group }}"
                                                   data-arm="{{ $subject->arm }}"
                                                   data-teacher-id="{{ $subject->teacher_id }}">
                                                    Edit
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteSubject({{ $subject->id }}, {{ json_encode($subject->name) }})">Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="ri-book-open-line fs-4 mb-2"></i>
                                    <p class="mb-0">No subjects found. <a href="#" data-bs-toggle="modal" data-bs-target="#addSubjectModal">Add your first subject</a></p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('admin.subjects._modals')

@push('styles')
<style>
    .breadcrumb-soft .breadcrumb-item + .breadcrumb-item::before { color: #9ca3af; }
    .breadcrumb-soft a { color: #6b7280; text-decoration: none; }
    .breadcrumb-soft .active { color: #9ca3af; }

    thead th { background-color: #f7f7f8; color: #6b7280; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid #ededed; border-top: 0; letter-spacing: .02em; }
    /* Soft filter pills */
    .btn-filter { background: #f7f7f8; border: 0; border-radius: 9999px; color: #111827; font-weight: 500; }
    .btn-filter:hover { background: #efefef; }
    /* Dark pill primary */
    .btn-pill-dark { background: #111827; color: #fff; border: 0; border-radius: 9999px; padding: .6rem 1.1rem; font-weight: 600; }
    .btn-pill-dark:hover { background: #0b1220; color: #fff; }
    /* Soft secondary */
    .btn-soft { background: #f7f7f8; color: #111827; border: 0; border-radius: 12px; padding: .6rem 1.1rem; font-weight: 600; }
    .btn-soft:hover { background: #efefef; }
    /* Soft search */
    .search-soft .input-group-text { background: #f7f7f8; border: 0; border-top-left-radius: 12px; border-bottom-left-radius: 12px; color: #6b7280; }
    .search-soft .form-control { background: #f7f7f8; border: 0; border-top-right-radius: 12px; border-bottom-right-radius: 12px; height: 44px; }
    .search-soft .form-control:focus { background: #fff; box-shadow: 0 0 0 .25rem rgba(17,24,39,.06); }
    .dropdown-menu { box-shadow: 0 8px 24px rgba(17,24,39,.08); border: 0; }
    .ri-more-2-fill { font-size: 20px; }
    .table > :not(caption) > * > * { padding: 1rem 1rem; }
    .table tbody tr + tr { border-top: 1px solid #f1f3f5; }
    .table tbody tr:hover { background: #fafafa; }
    .table td { color: #111827; }
    .btn-outline-secondary { border-color: #e5e7eb; }
    .fs-4 { font-size: 1.4rem; }
    .text-gray-800 { color: #1f2937; }
    .btn-link { text-decoration: none; }
    .not-assigned { color: #9ca3af; }
    .card.shadow-sm { border: 0; border-radius: 18px; }
</style>
@endpush

@push('scripts')
<script>
function deleteSubject(subjectId, subjectName) {
    if (confirm('Are you sure you want to delete ' + subjectName + '?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        const destroyTemplate = "{{ route('admin.subjects.destroy', ['subject' => '__ID__']) }}";
        form.action = destroyTemplate.replace('__ID__', subjectId);
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '{{ csrf_token() }}';
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
