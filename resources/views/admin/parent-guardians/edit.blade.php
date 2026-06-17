@extends('layouts.admin')

@section('title', 'Edit Parent/Guardian')

@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        /* Ensure Select2 dropdown appears above other elements */
        .select2-container {
            z-index: 9999;
        }
        .select2-dropdown {
            z-index: 9999;
        }
        /* Make Select2 match Bootstrap form-select height */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.parent-guardians.overview') }}">Parent/Guardians</a>
                    </li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('admin.parent-guardians.show', $parentGuardian->id) }}">{{ $parentGuardian->name }}</a>
                    </li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 fw-bold mt-2">Edit Parent/Guardian</h1>
        </div>
        <a href="{{ route('admin.parent-guardians.show', $parentGuardian->id) }}" class="btn btn-outline-secondary">
            <i class="ri-arrow-left-line me-1"></i> Back to Profile
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.parent-guardians.update', $parentGuardian->id) }}" class="needs-validation"
        novalidate>
        @csrf
        @method('PUT')

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
                                    value="{{ old('name', $parentGuardian->name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $parentGuardian->email) }}" required>
                                <small class="text-muted">Used as login username</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input type="tel" name="phone" class="form-control"
                                    value="{{ old('phone', $parentGuardian->phone) }}" placeholder="+234...">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Occupation</label>
                                <input type="text" name="occupation" class="form-control"
                                    value="{{ old('occupation', $parentGuardian->occupation) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Address</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address', $parentGuardian->address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Password (Optional) -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold">
                            <i class="ri-key-line me-2 text-primary"></i>Change Password (Optional)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info small">
                            <i class="ri-information-line me-1"></i>
                            Leave blank to keep current password
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">New Password</label>
                                <input type="password" name="password" class="form-control" minlength="6">
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control" minlength="6">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Linked Students -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="ri-links-line me-2 text-primary"></i>Linked Students (Dependents)
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addStudentBtn">
                            <i class="ri-add-line me-1"></i> Add Student
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="studentsContainer">
                            @forelse($parentGuardian->dependents as $index => $dependent)
                                <div class="student-row border rounded p-3 mb-3" data-index="{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 fw-semibold">Student {{ $index + 1 }}</h6>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-student-btn">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="form-label small fw-semibold">Select Student</label>
                                            <select name="students[]" class="form-select student-select" required>
                                                <option value="">Choose student...</option>
                                                @foreach ($students as $student)
                                                    <option value="{{ $student->id }}"
                                                        {{ $student->id == $dependent->id ? 'selected' : '' }}>
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
                                                <option value="Father"
                                                    {{ old('relationships.' . $index, $dependent->pivot->relationship) === 'Father' ? 'selected' : '' }}>
                                                    Father</option>
                                                <option value="Mother"
                                                    {{ old('relationships.' . $index, $dependent->pivot->relationship) === 'Mother' ? 'selected' : '' }}>
                                                    Mother</option>
                                                <option value="Guardian"
                                                    {{ old('relationships.' . $index, $dependent->pivot->relationship) === 'Guardian' ? 'selected' : '' }}>
                                                    Guardian</option>
                                                <option value="Uncle"
                                                    {{ old('relationships.' . $index, $dependent->pivot->relationship) === 'Uncle' ? 'selected' : '' }}>
                                                    Uncle</option>
                                                <option value="Aunt"
                                                    {{ old('relationships.' . $index, $dependent->pivot->relationship) === 'Aunt' ? 'selected' : '' }}>
                                                    Aunt</option>
                                                <option value="Grandparent"
                                                    {{ old('relationships.' . $index, $dependent->pivot->relationship) === 'Grandparent' ? 'selected' : '' }}>
                                                    Grandparent</option>
                                                <option value="Other"
                                                    {{ old('relationships.' . $index, $dependent->pivot->relationship) === 'Other' ? 'selected' : '' }}>
                                                    Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <!-- Empty state will be handled by JS -->
                            @endforelse
                        </div>
                        <p class="text-muted small mb-0">
                            <i class="ri-information-line me-1"></i>
                            Manage student connections for this parent/guardian
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Account Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Account Information</h6>
                        <div class="mb-2">
                            <small class="text-muted">Created:</small><br>
                            <span>{{ $parentGuardian->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Last Login:</small><br>
                            <span>{{ $parentGuardian->last_login_at ? $parentGuardian->last_login_at->diffForHumans() : 'Never' }}</span>
                        </div>
                        <div>
                            <small class="text-muted">Status:</small><br>
                            @if ($parentGuardian->last_login_at && $parentGuardian->last_login_at >= now()->subDays(30))
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="ri-save-3-line me-1"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.parent-guardians.show', $parentGuardian->id) }}"
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
                    <select name="students[]" class="form-select student-select" required>
                        <option value="">Choose student...</option>
                        @foreach ($students as $student)
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
        <!-- jQuery (required for Select2) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        
        <script>
            // Initialize Select2 for student dropdowns
            function initSelect2() {
                // Destroy existing Select2 instances first
                $('.student-select').each(function() {
                    if ($(this).hasClass("select2-hidden-accessible")) {
                        $(this).select2('destroy');
                    }
                });
                
                // Initialize Select2
                $('.student-select').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Search for a student...',
                    allowClear: true,
                    width: '100%'
                });
            }

            let studentCount = {{ count($parentGuardian->dependents) }};

            // Wait for jQuery and DOM to be ready
            $(document).ready(function() {
                // Initialize Select2 on page load
                initSelect2();

                // Add student row
                document.getElementById('addStudentBtn').addEventListener('click', function() {
                    const template = document.getElementById('studentRowTemplate');
                    const clone = template.content.cloneNode(true);

                    studentCount++;
                    clone.querySelector('.student-number').textContent = studentCount;

                    // Add remove functionality
                    const removeBtn = clone.querySelector('.remove-student-btn');
                    removeBtn.addEventListener('click', function() {
                        const row = this.closest('.student-row');
                        // Destroy Select2 before removing
                        $(row).find('.student-select').select2('destroy');
                        row.remove();
                        updateStudentNumbers();
                    });

                    document.getElementById('studentsContainer').appendChild(clone);
                    
                    // Initialize Select2 on the newly added select
                    initSelect2();
                });

                // Remove existing student rows
                document.querySelectorAll('.remove-student-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const row = this.closest('.student-row');
                        // Destroy Select2 before removing
                        $(row).find('.student-select').select2('destroy');
                        row.remove();
                        updateStudentNumbers();
                    });
                });

                // Update student numbers
                function updateStudentNumbers() {
                    const rows = document.querySelectorAll('.student-row');
                    rows.forEach((row, index) => {
                        const numberSpan = row.querySelector('.student-number') || row.querySelector('h6');
                        if (numberSpan) {
                            if (numberSpan.tagName === 'SPAN') {
                                numberSpan.textContent = index + 1;
                            } else {
                                numberSpan.textContent = `Student ${index + 1}`;
                            }
                        }
                    });
                    studentCount = rows.length;
                }
            });
        </script>
    @endpush
@endsection
