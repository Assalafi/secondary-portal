@extends('layouts.parent')

@section('title', 'Account Settings')
@section('page-title', 'Account')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="stat-card">
            <!-- Tabs -->
            <ul class="nav nav-tabs border-0 mb-0">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profileTab">
                        Profile
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#passwordTab">
                        Change Password
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#notificationTab">
                        Notification Preferences
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#manageDependentTab">
                        Manage Dependent
                    </button>
                </li>
            </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Profile Tab -->
            <div class="tab-pane fade show active" id="profileTab">
                <div class="stat-card">
                    <h5 class="mb-1">Account Information</h5>
                    <p class="text-muted small mb-4">Update your personal information</p>

                    <form id="profileForm">
                        @csrf
                        <div class="row g-3">
                            <!-- Personal Information -->
                            <div class="col-12">
                                <h6 class="mb-3">Personal Information</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" name="date_of_birth" 
                                       value="{{ $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '' }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select class="form-select" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ $user->gender === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ $user->gender === 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nationality</label>
                                <input type="text" class="form-control" name="nationality" value="{{ $user->nationality }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">State of Origin</label>
                                <input type="text" class="form-control" name="state_of_origin" value="{{ $user->state_of_origin }}">
                            </div>

                            <!-- Contact Information -->
                            <div class="col-12 mt-4">
                                <h6 class="mb-3">Contact Information</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone" value="{{ $user->phone }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="3">{{ $user->address }}</textarea>
                            </div>

                            <!-- Parent/Guardian Information -->
                            <div class="col-12 mt-4">
                                <h6 class="mb-3">Parent/Guardian Information</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Relationship</label>
                                <input type="text" class="form-control" value="Parent/Guardian" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" value="{{ $user->phone }}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Occupation</label>
                                <input type="text" class="form-control" name="occupation" placeholder="Enter occupation">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Guardian Status</label>
                                <input type="text" class="form-control" value="Active" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Dependents</label>
                                <input type="text" class="form-control" value="{{ Auth::user()->dependents->count() }} Student(s)" readonly>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-12 mt-4">
                                <button type="button" class="btn btn-outline-dark me-2" onclick="resetForm()">
                                    <i class="ri-refresh-line me-2"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <span class="submit-text"><i class="ri-save-line me-2"></i>Save Changes</span>
                                    <span class="submit-loading d-none">
                                        <span class="spinner-border spinner-border-sm me-2"></span>Saving...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Tab -->
            <div class="tab-pane fade" id="passwordTab">
                <div class="stat-card">
                    <h5 class="mb-1">Change Your Password</h5>
                    <p class="text-muted small mb-4">Update your password to keep your account secure</p>

                    <form id="passwordForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" name="current_password" 
                                       placeholder="Enter your current password" required>
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

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <span class="submit-text"><i class="ri-lock-line me-2"></i>Save Changes</span>
                                    <span class="submit-loading d-none">
                                        <span class="spinner-border spinner-border-sm me-2"></span>Updating...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="alert alert-info mt-4">
                        <h6 class="alert-heading">Password Requirements:</h6>
                        <ul class="mb-0 small">
                            <li>Minimum 8 characters</li>
                            <li>At least one uppercase letter</li>
                            <li>At least one number</li>
                            <li>At least one special character</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Notification Preferences Tab -->
            <div class="tab-pane fade" id="notificationTab">
                <div class="stat-card">
                    <h5 class="mb-1">In-app Notifications</h5>
                    <p class="text-muted small mb-4">Manage how you receive notifications</p>

                    <form id="notificationForm">
                        @csrf
                        <div class="list-group list-group-flush">
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">All Notifications</h6>
                                        <small class="text-muted">Enable all system notifications</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="email_notifications" checked>
                                    </div>
                                </div>
                            </div>

                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Academic Updates</h6>
                                        <small class="text-muted">Get notified about grades, assignments, and academic progress</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="academic_updates" checked>
                                    </div>
                                </div>
                            </div>

                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Payment Reminders</h6>
                                        <small class="text-muted">Receive reminders for upcoming payments</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="payment_reminders" checked>
                                    </div>
                                </div>
                            </div>

                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Attendance Alerts</h6>
                                        <small class="text-muted">Get alerts when your dependent is absent</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="attendance_alerts" checked>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <h6 class="mb-3">Email Notifications</h6>
                        </div>

                        <div class="list-group list-group-flush">
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">All Notifications</h6>
                                        <small class="text-muted">Receive email notifications for all updates</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="sms_notifications">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <span class="submit-text"><i class="ri-save-line me-2"></i>Save Changes</span>
                                <span class="submit-loading d-none">
                                    <span class="spinner-border spinner-border-sm me-2"></span>Saving...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
    const submitBtn = form.querySelector('button[type="submit"]');
    const submitText = submitBtn.querySelector('.submit-text');
    const submitLoading = submitBtn.querySelector('.submit-loading');
    
    // Show loading state
    submitText.classList.add('d-none');
    submitLoading.classList.remove('d-none');
    submitBtn.disabled = true;
    
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
    })
    .finally(() => {
        // Reset button state
        submitText.classList.remove('d-none');
        submitLoading.classList.add('d-none');
        submitBtn.disabled = false;
    });
}

function resetForm() {
    if (confirm('Are you sure you want to reset all changes?')) {
        document.getElementById('profileForm').reset();
    }
}
</script>
@endpush
@endsection
