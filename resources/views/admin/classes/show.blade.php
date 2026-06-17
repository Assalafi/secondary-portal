@extends('layouts.admin')

@section('title', '{{ $class->name }} {{ $classArm->name }}')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">{{ $class->name }} {{ $classArm->name }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.classes-subjects.overview') }}">Classes & Subjects</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">Classes Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $class->name }} {{ $classArm->name }}</li>
            </ol>
        </nav>
    </div>

    <!-- Overview Section -->
    <h4 class="mb-4">Overview</h4>
    
    <div class="row">
        <!-- Left Card -->
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="text-muted">Level:</span>
                            <p class="fw-bold">{{ $class->level }}</p>
                        </div>
                        <button class="btn btn-dark rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#editClassModal">Edit</button>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted">Class:</span>
                        <p class="fw-bold">{{ $class->name }} {{ $classArm->name }}</p>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted">Class Group:</span>
                        <p class="fw-bold">{{ $class->group ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-muted">Number of Students:</span>
                        <p class="fw-bold">{{ $classArm->students->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Card -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <span class="text-muted">Class Teacher:</span>
                            <p class="fw-bold">{{ $classArm->classTeacher->name ?? 'Not Assigned' }}</p>
                        </div>
                        <button class="btn btn-dark rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#assignTeacherModal">Update</button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted">Subjects:</span>
                            <p class="fw-bold">{{ $classArm->subjects->count() }}</p>
                        </div>
                        <button class="btn btn-dark rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addSubjectModal">Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Tabs -->
    <div class="border-bottom mb-4">
        <ul class="nav nav-tabs border-0" id="classDetailsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active border-0 border-bottom-2 border-dark text-dark fw-bold" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab">Students</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link border-0 text-muted" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button" role="tab">Subjects</button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="classDetailsTabContent">
        <!-- Students Tab Pane -->
        <div class="tab-pane fade show active" id="students" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-borderless">
                    <thead class="bg-light">
                        <tr>
                            <th class="fw-bold">#</th>
                            <th class="fw-bold">STUDENTS</th>
                            <th class="fw-bold">GENDER</th>
                            <th class="fw-bold">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classArm->students as $student)
                            <tr>
                                <td>{{ $loop->iteration }}.</td>
                                <td>{{ $student->fullName }}</td>
                                <td>{{ $student->gender }}</td>
                                <td>
                                    <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('admin.students.show', $student->id) }}">View Profile</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.students.edit', $student->id) }}">Edit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="removeStudent({{ $student->id }})">Remove</a></li>
                                    </ul>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <p class="mb-0">No students assigned to this class yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Subjects Tab Pane -->
        <div class="tab-pane fade" id="subjects" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-borderless">
                    <thead class="bg-light">
                        <tr>
                            <th class="fw-bold">#</th>
                            <th class="fw-bold">SUBJECTS</th>
                            <th class="fw-bold">TEACHER</th>
                            <th class="fw-bold">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classArm->subjects as $subject)
                            <tr>
                                <td>{{ $loop->iteration }}.</td>
                                <td>{{ $subject->name }}</td>
                                <td>
                                    @if($subject->pivot->teacher_id)
                                        {{ \App\Models\User::find($subject->pivot->teacher_id)->name ?? 'Not Assigned' }}
                                    @else
                                        Not Assigned
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('admin.subjects.show', $subject->id) }}">View Details</a></li>
                                        <li><a class="dropdown-item" href="#">Edit Assignment</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="removeSubject({{ $subject->id }})">Remove</a></li>
                                    </ul>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <p class="mb-0">No subjects assigned to this class yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function editClass() {
    // Redirect to edit class page
    window.location.href = "{{ route('admin.classes.edit', $class->id) }}";
}

function updateClass() {
    // Show update confirmation
    if (confirm('Are you sure you want to update this class?')) {
        // Here you can add AJAX call to update class or redirect to update form
        alert('Update functionality will be implemented');
    }
}

function addSubject() {
    // Show modal or redirect to add subject page
    alert('Add subject functionality will be implemented');
}

