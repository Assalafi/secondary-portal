@extends('layouts.admin')

@section('title', 'Create Timetable Entry')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Create Timetable Entry</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.index') }}" class="text-muted">Academic Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.academic-management.timetables.index') }}" class="text-muted">Timetables</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Create</li>
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

            <form action="{{ route('admin.academic-management.timetables.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="school_class_id" class="form-label">Class <span class="text-danger">*</span></label>
                        <select class="form-select @error('school_class_id') is-invalid @enderror" id="school_class_id" name="school_class_id" required>
                            <option value="">Select Class</option>
                            @foreach($schoolClasses as $schoolClass)
                                <option value="{{ $schoolClass->id }}">{{ $schoolClass->name }}</option>
                            @endforeach
                        </select>
                        @error('school_class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="class_arm_id" class="form-label">Class Arm <span class="text-danger">*</span></label>
                        <select class="form-select @error('class_arm_id') is-invalid @enderror" id="class_arm_id" name="class_arm_id" required disabled>
                            <option value="">Select Class First</option>
                        </select>
                        @error('class_arm_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Subjects <span class="text-danger">*</span></label>
                        <div id="subjects_container" class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            <p class="text-muted small mb-0">Select Class Arm First</p>
                        </div>
                        @error('subject_ids')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="teacher_id" class="form-label">Teacher</label>
                        <select class="form-select @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id">
                            <option value="">Select Teacher (Optional)</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Days <span class="text-danger">*</span></label>
                        <div class="border rounded p-3">
                            @foreach($days as $day)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="days[]" value="{{ $day }}" id="day_{{ $day }}" required>
                                    <label class="form-check-label" for="day_{{ $day }}">{{ $day }}</label>
                                </div>
                            @endforeach
                        </div>
                        @error('days')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" required>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" required>
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="room" class="form-label">Room</label>
                        <input type="text" class="form-control @error('room') is-invalid @enderror" id="room" name="room" placeholder="e.g., Room 101">
                        @error('room')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.academic-management.timetables.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Timetable Entries</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('school_class_id').addEventListener('change', function() {
    const schoolClassId = this.value;
    const classArmSelect = document.getElementById('class_arm_id');
    
    // Clear and disable class arm select
    classArmSelect.innerHTML = '<option value="">Select Class First</option>';
    classArmSelect.disabled = true;
    
    // Disable subject select
    document.getElementById('subject_ids').disabled = true;
    document.getElementById('subject_ids').innerHTML = '<option value="">Select Class Arm First</option>';
    
    if (schoolClassId) {
        // Fetch class arms for selected school class
        fetch(`{{ route('admin.academic-management.timetables.class-arms-by-class') }}?school_class_id=${schoolClassId}`)
            .then(response => response.json())
            .then(data => {
                classArmSelect.innerHTML = '<option value="">Select Class Arm</option>';
                data.classArms.forEach(arm => {
                    classArmSelect.innerHTML += `<option value="${arm.id}">${arm.name}</option>`;
                });
                classArmSelect.disabled = false;
            });
    }
});

document.getElementById('class_arm_id').addEventListener('change', function() {
    const classArmId = this.value;
    const subjectsContainer = document.getElementById('subjects_container');
    
    // Clear and disable subjects container
    subjectsContainer.innerHTML = '<p class="text-muted small mb-0">Select Class Arm First</p>';
    
    if (classArmId) {
        // Fetch subjects for selected class arm
        fetch(`{{ route('admin.academic-management.timetables.subjects-by-class-arm') }}?class_arm_id=${classArmId}`)
            .then(response => response.json())
            .then(data => {
                if (data.subjects.length === 0) {
                    subjectsContainer.innerHTML = '<p class="text-muted small mb-0">No subjects assigned to this class arm</p>';
                } else {
                    subjectsContainer.innerHTML = '';
                    data.subjects.forEach(subject => {
                        subjectsContainer.innerHTML += `
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="subject_ids[]" value="${subject.id}" id="subject_${subject.id}" required>
                                <label class="form-check-label" for="subject_${subject.id}">${subject.name} (${subject.code})</label>
                            </div>
                        `;
                    });
                }
            });
    }
});
</script>
@endsection
