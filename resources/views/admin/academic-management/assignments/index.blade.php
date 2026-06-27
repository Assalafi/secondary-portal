@extends('layouts.admin')

@section('title', 'Assignments')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Assignments</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.index') }}" class="text-muted">Academic Management</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Assignments</li>
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

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
                <h5 class="fw-semibold mb-0">All Assignments</h5>
                <a href="{{ route('admin.academic-management.assignments.create') }}" class="btn btn-primary btn-sm w-100 w-md-auto">Create New Assignment</a>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('admin.academic-management.assignments.index') }}" class="row g-3 mb-4">
                <div class="col-12 col-md-2">
                    <label class="form-label">Level</label>
                    <select name="level" class="form-select">
                        <option value="">All Levels</option>
                        <option value="Nursery" {{ request('level') === 'Nursery' ? 'selected' : '' }}>Nursery</option>
                        <option value="Primary" {{ request('level') === 'Primary' ? 'selected' : '' }}>Primary</option>
                        <option value="JSS" {{ request('level') === 'JSS' ? 'selected' : '' }}>JSS</option>
                        <option value="SS" {{ request('level') === 'SS' ? 'selected' : '' }}>SS</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Subject</label>
                    <select name="subject_id" class="form-select">
                        <option value="">All Subjects</option>
                        @foreach(\App\Models\Subject::all() as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Closed" {{ request('status') === 'Closed' ? 'selected' : '' }}>Closed</option>
                        <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Arm</label>
                    <select name="class_arm_id" class="form-select">
                        <option value="">All Arms</option>
                        @foreach(\App\Models\ClassArm::all() as $arm)
                            <option value="{{ $arm->id }}" {{ request('class_arm_id') == $arm->id ? 'selected' : '' }}>{{ $arm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.academic-management.assignments.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>

            @php
                $query = \App\Models\Assignment::with(['subject', 'class', 'classArm', 'teacher', 'createdBy']);

                if (request('level')) {
                    $query->where('level', request('level'));
                }
                if (request('subject_id')) {
                    $query->where('subject_id', request('subject_id'));
                }
                if (request('status')) {
                    $query->where('status', request('status'));
                }
                if (request('class_arm_id')) {
                    $query->where('class_arm_id', request('class_arm_id'));
                }

                $assignments = $query->latest('created_at')->paginate(20);
            @endphp

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Level</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                            <tr>
                                <td>{{ $loop->iteration + ($assignments->firstItem() - 1) }}</td>
                                <td>{{ $assignment->title }}</td>
                                <td>{{ $assignment->level }}</td>
                                <td>{{ $assignment->class->name ?? 'All Classes' }}</td>
                                <td>{{ $assignment->subject->name ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($assignment->status) {
                                            'Active' => 'bg-success',
                                            'Closed' => 'bg-danger',
                                            'Draft' => 'bg-secondary',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $assignment->status }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.academic-management.assignments.show', $assignment->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="{{ route('admin.academic-management.assignments.edit', $assignment->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No assignments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $assignments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
