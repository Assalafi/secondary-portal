@extends('layouts.admin')

@section('title', 'Staff Directory')

@section('content')
<div class="main-content-container overflow-hidden">
    <!-- Staff Directory Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <div>
                    <h3 class="fs-20 fw-semibold mb-1">Staff Directory</h3>
                    <p class="text-secondary mb-0">Manage school staff members and their information</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('admin.staff.enroll.step1') }}" class="btn btn-primary d-flex align-items-center gap-2 justify-content-center">
                <i class="ri-add-line"></i>
                Add New Staff
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card custom-shadow rounded-3 bg-white border mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.staff.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search by name, email, staff ID..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">All Genders</option>
                        <option value="Male" {{ request('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ request('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Department</label>
                    <select name="department" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-outline-primary d-block w-100">
                        <i class="ri-search-line"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Staff Table -->
    <div class="card custom-shadow rounded-3 bg-white border">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-semibold mb-0">Staff Members ({{ $staff->total() }})</h6>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary" onclick="selectAll()">Select All</button>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" id="bulkActionBtn" disabled>
                        Bulk Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('activate')">
                            <i class="ri-check-line me-2 text-success"></i>Activate</a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('deactivate')">
                            <i class="ri-close-line me-2 text-warning"></i>Deactivate</a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('suspend')">
                            <i class="ri-pause-line me-2 text-info"></i>Suspend</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="bulkAction('delete')">
                            <i class="ri-delete-bin-line me-2"></i>Delete</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($staff->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                                </th>
                                <th>ID</th>
                                <th>NAME</th>
                                <th>GENDER</th>
                                <th>ROLE</th>
                                <th>DEPARTMENT</th>
                                <th>ASSIGNED CLASS</th>
                                <th>STATUS</th>
                                <th width="100">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staff as $member)
                                @php
                                    $statusClass = match(strtolower($member->status)) {
                                        'active' => 'bg-success-subtle text-success',
                                        'inactive' => 'bg-secondary-subtle text-secondary',
                                        'suspended' => 'bg-warning-subtle text-warning',
                                        'terminated' => 'bg-danger-subtle text-danger',
                                        default => 'bg-secondary-subtle text-secondary'
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input staff-checkbox" value="{{ $member->id }}">
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary">{{ $member->staff_id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $photoUrl = $member->user->photo_path 
                                                    ? Storage::url($member->user->photo_path) 
                                                    : 'https://ui-avatars.com/api/?name='.urlencode($member->user->name).'&background=4f46e5&color=fff&size=32&rounded=true';
                                            @endphp
                                            <img src="{{ $photoUrl }}" alt="Staff" class="rounded-circle me-2" width="32" height="32">
                                            <div>
                                                <div class="fw-medium">{{ $member->user->name }}</div>
                                                <small class="text-muted">{{ $member->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $member->user->gender === 'Male' ? 'bg-info-subtle text-info' : 'bg-pink-subtle text-pink' }}">
                                            {{ $member->user->gender }}
                                        </span>
                                    </td>
                                    <td>{{ $member->user->role->name ?? '—' }}</td>
                                    <td>{{ $member->department ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">—</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $statusClass }}">{{ $member->status }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle border-0" type="button" data-bs-toggle="dropdown">
                                                <i class="ri-more-line"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('admin.staff.show', $member) }}">
                                                    <i class="ri-eye-line me-2"></i>View Profile</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.staff.edit', $member) }}">
                                                    <i class="ri-edit-line me-2"></i>Edit</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                @if($member->status === 'Active')
                                                    <li><a class="dropdown-item" href="#" onclick="singleAction({{ $member->id }}, 'deactivate')">
                                                        <i class="ri-pause-line me-2 text-warning"></i>Deactivate</a></li>
                                                @else
                                                    <li><a class="dropdown-item" href="#" onclick="singleAction({{ $member->id }}, 'activate')">
                                                        <i class="ri-play-line me-2 text-success"></i>Activate</a></li>
                                                @endif
                                                <li><a class="dropdown-item" href="#" onclick="singleAction({{ $member->id }}, 'suspend')">
                                                    <i class="ri-pause-circle-line me-2 text-info"></i>Suspend</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $member->id }}, '{{ $member->user->name }}')">
                                                    <i class="ri-delete-bin-line me-2"></i>Delete</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($staff->hasPages())
                    <div class="d-flex justify-content-between align-items-center p-3 border-top">
                        <div class="text-muted">
                            Showing {{ $staff->firstItem() }} to {{ $staff->lastItem() }} of {{ $staff->total() }} results
                        </div>
                        {{ $staff->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="ri-user-line display-4 text-muted"></i>
                    <h6 class="mt-3 mb-1">No Staff Found</h6>
                    <p class="text-muted mb-3">No staff members match your current filters.</p>
                    <a href="{{ route('admin.staff.enroll.step1') }}" class="btn btn-primary">
                        <i class="ri-add-line me-1"></i>Add First Staff Member
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="confirmationModalTitle">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmationModalMessage">Are you sure you want to perform this action?</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmActionBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="text-success mb-3">
                    <i class="ri-check-line display-4"></i>
                </div>
                <h5 id="successModalTitle">Action Successful!</h5>
                <p class="text-muted mb-3" id="successModalMessage">The action has been completed successfully.</p>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Success message handling
    @if(session('success'))
        showSuccessModal('Success!', {!! json_encode(session('success')) !!});
    @endif

    // Checkbox management
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const staffCheckboxes = document.querySelectorAll('.staff-checkbox');
        const bulkActionBtn = document.getElementById('bulkActionBtn');

        // Select all functionality
        selectAllCheckbox?.addEventListener('change', function() {
            staffCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBulkActionBtn();
        });

        // Individual checkbox handling
        staffCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const checkedCount = document.querySelectorAll('.staff-checkbox:checked').length;
                selectAllCheckbox.checked = checkedCount === staffCheckboxes.length;
                updateBulkActionBtn();
            });
        });

        function updateBulkActionBtn() {
            const checkedCount = document.querySelectorAll('.staff-checkbox:checked').length;
            bulkActionBtn.disabled = checkedCount === 0;
            bulkActionBtn.textContent = checkedCount > 0 ? `Bulk Actions (${checkedCount})` : 'Bulk Actions';
        }
    });

    function selectAll() {
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        selectAllCheckbox.click();
    }

    function bulkAction(action) {
        const checkedBoxes = document.querySelectorAll('.staff-checkbox:checked');
        const staffIds = Array.from(checkedBoxes).map(cb => cb.value);

        if (staffIds.length === 0) {
            alert('Please select at least one staff member.');
            return;
        }

        const actionMessages = {
            activate: 'activate the selected staff members',
            deactivate: 'deactivate the selected staff members',
            suspend: 'suspend the selected staff members',
            delete: 'permanently delete the selected staff members'
        };

        showConfirmationModal(
            `${action.charAt(0).toUpperCase() + action.slice(1)} Staff`,
            `Are you sure you want to ${actionMessages[action]}?`,
            () => performBulkAction(action, staffIds)
        );
    }

    function singleAction(staffId, action) {
        const actionMessages = {
            activate: 'activate this staff member',
            deactivate: 'deactivate this staff member',
            suspend: 'suspend this staff member'
        };

        showConfirmationModal(
            `${action.charAt(0).toUpperCase() + action.slice(1)} Staff`,
            `Are you sure you want to ${actionMessages[action]}?`,
            () => performBulkAction(action, [staffId])
        );
    }

    function confirmDelete(staffId, staffName) {
        showConfirmationModal(
            'Deactivate Staff?',
            `This action cannot be reversed. Are you sure you want to deactivate <strong>${staffName}</strong>?`,
            () => deleteStaff(staffId)
        );
    }

    function performBulkAction(action, staffIds) {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('action', action);
        staffIds.forEach(id => formData.append('staff_ids[]', id));

        fetch('{{ route("admin.staff.bulk-action") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessModal(`${action.charAt(0).toUpperCase() + action.slice(1)} Successful!`, data.message);
                setTimeout(() => window.location.reload(), 2000);
            } else {
                alert(data.message || 'Action failed. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }

    function deleteStaff(staffId) {
        fetch(`/admin/staff/${staffId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessModal('Staff deactivation is successful!', data.message);
                setTimeout(() => window.location.reload(), 2000);
            } else {
                alert(data.message || 'Delete failed. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }

    function showConfirmationModal(title, message, callback) {
        document.getElementById('confirmationModalTitle').textContent = title;
        document.getElementById('confirmationModalMessage').innerHTML = message;
        
        const confirmBtn = document.getElementById('confirmActionBtn');
        confirmBtn.onclick = function() {
            callback();
            bootstrap.Modal.getInstance(document.getElementById('confirmationModal')).hide();
        };
        
        new bootstrap.Modal(document.getElementById('confirmationModal')).show();
    }

    function showSuccessModal(title, message) {
        document.getElementById('successModalTitle').textContent = title;
        document.getElementById('successModalMessage').innerHTML = message;
        new bootstrap.Modal(document.getElementById('successModal')).show();
    }
</script>
@endpush

@push('styles')
<style>
    .bg-pink-subtle { background-color: #fdf2f8; }
    .text-pink { color: #ec4899; }
    .custom-shadow { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
    .table-hover tbody tr:hover { background-color: rgba(79, 70, 229, 0.05); }
</style>
@endpush
