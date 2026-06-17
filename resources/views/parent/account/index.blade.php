@extends('layouts.parent')

@section('title', 'Account Settings')
@section('page-title', 'Account')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 px-4 pt-4 pb-0">
                <!-- Tabs -->
                <ul class="nav nav-tabs border-bottom-0" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-3 py-2 border-0" id="profile-tab" data-bs-toggle="tab" 
                                data-bs-target="#profileTab" type="button" role="tab">
                            Profile
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-3 py-2 border-0" id="password-tab" data-bs-toggle="tab" 
                                data-bs-target="#passwordTab" type="button" role="tab">
                            Change Password
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-3 py-2 border-0" id="notification-tab" data-bs-toggle="tab" 
                                data-bs-target="#notificationTab" type="button" role="tab">
                            Notification Preferences
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-3 py-2 border-0" id="dependent-tab" data-bs-toggle="tab" 
                                data-bs-target="#manageDependentTab" type="button" role="tab">
                            Manage Dependent
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="profileTab" role="tabpanel">
                        <!-- User Avatar and Basic Info -->
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" 
                                 style="width: 64px; height: 64px;">
                                <i class="ri-user-line text-white" style="font-size: 32px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">{{ $user->name }}</h5>
                                <p class="text-muted mb-0 small">Role: Parent/Guardian</p>
                                <p class="text-muted mb-0 small">Gender: {{ $user->gender ?? 'Male' }}</p>
                                <p class="text-muted mb-0 small">Dependent: {{ Auth::user()->dependents->count() }}</p>
                            </div>
                        </div>

                        <!-- Personal Information Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Personal Information</h6>
                                <button class="btn btn-sm btn-dark rounded-pill px-3" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#personalInfoCollapse">
                                    Edit
                                </button>
                            </div>
                            
                            <div class="collapse" id="personalInfoCollapse">
                                <form id="profileForm" class="mt-3">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Date of Birth</label>
                                            <input type="date" class="form-control" name="date_of_birth" 
                                                   value="{{ $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '' }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">State of Origin</label>
                                            <input type="text" class="form-control" name="state_of_origin" 
                                                   value="{{ $user->state_of_origin }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Email Id</label>
                                            <input type="email" class="form-control" name="email" 
                                                   value="{{ $user->email }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Gender</label>
                                            <select class="form-select" name="gender">
                                                <option value="Male" {{ $user->gender === 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ $user->gender === 'Female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Religion</label>
                                            <input type="text" class="form-control" name="religion">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Nationality</label>
                                            <input type="text" class="form-control" name="nationality" 
                                                   value="{{ $user->nationality }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Phone Number</label>
                                            <input type="tel" class="form-control" name="phone" 
                                                   value="{{ $user->phone }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Email Address</label>
                                            <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-dark px-4">Save Changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Display mode -->
                            <div id="personalInfoDisplay">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <p class="mb-1 small text-muted">Date of Birth</p>
                                        <p class="mb-0">{{ $user->date_of_birth ? $user->date_of_birth->format('d M, Y') : '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1 small text-muted">Email Id</p>
                                        <p class="mb-0">{{ $user->email }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1 small text-muted">State of Origin</p>
                                        <p class="mb-0">{{ $user->state_of_origin ?? 'Borno' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1 small text-muted">Nationality</p>
                                        <p class="mb-0">{{ $user->nationality ?? 'Nigerian' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1 small text-muted">Phone Number</p>
                                        <p class="mb-0">{{ $user->phone ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Professional Information</h6>
                                <span class="badge bg-light text-dark">Read only</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <p class="mb-1 small text-muted">Academic Session</p>
                                    <p class="mb-0">{{ $globalSettings['academic_session'] ?? '2024/2025' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1 small text-muted">Email Id</p>
                                    <p class="mb-0">{{ $user->email }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1 small text-muted">Designation</p>
                                    <p class="mb-0">Parent/Guardian</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1 small text-muted">Date Joined</p>
                                    <p class="mb-0">{{ $user->created_at->format('d M, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Emergency Contact</h6>
                                <button class="btn btn-sm btn-dark rounded-pill px-3" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#emergencyContactCollapse">
                                    Edit
                                </button>
                            </div>
                            
                            <div class="collapse" id="emergencyContactCollapse">
                                <form class="mt-3">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label small text-muted">Parent Name</label>
                                            <input type="text" class="form-control" value="{{ $user->name }}">
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-dark px-4">Save Changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <div id="emergencyContactDisplay">
                                <p class="mb-1 small text-muted">Parent Name</p>
                                <p class="mb-0">{{ $user->name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password Tab -->
                    <div class="tab-pane fade" id="passwordTab" role="tabpanel">
                        <h5 class="mb-1">Change Your Password</h5>
                        <p class="text-muted small mb-4">Update your password to keep your account secure</p>

                        <form id="passwordForm">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" class="form-control" name="current_password" 
                                           placeholder="Enter current password" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="new_password" 
                                           placeholder="Choose new password" required minlength="8">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" name="new_password_confirmation" 
                                           placeholder="Confirm new password" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-dark w-100 py-2">Save Changes</button>
                                </div>
                                <div class="col-12 text-center">
                                    <a href="#" class="text-primary text-decoration-none small">Forgot current password</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Notification Preferences Tab -->
                    <div class="tab-pane fade" id="notificationTab" role="tabpanel">
                        <h5 class="mb-1">Notification Preferences</h5>
                        <p class="text-muted small mb-4">Manage how you receive notifications</p>

                        <form id="notificationForm">
                            @csrf
                            <!-- In-app Notifications -->
                            <h6 class="mb-3">In-app Notifications</h6>
                            <div class="list-group list-group-flush mb-4">
                                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0">All Notifications</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="all_notifications" checked>
                                    </div>
                                </div>
                                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0">Academic Updates</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="academic_updates" checked>
                                    </div>
                                </div>
                                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0">Payment Reminders</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="payment_reminders" checked>
                                    </div>
                                </div>
                            </div>

                            <!-- Email Notifications -->
                            <h6 class="mb-3">Email Notifications</h6>
                            <div class="list-group list-group-flush mb-4">
                                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0">All Notifications</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="email_all_notifications">
                                    </div>
                                </div>
                                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0">Academic Updates</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="email_academic_updates">
                                    </div>
                                </div>
                                <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0">Payment Reminders</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="email_payment_reminders">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-dark px-4">Save Changes</button>
                        </form>
                    </div>

                    <!-- Manage Dependent Tab -->
                    <div class="tab-pane fade" id="manageDependentTab" role="tabpanel">
                        <h5 class="mb-4">Manage Dependents</h5>

                        @if(Auth::user()->dependents->count() > 0)
                            <div class="row g-3 mb-4">
                                @foreach(Auth::user()->dependents as $dependent)
                                    <div class="col-md-6">
                                        <div class="border rounded p-3">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                                     style="width: 48px; height: 48px;">
                                                    <i class="ri-user-line text-white" style="font-size: 24px;"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">{{ $dependent->user->name }}</h6>
                                                    <p class="mb-0 small text-muted">{{ $dependent->admission_number }}</p>
                                                </div>
                                            </div>
                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <p class="mb-0 small text-muted">Relationship</p>
                                                    <p class="mb-0 small">{{ $dependent->pivot->relationship ?? 'Son' }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="mb-0 small text-muted">Date Added</p>
                                                    <p class="mb-0 small">{{ $dependent->pivot->created_at ? $dependent->pivot->created_at->format('M d, Y') : 'Jun 1, 2024' }}</p>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('parent.dependents.show', $dependent->id) }}" 
                                                   class="btn btn-sm btn-outline-dark flex-grow-1">View Profile</a>
                                                <button class="btn btn-sm btn-danger" 
                                                        onclick="confirmRemoveDependent({{ $dependent->id }}, '{{ $dependent->user->name }}')">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="alert alert-warning">
                                <h6 class="alert-heading"><i class="ri-error-warning-line me-2"></i>Important Notes</h6>
                                <ul class="mb-0 small">
                                    <li>Removing a dependent will revoke your access to their academics records.</li>
                                    <li>Changes may take up to 24 hours to reflect across all systems</li>
                                </ul>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="ri-user-line" style="font-size: 64px; color: #ccc;"></i>
                                <p class="text-muted mt-3">No dependents found</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 2px solid transparent;
    }
    .nav-tabs .nav-link.active {
        color: #000;
        background: transparent;
        border-bottom: 2px solid #000;
        font-weight: 500;
    }
    .form-check-input:checked {
        background-color: #000;
        border-color: #000;
    }
    .btn-dark {
        background-color: #000;
        border-color: #000;
    }
    .btn-dark:hover {
        background-color: #333;
        border-color: #333;
    }
</style>
@endpush

<!-- Remove Dependent Confirmation Modal -->
<div class="modal fade" id="removeDependentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Remove Dependent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 64px; height: 64px;">
                        <i class="ri-error-warning-line text-danger" style="font-size: 32px;"></i>
                    </div>
                    <h6 class="mb-2">Are you sure you want to remove this dependent?</h6>
                    <p class="text-muted small mb-0" id="dependentNameDisplay"></p>
                </div>
                <div class="alert alert-warning small">
                    <strong>Warning:</strong> This action will revoke your access to their academic records and cannot be undone immediately.
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmRemoveBtn">Remove Dependent</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let dependentToRemove = null;

// Profile Form Submission
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm(this, '{{ route("parent.account.profile.update") }}', 'Profile updated successfully!');
});

// Password Form Submission
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm(this, '{{ route("parent.account.password.update") }}', 'Password updated successfully!');
});

// Notification Form Submission
document.getElementById('notificationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitForm(this, '{{ route("parent.account.notifications.update") }}', 'Notification preferences updated!');
});

function submitForm(form, url, successMessage) {
    const formData = new FormData(form);
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(successMessage);
            if (form.id === 'passwordForm') {
                form.reset();
            }
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Confirm Remove Dependent
function confirmRemoveDependent(dependentId, dependentName) {
    dependentToRemove = dependentId;
    document.getElementById('dependentNameDisplay').textContent = dependentName;
    const modal = new bootstrap.Modal(document.getElementById('removeDependentModal'));
    modal.show();
}

// Remove Dependent Action
document.getElementById('confirmRemoveBtn').addEventListener('click', function() {
    if (!dependentToRemove) return;
    
    const btn = this;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Removing...';
    
    fetch(`/parent/dependents/${dependentToRemove}/remove`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('removeDependentModal')).hide();
            
            // Show success message
            alert('Dependent removed successfully!');
            
            // Reload page to reflect changes
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to remove dependent'));
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});
</script>
@endpush
@endsection
