@extends('layouts.admin')

@section('title', 'Enroll New Student - Step 3')

@section('content')
<div class="container-fluid">
    <div class="mb-3">
        <h1 class="h3 mb-1 fw-bold text-dark">Enroll New Student</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 breadcrumb-soft">
                <li class="breadcrumb-item"><a href="{{ route('admin.students.overview') }}">Students</a></li>
                <li class="breadcrumb-item active" aria-current="page">Enroll New Student</li>
            </ol>
        </nav>
    </div>

    <!-- Stepper -->
    <div class="soft-card p-4 mb-4">
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

    <div class="soft-card p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3">Guardian Information</h5>
        
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

        <form method="POST" action="{{ route('admin.students.enroll.step4') }}" id="step3-form">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-dark">Guardian Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control input-soft" name="guardian_name" placeholder="Enter Guardian Full Name" value="{{ old('guardian_name', $step3Data['guardian_name'] ?? '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">Relationship to Student <span class="text-danger">*</span></label>
                    @php $rel = old('relationship', $step3Data['relationship'] ?? 'Father'); @endphp
                    <select class="form-select input-soft" name="relationship" required>
                        <option value="">Select relationship</option>
                        <option value="Father" {{ $rel=='Father'?'selected':'' }}>Father</option>
                        <option value="Mother" {{ $rel=='Mother'?'selected':'' }}>Mother</option>
                        <option value="Guardian" {{ $rel=='Guardian'?'selected':'' }}>Guardian</option>
                        <option value="Uncle" {{ $rel=='Uncle'?'selected':'' }}>Uncle</option>
                        <option value="Aunt" {{ $rel=='Aunt'?'selected':'' }}>Aunt</option>
                        <option value="Grandfather" {{ $rel=='Grandfather'?'selected':'' }}>Grandfather</option>
                        <option value="Grandmother" {{ $rel=='Grandmother'?'selected':'' }}>Grandmother</option>
                        <option value="Brother" {{ $rel=='Brother'?'selected':'' }}>Brother</option>
                        <option value="Sister" {{ $rel=='Sister'?'selected':'' }}>Sister</option>
                        <option value="Other" {{ $rel=='Other'?'selected':'' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">Phone Number <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control input-soft" name="phone_number" placeholder="e.g., +234 801 234 5678" value="{{ old('phone_number', $step3Data['phone_number'] ?? '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">Email Address</label>
                    <input type="email" class="form-control input-soft" name="email_address" placeholder="guardian@example.com" value="{{ old('email_address', $step3Data['email_address'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">Occupation</label>
                    <input type="text" class="form-control input-soft" name="occupation" placeholder="Guardian's occupation" value="{{ old('occupation', $step3Data['occupation'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">Alternative Contact</label>
                    <input type="tel" class="form-control input-soft" name="emergency_contact" placeholder="Alternative phone number" value="{{ old('emergency_contact', $step3Data['emergency_contact'] ?? '') }}">
                </div>
                <div class="col-12">
                    <label class="form-label text-dark">Home Address <span class="text-danger">*</span></label>
                    <textarea class="form-control input-soft" name="address" rows="3" placeholder="Enter complete home address" required>{{ old('address', $step3Data['address'] ?? '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">City</label>
                    <input type="text" class="form-control input-soft" name="city" placeholder="Enter city" value="{{ old('city', $step3Data['city'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">State</label>
                    @php $gs = old('guardian_state', $step3Data['guardian_state'] ?? 'Borno'); @endphp
                    <select class="form-select input-soft" name="guardian_state">
                        <option value="">Select state</option>
                        <option value="Abia" {{ $gs=='Abia'?'selected':'' }}>Abia</option>
                        <option value="Adamawa" {{ $gs=='Adamawa'?'selected':'' }}>Adamawa</option>
                        <option value="Akwa Ibom" {{ $gs=='Akwa Ibom'?'selected':'' }}>Akwa Ibom</option>
                        <option value="Anambra" {{ $gs=='Anambra'?'selected':'' }}>Anambra</option>
                        <option value="Bauchi" {{ $gs=='Bauchi'?'selected':'' }}>Bauchi</option>
                        <option value="Bayelsa" {{ $gs=='Bayelsa'?'selected':'' }}>Bayelsa</option>
                        <option value="Benue" {{ $gs=='Benue'?'selected':'' }}>Benue</option>
                        <option value="Borno" {{ $gs=='Borno'?'selected':'' }}>Borno</option>
                        <option value="Cross River" {{ $gs=='Cross River'?'selected':'' }}>Cross River</option>
                        <option value="Delta" {{ $gs=='Delta'?'selected':'' }}>Delta</option>
                        <option value="Ebonyi" {{ $gs=='Ebonyi'?'selected':'' }}>Ebonyi</option>
                        <option value="Edo" {{ $gs=='Edo'?'selected':'' }}>Edo</option>
                        <option value="Ekiti" {{ $gs=='Ekiti'?'selected':'' }}>Ekiti</option>
                        <option value="Enugu" {{ $gs=='Enugu'?'selected':'' }}>Enugu</option>
                        <option value="FCT" {{ $gs=='FCT'?'selected':'' }}>FCT</option>
                        <option value="Gombe" {{ $gs=='Gombe'?'selected':'' }}>Gombe</option>
                        <option value="Imo" {{ $gs=='Imo'?'selected':'' }}>Imo</option>
                        <option value="Jigawa" {{ $gs=='Jigawa'?'selected':'' }}>Jigawa</option>
                        <option value="Kaduna" {{ $gs=='Kaduna'?'selected':'' }}>Kaduna</option>
                        <option value="Kano" {{ $gs=='Kano'?'selected':'' }}>Kano</option>
                        <option value="Katsina" {{ $gs=='Katsina'?'selected':'' }}>Katsina</option>
                        <option value="Kebbi" {{ $gs=='Kebbi'?'selected':'' }}>Kebbi</option>
                        <option value="Kogi" {{ $gs=='Kogi'?'selected':'' }}>Kogi</option>
                        <option value="Kwara" {{ $gs=='Kwara'?'selected':'' }}>Kwara</option>
                        <option value="Lagos" {{ $gs=='Lagos'?'selected':'' }}>Lagos</option>
                        <option value="Nasarawa" {{ $gs=='Nasarawa'?'selected':'' }}>Nasarawa</option>
                        <option value="Niger" {{ $gs=='Niger'?'selected':'' }}>Niger</option>
                        <option value="Ogun" {{ $gs=='Ogun'?'selected':'' }}>Ogun</option>
                        <option value="Ondo" {{ $gs=='Ondo'?'selected':'' }}>Ondo</option>
                        <option value="Osun" {{ $gs=='Osun'?'selected':'' }}>Osun</option>
                        <option value="Oyo" {{ $gs=='Oyo'?'selected':'' }}>Oyo</option>
                        <option value="Plateau" {{ $gs=='Plateau'?'selected':'' }}>Plateau</option>
                        <option value="Rivers" {{ $gs=='Rivers'?'selected':'' }}>Rivers</option>
                        <option value="Sokoto" {{ $gs=='Sokoto'?'selected':'' }}>Sokoto</option>
                        <option value="Taraba" {{ $gs=='Taraba'?'selected':'' }}>Taraba</option>
                        <option value="Yobe" {{ $gs=='Yobe'?'selected':'' }}>Yobe</option>
                        <option value="Zamfara" {{ $gs=='Zamfara'?'selected':'' }}>Zamfara</option>
                    </select>
                </div>
            </div>
        </form>
        <div class="d-flex justify-content-start align-items-center gap-2 mt-4">
            <a href="{{ route('admin.students.enroll.step2.show') }}" class="btn btn-pill-dark">Previous</a>
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
  .soft-card { background: #f7f7f8; border: 0; border-radius: 18px; }
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
        const form = document.getElementById('step3-form');
        const nextBtn = document.getElementById('nextBtn');
        
        // Add phone number formatting
        const phoneInput = document.querySelector('input[name="phone_number"]');
        const emergencyInput = document.querySelector('input[name="emergency_contact"]');
        
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
        
        if (phoneInput) formatPhone(phoneInput);
        if (emergencyInput) formatPhone(emergencyInput);
        
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
        const emailInput = document.querySelector('input[name="email_address"]');
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
