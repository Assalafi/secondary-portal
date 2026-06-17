@extends('layouts.admin')

@section('title', 'School Information')

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
                            <li class="breadcrumb-item active">School Information</li>
                        </ol>
                    </div>
                    <h4 class="page-title">School Information</h4>
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
                                        <i class="ri-building-line"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1">School Information</h5>
                                <p class="card-title-desc mb-0">Configure your school's basic information and branding</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <form id="schoolInfoForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                    <!-- School Logo -->
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label class="form-label">School Logo</label>
                                            <div class="d-flex align-items-start">
                                                <div class="me-3">
                                                    <div class="school-logo-preview">
                                                        @if($settings->school_logo)
                                                            <img src="{{ asset('storage/' . $settings->school_logo) }}" alt="School Logo" id="logoPreview">
                                                        @else
                                                            <div class="logo-placeholder" id="logoPreview">
                                                                <i class="ri-image-line"></i>
                                                                <span>No Logo</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <input type="file" class="form-control" name="school_logo" id="school_logo" accept="image/*">
                                                    <small class="text-muted">Upload PNG, JPG or GIF. Max size: 2MB</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Favicon -->
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label class="form-label">Favicon</label>
                                            <div class="d-flex align-items-start">
                                                <div class="me-3">
                                                    <div class="favicon-preview">
                                                        @if($settings->favicon)
                                                            <img src="{{ asset('storage/' . $settings->favicon) }}" alt="Favicon" id="faviconPreview">
                                                        @else
                                                            <div class="favicon-placeholder" id="faviconPreview">
                                                                <i class="ri-image-line"></i>
                                                                <span>No Favicon</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <input type="file" class="form-control" name="favicon" id="favicon" accept=".ico,.png">
                                                    <small class="text-muted">Upload ICO or PNG. Max size: 64KB. Recommended: 32x32px</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- School Name -->
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">School Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="school_name" value="{{ $settings->school_name }}" placeholder="Enter school name" required>
                                        </div>
                                    </div>

                                    <!-- Established Year -->
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Established Year</label>
                                            <input type="number" class="form-control" name="established_year" value="{{ $settings->established_year }}" placeholder="Enter year" min="1900" max="{{ date('Y') }}">
                                        </div>
                                    </div>

                                    <!-- School Address -->
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label class="form-label">School Address <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="school_address" rows="3" placeholder="Enter complete school address" required>{{ $settings->school_address }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Contact Information -->
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control" name="phone_number" value="{{ $settings->phone_number }}" placeholder="Enter phone number" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="email" value="{{ $settings->email }}" placeholder="Enter email address" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Website</label>
                                            <input type="url" class="form-control" name="website" value="{{ $settings->website }}" placeholder="https://www.yourschool.com">
                                        </div>
                                    </div>
                                </div>

                                <!-- Meta Tags Section -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h6 class="mb-3">SEO Meta Tags</h6>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label class="form-label">Meta Image (Social Media)</label>
                                            <div class="d-flex align-items-start">
                                                <div class="me-3">
                                                    <div class="meta-image-preview">
                                                        @if($settings->meta_image)
                                                            <img src="{{ asset('storage/' . $settings->meta_image) }}" alt="Meta Image" id="metaImagePreview">
                                                        @else
                                                            <div class="meta-image-placeholder" id="metaImagePreview">
                                                                <i class="ri-image-line"></i>
                                                                <span>No Image</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <input type="file" class="form-control" name="meta_image" id="meta_image" accept="image/*">
                                                    <small class="text-muted">Upload PNG, JPG or GIF. Max size: 2MB. Recommended: 1200x630px for social media</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label class="form-label">Meta Description</label>
                                            <textarea class="form-control" name="meta_description" rows="2" placeholder="Enter meta description for SEO">{{ $settings->meta_description }}</textarea>
                                            <small class="text-muted">Brief description of your school for search engines (max 500 characters)</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label class="form-label">Meta Keywords</label>
                                            <textarea class="form-control" name="meta_keywords" rows="2" placeholder="Enter meta keywords (comma-separated)">{{ $settings->meta_keywords }}</textarea>
                                            <small class="text-muted">Keywords for search engines (max 500 characters)</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label class="form-label">Meta Author</label>
                                            <input type="text" class="form-control" name="meta_author" value="{{ $settings->meta_author }}" placeholder="Enter author name">
                                            <small class="text-muted">Author name for search engines</small>
                                        </div>
                                    </div>
                                </div>

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
                <h5 class="mb-3">School information updated successfully!</h5>
                <p class="text-muted mb-4">Your changes have been saved.</p>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .school-logo-preview {
        width: 100px;
        height: 100px;
        border: 2px dashed #e9ecef;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    
    .school-logo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 6px;
    }
    
    .favicon-preview {
        width: 64px;
        height: 64px;
        border: 2px dashed #e9ecef;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    
    .favicon-preview img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 6px;
    }
    
    .logo-placeholder, .favicon-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
        font-size: 12px;
        text-align: center;
    }
    
    .logo-placeholder i, .favicon-placeholder i {
        font-size: 24px;
        margin-bottom: 4px;
    }
    
    .meta-image-preview {
        width: 120px;
        height: 63px;
        border: 2px dashed #e9ecef;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    
    .meta-image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 6px;
    }
    
    .meta-image-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
        font-size: 12px;
        text-align: center;
    }
    
    .meta-image-placeholder i {
        font-size: 24px;
        margin-bottom: 4px;
    }
    
    .card-header {
        border-bottom: 1px solid #f0f0f0;
    }
    
    .avatar-title {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .bg-soft-primary {
        background-color: rgba(86, 100, 210, 0.1) !important;
    }
    
    .text-primary {
        color: #5664d2 !important;
    }
    
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .form-control {
        border-radius: 6px;
        border: 1px solid #e9ecef;
        padding: 0.75rem;
    }
    
    .form-control:focus {
        border-color: #5664d2;
        box-shadow: 0 0 0 0.2rem rgba(86, 100, 210, 0.25);
    }
    
    .btn-primary {
        background-color: #5664d2;
        border-color: #5664d2;
    }
    
    .btn-primary:hover {
        background-color: #4c63d2;
        border-color: #4c63d2;
    }
    
    .btn-light {
        background-color: #f8f9fa;
        border-color: #f8f9fa;
        color: #6c757d;
    }
</style>
@endpush

@push('scripts')
<script>
    // Logo preview functionality
    document.getElementById('school_logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('logoPreview');
                preview.innerHTML = '<img src="' + e.target.result + '" alt="School Logo">';
            };
            reader.readAsDataURL(file);
        }
    });

    // Favicon preview functionality
    document.getElementById('favicon').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('faviconPreview');
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Favicon">';
            };
            reader.readAsDataURL(file);
        }
    });

    // Meta image preview functionality
    document.getElementById('meta_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('metaImagePreview');
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Meta Image">';
            };
            reader.readAsDataURL(file);
        }
    });

    // Form submission
    document.getElementById('schoolInfoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="ri-loader-4-line me-1 spinner-border spinner-border-sm"></i>Saving...';
        submitBtn.disabled = true;
        
        fetch('{{ route("admin.settings.school-info.update") }}', {
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
        if (confirm('Are you sure you want to reset all changes?')) {
            document.getElementById('schoolInfoForm').reset();
            // Reset logo preview if needed
            location.reload();
        }
    }
</script>
@endpush
