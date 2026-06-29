@extends('layouts.admin')

@section('title', 'Student Profile - Overview')

@section('content')
    <div class="main-content-container overflow-hidden">
        <!-- Student Profile Header -->
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <a href="{{ route('admin.students.index') }}"
                        class="btn btn-outline-secondary me-3 d-flex align-items-center gap-2">
                        <i class="ri-arrow-left-line"></i>
                        Back to Students
                    </a>
                    <div>
                        <h3 class="fs-20 fw-semibold mb-1">Student Profile</h3>
                        <p class="text-secondary mb-0">{{ $student->full_name ?? '-' }} - {{ $student->admission_no ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="d-flex gap-2 justify-content-md-end">
                    @if (\Illuminate\Support\Facades\Route::has('admin.students.edit'))
                        <a href="{{ route('admin.students.edit', $student->id) }}"
                            class="btn btn-outline-primary d-flex align-items-center gap-2">
                            <i class="ri-edit-line"></i>
                            Edit Profile
                        </a>
                    @else
                        <button class="btn btn-outline-primary d-flex align-items-center gap-2" disabled>
                            <i class="ri-edit-line"></i>
                            Edit Profile
                        </button>
                    @endif
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle d-flex align-items-center gap-2" type="button"
                            data-bs-toggle="dropdown">
                            <i class="ri-more-line"></i>
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="printProfile()"><i
                                        class="ri-printer-line me-2"></i>Print Profile</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportData()"><i
                                        class="ri-download-line me-2"></i>Export Data</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><span class="dropdown-item text-muted"><i class="ri-alert-line me-2"></i>More actions coming
                                    soon</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="card custom-shadow rounded-3 bg-white border mb-4">
            <div class="card-body p-0">
                <ul class="nav nav-tabs border-0 px-4 pt-3" id="studentProfileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active fw-medium"
                            href="{{ route('admin.students.profile.overview', $student->id) }}">
                            <i class="ri-user-line me-2"></i>Overview
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link fw-medium" href="{{ route('admin.students.profile.academic', $student->id) }}">
                            <i class="ri-graduation-cap-line me-2"></i>Academic Info
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link fw-medium" href="{{ route('admin.students.profile.fees', $student->id) }}">
                            <i class="ri-money-dollar-circle-line me-2"></i>Fees & Payments
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link fw-medium" href="{{ route('admin.students.profile.attendance', $student->id) }}">
                            <i class="ri-calendar-check-line me-2"></i>Attendance
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link fw-medium" href="{{ route('admin.students.profile.documents', $student->id) }}">
                            <i class="ri-file-text-line me-2"></i>Documents
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Profile Content -->
        <div class="row g-4">
            <!-- Student Photo & Basic Info -->
            <div class="col-lg-4">
                <div class="card custom-shadow rounded-3 bg-white border mb-4">
                    <div class="card-body text-center p-4">
                        @php
                            $photoUrl = $student->photo_path
                                ? \Illuminate\Support\Facades\Storage::url($student->photo_path)
                                : 'https://ui-avatars.com/api/?name=' .
                                    urlencode($student->full_name ?? 'Student') .
                                    '&background=4f46e5&color=fff&size=120&rounded=true';
                            $cls = data_get($student, 'classArm.schoolClass.name');
                            $arm = data_get($student, 'classArm.name');
                            $className = trim(($cls ?: '-') . ' ' . ($arm ?: ''));
                            $status = strtolower($student->status ?? 'Inactive');
                        @endphp
                        <div class="position-relative d-inline-block mb-3">
                            <img src="{{ $photoUrl }}" alt="Student Photo" class="rounded-circle" width="120"
                                height="120">
                            <span
                                class="position-absolute bottom-0 end-0 {{ $status === 'active' ? 'bg-success' : 'bg-secondary' }} border border-white rounded-circle"
                                style="width: 25px; height: 25px;"
                                title="{{ ucfirst($student->status ?? 'Inactive') }}"></span>
                        </div>
                        <h5 class="fw-semibold mb-1">{{ $student->full_name ?? '-' }}</h5>
                        <p class="text-secondary mb-2">{{ trim($className) }} •
                            {{ data_get($student, 'academicSession.name', '—') }}</p>
                        <span
                            class="badge {{ $status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-3 py-2">{{ ucfirst($student->status ?? 'Inactive') }}
                            Student</span>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card custom-shadow rounded-3 bg-white border">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h6 class="fw-semibold mb-0">Quick Stats</h6>
                    </div>
                    <div class="card-body pt-2">
                        @php
                            $attTotal = isset($student->attendances) ? $student->attendances->count() : 0;
                            $attPresent = $attTotal ? $student->attendances->where('status', 'Present')->count() : 0;
                            $attendanceRate = $attTotal ? round(($attPresent / $attTotal) * 100) : null;
                            $currentTerm = null; // No is_current column; controller can pass later
                        @endphp
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="text-secondary">Attendance Rate</span>
                            <span
                                class="fw-medium {{ $attendanceRate !== null ? 'text-success' : 'text-secondary' }}">{{ $attendanceRate !== null ? $attendanceRate . '%' : '—' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="text-secondary">Current Term</span>
                            <span class="fw-medium">{{ $currentTerm->name ?? '—' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="text-secondary">Fee Status</span>
                            <span class="badge bg-light text-secondary">—</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="text-secondary">Last Login</span>
                            <span
                                class="fw-medium">{{ isset($student->user) && !empty($student->user->last_login_at) ? \Carbon\Carbon::parse($student->user->last_login_at)->diffForHumans() : '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Profile Information -->
            <div class="col-lg-8">
                <div class="row g-4">
                    <!-- Student Information -->
                    <div class="col-12">
                        <div class="card custom-shadow rounded-3 bg-white border">
                            <div
                                class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                                <h6 class="fw-semibold mb-0">
                                    <i class="ri-user-line me-2 text-primary"></i>Student Information
                                </h6>
                                <a href="{{ route('admin.students.edit', $student->id) }}"
                                    class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                    <i class="ri-edit-line"></i>
                                    Edit
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2" style="min-width: 120px;">Full
                                                Name:</span>
                                            <span>{{ $student->full_name ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2"
                                                style="min-width: 120px;">Gender:</span>
                                            <span>{{ $student->gender ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2" style="min-width: 120px;">Date of
                                                Birth:</span>
                                            <span>{{ $student->dob ? $student->dob->format('jS M Y') : '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2"
                                                style="min-width: 120px;">Age:</span>
                                            @php $age = $student->dob ? \Carbon\Carbon::parse($student->dob)->age : null; @endphp
                                            <span>{{ $age !== null ? $age . ' years' : '—' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2" style="min-width: 120px;">State of
                                                Origin:</span>
                                            <span>{{ $student->state_of_origin ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2"
                                                style="min-width: 120px;">LGA:</span>
                                            <span>{{ $student->lga ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2"
                                                style="min-width: 120px;">Enrollment Date:</span>
                                            <span>{{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('jS M Y') : '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2" style="min-width: 120px;">Student
                                                ID:</span>
                                            <span
                                                class="badge bg-primary-subtle text-primary">{{ $student->admission_no ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Placement -->
                    <div class="col-md-6">
                        <div class="card custom-shadow rounded-3 bg-white border h-100">
                            <div
                                class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                                <h6 class="fw-semibold mb-0">
                                    <i class="ri-graduation-cap-line me-2 text-primary"></i>Academic Placement
                                </h6>
                                <a href="{{ route('admin.students.edit', $student->id) }}#academic-section"
                                    class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                    <i class="ri-edit-line"></i>
                                    Edit
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2"
                                                style="min-width: 100px;">Class:</span>
                                            <span>{{ trim($className) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2"
                                                style="min-width: 100px;">Shift:</span>
                                            <span>—</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2" style="min-width: 100px;">Academic
                                                Year:</span>
                                            <span>{{ data_get($student, 'academicSession.name', '—') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2" style="min-width: 100px;">Current
                                                Term:</span>
                                            <span
                                                class="badge bg-info-subtle text-info">{{ $currentTerm->name ?? '—' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Guardian Information -->
                    <div class="col-md-6">
                        <div class="card custom-shadow rounded-3 bg-white border h-100">
                            <div
                                class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                                <h6 class="fw-semibold mb-0">
                                    <i class="ri-parent-line me-2 text-primary"></i>Guardian Information
                                </h6>
                                <a href="{{ route('admin.students.edit', $student->id) }}#guardian-section"
                                    class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                    <i class="ri-edit-line"></i>
                                    Edit
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2"
                                                style="min-width: 100px;">Name:</span>
                                            @php
                                                $primaryGuardian = null;
                                                if (isset($student->parentsGuardians)) {
                                                    $primaryGuardian =
                                                        $student->parentsGuardians->firstWhere(
                                                            'pivot.is_primary_contact',
                                                            true,
                                                        ) ?? $student->parentsGuardians->first();
                                                }
                                            @endphp
                                            <span>{{ $primaryGuardian->full_name ?? '—' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2"
                                                style="min-width: 100px;">Relationship:</span>
                                            <span>{{ $primaryGuardian->relationship ?? '—' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2"
                                                style="min-width: 100px;">Phone:</span>
                                            <span>{{ $primaryGuardian->phone ?? '—' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2"
                                                style="min-width: 100px;">Email:</span>
                                            <span>{{ $primaryGuardian->email ?? '—' }}</span>
                                        </div>
                                    </div>
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
                                            <span class="fw-medium text-secondary me-2" style="min-width: 100px;">Username
                                                (Email):</span>
                                            <span
                                                class="badge bg-secondary-subtle text-secondary">{{ $student->user->email ?? '—' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <span class="fw-medium text-secondary me-2"
                                                style="min-width: 100px;">Password:</span>
                                            <div class="d-flex align-items-center gap-2">
                                                <span
                                                    class="badge bg-info-subtle text-info">{{ $student->admission_no ?? 'STUDENT' . $student->id }}</span>
                                                <button class="btn btn-sm btn-outline-warning"
                                                    onclick="resetToDefault({{ $student->id }})">
                                                    <i class="ri-refresh-line me-1"></i>Reset to Default
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2" style="min-width: 100px;">Last
                                                Login:</span>
                                            <span>{{ isset($student->user) && !empty($student->user->last_login_at) ? \Carbon\Carbon::parse($student->user->last_login_at)->diffForHumans() : '—' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <span class="fw-medium text-secondary me-2"
                                                style="min-width: 100px;">Status:</span>
                                            <span
                                                class="badge {{ $status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">{{ ucfirst($student->status ?? 'Inactive') }}</span>
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
        // Print Profile Function
        function printProfile() {
            // Open PDF in new tab
            window.open('{{ route('admin.students.profile.pdf', $student->id) }}', '_blank');
        }

        // Export Data Function
        function exportData() {
            // Create student data object
            const studentData = {
                name: "{{ $student->full_name ?? '' }}",
                admission_no: "{{ $student->admission_no ?? '' }}",
                gender: "{{ $student->gender ?? '' }}",
                date_of_birth: "{{ $student->dob ? $student->dob->format('Y-m-d') : '' }}",
                age: "{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->age : '' }}",
                state_of_origin: "{{ $student->state_of_origin ?? '' }}",
                lga: "{{ $student->lga ?? '' }}",
                enrollment_date: "{{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('Y-m-d') : '' }}",
                class: "{{ trim($className ?? '') }}",
                academic_year: "{{ data_get($student, 'academicSession.name', '') }}",
                status: "{{ $student->status ?? '' }}",
                email: "{{ $student->user->email ?? '' }}",
                last_login: "{{ isset($student->user) && !empty($student->user->last_login_at) ? \Carbon\Carbon::parse($student->user->last_login_at)->format('Y-m-d H:i:s') : '' }}",
                guardian_name: "{{ $primaryGuardian->full_name ?? '' }}",
                guardian_relationship: "{{ $primaryGuardian->relationship ?? '' }}",
                guardian_phone: "{{ $primaryGuardian->phone ?? '' }}",
                guardian_email: "{{ $primaryGuardian->email ?? '' }}"
            };

            // Convert to CSV format
            const csvContent = Object.entries(studentData)
                .map(([key, value]) => `"${key.replace(/_/g, ' ').toUpperCase()}","${value}"`)
                .join('\n');

            const csvHeader = '"FIELD","VALUE"\n';
            const csvData = csvHeader + csvContent;

            // Create and download file
            const blob = new Blob([csvData], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            link.setAttribute('href', url);
            link.setAttribute('download', `student_${studentData.admission_no}_profile.csv`);
            link.style.visibility = 'hidden';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Show success message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Export Successful',
                    text: 'Student profile data has been exported to CSV file.',
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                alert('Student profile data exported successfully!');
            }
        }

        // Add smooth scrolling for hash links in edit buttons
        document.addEventListener('DOMContentLoaded', function() {
            const editLinks = document.querySelectorAll('a[href*="#"]');
            editLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const hash = this.getAttribute('href').split('#')[1];
                    if (hash) {
                        // Store the hash in sessionStorage to scroll after page load
                        sessionStorage.setItem('scrollToSection', hash);
                    }
                });
            });
        });

        // Reset to Default Password Function
        function resetToDefault(studentId) {
            if (!confirm(
                    'Are you sure you want to reset this student\'s password to the default format? This will allow them to login with the displayed password.'
                    )) {
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
            form.action = `{{ url('/admin/students') }}/${studentId}/reset-password`;
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
