@extends('layouts.admin')

@section('title', 'Add New Staff - Employment Details')

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
                    <p class="text-secondary mb-0">Step 3 of 4: Employment Details</p>
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
                <div class="step active">3</div>
                <div class="bar"></div>
                <div class="step">4</div>
            </div>
        </div>
    </div>

    <div class="soft-card p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3">Employment Details</h5>
        
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

        <form method="POST" action="{{ route('admin.staff.enroll.step3.store') }}" id="step3-form">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-dark">Staff ID <span class="text-danger">*</span></label>
                    <input type="text" class="form-control input-soft" name="staff_id" placeholder="Enter Staff ID" value="{{ old('staff_id') }}" required>
                    <div class="form-text text-muted">Unique identifier for this staff member</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">Designation <span class="text-danger">*</span></label>
                    <input type="text" class="form-control input-soft" name="designation" placeholder="e.g., Mathematics Teacher" value="{{ old('designation') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">Department <span class="text-danger">*</span></label>
                    <select class="form-select input-soft" name="department" required>
                        <option value="">Select Department</option>
                        <option value="Academic" {{ old('department') === 'Academic' ? 'selected' : '' }}>Academic</option>
                        <option value="Administration" {{ old('department') === 'Administration' ? 'selected' : '' }}>Administration</option>
                        <option value="Finance" {{ old('department') === 'Finance' ? 'selected' : '' }}>Finance</option>
                        <option value="ICT" {{ old('department') === 'ICT' ? 'selected' : '' }}>ICT</option>
                        <option value="Security" {{ old('department') === 'Security' ? 'selected' : '' }}>Security</option>
                        <option value="Maintenance" {{ old('department') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="Library" {{ old('department') === 'Library' ? 'selected' : '' }}>Library</option>
                        <option value="Health" {{ old('department') === 'Health' ? 'selected' : '' }}>Health</option>
                        <option value="Sports" {{ old('department') === 'Sports' ? 'selected' : '' }}>Sports</option>
                        <option value="Other" {{ old('department') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">Date of Employment <span class="text-danger">*</span></label>
                    <input type="date" class="form-control input-soft" name="date_of_employment" value="{{ old('date_of_employment') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">Employment Type <span class="text-danger">*</span></label>
                    <select class="form-select input-soft" name="employment_type" required>
                        <option value="">Select Employment Type</option>
                        <option value="Full-time" {{ old('employment_type') === 'Full-time' ? 'selected' : '' }}>Full-time</option>
                        <option value="Part-time" {{ old('employment_type') === 'Part-time' ? 'selected' : '' }}>Part-time</option>
                        <option value="Contract" {{ old('employment_type') === 'Contract' ? 'selected' : '' }}>Contract</option>
                        <option value="Temporary" {{ old('employment_type') === 'Temporary' ? 'selected' : '' }}>Temporary</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">Monthly Salary (₦) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control input-soft" name="salary" placeholder="Enter Monthly Salary" value="{{ old('salary') }}" min="0" step="0.01" required>
                </div>
                <div class="col-12">
                    <label class="form-label text-dark">Qualifications</label>
                    <textarea class="form-control input-soft" name="qualifications" rows="4" placeholder="List educational qualifications, certifications, and relevant experience">{{ old('qualifications') }}</textarea>
                    <div class="form-text text-muted">Include degrees, certifications, and relevant experience</div>
                </div>
            </div>
        </form>
        <div class="d-flex justify-content-between align-items-center gap-2 mt-4">
            <a href="{{ route('admin.staff.enroll.step2') }}" class="btn btn-pill-dark">Previous</a>
            <button type="submit" class="btn btn-soft" form="step3-form" id="nextBtn">Next</button>
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
  textarea.input-soft { height: auto; min-height: 120px; }
</style>
@endpush

@push('scripts')
<script>
    // Form validation and UX improvements
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('step3-form');
        const nextBtn = document.getElementById('nextBtn');
        
        // Salary formatting
        const salaryInput = document.querySelector('input[name="salary"]');
        if (salaryInput) {
            salaryInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^\d.]/g, '');
                if (value < 0) {
                    e.target.setCustomValidity('Salary must be a positive number');
                } else {
                    e.target.setCustomValidity('');
                }
            });
        }
        
        // Staff ID validation
        const staffIdInput = document.querySelector('input[name="staff_id"]');
        if (staffIdInput) {
            staffIdInput.addEventListener('input', function(e) {
                // Convert to uppercase and remove special characters except hyphens and underscores
                e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9_-]/g, '');
            });
        }
        
        // Date validation
        const dateInput = document.querySelector('input[name="date_of_employment"]');
        if (dateInput) {
            dateInput.addEventListener('change', function(e) {
                const employmentDate = new Date(e.target.value);
                const today = new Date();
                
                if (employmentDate > today) {
                    e.target.setCustomValidity('Employment date cannot be in the future');
                } else {
                    e.target.setCustomValidity('');
                }
            });
        }
        
        // Form submission with validation
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                nextBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                nextBtn.disabled = true;
            }
            form.classList.add('was-validated');
        });
    });
</script>
@endpush