function removeStudent(studentId) {
    if (confirm('Are you sure you want to remove this student from the class?')) {
        // AJAX call to remove student
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/classes/{{ $class->id }}/students/' + studentId + '/remove';
        
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

function removeSubject(subjectId) {
    if (confirm('Are you sure you want to remove this subject from the class?')) {
        // AJAX call to remove subject
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('admin.class-arms.subjects.remove', ['class_arm' => $classArm->id, 'subject' => ':subjectId']) }}".replace(':subjectId', subjectId);
        
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

// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('#classDetailsTab button');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all tabs
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'border-bottom-2', 'border-dark', 'text-dark', 'fw-bold');
                btn.classList.add('text-muted');
            });
            
            // Add active class to clicked tab
            this.classList.add('active', 'border-bottom-2', 'border-dark', 'text-dark', 'fw-bold');
            this.classList.remove('text-muted');
        });
    });
});
</script>

<!-- Edit Class Modal -->
<div class="modal fade" id="editClassModal" tabindex="-1" aria-labelledby="editClassModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold" id="editClassModalLabel">Edit Class Details</h5>
                <button type="button" class="btn-close bg-light rounded-circle" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.classes.update', $class->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="level" class="form-label">Level</label>
                        <select class="form-select form-select-lg bg-light border-0" id="level" name="level" required>
                            <option value="JSS" {{ $class->level == 'JSS' ? 'selected' : '' }}>JSS</option>
                            <option value="SS" {{ $class->level == 'SS' ? 'selected' : '' }}>SS</option>
                            <option value="Primary" {{ $class->level == 'Primary' ? 'selected' : '' }}>Primary</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Class</label>
                        <select class="form-select form-select-lg bg-light border-0" id="name" name="name" required>
                            <option value="1" {{ $class->name == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ $class->name == '2' ? 'selected' : '' }}>2</option>
                            <option value="3" {{ $class->name == '3' ? 'selected' : '' }}>3</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="group" class="form-label">Group</label>
                        <select class="form-select form-select-lg bg-light border-0" id="group" name="group">
                            <option value="" {{ is_null($class->group) ? 'selected' : '' }}>None</option>
                            <option value="Science" {{ $class->group == 'Science' ? 'selected' : '' }}>Science</option>
                            <option value="Arts" {{ $class->group == 'Arts' ? 'selected' : '' }}>Arts</option>
                            <option value="Commercial" {{ $class->group == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="arm_name" class="form-label">Arm</label>
                        <select class="form-select form-select-lg bg-light border-0" id="arm_name" name="arm_name" required>
                            <option value="A" {{ $classArm->name == 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ $classArm->name == 'B' ? 'selected' : '' }}>B</option>
                            <option value="C" {{ $classArm->name == 'C' ? 'selected' : '' }}>C</option>
                            <option value="D" {{ $classArm->name == 'D' ? 'selected' : '' }}>D</option>
                            <option value="E" {{ $classArm->name == 'E' ? 'selected' : '' }}>E</option>
                            <option value="F" {{ $classArm->name == 'F' ? 'selected' : '' }}>F</option>
                        </select>
                    </div>
                    <input type="hidden" name="class_arm_id" value="{{ $classArm->id }}">
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

<!-- Assign Teacher Modal -->
<div class="modal fade" id="assignTeacherModal" tabindex="-1" aria-labelledby="assignTeacherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold" id="assignTeacherModalLabel">Assign Teacher to Class</h5>
                <button type="button" class="btn-close bg-light rounded-circle" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.class-arms.update-teacher', $classArm->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <p><span class="text-muted">Class:</span> <span class="fw-bold">{{ $class->name }} {{ $classArm->name }}</span></p>
                    <p><span class="text-muted">Current Teacher:</span> <span class="fw-bold">{{ $classArm->classTeacher->name ?? 'Not Assigned' }}</span></p>
                    <hr class="my-4">
                    <div class="mb-3">
                        <label for="teacher_id" class="form-label">Select Teacher</label>
                        <select class="form-select form-select-lg bg-light border-0" id="teacher_id" name="teacher_id" required style="background-color: #f9f9f9 !important;">
                            <option value="">Select Teacher</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ ($classArm->class_teacher_id == $teacher->id) ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold" id="addSubjectModalLabel">Add Subjects</h5>
                <button type="button" class="btn-close bg-light rounded-circle" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.class-arms.subjects.add', $classArm->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p><span class="text-muted">Class:</span> <span class="fw-bold">{{ $class->name }} {{ $classArm->name }}</span></p>
                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Subjects List</label>
                        <select class="form-select form-select-lg bg-light border-0" id="subject_id" name="subject_id" required style="background-color: #f9f9f9 !important;">
                            <option value="">Select Subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4">Add Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    #editClassModal .form-select.bg-light {
        background-color: #f9f9f9 !important;
    }
</style>
@endpush
