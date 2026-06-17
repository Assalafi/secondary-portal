@extends('layouts.admin')

@section('title', 'Notification Preferences')

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
                            <li class="breadcrumb-item active">Notification Preferences</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Notification Preferences</h4>
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
                                            <i class="ri-notification-line"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">Notification Preferences</h5>
                                    <p class="card-title-desc mb-0">Configure notification settings for the system</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <form id="notificationForm">
                                @csrf
                                
                                <!-- In-app Notifications -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="fw-semibold text-primary mb-3">
                                            <i class="ri-smartphone-line me-2"></i>In-app Notifications
                                        </h6>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="notification-item">
                                            <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                                <div>
                                                    <h6 class="mb-1">All Notifications</h6>
                                                    <p class="text-muted mb-0 small">Enable or disable all in-app notifications</p>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="in_app_all_notifications" value="1"
                                                           id="in_app_all" {{ ($notificationSettings['in_app']['all_notifications'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="in_app_all"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="notification-item">
                                            <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                                <div>
                                                    <h6 class="mb-1">Academic Updates</h6>
                                                    <p class="text-muted mb-0 small">Notifications about academic activities</p>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="in_app_academic_updates" value="1"
                                                           id="in_app_academic" {{ ($notificationSettings['in_app']['academic_updates'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="in_app_academic"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-lg-6">
                                        <div class="notification-item">
                                            <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                                <div>
                                                    <h6 class="mb-1">Payment Reminders</h6>
                                                    <p class="text-muted mb-0 small">Notifications about fees and payments</p>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="in_app_payment_reminders" value="1"
                                                           id="in_app_payment" {{ ($notificationSettings['in_app']['payment_reminders'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="in_app_payment"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="notification-item">
                                            <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                                <div>
                                                    <h6 class="mb-1">Result Publication</h6>
                                                    <p class="text-muted mb-0 small">Notifications when results are published</p>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="in_app_result_publication" value="1"
                                                           id="in_app_result" {{ ($notificationSettings['in_app']['result_publication'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="in_app_result"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <!-- Email Notifications -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="fw-semibold text-primary mb-3">
                                            <i class="ri-mail-line me-2"></i>Email Notifications
                                        </h6>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="notification-item">
                                            <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                                <div>
                                                    <h6 class="mb-1">All Notifications</h6>
                                                    <p class="text-muted mb-0 small">Enable or disable all email notifications</p>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="email_all_notifications" value="1"
                                                           id="email_all" {{ ($notificationSettings['email']['all_notifications'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="email_all"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="notification-item">
                                            <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                                <div>
                                                    <h6 class="mb-1">Academic Updates</h6>
                                                    <p class="text-muted mb-0 small">Email notifications about academic activities</p>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="email_academic_updates" value="1"
                                                           id="email_academic" {{ ($notificationSettings['email']['academic_updates'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="email_academic"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-lg-6">
                                        <div class="notification-item">
                                            <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                                <div>
                                                    <h6 class="mb-1">Payment Reminders</h6>
                                                    <p class="text-muted mb-0 small">Email notifications about fees and payments</p>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="email_payment_reminders" value="1"
                                                           id="email_payment" {{ ($notificationSettings['email']['payment_reminders'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="email_payment"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="notification-item">
                                            <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                                <div>
                                                    <h6 class="mb-1">Result Publication</h6>
                                                    <p class="text-muted mb-0 small">Email notifications when results are published</p>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="email_result_publication" value="1"
                                                           id="email_result" {{ ($notificationSettings['email']['result_publication'] ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="email_result"></label>
                                                </div>
                                            </div>
                                        </div>
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
                                                <i class="ri-save-line me-1"></i>Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
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
                <h5 class="mb-3">Notification preferences updated successfully!</h5>
                <p class="text-muted mb-4">Your notification settings have been saved.</p>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .notification-item {
        margin-bottom: 1rem;
    }
    
    .notification-item .border {
        border-color: #e9ecef !important;
        transition: all 0.3s ease;
    }
    
    .notification-item .border:hover {
        border-color: #5664d2 !important;
        box-shadow: 0 0.125rem 0.25rem rgba(86, 100, 210, 0.15);
    }
    
    .form-check-input:checked {
        background-color: #5664d2;
        border-color: #5664d2;
    }
    
    .form-switch .form-check-input {
        width: 3rem;
        height: 1.5rem;
        border-radius: 1rem;
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
    
    .notification-item h6 {
        color: #495057;
        font-weight: 600;
    }
    
    .notification-item .text-muted {
        font-size: 0.875rem;
    }
    
    hr {
        border-color: #e9ecef;
        opacity: 1;
    }
</style>
@endpush

@push('scripts')
<script>
    // Form submission
    document.getElementById('notificationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="ri-loader-4-line me-1 spinner-border spinner-border-sm"></i>Saving...';
        submitBtn.disabled = true;
        
        fetch('{{ route("admin.settings.notification-preferences.update") }}', {
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

    // Master toggle functionality
    document.getElementById('in_app_all').addEventListener('change', function() {
        const inAppCheckboxes = document.querySelectorAll('input[name^="in_app_"]:not(#in_app_all)');
        inAppCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    document.getElementById('email_all').addEventListener('change', function() {
        const emailCheckboxes = document.querySelectorAll('input[name^="email_"]:not(#email_all)');
        emailCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Reset form
    function resetForm() {
        if (confirm('Are you sure you want to reset all notification preferences?')) {
            location.reload();
        }
    }
</script>
@endpush
