@extends('layouts.parent')

@section('title', 'Profile - ' . $student->user->name)
@section('page-title', 'Profile')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.dependents.index') }}" class="text-decoration-none">My Dependents</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.dependents.show', $student->id) }}" class="text-decoration-none">{{ $student->user->name }}</a></li>
                <li class="breadcrumb-item active text-muted">Profile</li>
            </ol>
        </nav>

        <!-- Student Quick Info Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    @if($student->user->photo_path)
                        <img src="{{ asset('storage/' . $student->user->photo_path) }}" 
                             alt="{{ $student->user->name }}"
                             class="rounded-circle" 
                             style="width: 60px; height: 60px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px;">
                            <i class="ri-user-line text-white" style="font-size: 30px;"></i>
                        </div>
                    @endif
                    <div>
                        <p class="mb-1"><span class="text-muted">Name:</span> <strong>{{ $student->user->name }}</strong></p>
                        <p class="mb-1"><span class="text-muted">Class:</span> <strong>{{ optional(optional($student->classArm)->schoolClass)->name ?? 'N/A' }} {{ optional($student->classArm)->name ?? '' }}</strong></p>
                        <p class="mb-0"><span class="text-muted">Gender:</span> <strong>{{ $student->user->gender ?? 'Male' }}</strong></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Informations -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Personal Informations</h5>
                    <button class="btn btn-dark btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#editPersonalModal">
                        Edit
                    </button>
                </div>
                
                <div class="row g-3">
                    <div class="col-12">
                        <p class="mb-1 text-muted">Date of Birth:</p>
                        <p class="mb-0">{{ $student->user->date_of_birth ? $student->user->date_of_birth->format('d M, Y') : '02 Jan, 2024' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-1 text-muted">State of Origin:</p>
                        <p class="mb-0">{{ $student->user->state_of_origin ?? 'Borno' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-1 text-muted">LGA:</p>
                        <p class="mb-0">{{ $student->user->lga ?? 'MMC' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-1 text-muted">Religion:</p>
                        <p class="mb-0">{{ $student->user->religion ?? 'Islam' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-1 text-muted">Address:</p>
                        <p class="mb-0">{{ $student->user->address ?? 'House No. 2, Ahmadu Ali Cresent' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-1 text-muted">Phone Number:</p>
                        <p class="mb-0">{{ $student->user->phone ?? '080123456789' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-1 text-muted">Email Address:</p>
                        <p class="mb-0">{{ $student->user->email ?? 'johndoe@example.com' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Informations -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Academic Informations</h5>
                    <span class="badge bg-light text-dark">Read-only info</span>
                </div>
                
                <div class="row g-3">
                    <div class="col-12">
                        <p class="mb-1 text-muted">Admission Number:</p>
                        <p class="mb-0">{{ $student->admission_number ?? 'ABC123' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-1 text-muted">Class:</p>
                        <p class="mb-0">{{ optional(optional($student->classArm)->schoolClass)->name ?? 'JSS' }} {{ optional($student->classArm)->name ?? '3D' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-1 text-muted">Class Group:</p>
                        <p class="mb-0">Science</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-1 text-muted">Academic Session:</p>
                        <p class="mb-0">{{ $globalSettings['academic_session'] ?? '2024/2025' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-1 text-muted">Current Term:</p>
                        <p class="mb-0">{{ $globalSettings['current_term'] ?? '3rd' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-1 text-muted">Session:</p>
                        <p class="mb-0">Morning Session</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parent/Guardian Informations -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Parent/Guardian Informations</h5>
                    <button class="btn btn-dark btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#editParentModal">
                        Edit
                    </button>
                </div>
                
                <div class="row g-3">
                    <div class="col-12">
                        <p class="mb-1 text-muted">Full Name:</p>
                        <p class="mb-0">{{ Auth::user()->name ?? 'Doe Musa' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-1 text-muted">Relationship:</p>
                        <p class="mb-0">{{ $relationship->relationship ?? 'Father' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-1 text-muted">Phone Number:</p>
                        <p class="mb-0">{{ Auth::user()->phone ?? '080123456789' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-1 text-muted">Email Address:</p>
                        <p class="mb-0">{{ Auth::user()->email ?? 'doemusa@example.com' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-1 text-muted">Occupation:</p>
                        <p class="mb-0">{{ Auth::user()->occupation ?? 'Police' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="mb-1 text-muted">Home Address:</p>
                        <p class="mb-0">{{ Auth::user()->address ?? 'House No. 2, Ahmadu Ali Cresent' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Personal Information Modal -->
<div class="modal fade" id="editPersonalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Personal Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editPersonalForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth" 
                               value="{{ $student->user->date_of_birth ? $student->user->date_of_birth->format('Y-m-d') : '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">State of Origin</label>
                        <input type="text" class="form-control" name="state_of_origin" 
                               value="{{ $student->user->state_of_origin ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">LGA</label>
                        <input type="text" class="form-control" name="lga" 
                               value="{{ $student->user->lga ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Religion</label>
                        <input type="text" class="form-control" name="religion" 
                               value="{{ $student->user->religion ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2">{{ $student->user->address ?? '' }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="phone" 
                               value="{{ $student->user->phone ?? '' }}">
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Parent/Guardian Information Modal -->
<div class="modal fade" id="editParentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Parent/Guardian Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editParentForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="name" 
                               value="{{ Auth::user()->name ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Relationship</label>
                        <select class="form-select" name="relationship">
                            <option value="Father" {{ ($relationship->relationship ?? '') == 'Father' ? 'selected' : '' }}>Father</option>
                            <option value="Mother" {{ ($relationship->relationship ?? '') == 'Mother' ? 'selected' : '' }}>Mother</option>
                            <option value="Guardian" {{ ($relationship->relationship ?? '') == 'Guardian' ? 'selected' : '' }}>Guardian</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="phone" 
                               value="{{ Auth::user()->phone ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Occupation</label>
                        <input type="text" class="form-control" name="occupation" 
                               value="{{ Auth::user()->occupation ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Home Address</label>
                        <textarea class="form-control" name="address" rows="2">{{ Auth::user()->address ?? '' }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Handle Personal Information Form Submission
document.getElementById('editPersonalForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
    
    const formData = new FormData(form);
    
    fetch('{{ route("parent.dependents.profile.personal.update", $student->id) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert(data.message);
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('editPersonalModal')).hide();
            
            // Reload page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 500);
        } else {
            alert('Error: ' + data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Handle Parent/Guardian Information Form Submission
document.getElementById('editParentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
    
    const formData = new FormData(form);
    
    fetch('{{ route("parent.dependents.profile.parent.update", $student->id) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert(data.message);
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('editParentModal')).hide();
            
            // Reload page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 500);
        } else {
            alert('Error: ' + data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});
</script>
@endpush
@endsection
