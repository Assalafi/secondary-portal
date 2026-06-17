@extends('layouts.admin')

@section('title', 'Staff Profile - Overview')

@section('content')
<div class="main-content-container overflow-hidden">
    <!-- Staff Profile Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary me-3 d-flex align-items-center gap-2">
                    <i class="ri-arrow-left-line"></i>
                    Back to Staff
                </a>
                <div>
                    <h3 class="fs-20 fw-semibold mb-1">Staff Profile</h3>
                    <p class="text-secondary mb-0">{{ $staff->user->name ?? '-' }} - {{ $staff->staff_id ?? '-' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="d-flex gap-2 justify-content-md-end">
                <a href="{{ route('admin.staff.edit', $staff->id) }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <i class="ri-edit-line"></i>
                    Edit Profile
                </a>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <i class="ri-more-line"></i>
                        Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="printProfile()"><i class="ri-printer-line me-2"></i>Print Profile</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportData()"><i class="ri-download-line me-2"></i>Export Data</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><span class="dropdown-item text-muted"><i class="ri-alert-line me-2"></i>More actions coming soon</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Content -->
    <div class="row g-4">
        <!-- Staff Photo & Basic Info -->
        <div class="col-lg-4">
            <div class="card custom-shadow rounded-3 bg-white border mb-4">
                <div class="card-body text-center p-4">
                    @php
                        $photoUrl = $staff->user->photo_path ? (\Illuminate\Support\Facades\Storage::url($staff->user->photo_path)) : 'https://ui-avatars.com/api/?name='.urlencode($staff->user->name ?? 'Staff').'&background=4f46e5&color=fff&size=120&rounded=true';
                        $status = strtolower($staff->status ?? 'Inactive');
                    @endphp
                    <div class="position-relative d-inline-block mb-3">
                        <img src="{{ $photoUrl }}" alt="Staff Photo" class="rounded-circle" width="120" height="120">
                        <span class="position-absolute bottom-0 end-0 {{ $status === 'active' ? 'bg-success' : 'bg-secondary' }} border border-white rounded-circle" 
                              style="width: 25px; height: 25px;" title="{{ ucfirst($staff->status ?? 'Inactive') }}"></span>
                    </div>
                    <h5 class="fw-semibold mb-1">{{ $staff->user->name ?? '-' }}</h5>
                    <p class="text-secondary mb-2">{{ $staff->designation ?? '—' }} • {{ $staff->department ?? '—' }}</p>
                    <span class="badge {{ $status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-3 py-2">{{ ucfirst($staff->status ?? 'Inactive') }} Staff</span>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="fw-semibold mb-0">Quick Stats</h6>
                </div>
                <div class="card-body pt-2">
                    @php
                        $employmentDate = $staff->date_of_employment ? \Carbon\Carbon::parse($staff->date_of_employment) : null;
                        $yearsOfService = $employmentDate ? $employmentDate->diffInYears(now()) : null;
                    @endphp
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-secondary">Years of Service</span>
                        <span class="fw-medium">{{ $yearsOfService !== null ? $yearsOfService : '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-secondary">Employment Type</span>
                        <span class="fw-medium">{{ $staff->employment_type ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-secondary">Salary</span>
                        <span class="fw-medium text-success">₦{{ $staff->salary ? number_format($staff->salary, 2) : '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-secondary">Last Login</span>
                        <span class="fw-medium">{{ isset($staff->user) && !empty($staff->user->last_login_at) ? \Carbon\Carbon::parse($staff->user->last_login_at)->diffForHumans() : '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Profile Information -->
        <div class="col-lg-8">
            <div class="row g-4">
                <!-- Staff Information -->
                <div class="col-12">
                    <div class="card custom-shadow rounded-3 bg-white border">
                        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                            <h6 class="fw-semibold mb-0">
                                <i class="ri-user-line me-2 text-primary"></i>Staff Information
                            </h6>
                            <a href="{{ route('admin.staff.edit', $staff->id) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                <i class="ri-edit-line"></i>
                                Edit
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <span class="fw-medium text-secondary me-2" style="min-width: 120px;">Full Name:</span>
                                        <span>{{ $staff->user->name ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <span class="fw-medium text-secondary me-2" style="min-width: 120px;">Gender:</span>
                                        <span>{{ $staff->user->gender ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <span class="fw-medium text-secondary me-2" style="min-width: 120px;">Date of Birth:</span>
                                        <span>{{ $staff->user->date_of_birth ? $staff->user->date_of_birth->format('jS M Y') : '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <span class="fw-medium text-secondary me-2" style="min-width: 120px;">Age:</span>
                                        @php $age = $staff->user->date_of_birth ? \Carbon\Carbon::parse($staff->user->date_of_birth)->age : null; @endphp
                                        <span>{{ $age !== null ? $age.' years' : '—' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <span class="fw-medium text-secondary me-2" style="min-width: 120px;">Email:</span>
                                        <span>{{ $staff->user->email ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <span class="fw-medium text-secondary me-2" style="min-width: 120px;">Phone:</span>
                                        <span>{{ $staff->user->phone ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <span class="fw-medium text-secondary me-2" style="min-width: 120px;">State of Origin:</span>
                                        <span>{{ $staff->user->state_of_origin ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <span class="fw-medium text-secondary me-2" style="min-width: 120px;">Staff ID:</span>
                                        <span class="badge bg-primary-subtle text-primary">{{ $staff->staff_id ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employment Details -->
                <div class="col-md-6">
                    <div class="card custom-shadow rounded-3 bg-white border h-100">
                        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                            <h6 class="fw-semibold mb-0">
                                <i class="ri-briefcase-line me-2 text-primary"></i>Employment Details
                            </h6>
                            <a href="{{ route('admin.staff.edit', $staff->id) }}#employment-section" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                <i class="ri-edit-line"></i>
                                Edit
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex">
                                        <span class="fw-medium text-secondary me-2" style="min-width: 100px;">Department:</span>
                                        <span>{{ $staff->department ?? '—' }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex">
                                        <span class="fw-medium text-secondary me-2" style="min-width: 100px;">Designation:</span>
                                        <span>{{ $staff->designation ?? '—' }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex">
                                        <span class="fw-medium text-secondary me-2" style="min-width: 100px;">Employment:</span>
                                        <span>{{ $staff->employment_type ?? '—' }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex">
                                        <span class="fw-medium text-secondary me-2" style="min-width: 100px;">Start Date:</span>
                                        <span class="badge bg-info-subtle text-info">{{ $staff->date_of_employment ? \Carbon\Carbon::parse($staff->date_of_employment)->format('jS M Y') : '—' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Log -->
                <div class="col-md-6">
                    <div class="card custom-shadow rounded-3 bg-white border h-100">
                        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                            <h6 class="fw-semibold mb-0">
                                <i class="ri-line-chart-line me-2 text-primary"></i>Performance Log
                            </h6>
                            <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" disabled>
                                <i class="ri-add-line"></i>
                                Add Record
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-3">
                                <i class="ri-file-list-line display-6 text-muted"></i>
                                <p class="text-muted mb-0 mt-2">No performance records yet</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Portal Access Details -->
                <div class="col-12">
                    <div class="card custom-shadow rounded-3 bg-white border">
                        <div class="card-header bg-transparent border-0">
                            <h6 class="fw-semibold mb-0">
                                <i class="ri-key-line me-2 text-primary"></i>Portal Access Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <span class="fw-medium text-secondary me-2" style="min-width: 100px;">Username (Email):</span>
                                        <span class="badge bg-secondary-subtle text-secondary">{{ $staff->user->email ?? '—' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <span class="fw-medium text-secondary me-2" style="min-width: 100px;">Password:</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-info-subtle text-info">{{ ($staff->staff_id ?? 'STAFF' . $staff->id) . '2024' }}</span>
                                            <button class="btn btn-sm btn-outline-warning" onclick="resetToDefault({{ $staff->id }})">
                                                <i class="ri-refresh-line me-1"></i>Reset to Default
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <span class="fw-medium text-secondary me-2" style="min-width: 100px;">Role:</span>
                                        <span class="badge bg-primary-subtle text-primary">{{ $staff->user->role->name ?? '—' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <span class="fw-medium text-secondary me-2" style="min-width: 100px;">Last Login:</span>
                                        <span>{{ isset($staff->user) && !empty($staff->user->last_login_at) ? \Carbon\Carbon::parse($staff->user->last_login_at)->diffForHumans() : '—' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <span class="fw-medium text-secondary me-2" style="min-width: 100px;">Status:</span>
                                        <span class="badge {{ $status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">{{ ucfirst($staff->status ?? 'Inactive') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Success message handling
    @if(session('success'))
        alert({!! json_encode(session('success')) !!});
    @endif

    // Print Profile Function
    function printProfile() {
        // Hide unnecessary elements for printing
        const elementsToHide = [
            '.btn', 
            '.dropdown',
            'nav',
            '.breadcrumb'
        ];
        
        elementsToHide.forEach(selector => {
            document.querySelectorAll(selector).forEach(el => {
                el.style.display = 'none';
            });
        });
        
        // Print
        window.print();
        
        // Restore elements after printing
        setTimeout(() => {
            elementsToHide.forEach(selector => {
                document.querySelectorAll(selector).forEach(el => {
                    el.style.display = '';
                });
            });
        }, 1000);
    }
    
    // Export Data Function
    function exportData() {
        // Create staff data object
        const staffData = {
            name: "{{ $staff->user->name ?? '' }}",
            staff_id: "{{ $staff->staff_id ?? '' }}",
            email: "{{ $staff->user->email ?? '' }}",
            phone: "{{ $staff->user->phone ?? '' }}",
            gender: "{{ $staff->user->gender ?? '' }}",
            date_of_birth: "{{ $staff->user->date_of_birth ? $staff->user->date_of_birth->format('Y-m-d') : '' }}",
            age: "{{ $staff->user->date_of_birth ? \Carbon\Carbon::parse($staff->user->date_of_birth)->age : '' }}",
            state_of_origin: "{{ $staff->user->state_of_origin ?? '' }}",
            department: "{{ $staff->department ?? '' }}",
            designation: "{{ $staff->designation ?? '' }}",
            employment_type: "{{ $staff->employment_type ?? '' }}",
            date_of_employment: "{{ $staff->date_of_employment ? \Carbon\Carbon::parse($staff->date_of_employment)->format('Y-m-d') : '' }}",
            salary: "{{ $staff->salary ?? '' }}",
            status: "{{ $staff->status ?? '' }}",
            role: "{{ $staff->user->role->name ?? '' }}",
            last_login: "{{ isset($staff->user) && !empty($staff->user->last_login_at) ? \Carbon\Carbon::parse($staff->user->last_login_at)->format('Y-m-d H:i:s') : '' }}"
        };
        
        // Convert to CSV format
        const csvContent = Object.entries(staffData)
            .map(([key, value]) => `"${key.replace(/_/g, ' ').toUpperCase()}","${value}"`)
            .join('\n');
        
        const csvHeader = '"FIELD","VALUE"\n';
        const csvData = csvHeader + csvContent;
        
        // Create and download file
        const blob = new Blob([csvData], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', `staff_${staffData.staff_id}_profile.csv`);
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        alert('Staff profile data exported successfully!');
    }
    
    // Reset to Default Password Function
    function resetToDefault(staffId) {
        if (!confirm('Are you sure you want to reset this staff member\'s password to the default format? This will allow them to login with the displayed password.')) {
            return;
        }
        
        // Show loading state
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Resetting...';
        button.disabled = true;
        
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ url('/admin/staff') }}/${staffId}/reset-password`;
        form.style.display = 'none';
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken ? csrfToken.getAttribute('content') : '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add method spoofing if needed
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'POST';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
</script>
@endpush

@push('styles')
<style>
    .custom-shadow { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
    .table-hover tbody tr:hover { background-color: rgba(79, 70, 229, 0.05); }
</style>
@endpush
