@extends('layouts.admin')

@section('title', 'Role & Class Assignment')

@section('content')
<div class="main-content-container overflow-hidden">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.staff.overview') }}" class="btn btn-outline-secondary me-3 d-flex align-items-center gap-2">
                    <i class="ri-arrow-left-line"></i>
                    Back to Staff
                </a>
                <div>
                    <h3 class="fs-20 fw-semibold mb-1">Role & Class Assignment</h3>
                    <p class="text-secondary mb-0">Dashboard / Staff / Role & Class Assignment</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Filters and Search -->
            <div class="card custom-shadow rounded-3 bg-white border mb-4">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <select class="form-select" id="roleFilter">
                                <option value="">Role</option>
                                <option value="Principal">Principal</option>
                                <option value="Vice Principal">Vice Principal</option>
                                <option value="Class Teacher">Class Teacher</option>
                                <option value="Subject Teacher">Subject Teacher</option>
                                <option value="Admin Staff">Admin Staff</option>
                                <option value="Librarian">Librarian</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="departmentFilter">
                                <option value="">Department</option>
                                <option value="Academic">Academic</option>
                                <option value="Administration">Administration</option>
                                <option value="Finance">Finance</option>
                                <option value="ICT">ICT</option>
                                <option value="Library">Library</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">Status</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Suspended">Suspended</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search..." id="searchInput">
                                <span class="input-group-text">
                                    <i class="ri-search-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staff Assignment Table -->
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>NAME</th>
                                    <th>GENDER</th>
                                    <th>ROLE</th>
                                    <th>DEPARTMENT</th>
                                    <th>ASSIGNED CLASS</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody id="staffTableBody">
                                @forelse($staff as $member)
                                    <tr>
                                        <td>{{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $photoUrl = $member->user->photo_path 
                                                        ? Storage::url($member->user->photo_path) 
                                                        : 'https://ui-avatars.com/api/?name='.urlencode($member->user->name ?? 'Staff').'&background=4f46e5&color=fff&size=32&rounded=true';
                                                @endphp
                                                <img src="{{ $photoUrl }}" alt="Staff" class="rounded-circle me-2" width="32" height="32">
                                                <span>{{ $member->user->name ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $member->user->gender === 'Male' ? 'primary' : 'info' }}-subtle text-{{ $member->user->gender === 'Male' ? 'primary' : 'info' }}">
                                                {{ substr($member->user->gender ?? 'N/A', 0, 1) }}
                                            </span>
                                        </td>
                                        <td>{{ $member->user->role->name ?? '—' }}</td>
                                        <td>{{ strtoupper($member->department ?? 'N/A') }}</td>
                                        <td>
                                            @if($member->assignedClasses && $member->assignedClasses->count() > 0)
                                                @php
                                                    $classNames = [];
                                                    foreach($member->assignedClasses as $classArm) {
                                                        $schoolClass = $classArm->schoolClass ?? null;
                                                        if ($schoolClass) {
                                                            $classNames[] = $schoolClass->name . ' ' . $classArm->name;
                                                        } else {
                                                            $classNames[] = $classArm->name;
                                                        }
                                                    }
                                                    $displayClasses = array_slice($classNames, 0, 2);
                                                    $remainingCount = count($classNames) - 2;
                                                @endphp
                                                @foreach($displayClasses as $className)
                                                    <span class="badge bg-success-subtle text-success me-1">{{ $className }}</span>
                                                @endforeach
                                                @if($remainingCount > 0)
                                                    <span class="badge bg-secondary-subtle text-secondary">+{{ $remainingCount }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $member->status === 'Active' ? 'success' : 'secondary' }}-subtle text-{{ $member->status === 'Active' ? 'success' : 'secondary' }}">
                                                {{ $member->status ?? 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="openAssignmentModal({{ $member->id }}, '{{ $member->user->name }}', '{{ $member->user->role->name ?? '' }}', '{{ $member->department }}', {{ $member->user->role_id ?? 'null' }})">
                                                <i class="ri-settings-3-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ri-user-line display-6"></i>
                                                <p class="mt-2">No staff members found</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Actions Overview -->
            <div class="card custom-shadow rounded-3 bg-white border mb-4">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-semibold mb-0">Actions Overview</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Main Categories</small>
                        <div class="mt-2">
                            <div class="d-grid gap-2">
                                <button class="btn btn-sm btn-outline-primary text-start" onclick="filterByRole('all')">
                                    <i class="ri-user-line me-2"></i>All Staff
                                </button>
                                <button class="btn btn-sm btn-outline-success text-start" onclick="filterByRole('Class Teacher')">
                                    <i class="ri-user-star-line me-2"></i>Class Teachers
                                </button>
                                <button class="btn btn-sm btn-outline-info text-start" onclick="filterByRole('Subject Teacher')">
                                    <i class="ri-book-line me-2"></i>Subject Teachers
                                </button>
                                <button class="btn btn-sm btn-outline-warning text-start" onclick="filterByRole('Admin Staff')">
                                    <i class="ri-admin-line me-2"></i>Admin Staff
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Assign Role Section -->
                    <div class="mb-3">
                        <small class="text-muted">Assign Role</small>
                        <div class="mt-2 d-flex flex-wrap gap-1">
                            <span class="badge bg-primary-subtle text-primary role-badge" data-role="HR">HR</span>
                            <span class="badge bg-info-subtle text-info role-badge" data-role="Sales">Sales</span>
                            <span class="badge bg-success-subtle text-success role-badge" data-role="English">English</span>
                            <span class="badge bg-warning-subtle text-warning role-badge" data-role="Finance">Finance</span>
                            <span class="badge bg-danger-subtle text-danger role-badge" data-role="Biology">Biology</span>
                            <span class="badge bg-secondary-subtle text-secondary role-badge" data-role="Chemistry">Chemistry</span>
                            <span class="badge bg-dark-subtle text-dark role-badge" data-role="Computer">Computer</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-semibold mb-0">Assignment Stats</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="text-center p-2 bg-primary-subtle rounded">
                                <div class="fw-bold text-primary">{{ $stats['assigned'] ?? 0 }}</div>
                                <small class="text-muted">Assigned</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-warning-subtle rounded">
                                <div class="fw-bold text-warning">{{ $stats['unassigned'] ?? 0 }}</div>
                                <small class="text-muted">Unassigned</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-success-subtle rounded">
                                <div class="fw-bold text-success">{{ $stats['teachers'] ?? 0 }}</div>
                                <small class="text-muted">Teachers</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-info-subtle rounded">
                                <div class="fw-bold text-info">{{ $stats['admins'] ?? 0 }}</div>
                                <small class="text-muted">Admins</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assignment Modal -->
<div class="modal fade" id="assignmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Role/Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignmentForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Staff Info -->
                        <div class="col-12">
                            <div class="p-3 bg-light rounded">
                                <strong>Staff:</strong> <span id="modalStaffName"></span>
                            </div>
                        </div>

                        <!-- Role Assignment -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Role</label>
                            <select class="form-select" name="role_id" id="roleSelect">
                                <option value="">Select role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Department -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department</label>
                            <select class="form-select" name="department" id="departmentSelect">
                                <option value="">Select department</option>
                                <option value="Academic">Academic</option>
                                <option value="Administration">Administration</option>
                                <option value="Finance">Finance</option>
                                <option value="ICT">ICT</option>
                                <option value="Library">Library</option>
                                <option value="Security">Security</option>
                            </select>
                        </div>

                        <!-- Current Role Display -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Current Role</label>
                            <div class="p-2 bg-secondary-subtle rounded">
                                <span id="currentRole" class="text-muted">Current role</span>
                            </div>
                        </div>

                        <!-- Assigned Class -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Assigned Class</label>
                            <select class="form-select" name="assigned_class" id="assignedClassSelect">
                                <option value="">Choose class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Class Arm -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Class Arm</label>
                            <select class="form-select" name="class_arm[]" id="classArmSelect" multiple>
                                <option value="">Choose class arm</option>
                            </select>
                            <small class="text-muted">You can choose multiple options</small>
                        </div>

                        <!-- Assigned Subject -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Assigned Subject</label>
                            <select class="form-select" name="assigned_subject[]" id="assignedSubjectSelect" multiple>
                                <option value="">Choose subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentStaffId = null;

    // Open Assignment Modal
    function openAssignmentModal(staffId, staffName, currentRole, currentDept, currentRoleId) {
        currentStaffId = staffId;
        document.getElementById('modalStaffName').textContent = staffName;
        document.getElementById('currentRole').textContent = currentRole || 'No role assigned';
        
        // Reset form
        document.getElementById('assignmentForm').reset();
        
        // Set current values
        if (currentRoleId) {
            document.getElementById('roleSelect').value = currentRoleId;
        }
        if (currentDept) {
            document.getElementById('departmentSelect').value = currentDept;
        }
        
        // Show modal
        new bootstrap.Modal(document.getElementById('assignmentModal')).show();
    }

    // Handle class selection change to load class arms
    document.getElementById('assignedClassSelect').addEventListener('change', function() {
        const classId = this.value;
        const classArmSelect = document.getElementById('classArmSelect');
        
        // Clear previous options
        classArmSelect.innerHTML = '<option value="">Choose class arm</option>';
        
        if (classId) {
            // Fetch class arms for selected class
            fetch(`/admin/staff/get-class-arms/${classId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(arm => {
                        const option = document.createElement('option');
                        option.value = arm.id;
                        option.textContent = arm.name;
                        classArmSelect.appendChild(option);
                    });
                });
        }
    });

    // Handle form submission
    document.getElementById('assignmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!currentStaffId) return;
        
        const formData = new FormData(this);
        formData.append('staff_id', currentStaffId);
        
        fetch('/admin/staff/assign-role-class', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Response error:', text);
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                alert('Assignment updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Assignment failed'));
            }
        })
        .catch(error => {
            console.error('Full error:', error);
            alert('Error details: ' + error.message);
        });
    });

    // Filter functions
    function filterByRole(role) {
        const roleFilter = document.getElementById('roleFilter');
        roleFilter.value = role === 'all' ? '' : role;
        filterTable();
    }

    function filterTable() {
        // Implementation for filtering would go here
        // This would filter the table based on current filter values
    }

    // Role badge click handlers
    document.querySelectorAll('.role-badge').forEach(badge => {
        badge.addEventListener('click', function() {
            const role = this.dataset.role;
            filterByRole(role);
        });
    });
</script>
@endpush

@push('styles')
<style>
    .custom-shadow { 
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); 
    }
    .table-hover tbody tr:hover { 
        background-color: rgba(79, 70, 229, 0.05); 
    }
    .role-badge {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .role-badge:hover {
        transform: scale(1.05);
    }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
    .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
    .bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1) !important; }
    .bg-dark-subtle { background-color: rgba(33, 37, 41, 0.1) !important; }
</style>
@endpush
