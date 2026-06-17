@extends('layouts.admin')

@section('title', 'Role & Permissions')

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
                            <li class="breadcrumb-item active">Role & Permissions</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Role & Permissions</h4>
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
                                                <i class="ri-user-star-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1">Role & Permissions</h5>
                                        <p class="card-title-desc mb-0">Configure user roles and access permissions</p>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                                    <i class="ri-add-line me-1"></i>Create New Role
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ROLE NAME</th>
                                            <th>USERS</th>
                                            <th>PERMISSIONS</th>
                                            <th>CREATED</th>
                                            <th>ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($roles as $role)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 avatar-xs me-3">
                                                            <div class="avatar-title bg-soft-{{ $role->name === 'Admin' ? 'danger' : ($role->name === 'Teacher' ? 'success' : 'info') }} text-{{ $role->name === 'Admin' ? 'danger' : ($role->name === 'Teacher' ? 'success' : 'info') }} rounded-circle">
                                                                @if($role->name === 'Admin')
                                                                    <i class="ri-admin-line"></i>
                                                                @elseif($role->name === 'Teacher')
                                                                    <i class="ri-user-star-line"></i>
                                                                @elseif($role->name === 'Student')
                                                                    <i class="ri-graduation-cap-line"></i>
                                                                @else
                                                                    <i class="ri-user-line"></i>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0">{{ $role->name }}</h6>
                                                            <small class="text-muted">{{ $role->description ?? 'No description' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary">{{ $role->users_count ?? 0 }} users</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @if($role->permissions && $role->permissions->count() > 0)
                                                            @foreach($role->permissions->take(3) as $permission)
                                                                <span class="badge bg-light text-muted">{{ $permission->name }}</span>
                                                            @endforeach
                                                            @if($role->permissions->count() > 3)
                                                                <span class="badge bg-light text-muted">+{{ $role->permissions->count() - 3 }} more</span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">No permissions assigned</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $role->created_at ? $role->created_at->format('M d, Y') : 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                                                            <i class="ri-more-line"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item view-permissions-btn" href="#" data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}"><i class="ri-eye-line me-2"></i>View Permissions</a></li>
                                                            <li><a class="dropdown-item edit-role-btn" href="#" data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}" data-role-description="{{ $role->description }}"><i class="ri-edit-line me-2"></i>Edit Role</a></li>
                                                            @if(!in_array($role->name, ['Admin', 'Teacher', 'Student']))
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li><a class="dropdown-item text-danger delete-role-btn" href="#" data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}"><i class="ri-delete-bin-line me-2"></i>Delete Role</a></li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="ri-user-star-line display-6"></i>
                                                        <p class="mt-2">No roles found</p>
                                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                                                            Create First Role
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
    </div>

    <!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRoleModalLabel">Create New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addRoleForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Role Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" placeholder="Enter role name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <input type="text" class="form-control" name="description" placeholder="Enter role description">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="border rounded p-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="fw-semibold mb-3">Dashboard & Analytics</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="view_dashboard" id="perm_dashboard">
                                        <label class="form-check-label" for="perm_dashboard">View Dashboard</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="view_analytics" id="perm_analytics">
                                        <label class="form-check-label" for="perm_analytics">View Analytics</label>
                                    </div>
                                    
                                    <h6 class="fw-semibold mb-3 mt-4">Student Management</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="view_students" id="perm_view_students">
                                        <label class="form-check-label" for="perm_view_students">View Students</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="create_students" id="perm_create_students">
                                        <label class="form-check-label" for="perm_create_students">Create Students</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="edit_students" id="perm_edit_students">
                                        <label class="form-check-label" for="perm_edit_students">Edit Students</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="delete_students" id="perm_delete_students">
                                        <label class="form-check-label" for="perm_delete_students">Delete Students</label>
                                    </div>
                                    
                                    <h6 class="fw-semibold mb-3 mt-4">Academic Management</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_classes" id="perm_classes">
                                        <label class="form-check-label" for="perm_classes">Manage Classes</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_subjects" id="perm_subjects">
                                        <label class="form-check-label" for="perm_subjects">Manage Subjects</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="fw-semibold mb-3">Staff Management</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="view_staff" id="perm_view_staff">
                                        <label class="form-check-label" for="perm_view_staff">View Staff</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="create_staff" id="perm_create_staff">
                                        <label class="form-check-label" for="perm_create_staff">Create Staff</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="edit_staff" id="perm_edit_staff">
                                        <label class="form-check-label" for="perm_edit_staff">Edit Staff</label>
                                    </div>
                                    
                                    <h6 class="fw-semibold mb-3 mt-4">Finance & Payments</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="view_payments" id="perm_payments">
                                        <label class="form-check-label" for="perm_payments">View Payments</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_fees" id="perm_fees">
                                        <label class="form-check-label" for="perm_fees">Manage Fees</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="generate_reports" id="perm_reports">
                                        <label class="form-check-label" for="perm_reports">Generate Reports</label>
                                    </div>
                                    
                                    <h6 class="fw-semibold mb-3 mt-4">System Settings</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_settings" id="perm_settings">
                                        <label class="form-check-label" for="perm_settings">Manage Settings</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="manage_users" id="perm_users">
                                        <label class="form-check-label" for="perm_users">Manage Users</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Permissions Modal -->
