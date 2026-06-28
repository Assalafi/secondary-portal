@extends('layouts.admin')

@section('title', 'Create Timetable Entry')

@push('styles')
<style>
    .timetable-row {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        border: 1px solid #e9ecef;
    }
    .timetable-row:hover {
        border-color: #dee2e6;
    }
    .remove-row-btn {
        cursor: pointer;
        color: #dc3545;
        transition: color 0.2s;
    }
    .remove-row-btn:hover {
        color: #a71d2a;
    }
</style>
@endpush

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

            <form action="{{ route('admin.academic-management.timetables.store') }}" method="POST" id="timetableForm">
                @csrf

                <!-- Class Selection -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Class Selection</h6>
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
                        </div>
                    </div>
                </div>

                <!-- Timetable Entries -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Timetable Entries</h6>

                        <div id="timetableRows">
                            <!-- Initial row -->
                            <div class="timetable-row" data-row="0">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small">Subject <span class="text-danger">*</span></label>
                                        <select class="form-select form-select-sm subject-select" name="entries[0][subject_id]" required disabled>
                                            <option value="">Select Class Arm First</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small">Day <span class="text-danger">*</span></label>
                                        <select class="form-select form-select-sm" name="entries[0][day]" required>
                                            @foreach($days as $day)
                                                <option value="{{ $day }}">{{ $day }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small">Lecturer</label>
                                        <select class="form-select form-select-sm" name="entries[0][teacher_id]">
                                            <option value="">None</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label small">Start Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control form-control-sm" name="entries[0][start_time]" required>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label small">End Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control form-control-sm" name="entries[0][end_time]" required>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small">Room</label>
                                        <input type="text" class="form-control form-control-sm" name="entries[0][room]" placeholder="Room">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="form-label small">&nbsp;</label>
                                        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-row-btn">
                                            <i class="ri-delete-bin-line"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Common Settings -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">Common Settings</h6>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addRowBtn">
                                <i class="ri-add-line me-1"></i>Add Entry
                            </button>
                        </div>
                        <div class="row">
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
let rowCount = 1;
let availableSubjects = [];

document.getElementById('school_class_id').addEventListener('change', function() {
    const schoolClassId = this.value;
    const classArmSelect = document.getElementById('class_arm_id');
    
    classArmSelect.innerHTML = '<option value="">Select Class First</option>';
    classArmSelect.disabled = true;
    
    // Disable all subject selects
    document.querySelectorAll('.subject-select').forEach(select => {
        select.innerHTML = '<option value="">Select Class Arm First</option>';
        select.disabled = true;
    });
    
    if (schoolClassId) {
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
    
    if (classArmId) {
        fetch(`{{ route('admin.academic-management.timetables.subjects-by-class-arm') }}?class_arm_id=${classArmId}`)
            .then(response => response.json())
            .then(data => {
                availableSubjects = data.subjects;
                
                // Update all subject selects
                document.querySelectorAll('.subject-select').forEach(select => {
                    select.innerHTML = '<option value="">Select Subject</option>';
                    data.subjects.forEach(subject => {
                        select.innerHTML += `<option value="${subject.id}">${subject.name} (${subject.code})</option>`;
                    });
                    select.disabled = false;
                });
            });
    } else {
        availableSubjects = [];
        document.querySelectorAll('.subject-select').forEach(select => {
            select.innerHTML = '<option value="">Select Class Arm First</option>';
            select.disabled = true;
        });
    }
});

document.getElementById('addRowBtn').addEventListener('click', function() {
    const container = document.getElementById('timetableRows');
    const newRow = document.createElement('div');
    newRow.className = 'timetable-row';
    newRow.setAttribute('data-row', rowCount);
    
    let subjectOptions = '<option value="">Select Subject</option>';
    if (availableSubjects.length > 0) {
        availableSubjects.forEach(subject => {
            subjectOptions += `<option value="${subject.id}">${subject.name} (${subject.code})</option>`;
        });
    } else {
        subjectOptions = '<option value="">Select Class Arm First</option>';
    }
    
    let dayOptions = '';
    @foreach($days as $day)
        dayOptions += `<option value="{{ $day }}">{{ $day }}</option>`;
    @endforeach
    
    let teacherOptions = '<option value="">None</option>';
    @foreach($teachers as $teacher)
        teacherOptions += `<option value="{{ $teacher->id }}">{{ $teacher->name }}</option>`;
    @endforeach
    
    newRow.innerHTML = `
        <div class="row">
            <div class="col-md-4 mb-2">
                <label class="form-label small">Subject <span class="text-danger">*</span></label>
                <select class="form-select form-select-sm subject-select" name="entries[${rowCount}][subject_id]" required ${availableSubjects.length === 0 ? 'disabled' : ''}>
                    ${subjectOptions}
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label small">Day <span class="text-danger">*</span></label>
                <select class="form-select form-select-sm" name="entries[${rowCount}][day]" required>
                    ${dayOptions}
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label small">Lecturer</label>
                <select class="form-select form-select-sm" name="entries[${rowCount}][teacher_id]">
                    ${teacherOptions}
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 mb-2">
                <label class="form-label small">Start Time <span class="text-danger">*</span></label>
                <input type="time" class="form-control form-control-sm" name="entries[${rowCount}][start_time]" required>
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label small">End Time <span class="text-danger">*</span></label>
                <input type="time" class="form-control form-control-sm" name="entries[${rowCount}][end_time]" required>
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label small">Room</label>
                <input type="text" class="form-control form-control-sm" name="entries[${rowCount}][room]" placeholder="Room">
            </div>
            <div class="col-md-2 mb-2">
                <label class="form-label small">&nbsp;</label>
                <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-row-btn">
                    <i class="ri-delete-bin-line"></i> Delete
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(newRow);
    rowCount++;
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-row-btn')) {
        const row = e.target.closest('.timetable-row');
        if (document.querySelectorAll('.timetable-row').length > 1) {
            row.remove();
        } else {
            alert('At least one entry is required');
        }
    }
});
</script>
@endsection
