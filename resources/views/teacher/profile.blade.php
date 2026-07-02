@extends('layouts.teacher')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                @if($teacher->photo_path)
                    <img src="{{ asset('storage/' . $teacher->photo_path) }}" alt="Profile" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                        <span class="text-white fw-bold" style="font-size: 36px;">{{ strtoupper(substr($teacher->name, 0, 2)) }}</span>
                    </div>
                @endif
                <h5 class="fw-bold mb-1">{{ $teacher->name }}</h5>
                <p class="text-muted mb-2">Teacher</p>
                <span class="badge bg-success bg-opacity-10 text-success">Active</span>
            </div>
        </div>
    </div>

    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="ri-user-line me-2 text-primary"></i>Personal Information</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Full Name</label>
                        <p class="fw-medium mb-0">{{ $teacher->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Email Address</label>
                        <p class="fw-medium mb-0">{{ $teacher->email ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Phone Number</label>
                        <p class="fw-medium mb-0">{{ $teacher->phone ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Gender</label>
                        <p class="fw-medium mb-0">{{ $teacher->gender ?? 'N/A' }}</p>
                    </div>
                    @if($staff)
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Staff ID</label>
                            <p class="fw-medium mb-0">{{ $staff->staff_id ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Department</label>
                            <p class="fw-medium mb-0">{{ $staff->department ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Qualification</label>
                            <p class="fw-medium mb-0">{{ $staff->qualification ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Date Joined</label>
                            <p class="fw-medium mb-0">{{ $staff->date_joined ? \Carbon\Carbon::parse($staff->date_joined)->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="ri-lock-line me-2 text-warning"></i>Change Password</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('teacher.profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">New Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-warning btn-sm px-4">
                            <i class="ri-lock-line me-1"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
