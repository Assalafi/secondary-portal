@extends('layouts.admin')

@section('title', 'Enroll New Student - Step 1')

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
            <div class="step active">1</div>
            <div class="bar"></div>
            <div class="step">2</div>
            <div class="bar"></div>
            <div class="step">3</div>
            <div class="bar"></div>
            <div class="step">4</div>
        </div>
    </div>

    <div class="soft-card p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3">Student Information</h5>
        <form method="POST" action="{{ route('admin.students.enroll.step2') }}" id="step1-form">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label text-dark">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control input-soft" name="first_name" placeholder="Enter First Name" value="{{ old('first_name', $step1Data['first_name'] ?? '') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-dark">Surname:</label>
                    <input type="text" class="form-control input-soft" name="middle_name" placeholder="Enter Surname" value="{{ old('middle_name', $step1Data['middle_name'] ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-dark">Last Name:</label>
                    <input type="text" class="form-control input-soft" name="last_name" placeholder="Enter Last Name" value="{{ old('last_name', $step1Data['last_name'] ?? '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">Gender <span class="text-danger">*</span></label>
                    <div class="d-flex align-items-center gap-3 ms-1 mt-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gender" value="Male" id="male" {{ old('gender', $step1Data['gender'] ?? '') == 'Male' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="male">Male</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gender" value="Female" id="female" {{ old('gender', $step1Data['gender'] ?? '') == 'Female' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="female">Female</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" class="form-control input-soft" name="date_of_birth" value="{{ old('date_of_birth', $step1Data['date_of_birth'] ?? '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">State of Origin <span class="text-danger">*</span></label>
                    <select class="form-select input-soft" id="stateSelect" name="state_of_origin" required>
                        <option value="">Select State</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-dark">LGA <span class="text-danger">*</span></label>
                    <select class="form-select input-soft" id="lgaSelect" name="lga" required disabled>
                        <option value="">Select State First</option>
                    </select>
                </div>
            </div>
        </form>
        <div class="d-flex justify-content-start align-items-center gap-2 mt-4">
            <a href="{{ route('admin.students.overview') }}" class="btn btn-soft">Cancel</a>
            <button type="submit" class="btn btn-pill-dark" form="step1-form">Next</button>
        </div>
    </div>
</div>

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
  .stepper .bar { flex: 1; height: 2px; background: #e5e7eb; }
</style>
@endpush
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function(){
    const stateSelect = document.getElementById('stateSelect');
    const lgaSelect = document.getElementById('lgaSelect');
    const savedState = @json(old('state_of_origin', $step1Data['state_of_origin'] ?? ''));
    const savedLga = @json(old('lga', $step1Data['lga'] ?? ''));
    
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
  });
  </script>
@endpush
@endsection
