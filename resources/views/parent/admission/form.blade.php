@extends('layouts.parent')

@section('title', 'Application Form')
@section('page-title', 'Application Form')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('parent.admission.index') }}" class="text-decoration-none">Applications</a></li>
                <li class="breadcrumb-item active text-muted">Application Form</li>
            </ol>
        </nav>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Application Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Admission Application Form</h5>
                        <p class="text-muted small mb-0">Application No: <strong>{{ $application->application_number }}</strong></p>
                    </div>
                    <div>
                        <span class="badge {{ $application->getStatusBadgeClass() }} px-3 py-2">
                            {{ $application->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('parent.admission.save', $application->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Main Form -->
                <div class="col-lg-8">
                    <!-- Student Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ri-user-line me-2"></i>Student Information
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control" 
                                           value="{{ old('first_name', $application->first_name === 'Pending' ? '' : $application->first_name) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control" 
                                           value="{{ old('last_name', $application->last_name === 'Pending' ? '' : $application->last_name) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Other Name</label>
                                    <input type="text" name="other_name" class="form-control" 
                                           value="{{ old('other_name', $application->other_name) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="date_of_birth" class="form-control" 
                                           value="{{ old('date_of_birth', $application->date_of_birth?->format('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" class="form-select" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male" {{ old('gender', $application->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender', $application->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Nationality <span class="text-danger">*</span></label>
                                    <input type="text" name="nationality" class="form-control" 
                                           value="{{ old('nationality', $application->nationality) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">State of Origin</label>
                                    <input type="text" name="state_of_origin" class="form-control" 
                                           value="{{ old('state_of_origin', $application->state_of_origin) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">LGA</label>
                                    <input type="text" name="lga" class="form-control" 
                                           value="{{ old('lga', $application->lga) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Religion</label>
                                    <input type="text" name="religion" class="form-control" 
                                           value="{{ old('religion', $application->religion) }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Blood Group</label>
                                    <select name="blood_group" class="form-select">
                                        <option value="">Select Blood Group</option>
                                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                                            <option value="{{ $group }}" {{ old('blood_group', $application->blood_group) === $group ? 'selected' : '' }}>
                                                {{ $group }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Medical Conditions</label>
                                    <input type="text" name="medical_conditions" class="form-control" 
                                           value="{{ old('medical_conditions', $application->medical_conditions) }}"
                                           placeholder="Any allergies or medical conditions">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Place of Birth (Town)</label>
                                    <input type="text" name="place_of_birth_town" class="form-control" 
                                           value="{{ old('place_of_birth_town', $application->place_of_birth_town) }}"
                                           placeholder="e.g., Ikeja">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Place of Birth (LGA)</label>
                                    <input type="text" name="place_of_birth_lga" class="form-control" 
                                           value="{{ old('place_of_birth_lga', $application->place_of_birth_lga) }}"
                                           placeholder="e.g., Ikeja LGA">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Place of Birth (State)</label>
                                    <input type="text" name="place_of_birth_state" class="form-control" 
                                           value="{{ old('place_of_birth_state', $application->place_of_birth_state) }}"
                                           placeholder="e.g., Lagos">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Health Status</label>
                                    <select name="health_status" class="form-select">
                                        <option value="">Select Health Status</option>
                                        <option value="Excellent" {{ old('health_status', $application->health_status) === 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                        <option value="Good" {{ old('health_status', $application->health_status) === 'Good' ? 'selected' : '' }}>Good</option>
                                        <option value="Fair" {{ old('health_status', $application->health_status) === 'Fair' ? 'selected' : '' }}>Fair</option>
                                        <option value="Poor" {{ old('health_status', $application->health_status) === 'Poor' ? 'selected' : '' }}>Poor</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Disability Details (if any)</label>
                                    <input type="text" name="disability_details" class="form-control" 
                                           value="{{ old('disability_details', $application->disability_details) }}"
                                           placeholder="Specify any disabilities">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Home Address</label>
                                    <textarea name="home_address" class="form-control" rows="2">{{ old('home_address', $application->home_address) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ri-book-line me-2"></i>Academic Information
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Proposed Class <span class="text-danger">*</span></label>
                                    <select name="proposed_class_id" class="form-select" required id="proposedClass">
                                        <option value="">Select Class</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('proposed_class_id', $application->proposed_class_id) == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Proposed Class Arm</label>
                                    <select name="proposed_class_arm_id" class="form-select" id="proposedClassArm">
                                        <option value="">Select Class Arm (Optional)</option>
                                        @foreach($classArms as $arm)
                                            <option value="{{ $arm->id }}" data-class="{{ $arm->school_class_id }}" 
                                                    {{ old('proposed_class_arm_id', $application->proposed_class_arm_id) == $arm->id ? 'selected' : '' }}>
                                                {{ $arm->schoolClass->name }} - {{ $arm->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                                    <select name="academic_session_id" class="form-select" required>
                                        <option value="">Select Session</option>
                                        @foreach($academicSessions as $session)
                                            <option value="{{ $session->id }}" {{ old('academic_session_id', $application->academic_session_id) == $session->id ? 'selected' : '' }}>
                                                {{ $session->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Previous School</label>
                                    <input type="text" name="previous_school" class="form-control" 
                                           value="{{ old('previous_school', $application->previous_school) }}"
                                           placeholder="Name of previous school (if any)">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Reason for Admission</label>
                                    <textarea name="reason_for_admission" class="form-control" rows="3" 
                                              placeholder="Why do you want your child to attend this school?">{{ old('reason_for_admission', $application->reason_for_admission) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Guardian Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ri-parent-line me-2"></i>Guardian Information
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Guardian Name <span class="text-danger">*</span></label>
                                    <input type="text" name="guardian_name" class="form-control" 
                                           value="{{ old('guardian_name', $application->guardian_name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Relationship <span class="text-danger">*</span></label>
                                    <select name="guardian_relationship" class="form-select" required>
                                        @foreach(['Father', 'Mother', 'Guardian', 'Uncle', 'Aunt', 'Grandparent', 'Other'] as $rel)
                                            <option value="{{ $rel }}" {{ old('guardian_relationship', $application->guardian_relationship) === $rel ? 'selected' : '' }}>
                                                {{ $rel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="guardian_phone" class="form-control" 
                                           value="{{ old('guardian_phone', $application->guardian_phone) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="guardian_email" class="form-control" 
                                           value="{{ old('guardian_email', $application->guardian_email) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Occupation</label>
                                    <input type="text" name="guardian_occupation" class="form-control" 
                                           value="{{ old('guardian_occupation', $application->guardian_occupation) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Guardian Address</label>
                                    <textarea name="guardian_address" class="form-control" rows="2">{{ old('guardian_address', $application->guardian_address) }}</textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">City</label>
                                    <input type="text" name="guardian_city" class="form-control" 
                                           value="{{ old('guardian_city', $application->guardian_city) }}"
                                           placeholder="e.g., Maiduguri">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">State</label>
                                    <select name="guardian_state" class="form-select">
                                        <option value="">Select State</option>
                                        @foreach(['Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno', 'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT', 'Gombe', 'Imo', 'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos', 'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau', 'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara'] as $state)
                                            <option value="{{ $state }}" {{ old('guardian_state', $application->guardian_state) === $state ? 'selected' : '' }}>
                                                {{ $state }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ri-phone-line me-2"></i>Emergency Contact
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Contact Name</label>
                                    <input type="text" name="emergency_contact_name" class="form-control" 
                                           value="{{ old('emergency_contact_name', $application->emergency_contact_name) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Contact Phone</label>
                                    <input type="tel" name="emergency_contact_phone" class="form-control" 
                                           value="{{ old('emergency_contact_phone', $application->emergency_contact_phone) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Relationship</label>
                                    <input type="text" name="emergency_contact_relationship" class="form-control" 
                                           value="{{ old('emergency_contact_relationship', $application->emergency_contact_relationship) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-3 mb-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ri-save-line me-2"></i>Save Application
                        </button>
                        <a href="{{ route('parent.admission.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Document Uploads -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ri-file-upload-line me-2"></i>Document Uploads
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <!-- Passport Photo -->
                            <div class="mb-4">
                                <label class="form-label">Passport Photograph <span class="text-danger">*</span></label>
                                <input type="file" name="passport_photo" class="form-control" accept="image/*" {{ $application->passport_photo_path ? '' : 'required' }}>
                                <small class="text-muted">Max: 2MB, Format: JPG, PNG</small>
                                @if($application->passport_photo_path)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $application->passport_photo_path) }}" target="_blank" class="text-decoration-none">
                                            <i class="ri-eye-line me-1"></i>View Current Photo
                                        </a>
                                    </div>
                                @else
                                    <small class="text-danger d-block mt-1">Required for submission</small>
                                @endif
                            </div>

                            <!-- Birth Certificate -->
                            <div class="mb-4">
                                <label class="form-label">Birth Certificate <span class="text-danger">*</span></label>
                                <input type="file" name="birth_certificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png" {{ $application->birth_certificate_path ? '' : 'required' }}>
                                <small class="text-muted">Max: 5MB</small>
                                @if($application->birth_certificate_path)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $application->birth_certificate_path) }}" target="_blank" class="text-decoration-none">
                                            <i class="ri-eye-line me-1"></i>View Current Certificate
                                        </a>
                                    </div>
                                @else
                                    <small class="text-danger d-block mt-1">Required for submission</small>
                                @endif
                            </div>

                            <!-- Previous Report -->
                            <div class="mb-0">
                                <label class="form-label">Previous School Report</label>
                                <input type="file" name="previous_report" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Max: 5MB (If applicable)</small>
                                @if($application->previous_report_path)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $application->previous_report_path) }}" target="_blank" class="text-decoration-none">
                                            <i class="ri-eye-line me-1"></i>View Current Report
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Submit Application Card (Outside Main Form) -->
        <div class="row">
            <div class="col-lg-8"></div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body p-4 text-white">
                        <h6 class="mb-3 text-white">Ready to Submit?</h6>
                        <p class="small mb-3 opacity-90">
                            Once you submit, the application will be reviewed by the admissions team. Make sure all information and documents are correct before submitting.
                        </p>
                        <form action="{{ route('parent.admission.submit', $application->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-light w-100" 
                                    onclick="return confirm('Are you sure you want to submit this application? You won\'t be able to edit it after submission.')">
                                <i class="ri-send-plane-line me-2"></i>Submit Application
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Filter class arms based on selected class
    document.getElementById('proposedClass').addEventListener('change', function() {
        const classId = this.value;
        const armSelect = document.getElementById('proposedClassArm');
        const allOptions = armSelect.querySelectorAll('option[data-class]');
        
        allOptions.forEach(option => {
            if (classId === '' || option.dataset.class === classId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
        
        armSelect.value = '';
    });

    // Trigger on page load if class is selected
    if (document.getElementById('proposedClass').value) {
        document.getElementById('proposedClass').dispatchEvent(new Event('change'));
    }
</script>
@endpush
@endsection
