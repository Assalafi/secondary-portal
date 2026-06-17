@extends('layouts.admin')

@section('title', 'Add New Staff - Personal Information')

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
                    <p class="text-secondary mb-0">Step 1 of 4: Personal Information</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="card custom-shadow rounded-3 bg-white border mb-4">
        <div class="card-body py-3">
            <div class="stepper">
                <div class="step active">1</div>
                <div class="bar"></div>
                <div class="step">2</div>
                <div class="bar"></div>
                <div class="step">3</div>
                <div class="bar"></div>
                <div class="step">4</div>
            </div>
        </div>
    </div>

    <div class="soft-card p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3">Personal Information</h5>
        
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

        <form method="POST" action="{{ route('admin.staff.enroll.step1.store') }}" id="step1-form">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label text-dark">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control input-soft" name="first_name" placeholder="Enter First Name" value="{{ old('first_name') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-dark">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control input-soft" name="last_name" placeholder="Enter Last Name" value="{{ old('last_name') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-dark">Middle Name</label>
                    <input type="text" class="form-control input-soft" name="middle_name" placeholder="Enter Middle Name" value="{{ old('middle_name') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">Gender <span class="text-danger">*</span></label>
                    <select class="form-select input-soft" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" class="form-control input-soft" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-dark">Nationality <span class="text-danger">*</span></label>
                    <input type="text" class="form-control input-soft" name="nationality" placeholder="Enter Nationality" value="{{ old('nationality', 'Nigerian') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-dark">State of Origin <span class="text-danger">*</span></label>
                    <select class="form-select input-soft" id="stateSelect" name="state_of_origin" required>
                        <option value="">Select State</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-dark">LGA <span class="text-danger">*</span></label>
                    <select class="form-select input-soft" id="lgaSelect" name="lga" required disabled>
                        <option value="">Select State First</option>
                    </select>
                </div>
            </div>
        </form>
        <div class="d-flex justify-content-end align-items-center gap-2 mt-4">
            <button type="submit" class="btn btn-soft" form="step1-form" id="nextBtn">Next</button>
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
        const form = document.getElementById('step1-form');
        const nextBtn = document.getElementById('nextBtn');
        
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
        
        // Age validation for date of birth
        const dobInput = document.querySelector('input[name="date_of_birth"]');
        if (dobInput) {
            dobInput.addEventListener('change', function(e) {
                const birthDate = new Date(e.target.value);
                const today = new Date();
                const age = today.getFullYear() - birthDate.getFullYear();
                
                if (age < 18) {
                    e.target.setCustomValidity('Staff member must be at least 18 years old');
                } else if (age > 65) {
                    e.target.setCustomValidity('Please verify the age is correct');
                } else {
                    e.target.setCustomValidity('');
                }
            });
        }
    });
    
    // State and LGA dynamic loading
    const stateSelect = document.getElementById('stateSelect');
    const lgaSelect = document.getElementById('lgaSelect');
    const savedState = @json(old('state_of_origin', ''));
    const savedLga = @json(old('lga', ''));
    
    // Load all states from API
    fetch('{{ route("api.locations.states") }}')
      .then(response => response.json())
      .then(data => {
        // Populate state dropdown
        data.forEach(state => {
          const option = document.createElement('option');
          option.value = state;
          option.textContent = state;
          if (state === savedState) {
            option.selected = true;
          }
          stateSelect.appendChild(option);
        });
        
        // If we have a saved state, load its LGAs
        if (savedState) {
          loadLGAs(savedState, savedLga);
        }
      })
      .catch(error => console.error('Error loading states:', error));
    
    // Add event listener for state change
    stateSelect.addEventListener('change', function() {
      if (this.value) {
        loadLGAs(this.value);
      } else {
        // Reset LGA dropdown
        lgaSelect.innerHTML = '<option value="">Select State First</option>';
        lgaSelect.disabled = true;
      }
    });
    
    // Function to load LGAs for selected state
    function loadLGAs(state, selectedLGA = null) {
      // Enable LGA dropdown
      lgaSelect.disabled = false;
      
      // Show loading
      lgaSelect.innerHTML = '<option value="">Loading LGAs...</option>';
      
      // Fetch LGAs from API
      fetch(`{{ route("api.locations.lgas") }}?state=${encodeURIComponent(state)}`)
        .then(response => response.json())
        .then(data => {
          // Reset dropdown
          lgaSelect.innerHTML = '<option value="">Select LGA</option>';
          
          // Populate LGAs
          data.forEach(lga => {
            const option = document.createElement('option');
            option.value = lga;
            option.textContent = lga;
            if (lga === selectedLGA) {
              option.selected = true;
            }
            lgaSelect.appendChild(option);
          });
        })
        .catch(error => {
          console.error('Error loading LGAs:', error);
          lgaSelect.innerHTML = '<option value="">Error loading LGAs</option>';
        });
    }
</script>
@endpush
