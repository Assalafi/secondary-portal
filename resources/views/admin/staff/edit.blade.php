@extends('layouts.admin')

@section('title', 'Edit Staff - ' . ($staff->user->name ?? 'Staff Member'))

@section('content')
<div class="main-content-container overflow-hidden">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.staff.show', $staff) }}" class="btn btn-outline-secondary me-3 d-flex align-items-center gap-2">
                    <i class="ri-arrow-left-line"></i>
                    Back to Profile
                </a>
                <div>
                    <h3 class="fs-20 fw-semibold mb-1">Edit Staff Profile</h3>
                    <p class="text-secondary mb-0">{{ $staff->user->name ?? 'Staff Member' }} • {{ $staff->staff_id ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.staff.update', $staff) }}" enctype="multipart/form-data" id="editStaffForm">
        @csrf
        @method('PUT')

        <!-- Personal Information Section -->
        <div class="card custom-shadow rounded-3 bg-white border mb-4">
            <div class="card-header bg-transparent border-0">
                <h6 class="fw-semibold mb-0">
                    <i class="ri-user-line me-2 text-primary"></i>Personal Information
                </h6>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <div class="fw-semibold mb-1">Please correct the following errors:</div>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        @php $fullName = explode(' ', $staff->user->name ?? ''); @endphp
                        <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $fullName[0] ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $fullName[1] ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Middle Name</label>
                        <input type="text" class="form-control" name="middle_name" value="{{ old('middle_name', isset($fullName[2]) ? implode(' ', array_slice($fullName, 2)) : '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender <span class="text-danger">*</span></label>
                        <select class="form-select" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender', $staff->user->gender ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender', $staff->user->gender ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth', $staff->user->date_of_birth ? $staff->user->date_of_birth->format('Y-m-d') : '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nationality <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nationality" value="{{ old('nationality', $staff->user->nationality ?? 'Nigerian') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">State of Origin <span class="text-danger">*</span></label>
                        <select class="form-select" id="stateSelect" name="state_of_origin" required>
                            <option value="">Select State</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">LGA <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="lga" id="lgaInput" value="{{ old('lga', $staff->user->lga ?? '') }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information Section -->
        <div class="card custom-shadow rounded-3 bg-white border mb-4">
            <div class="card-header bg-transparent border-0">
                <h6 class="fw-semibold mb-0">
                    <i class="ri-phone-line me-2 text-primary"></i>Contact Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="{{ old('email', $staff->user->email ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" name="phone" value="{{ old('phone', $staff->user->phone ?? '') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="address" rows="3" required>{{ old('address', $staff->user->address ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employment Details Section -->
        <div class="card custom-shadow rounded-3 bg-white border mb-4" id="employment-section">
            <div class="card-header bg-transparent border-0">
                <h6 class="fw-semibold mb-0">
                    <i class="ri-briefcase-line me-2 text-primary"></i>Employment Details
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Staff ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="staff_id" value="{{ old('staff_id', $staff->staff_id ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Designation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="designation" value="{{ old('designation', $staff->designation ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Department <span class="text-danger">*</span></label>
                        <select class="form-select" name="department" required>
                            <option value="">Select Department</option>
                            @php $dept = old('department', $staff->department ?? ''); @endphp
                            <option value="Academic" {{ $dept === 'Academic' ? 'selected' : '' }}>Academic</option>
                            <option value="Administration" {{ $dept === 'Administration' ? 'selected' : '' }}>Administration</option>
                            <option value="Finance" {{ $dept === 'Finance' ? 'selected' : '' }}>Finance</option>
                            <option value="ICT" {{ $dept === 'ICT' ? 'selected' : '' }}>ICT</option>
                            <option value="Security" {{ $dept === 'Security' ? 'selected' : '' }}>Security</option>
                            <option value="Maintenance" {{ $dept === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="Library" {{ $dept === 'Library' ? 'selected' : '' }}>Library</option>
                            <option value="Health" {{ $dept === 'Health' ? 'selected' : '' }}>Health</option>
                            <option value="Sports" {{ $dept === 'Sports' ? 'selected' : '' }}>Sports</option>
                            <option value="Other" {{ $dept === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date of Employment <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="date_of_employment" value="{{ old('date_of_employment', $staff->date_of_employment ? \Carbon\Carbon::parse($staff->date_of_employment)->format('Y-m-d') : '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="employment_type" required>
                            <option value="">Select Employment Type</option>
                            @php $empType = old('employment_type', $staff->employment_type ?? ''); @endphp
                            <option value="Full-time" {{ $empType === 'Full-time' ? 'selected' : '' }}>Full-time</option>
                            <option value="Part-time" {{ $empType === 'Part-time' ? 'selected' : '' }}>Part-time</option>
                            <option value="Contract" {{ $empType === 'Contract' ? 'selected' : '' }}>Contract</option>
                            <option value="Temporary" {{ $empType === 'Temporary' ? 'selected' : '' }}>Temporary</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Monthly Salary (₦) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="salary" value="{{ old('salary', $staff->salary ?? '') }}" min="0" step="0.01" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Qualifications</label>
                        <textarea class="form-control" name="qualifications" rows="4">{{ old('qualifications', $staff->qualifications ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Settings Section -->
        <div class="card custom-shadow rounded-3 bg-white border mb-4">
            <div class="card-header bg-transparent border-0">
                <h6 class="fw-semibold mb-0">
                    <i class="ri-settings-line me-2 text-primary"></i>Account Settings
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">User Role <span class="text-danger">*</span></label>
                        <select class="form-select" name="role_id" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $staff->user->role_id ?? '') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" required>
                            <option value="">Select Status</option>
                            @php $status = old('status', $staff->status ?? ''); @endphp
                            <option value="Active" {{ $status === 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ $status === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="Suspended" {{ $status === 'Suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="Terminated" {{ $status === 'Terminated' ? 'selected' : '' }}>Terminated</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Profile Photo</label>
                        <input type="file" class="form-control" name="photo" accept="image/*">
                        <div class="form-text">Max size: 2MB. Formats: JPG, PNG, JPEG</div>
                    </div>
                    @if($staff->user->photo_path)
                        <div class="col-md-6">
                            <label class="form-label">Current Photo</label>
                            <div>
                                <img src="{{ Storage::url($staff->user->photo_path) }}" alt="Current Photo" class="rounded" width="100" height="100">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('admin.staff.show', $staff) }}" class="btn btn-outline-secondary">
                <i class="ri-arrow-left-line me-1"></i>Cancel
            </a>
            <button type="submit" class="btn btn-primary" id="updateBtn">
                <i class="ri-save-line me-1"></i>Update Staff Profile
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Form submission handling
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editStaffForm');
        const updateBtn = document.getElementById('updateBtn');
        
        // Phone number formatting
        const phoneInput = document.querySelector('input[name="phone"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.startsWith('234')) {
                    value = '+' + value;
                } else if (value.startsWith('0')) {
                    value = '+234' + value.substring(1);
                } else if (value.length > 0 && !value.startsWith('+')) {
                    value = '+234' + value;
                }
                e.target.value = value;
            });
        }
        
        // Staff ID formatting
        const staffIdInput = document.querySelector('input[name="staff_id"]');
        if (staffIdInput) {
            staffIdInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9_-]/g, '');
            });
        }
        
        // Form submission
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                updateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
                updateBtn.disabled = true;
            }
            form.classList.add('was-validated');
        });

        // Handle section scrolling from profile page
        if (window.location.hash) {
            setTimeout(() => {
                const element = document.querySelector(window.location.hash);
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    element.style.boxShadow = '0 0 0 3px rgba(79, 70, 229, 0.2)';
                    setTimeout(() => {
                        element.style.boxShadow = '';
                    }, 3000);
                }
            }, 500);
        }
    });
    
    // Load states directly without API call
    const stateSelect = document.getElementById('stateSelect');
    const lgaInput = document.getElementById('lgaInput');
    const savedState = @json(old('state_of_origin', $staff->user->state_of_origin ?? ''));
    
    // List of states
    const states = [
        'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi',
        'Bayelsa', 'Benue', 'Borno', 'Cross River', 'Delta',
        'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT',
        'Gombe', 'Imo', 'Jigawa', 'Kaduna', 'Kano',
        'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos',
        'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun',
        'Oyo', 'Plateau', 'Rivers', 'Sokoto', 'Taraba',
        'Yobe', 'Zamfara'
    ];
    
    // Populate state dropdown
    states.forEach(state => {
        const option = document.createElement('option');
        option.value = state;
        option.textContent = state;
        if (state === savedState) {
            option.selected = true;
        }
        stateSelect.appendChild(option);
    });
    
    // Add an event listener to update LGA input placeholder
    stateSelect.addEventListener('change', function() {
        if (this.value) {
            lgaInput.placeholder = 'Enter LGA for ' + this.value;
        } else {
            lgaInput.placeholder = 'Select a state first';
        }
    });
    
    // Set initial placeholder
    if (stateSelect.value) {
        lgaInput.placeholder = 'Enter LGA for ' + stateSelect.value;
    }
</script>
@endpush

@push('styles')
<style>
    .custom-shadow { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
    .was-validated .form-control:valid { border-color: #10b981; background-image: none; }
    .was-validated .form-control:invalid { border-color: #ef4444; background-image: none; }
    .was-validated .form-select:valid { border-color: #10b981; background-image: none; }
    .was-validated .form-select:invalid { border-color: #ef4444; background-image: none; }
</style>
@endpush
