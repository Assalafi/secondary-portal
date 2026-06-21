@extends('layouts.student')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item active text-muted">Profile</li>
    </ol>
</nav>

<div class="row g-4">
    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-4">
                @if(Auth::user()->photo_path)
                    <img src="{{ asset('storage/' . Auth::user()->photo_path) }}" alt="photo"
                         class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover;border:3px solid #667eea;">
                @else
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width:100px;height:100px;background:linear-gradient(135deg,#667eea,#764ba2);">
                        <span class="text-white fw-bold" style="font-size:36px;">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                    </div>
                @endif
                <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                <p class="text-muted mb-2">Student</p>
                @if($student)
                    <span class="badge bg-primary px-3 py-2 mb-2">{{ $student->classArm->schoolClass->name ?? '' }} {{ $student->classArm->name ?? '' }}</span>
                    <br>
                    <small class="text-muted">Admission No: {{ $student->admission_no }}</small>
                @endif
            </div>
            <div class="card-body border-top">
                <div class="mb-3">
                    <small class="text-muted d-block">Email</small>
                    <span><i class="ri-mail-line me-1 text-primary"></i>{{ Auth::user()->email }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Phone</small>
                    <span><i class="ri-phone-line me-1 text-primary"></i>{{ Auth::user()->phone ?? 'Not set' }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Gender</small>
                    <span><i class="ri-user-line me-1 text-primary"></i>{{ Auth::user()->gender ?? ($student->gender ?? 'Not set') }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Date of Birth</small>
                    <span><i class="ri-cake-2-line me-1 text-primary"></i>{{ $student && $student->dob ? $student->dob->format('d M Y') : 'Not set' }}</span>
                </div>
                <div>
                    <small class="text-muted d-block">Address</small>
                    <span><i class="ri-map-pin-line me-1 text-primary"></i>{{ Auth::user()->address ?? 'Not set' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Details & Settings -->
    <div class="col-lg-8">
        <!-- Personal Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="ri-user-settings-line me-2 text-primary"></i>Personal Information</h6>
            </div>
            <div class="card-body">
                @if($student)
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Full Name</label>
                            <p class="fw-medium mb-0">{{ Auth::user()->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Admission Number</label>
                            <p class="fw-medium mb-0">{{ $student->admission_no }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Class</label>
                            <p class="fw-medium mb-0">{{ $student->classArm->schoolClass->name ?? 'N/A' }} {{ $student->classArm->name ?? '' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Admission Date</label>
                            <p class="fw-medium mb-0">{{ $student->admission_date ? $student->admission_date->format('d M Y') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">State of Origin</label>
                            <p class="fw-medium mb-0">{{ $student->state_of_origin ?? Auth::user()->state_of_origin ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">LGA</label>
                            <p class="fw-medium mb-0">{{ $student->lga ?? Auth::user()->lga ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Nationality</label>
                            <p class="fw-medium mb-0">{{ $student->nationality ?? Auth::user()->nationality ?? 'Nigerian' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Status</label>
                            <p class="mb-0">
                                <span class="badge {{ $student->status === 'Active' ? 'bg-success' : 'bg-secondary' }}">{{ $student->status ?? 'Active' }}</span>
                            </p>
                        </div>
                    </div>
                @else
                    <p class="text-muted">Student profile not found.</p>
                @endif
            </div>
        </div>

        <!-- Update Contact Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="ri-edit-line me-2 text-primary"></i>Update Contact Information</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('student.profile.update') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phone" value="{{ Auth::user()->phone }}" placeholder="Enter phone number">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" name="address" value="{{ Auth::user()->address }}" placeholder="Enter address">
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="ri-lock-line me-2 text-primary"></i>Change Password</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('student.password.update') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-warning">
                            <i class="ri-lock-password-line me-1"></i>Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
