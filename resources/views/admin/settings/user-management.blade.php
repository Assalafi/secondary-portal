@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
    <div class="container-fluid">
        <!-- Page Title & Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
                            <li class="breadcrumb-item active">User Management</li>
                        </ol>
                    </div>
                    <h4 class="page-title">User Management</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm">
                                            <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                <i class="ri-user-settings-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1">User Management</h5>
                                        <p class="card-title-desc mb-0">Manage system users, roles, and permissions</p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                                        <i class="ri-user-add-line me-1"></i>Add Staff
                                    </button>
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                        <i class="ri-graduation-cap-line me-1"></i>Add Student
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <!-- User Tabs -->
                            <ul class="nav nav-pills nav-justified mb-4" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-bs-toggle="pill" href="#staff-tab" role="tab">
                                        <i class="ri-user-star-line d-block check-nav-icon mt-4 mb-2"></i>
                                        <p class="fw-normal mb-0">Staff</p>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="pill" href="#students-tab" role="tab">
                                        <i class="ri-graduation-cap-line d-block check-nav-icon mt-4 mb-2"></i>
                                        <p class="fw-normal mb-0">Students</p>
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content">
                                <!-- Staff Tab -->
                                <div class="tab-pane active" id="staff-tab" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>STAFF NAME</th>
                                                    <th>EMAIL</th>
                                                    <th>ROLE</th>
                                                    <th>DEPARTMENT</th>
                                                    <th>STATUS</th>
                                                    <th>ACTION</th>
                                                </tr>
                                            </thead>
                                            <tbody id="staffTableBody">
                                                @php
                                                    $staffUsers = $users->filter(function($user) {
                                                        return in_array($user->role->name ?? '', ['Admin', 'Teacher', 'Staff']);
                                                    });
                                                @endphp
                                                
                                                @forelse($staffUsers as $user)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 avatar-xs me-3">
                                                                    <div class="avatar-title bg-soft-success text-success rounded-circle">
                                                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                                                    <small class="text-muted">{{ $user->phone ?: 'No phone' }}</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>
                                                            <span class="badge bg-primary-subtle text-primary">{{ $user->role->name ?? 'No Role' }}</span>
                                                        </td>
                                                        <td>{{ $user->department ?? 'N/A' }}</td>
                                                        <td>
                                                            @if($user->is_active)
                                                                <span class="badge bg-success-subtle text-success">Active</span>
                                                            @else
                                                                <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                                                                    <i class="ri-more-line"></i>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li><a class="dropdown-item" href="#"><i class="ri-eye-line me-2"></i>View Details</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i class="ri-edit-line me-2"></i>Edit</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i class="ri-lock-line me-2"></i>Reset Password</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    @if($user->is_active)
                                                                        <li><a class="dropdown-item text-warning" href="#"><i class="ri-pause-line me-2"></i>Deactivate</a></li>
                                                                    @else
                                                                        <li><a class="dropdown-item text-success" href="#"><i class="ri-play-line me-2"></i>Activate</a></li>
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <i class="ri-user-line display-6"></i>
                                                                <p class="mt-2">No staff members found</p>
                                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                                                                    Add First Staff
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Students Tab -->
                                <div class="tab-pane" id="students-tab" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>STUDENT NAME</th>
                                                    <th>EMAIL</th>
                                                    <th>STUDENT ID</th>
                                                    <th>CLASS</th>
                                                    <th>STATUS</th>
                                                    <th>ACTION</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $studentUsers = $users->filter(function($user) {
                                                        return ($user->role->name ?? '') === 'Student';
                                                    });
                                                @endphp
                                                
                                                @forelse($studentUsers as $user)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0 avatar-xs me-3">
                                                                    <div class="avatar-title bg-soft-info text-info rounded-circle">
                                                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                                                    <small class="text-muted">{{ $user->phone ?: 'No phone' }}</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>
                                                            <span class="badge bg-info-subtle text-info">{{ $user->student_id ?? 'TBD' }}</span>
                                                        </td>
                                                        <td>{{ $user->class ?? 'N/A' }}</td>
                                                        <td>
                                                            @if($user->is_active)
                                                                <span class="badge bg-success-subtle text-success">Active</span>
                                                            @else
                                                                <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                                                                    <i class="ri-more-line"></i>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li><a class="dropdown-item" href="#"><i class="ri-eye-line me-2"></i>View Details</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i class="ri-edit-line me-2"></i>Edit</a></li>
                                                                    <li><a class="dropdown-item" href="#"><i class="ri-lock-line me-2"></i>Reset Password</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    @if($user->is_active)
                                                                        <li><a class="dropdown-item text-warning" href="#"><i class="ri-pause-line me-2"></i>Deactivate</a></li>
                                                                    @else
                                                                        <li><a class="dropdown-item text-success" href="#"><i class="ri-play-line me-2"></i>Activate</a></li>
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <i class="ri-graduation-cap-line display-6"></i>
                                                                <p class="mt-2">No students found</p>
                                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                                                    Add First Student
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="text-muted">
                                    Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
                                </div>
                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
            </div>
            <form id="addStaffForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select" name="role_id" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles->where('name', '!=', 'Student') as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ri-save-line me-1"></i>Add Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addStudentForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Student ID</label>
                                <input type="text" class="form-control" name="student_id">
                                <small class="text-muted">Leave empty for auto-generation</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Class <span class="text-danger">*</span></label>
                                <select class="form-select" name="class" required>
                                    <option value="">Select Class</option>
                                    <option value="1A">1A</option>
                                    <option value="1B">1B</option>
                                    <option value="2A">2A</option>
                                    <option value="2B">2B</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="ri-save-line me-1"></i>Add Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-success mb-4">
                    <i class="ri-check-double-line display-4"></i>
                </div>
                <h5 class="mb-3" id="successMessage">User created successfully!</h5>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
