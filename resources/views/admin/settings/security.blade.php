@extends('layouts.admin')

@section('title', 'Security Settings')

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
                            <li class="breadcrumb-item active">Security</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Security Settings</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                        <i class="ri-shield-line"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1">Security Settings</h5>
                                <p class="card-title-desc mb-0">Configure security policies and authentication settings</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                            <form id="securityForm">
                                @csrf
                                
                                <!-- Password Policies -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="fw-semibold text-primary mb-3">
                                            <i class="ri-lock-line me-2"></i>Password Policies
                                        </h6>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Minimum Password Length <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="password_min_length" 
                                                   value="{{ $securitySettings['password_min_length'] ?? 8 }}" 
                                                   min="6" max="20" required>
                                            <small class="text-muted">Must be between 6 and 20 characters</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Password Requirements</label>
                                            <div class="mt-2">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="password_require_uppercase" value="1"
                                                           id="require_uppercase" {{ ($securitySettings['password_require_uppercase'] ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="require_uppercase">
                                                        Require uppercase letters
                                                    </label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="password_require_lowercase" value="1"
                                                           id="require_lowercase" {{ ($securitySettings['password_require_lowercase'] ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="require_lowercase">
                                                        Require lowercase letters
                                                    </label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="password_require_numbers" value="1"
                                                           id="require_numbers" {{ ($securitySettings['password_require_numbers'] ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="require_numbers">
                                                        Require numbers
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="password_require_symbols" value="1"
                                                           id="require_symbols" {{ ($securitySettings['password_require_symbols'] ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="require_symbols">
                                                        Require special characters
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- Login Security -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="fw-semibold text-primary mb-3">
                                            <i class="ri-user-settings-line me-2"></i>Login Security
                                        </h6>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Max Login Attempts <span class="text-danger">*</span></label>
                                            <select class="form-select" name="max_login_attempts" required>
                                                <option value="3" {{ ($securitySettings['max_login_attempts'] ?? 5) == 3 ? 'selected' : '' }}>3 attempts</option>
                                                <option value="5" {{ ($securitySettings['max_login_attempts'] ?? 5) == 5 ? 'selected' : '' }}>5 attempts</option>
                                                <option value="7" {{ ($securitySettings['max_login_attempts'] ?? 5) == 7 ? 'selected' : '' }}>7 attempts</option>
                                                <option value="10" {{ ($securitySettings['max_login_attempts'] ?? 5) == 10 ? 'selected' : '' }}>10 attempts</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Lockout Duration <span class="text-danger">*</span></label>
                                            <select class="form-select" name="lockout_duration" required>
                                                <option value="15" {{ ($securitySettings['lockout_duration'] ?? 30) == 15 ? 'selected' : '' }}>15 minutes</option>
                                                <option value="30" {{ ($securitySettings['lockout_duration'] ?? 30) == 30 ? 'selected' : '' }}>30 minutes</option>
                                                <option value="60" {{ ($securitySettings['lockout_duration'] ?? 30) == 60 ? 'selected' : '' }}>1 hour</option>
                                                <option value="120" {{ ($securitySettings['lockout_duration'] ?? 30) == 120 ? 'selected' : '' }}>2 hours</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Session Timeout <span class="text-danger">*</span></label>
                                            <select class="form-select" name="session_timeout" required>
                                                <option value="30" {{ ($securitySettings['session_timeout'] ?? 120) == 30 ? 'selected' : '' }}>30 minutes</option>
                                                <option value="60" {{ ($securitySettings['session_timeout'] ?? 120) == 60 ? 'selected' : '' }}>1 hour</option>
                                                <option value="120" {{ ($securitySettings['session_timeout'] ?? 120) == 120 ? 'selected' : '' }}>2 hours</option>
                                                <option value="240" {{ ($securitySettings['session_timeout'] ?? 120) == 240 ? 'selected' : '' }}>4 hours</option>
                                                <option value="480" {{ ($securitySettings['session_timeout'] ?? 120) == 480 ? 'selected' : '' }}>8 hours</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- Two-Factor Authentication -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="fw-semibold text-primary mb-3">
                                            <i class="ri-smartphone-line me-2"></i>Two-Factor Authentication
                                        </h6>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <div class="form-check form-switch form-check-lg">
                                                <input class="form-check-input" type="checkbox" name="enable_two_factor" value="1"
                                                       id="enable_two_factor" {{ ($securitySettings['enable_two_factor'] ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="enable_two_factor">
                                                    <span class="fw-medium">Enable Two-Factor Authentication</span>
                                                    <small class="d-block text-muted">Users will need to verify their identity with a second factor</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="two-factor-options" style="{{ ($securitySettings['enable_two_factor'] ?? false) ? '' : 'display: none;' }}">
                                    <div class="alert alert-info" role="alert">
                                        <i class="ri-information-line me-2"></i>
                                        <strong>Note:</strong> Two-factor authentication will be enforced for all admin users when enabled.
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- Action Buttons -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-light" onclick="resetForm()">
                                                <i class="ri-refresh-line me-1"></i>Reset
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-save-line me-1"></i>Save Security Settings
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
    </div>

    <!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Security Settings Updated Successfully!</h5>
{{ ... }}
            </div>
            <div class="modal-body">
                <div class="text-warning mb-3">
                    <i class="ri-alert-line display-6"></i>
                </div>
                <h6 class="mb-3">Are you sure you want to update the security settings?</h6>
                <p class="text-muted mb-3">
                    These changes will affect all users on the system and may require them to create new passwords 
                    if they don't meet the new requirements.
                </p>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirmUpdate">
                    <label class="form-check-label" for="confirmUpdate">
                        I understand the implications and want to proceed
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSubmit" disabled>
                    <i class="ri-save-line me-1"></i>Yes, Update Settings
                </button>
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
                <h5 class="mb-3">Security Settings Updated Successfully!</h5>
                <p class="text-muted mb-4">Your security configuration has been saved and is now in effect.</p>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-check-input:checked {
        background-color: #5664d2;
        border-color: #5664d2;
    }
    
    .form-check-lg .form-check-input {
        width: 2rem;
        height: 1rem;
    }
    
    .form-switch.form-check-lg .form-check-input {
        width: 3rem;
        height: 1.5rem;
    }
    
    .form-switch .form-check-input:focus {
        border-color: rgba(86, 100, 210, 0.25);
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(86, 100, 210, 0.25);
    }
    
    .text-primary {
        color: #5664d2 !important;
    }
    
    .bg-soft-primary {
        background-color: rgba(86, 100, 210, 0.1) !important;
    }
    
    .alert-info {
        background-color: rgba(13, 202, 240, 0.1);
        border-color: rgba(13, 202, 240, 0.2);
        color: #055160;
    }
    
    hr {
        border-color: #e9ecef;
        opacity: 1;
    }
</style>
@endpush

@push('scripts')
<script>
    // Two-factor authentication toggle
    document.getElementById('enable_two_factor').addEventListener('change', function() {
        const options = document.querySelector('.two-factor-options');
        if (this.checked) {
            options.style.display = 'block';
        } else {
            options.style.display = 'none';
        }
    });

    // Confirmation checkbox
    document.getElementById('confirmUpdate').addEventListener('change', function() {
        document.getElementById('confirmSubmit').disabled = !this.checked;
    });

    // Form submission with confirmation
    document.getElementById('securityForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show confirmation modal
        new bootstrap.Modal(document.getElementById('confirmModal')).show();
    });

    // Confirmed submission
    document.getElementById('confirmSubmit').addEventListener('click', function() {
        const form = document.getElementById('securityForm');
        const formData = new FormData(form);
        
        // Close confirmation modal
        bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="ri-loader-4-line spinner-border spinner-border-sm me-1"></i>Updating...';
        submitBtn.disabled = true;
        
        fetch('{{ route("admin.settings.security.update") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success modal
                new bootstrap.Modal(document.getElementById('successModal')).show();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving. Please try again.');
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Reset form
    function resetForm() {
        if (confirm('Are you sure you want to reset all security settings to their previous values?')) {
            location.reload();
        }
    }
</script>
@endpush
