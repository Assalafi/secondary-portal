@extends('layouts.admin')

@section('title', 'Add New Staff - Portal Setup')

@section('content')
<div class="main-content-container overflow-hidden">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary me-3 d-flex align-items-center gap-2">
                    <i class="ri-arrow-left-line"></i>
                    Back to Staff
                </a>
                <div>
                    <h3 class="fs-20 fw-semibold mb-1">Add New Staff</h3>
                    <p class="text-secondary mb-0">Step 4 of 4: Portal Setup</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="card custom-shadow rounded-3 bg-white border mb-4">
        <div class="card-body py-3">
            <div class="stepper">
                <div class="step done">1</div>
                <div class="bar"></div>
                <div class="step done">2</div>
                <div class="bar"></div>
                <div class="step done">3</div>
                <div class="bar"></div>
                <div class="step active">4</div>
            </div>
        </div>
    </div>

    <div class="soft-card p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3">Portal Setup</h5>
        
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

        <!-- Summary of Previous Steps -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="p-3 bg-light rounded">
                    <h6 class="fw-semibold mb-2">Personal Information</h6>
                    <p class="mb-1"><strong>Name:</strong> {{ $step1Data['first_name'] }} {{ $step1Data['last_name'] }}</p>
                    <p class="mb-1"><strong>Gender:</strong> {{ $step1Data['gender'] }}</p>
                    <p class="mb-0"><strong>State:</strong> {{ $step1Data['state_of_origin'] }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 bg-light rounded">
                    <h6 class="fw-semibold mb-2">Contact Details</h6>
                    <p class="mb-1"><strong>Email:</strong> {{ $step2Data['email'] }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $step2Data['phone_number'] }}</p>
                    <p class="mb-0"><strong>Emergency:</strong> {{ $step2Data['emergency_contact_name'] }}</p>
                </div>
            </div>
            <div class="col-12">
                <div class="p-3 bg-light rounded">
                    <h6 class="fw-semibold mb-2">Employment Details</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <p class="mb-0"><strong>Staff ID:</strong> {{ $step3Data['staff_id'] }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-0"><strong>Department:</strong> {{ $step3Data['department'] }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-0"><strong>Designation:</strong> {{ $step3Data['designation'] }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-0"><strong>Salary:</strong> ₦{{ number_format($step3Data['salary'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.staff.enroll.complete') }}" id="step4-form">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-dark">User Role <span class="text-danger">*</span></label>
                    <select class="form-select input-soft" name="role_id" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text text-muted">This determines what the staff member can access in the portal</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">Account Status <span class="text-danger">*</span></label>
                    <select class="form-select input-soft" name="status" required>
                        <option value="">Select Status</option>
                        <option value="Active" {{ old('status', 'Active') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12">
                    <div class="alert alert-info">
                        <h6 class="fw-semibold mb-2"><i class="ri-information-line me-2"></i>Default Portal Credentials</h6>
                        <p class="mb-1"><strong>Username (Email):</strong> {{ $step2Data['email'] }}</p>
                        <p class="mb-0"><strong>Default Password:</strong> {{ $step3Data['staff_id'] }}2024</p>
                        <small class="text-muted">The staff member should change this password on first login.</small>
                    </div>
                </div>
            </div>
        </form>
        <div class="d-flex justify-content-between align-items-center gap-2 mt-4">
            <a href="{{ route('admin.staff.enroll.step3') }}" class="btn btn-pill-dark">Previous</a>
            <button type="submit" class="btn btn-soft" form="step4-form" id="completeBtn">
                <i class="ri-check-line me-1"></i>Complete Enrollment
            </button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
  .breadcrumb-soft .breadcrumb-item + .breadcrumb-item::before { color: #9ca3af; }
  .breadcrumb-soft a { color: #6b7280; text-decoration: none; }
  .breadcrumb-soft .active { color: #9ca3af; }
  .soft-card { background: #ffffff; border-radius: 12px; border: 1px solid #e5e7eb; }
  .input-soft { background: #f7f7f8; border: 0; height: 52px; border-radius: 12px; }
  .input-soft:focus { background: #fff; box-shadow: 0 0 0 .25rem rgba(17,24,39,.06); }
  .btn-pill-dark { background: #111827; color: #fff; border: 0; border-radius: 9999px; padding: .6rem 1.1rem; font-weight: 600; }
  .btn-pill-dark:hover { background: #0b1220; color: #fff; }
  .btn-soft { background: #f1f1f1; color: #111827; border: 0; border-radius: 9999px; padding: .6rem 1.1rem; font-weight: 600; }
  .stepper { display: flex; align-items: center; gap: 10px; }
  .stepper .step { width: 36px; height: 36px; border-radius: 9999px; background: #e5e7eb; color: #111827; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; }
  .stepper .step.active { background: #111827; color: #fff; }
  .stepper .step.done { background: #9ca3af; color: #fff; }
  .stepper .bar { flex: 1; height: 2px; background: #e5e7eb; }
  .alert { border: 0; border-radius: 12px; }
  .was-validated .form-control:valid { border-color: #10b981; background-image: none; }
  .was-validated .form-control:invalid { border-color: #ef4444; background-image: none; }
  .was-validated .form-select:valid { border-color: #10b981; background-image: none; }
  .was-validated .form-select:invalid { border-color: #ef4444; background-image: none; }
</style>
@endpush

@push('scripts')
<script>
    // Form validation and UX improvements
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('step4-form');
        const completeBtn = document.getElementById('completeBtn');
        
        // Form submission with validation
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                completeBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Completing Enrollment...';
                completeBtn.disabled = true;
            }
            form.classList.add('was-validated');
        });
    });
</script>
@endpush
