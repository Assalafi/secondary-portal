@extends('layouts.admin')

@section('title', 'Add Parent/Guardian')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.parent-guardians.overview') }}">Parent/Guardians</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 fw-bold mt-2">Add Parent/Guardian</h1>
        </div>
        <a href="{{ route('admin.parent-guardians.index') }}" class="btn btn-outline-secondary">
            <i class="ri-arrow-left-line me-1"></i> Back to List
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.parent-guardians.store') }}" class="needs-validation" novalidate>
        @csrf

        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <!-- Personal Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold">
                            <i class="ri-user-3-line me-2 text-primary"></i>Personal Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" class="form-control" 
                                       value="{{ old('name') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" class="form-control" 
                                       value="{{ old('email') }}" required>
                                <small class="text-muted">Will be used as login username</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" 
                                       value="{{ old('phone') }}" placeholder="+234...">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Occupation</label>
                                <input type="text" name="occupation" class="form-control" 
                                       value="{{ old('occupation') }}" placeholder="e.g., Engineer, Teacher">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Address</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Portal Access -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold">
                            <i class="ri-key-line me-2 text-primary"></i>Portal Access
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password" class="form-control" 
                                       required minlength="6">
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Confirm Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password_confirmation" class="form-control" 
                                       required minlength="6">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Link Students -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="ri-links-line me-2 text-primary"></i>Link Students (Dependents)
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addStudentBtn">
                            <i class="ri-add-line me-1"></i> Add Student
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="studentsContainer">
                            <!-- Student rows will be added here -->
                        </div>
                        <p class="text-muted small mb-0">
                            <i class="ri-information-line me-1"></i>
                            Link this parent/guardian to their students/dependents
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Help Card -->
                <div class="card border-0 shadow-sm mb-4 bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="ri-lightbulb-line me-2 text-warning"></i>Quick Tips
                        </h6>
                        <ul class="small mb-0">
                            <li class="mb-2">Email address will be used for portal login</li>
                            <li class="mb-2">Link to student(s) to give access to their records</li>
                            <li class="mb-2">Parent can view payments, attendance, and reports</li>
                            <li class="mb-0">You can add more students later from edit page</li>
                        </ul>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="ri-save-3-line me-1"></i> Create Parent/Guardian
                        </button>
                        <a href="{{ route('admin.parent-guardians.index') }}" 
                           class="btn btn-outline-secondary w-100">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Student Row Template -->
    <template id="studentRowTemplate">
        <div class="student-row border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 fw-semibold">Student <span class="student-number"></span></h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-student-btn">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label small fw-semibold">Select Student</label>
                    <select name="students[]" class="form-select" required>
                        <option value="">Choose student...</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">
                                {{ $student->admission_no }} - {{ $student->full_name }} 
                                ({{ optional($student->classArm->schoolClass)->name ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Relationship</label>
                    <select name="relationships[]" class="form-select" required>
                        <option value="">Select...</option>
                        <option value="Father">Father</option>
                        <option value="Mother">Mother</option>
                        <option value="Guardian">Guardian</option>
                        <option value="Uncle">Uncle</option>
                        <option value="Aunt">Aunt</option>
                        <option value="Grandparent">Grandparent</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
        </div>
    </template>

    @push('scripts')
    <script>
        let studentCount = 0;

        // Add student row
        document.getElementById('addStudentBtn').addEventListener('click', function() {
            const template = document.getElementById('studentRowTemplate');
            const clone = template.content.cloneNode(true);
            
            studentCount++;
            clone.querySelector('.student-number').textContent = studentCount;
            
            // Add remove functionality
            const removeBtn = clone.querySelector('.remove-student-btn');
            removeBtn.addEventListener('click', function() {
                this.closest('.student-row').remove();
                updateStudentNumbers();
            });
            
            document.getElementById('studentsContainer').appendChild(clone);
        });

        // Update student numbers
        function updateStudentNumbers() {
            const rows = document.querySelectorAll('.student-row');
            rows.forEach((row, index) => {
                row.querySelector('.student-number').textContent = index + 1;
            });
            studentCount = rows.length;
        }

        // Add first student row by default
        document.getElementById('addStudentBtn').click();
    </script>
    @endpush
@endsection
