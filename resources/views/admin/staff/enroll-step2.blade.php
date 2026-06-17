@extends('layouts.admin')

@section('title', 'Add New Staff - Contact Details')

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
                    <p class="text-secondary mb-0">Step 2 of 4: Contact Details</p>
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
                <div class="step active">2</div>
                <div class="bar"></div>
                <div class="step">3</div>
                <div class="bar"></div>
                <div class="step">4</div>
            </div>
        </div>
    </div>

    <div class="soft-card p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3">Contact Details</h5>
        
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

        <form method="POST" action="{{ route('admin.staff.enroll.step2.store') }}" id="step2-form">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-dark">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control input-soft" name="email" placeholder="Enter Email Address" value="{{ old('email') }}" required>
                    <div class="form-text text-muted">This will be used for portal login</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">Phone Number <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control input-soft" name="phone_number" placeholder="Enter Phone Number" value="{{ old('phone_number') }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label text-dark">Residential Address <span class="text-danger">*</span></label>
                    <textarea class="form-control input-soft" name="address" rows="3" placeholder="Enter Full Address" required>{{ old('address') }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-dark">Emergency Contact Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control input-soft" name="emergency_contact_name" placeholder="Enter Contact Name" value="{{ old('emergency_contact_name') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-dark">Emergency Contact Phone <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control input-soft" name="emergency_contact_phone" placeholder="Enter Contact Phone" value="{{ old('emergency_contact_phone') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-dark">Relationship <span class="text-danger">*</span></label>
                    <select class="form-select input-soft" name="emergency_contact_relationship" required>
                        <option value="">Select Relationship</option>
                        <option value="Spouse" {{ old('emergency_contact_relationship') === 'Spouse' ? 'selected' : '' }}>Spouse</option>
                        <option value="Parent" {{ old('emergency_contact_relationship') === 'Parent' ? 'selected' : '' }}>Parent</option>
                        <option value="Sibling" {{ old('emergency_contact_relationship') === 'Sibling' ? 'selected' : '' }}>Sibling</option>
                        <option value="Child" {{ old('emergency_contact_relationship') === 'Child' ? 'selected' : '' }}>Child</option>
                        <option value="Friend" {{ old('emergency_contact_relationship') === 'Friend' ? 'selected' : '' }}>Friend</option>
                        <option value="Other" {{ old('emergency_contact_relationship') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>
        </form>
        <div class="d-flex justify-content-between align-items-center gap-2 mt-4">
            <a href="{{ route('admin.staff.enroll.step1') }}" class="btn btn-pill-dark">Previous</a>
            <button type="submit" class="btn btn-soft" form="step2-form" id="nextBtn">Next</button>
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
  textarea.input-soft { height: auto; min-height: 100px; }
</style>
@endpush

@push('scripts')
<script>
    // Form validation and UX improvements
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('step2-form');
        const nextBtn = document.getElementById('nextBtn');
        
        // Add phone number formatting
        const phoneInputs = document.querySelectorAll('input[type="tel"]');
        
        function formatPhone(input) {
            input.addEventListener('input', function(e) {
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
        
        phoneInputs.forEach(formatPhone);
        
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
        
        // Email validation
        const emailInput = document.querySelector('input[name="email"]');
        if (emailInput) {
            emailInput.addEventListener('input', function(e) {
                const email = e.target.value;
                if (email && !email.includes('@')) {
                    e.target.setCustomValidity('Please enter a valid email address');
                } else {
                    e.target.setCustomValidity('');
                }
            });
        }
    });
</script>
@endpush