<div class="modal fade" id="viewPermissionsModal" tabindex="-1" aria-labelledby="viewPermissionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPermissionsModalLabel">Role Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" role="alert">
                    <i class="ri-information-line me-2"></i>
                    Permissions for <strong id="roleNameDisplay"></strong> role
                </div>
                <div id="permissionsList">
                    <!-- Permissions will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
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
                <h5 class="mb-3" id="successMessage">Operation completed successfully!</h5>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-soft-danger {
        background-color: rgba(248, 81, 81, 0.1) !important;
    }
    
    .text-danger {
        color: #f85151 !important;
    }
    
    .bg-soft-success {
        background-color: rgba(40, 167, 69, 0.1) !important;
    }
    
    .text-success {
        color: #28a745 !important;
    }
    
    .bg-soft-info {
        background-color: rgba(13, 202, 240, 0.1) !important;
    }
    
    .text-info {
        color: #0dcaf0 !important;
    }
    
    .avatar-xs {
        height: 2rem;
        width: 2rem;
    }
    
    .avatar-title {
        align-items: center;
        display: flex;
        font-weight: 500;
        height: 100%;
        justify-content: center;
        width: 100%;
        font-size: 0.875rem;
    }
    
    .form-check-input:checked {
        background-color: #5664d2;
        border-color: #5664d2;
    }
    
    .border {
        border-color: #e9ecef !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Create role form submission
    document.getElementById('addRoleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="ri-loader-4-line spinner-border spinner-border-sm me-1"></i>Creating...';
        submitBtn.disabled = true;
        
        fetch('{{ route("admin.settings.role-permissions.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('successMessage').textContent = 'Role created successfully!';
                new bootstrap.Modal(document.getElementById('successModal')).show();
                document.getElementById('addRoleModal').querySelector('.btn-close').click();
                this.reset();
                setTimeout(() => location.reload(), 1500);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // View permissions functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-permissions-btn') || e.target.closest('.view-permissions-btn')) {
            const btn = e.target.classList.contains('view-permissions-btn') ? e.target : e.target.closest('.view-permissions-btn');
            e.preventDefault();
            
            const roleName = btn.dataset.roleName;
            document.getElementById('roleNameDisplay').textContent = roleName;
            
            // Show modal
            new bootstrap.Modal(document.getElementById('viewPermissionsModal')).show();
            
            // Load permissions (you can implement this based on your needs)
            document.getElementById('permissionsList').innerHTML = `
                <div class="text-center py-3">
                    <i class="ri-loader-4-line spinner-border"></i>
                    <p class="mt-2">Loading permissions...</p>
                </div>
            `;
            
            // Simulate loading permissions
            setTimeout(() => {
                document.getElementById('permissionsList').innerHTML = `
                    <div class="row">
                        <div class="col-12">
                            <p class="text-muted">Permissions for this role will be displayed here.</p>
                        </div>
                    </div>
                `;
            }, 1000);
        }
    });
</script>
@endpush
