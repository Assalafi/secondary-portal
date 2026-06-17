@extends('layouts.admin')

@section('title', 'Edit Student')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 breadcrumb-soft">
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
                </ol>
            </nav>
            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <i class="ri-arrow-left-line"></i> Back to Students
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1">There were some problems with your input:</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.students.update', $student->id) }}" enctype="multipart/form-data"
            class="needs-validation" novalidate>
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card custom-shadow rounded-3 bg-white border">
                        <div class="card-header bg-transparent border-0">
                            <h6 class="fw-semibold mb-0"><i class="ri-user-3-line me-2 text-primary"></i>Personal
                                Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Admission Number</label>
                                    <input type="text" class="form-control" value="{{ $student->admission_no }}"
                                        disabled>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control"
                                        value="{{ old('first_name', $student->first_name) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" name="middle_name" class="form-control"
                                        value="{{ old('middle_name', $student->middle_name) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Surname (Last Name) <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control"
                                        value="{{ old('last_name', $student->surname) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Email (Login) <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', optional($student->user)->email) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" class="form-select" required>
                                        <option value="">Select gender</option>
                                        <option value="Male"
                                            {{ old('gender', $student->gender) === 'Male' ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="Female"
                                            {{ old('gender', $student->gender) === 'Female' ? 'selected' : '' }}>Female
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="date_of_birth" class="form-control"
                                        value="{{ old('date_of_birth', optional($student->dob)->format('Y-m-d')) }}"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">State of Origin</label>
                                    <select id="stateSelect" class="form-select" name="state_of_origin"
                                        onchange="updateLGAs()">
                                        <option value="">Select State</option>
                                        <option value="Abia"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Abia' ? 'selected' : '' }}>
                                            Abia</option>
                                        <option value="Adamawa"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Adamawa' ? 'selected' : '' }}>
                                            Adamawa</option>
                                        <option value="Akwa Ibom"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Akwa Ibom' ? 'selected' : '' }}>
                                            Akwa Ibom</option>
                                        <option value="Anambra"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Anambra' ? 'selected' : '' }}>
                                            Anambra</option>
                                        <option value="Bauchi"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Bauchi' ? 'selected' : '' }}>
                                            Bauchi</option>
                                        <option value="Bayelsa"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Bayelsa' ? 'selected' : '' }}>
                                            Bayelsa</option>
                                        <option value="Benue"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Benue' ? 'selected' : '' }}>
                                            Benue</option>
                                        <option value="Borno"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Borno' ? 'selected' : '' }}>
                                            Borno</option>
                                        <option value="Cross River"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Cross River' ? 'selected' : '' }}>
                                            Cross River</option>
                                        <option value="Delta"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Delta' ? 'selected' : '' }}>
                                            Delta</option>
                                        <option value="Ebonyi"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Ebonyi' ? 'selected' : '' }}>
                                            Ebonyi</option>
                                        <option value="Edo"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Edo' ? 'selected' : '' }}>
                                            Edo</option>
                                        <option value="Ekiti"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Ekiti' ? 'selected' : '' }}>
                                            Ekiti</option>
                                        <option value="Enugu"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Enugu' ? 'selected' : '' }}>
                                            Enugu</option>
                                        <option value="FCT"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'FCT' ? 'selected' : '' }}>
                                            FCT</option>
                                        <option value="Gombe"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Gombe' ? 'selected' : '' }}>
                                            Gombe</option>
                                        <option value="Imo"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Imo' ? 'selected' : '' }}>
                                            Imo</option>
                                        <option value="Jigawa"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Jigawa' ? 'selected' : '' }}>
                                            Jigawa</option>
                                        <option value="Kaduna"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Kaduna' ? 'selected' : '' }}>
                                            Kaduna</option>
                                        <option value="Kano"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Kano' ? 'selected' : '' }}>
                                            Kano</option>
                                        <option value="Katsina"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Katsina' ? 'selected' : '' }}>
                                            Katsina</option>
                                        <option value="Kebbi"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Kebbi' ? 'selected' : '' }}>
                                            Kebbi</option>
                                        <option value="Kogi"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Kogi' ? 'selected' : '' }}>
                                            Kogi</option>
                                        <option value="Kwara"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Kwara' ? 'selected' : '' }}>
                                            Kwara</option>
                                        <option value="Lagos"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Lagos' ? 'selected' : '' }}>
                                            Lagos</option>
                                        <option value="Nasarawa"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Nasarawa' ? 'selected' : '' }}>
                                            Nasarawa</option>
                                        <option value="Niger"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Niger' ? 'selected' : '' }}>
                                            Niger</option>
                                        <option value="Ogun"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Ogun' ? 'selected' : '' }}>
                                            Ogun</option>
                                        <option value="Ondo"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Ondo' ? 'selected' : '' }}>
                                            Ondo</option>
                                        <option value="Osun"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Osun' ? 'selected' : '' }}>
                                            Osun</option>
                                        <option value="Oyo"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Oyo' ? 'selected' : '' }}>
                                            Oyo</option>
                                        <option value="Plateau"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Plateau' ? 'selected' : '' }}>
                                            Plateau</option>
                                        <option value="Rivers"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Rivers' ? 'selected' : '' }}>
                                            Rivers</option>
                                        <option value="Sokoto"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Sokoto' ? 'selected' : '' }}>
                                            Sokoto</option>
                                        <option value="Taraba"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Taraba' ? 'selected' : '' }}>
                                            Taraba</option>
                                        <option value="Yobe"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Yobe' ? 'selected' : '' }}>
                                            Yobe</option>
                                        <option value="Zamfara"
                                            {{ old('state_of_origin', $student->state_of_origin ?? '') == 'Zamfara' ? 'selected' : '' }}>
                                            Zamfara</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">LGA</label>
                                    <input type="text" class="form-control" name="lga" id="lgaInput" value="{{ old('lga', $student->lga ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card custom-shadow rounded-3 bg-white border mt-4" id="academic-section">
                        <div class="card-header bg-transparent border-0">
                            <h6 class="fw-semibold mb-0"><i class="ri-school-line me-2 text-primary"></i>Academic
                                Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Class</label>
                                    <select id="classSelect" name="class_id" class="form-select">
                                        <option value="">Select class</option>
                                        @foreach ($classes as $c)
                                            <option value="{{ $c->id }}"
                                                {{ optional(optional($student->classArm)->schoolClass)->id == $c->id ? 'selected' : '' }}>
                                                {{ $c->name }} ({{ $c->level }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Class Arm <span class="text-danger">*</span></label>
                                    <select id="classArmSelect" name="class_arm_id" class="form-select" required>
                                        <option value="">Select arm</option>
                                        @foreach ($classArms as $arm)
                                            <option value="{{ $arm->id }}"
                                                data-class-id="{{ $arm->school_class_id }}"
                                                {{ $student->current_class_arm_id == $arm->id ? 'selected' : '' }}>
                                                {{ $arm->name }} @if ($arm->relationLoaded('schoolClass') || method_exists($arm, 'schoolClass'))
                                                    - {{ optional($arm->schoolClass)->name }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Academic Session <span class="text-danger">*</span></label>
                                    <select name="academic_session_id" class="form-select" required>
                                        <option value="">Select session</option>
                                        @foreach ($academicSessions as $sess)
                                            <option value="{{ $sess->id }}"
                                                {{ $student->academic_session_id == $sess->id ? 'selected' : '' }}>
                                                {{ $sess->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                                    <input type="date" name="admission_date" class="form-control"
                                        value="{{ old('admission_date', optional($student->admission_date)->format('Y-m-d')) }}"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" required>
                                        <option value="Active"
                                            {{ old('status', $student->status) === 'Active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="Inactive"
                                            {{ old('status', $student->status) === 'Inactive' ? 'selected' : '' }}>Inactive
                                        </option>
                                        <option value="Graduated"
                                            {{ old('status', $student->status) === 'Graduated' ? 'selected' : '' }}>
                                            Graduated</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card custom-shadow rounded-3 bg-white border mt-4" id="guardian-section">
                        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                            <h6 class="fw-semibold mb-0"><i class="ri-parent-line me-2 text-primary"></i>Guardian
                                Information</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addGuardian()">
                                <i class="ri-add-line me-1"></i>Add Guardian
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="guardiansContainer">
                                @forelse($student->parentsGuardians as $index => $guardian)
                                    <div class="guardian-row border rounded p-3 mb-3" data-index="{{ $index }}">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0">Guardian {{ $index + 1 }}</h6>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                        name="primary_guardian" value="{{ $index }}"
                                                        {{ $guardian->pivot->is_primary_contact ? 'checked' : '' }}>
                                                    <label class="form-check-label">Primary Contact</label>
                                                </div>
                                                @if ($index > 0)
                                                    <button type="button" class="btn btn-sm btn-outline-danger ms-2"
                                                        onclick="removeGuardian(this)">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <input type="hidden" name="guardians[{{ $index }}][id]"
                                                value="{{ $guardian->id }}">
                                            <div class="col-md-6">
                                                <label class="form-label">Full Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="guardians[{{ $index }}][full_name]"
                                                    class="form-control"
                                                    value="{{ old('guardians.' . $index . '.full_name', $guardian->full_name) }}"
                                                    required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Relationship <span
                                                        class="text-danger">*</span></label>
                                                <select name="guardians[{{ $index }}][relationship_to_student]"
                                                    class="form-select" required>
                                                    <option value="">Select relationship</option>
                                                    <option value="Father"
                                                        {{ old('guardians.' . $index . '.relationship_to_student', $guardian->relationship_to_student) === 'Father' ? 'selected' : '' }}>
                                                        Father</option>
                                                    <option value="Mother"
                                                        {{ old('guardians.' . $index . '.relationship_to_student', $guardian->relationship_to_student) === 'Mother' ? 'selected' : '' }}>
                                                        Mother</option>
                                                    <option value="Guardian"
                                                        {{ old('guardians.' . $index . '.relationship_to_student', $guardian->relationship_to_student) === 'Guardian' ? 'selected' : '' }}>
                                                        Guardian</option>
                                                    <option value="Uncle"
                                                        {{ old('guardians.' . $index . '.relationship_to_student', $guardian->relationship_to_student) === 'Uncle' ? 'selected' : '' }}>
                                                        Uncle</option>
                                                    <option value="Aunt"
                                                        {{ old('guardians.' . $index . '.relationship_to_student', $guardian->relationship_to_student) === 'Aunt' ? 'selected' : '' }}>
                                                        Aunt</option>
                                                    <option value="Grandparent"
                                                        {{ old('guardians.' . $index . '.relationship_to_student', $guardian->relationship_to_student) === 'Grandparent' ? 'selected' : '' }}>
                                                        Grandparent</option>
                                                    <option value="Other"
                                                        {{ old('guardians.' . $index . '.relationship_to_student', $guardian->relationship_to_student) === 'Other' ? 'selected' : '' }}>
                                                        Other</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Phone (Residence)</label>
                                                <input type="tel"
                                                    name="guardians[{{ $index }}][phone_residence]"
                                                    class="form-control"
                                                    value="{{ old('guardians.' . $index . '.phone_residence', $guardian->phone_residence) }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Phone (Office)</label>
                                                <input type="tel" name="guardians[{{ $index }}][phone_office]"
                                                    class="form-control"
                                                    value="{{ old('guardians.' . $index . '.phone_office', $guardian->phone_office) }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="guardians[{{ $index }}][email]"
                                                    class="form-control"
                                                    value="{{ old('guardians.' . $index . '.email', $guardian->email) }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Present Address</label>
                                                <textarea name="guardians[{{ $index }}][present_address]" class="form-control" rows="2">{{ old('guardians.' . $index . '.present_address', $guardian->present_address) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="guardian-row border rounded p-3 mb-3" data-index="0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0">Guardian 1</h6>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="primary_guardian"
                                                    value="0" checked>
                                                <label class="form-check-label">Primary Contact</label>
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Full Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="guardians[0][full_name]" class="form-control"
                                                    required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Relationship <span
                                                        class="text-danger">*</span></label>
                                                <select name="guardians[0][relationship_to_student]" class="form-select"
                                                    required>
                                                    <option value="">Select relationship</option>
                                                    <option value="Father">Father</option>
                                                    <option value="Mother">Mother</option>
                                                    <option value="Guardian">Guardian</option>
                                                    <option value="Uncle">Uncle</option>
                                                    <option value="Aunt">Aunt</option>
                                                    <option value="Grandparent">Grandparent</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Phone (Residence)</label>
                                                <input type="tel" name="guardians[0][phone_residence]"
                                                    class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Phone (Office)</label>
                                                <input type="tel" name="guardians[0][phone_office]"
                                                    class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="guardians[0][email]" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Present Address</label>
                                                <textarea name="guardians[0][present_address]" class="form-control" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card custom-shadow rounded-3 bg-white border">
                        <div class="card-header bg-transparent border-0">
                            <h6 class="fw-semibold mb-0"><i class="ri-image-line me-2 text-primary"></i>Profile Photo</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $photoUrl = $student->photo_path
                                    ? \Illuminate\Support\Facades\Storage::url($student->photo_path)
                                    : 'https://ui-avatars.com/api/?name=' .
                                        urlencode($student->full_name ?? 'Student') .
                                        '&background=4f46e5&color=fff&size=120&rounded=true';
                            @endphp
                            <div class="text-center mb-3">
                                <img id="photoPreview" src="{{ $photoUrl }}" class="rounded-circle" width="120"
                                    height="120" alt="Photo Preview">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Change Photo</label>
                                <input type="file" name="profile_picture" accept="image/*" class="form-control">
                                <div class="form-text">Max 2MB. JPG/PNG/GIF.</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit"
                            class="btn btn-primary btn-lg d-flex align-items-center gap-2 justify-content-center">
                            <i class="ri-save-3-line"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Preview selected profile image
        document.querySelector('input[name="profile_picture"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(evt) {
                document.getElementById('photoPreview').src = evt.target.result;
            };
            reader.readAsDataURL(file);
        });

        // Filter class arms by selected class (client-side)
        (function() {
            const classSelect = document.getElementById('classSelect');
            const armSelect = document.getElementById('classArmSelect');

            function filterArms() {
                const classId = classSelect.value;
                const options = Array.from(armSelect.options);
                armSelect.value = '';
                options.forEach(opt => {
                    if (!opt.value) return; // keep placeholder
                    const matches = !classId || opt.dataset.classId === classId;
                    opt.hidden = !matches;
                });
            }

            if (classSelect && armSelect) {
                filterArms();
                classSelect.addEventListener('change', filterArms);
            }
        })();

        // Guardian management functions
        let guardianIndex = {{ count($student->parentsGuardians) > 0 ? count($student->parentsGuardians) : 1 }};

        function addGuardian() {
            const container = document.getElementById('guardiansContainer');
            const newGuardian = document.createElement('div');
            newGuardian.className = 'guardian-row border rounded p-3 mb-3';
            newGuardian.setAttribute('data-index', guardianIndex);

            newGuardian.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Guardian ${guardianIndex + 1}</h6>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="primary_guardian" value="${guardianIndex}">
                        <label class="form-check-label">Primary Contact</label>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeGuardian(this)">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="guardians[${guardianIndex}][full_name]" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Relationship <span class="text-danger">*</span></label>
                    <select name="guardians[${guardianIndex}][relationship_to_student]" class="form-select" required>
                        <option value="">Select relationship</option>
                        <option value="Father">Father</option>
                        <option value="Mother">Mother</option>
                        <option value="Guardian">Guardian</option>
                        <option value="Uncle">Uncle</option>
                        <option value="Aunt">Aunt</option>
                        <option value="Grandparent">Grandparent</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone (Residence)</label>
                    <input type="tel" name="guardians[${guardianIndex}][phone_residence]" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone (Office)</label>
                    <input type="tel" name="guardians[${guardianIndex}][phone_office]" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="guardians[${guardianIndex}][email]" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Present Address</label>
                    <textarea name="guardians[${guardianIndex}][present_address]" class="form-control" rows="2"></textarea>
                </div>
            </div>
        `;

            container.appendChild(newGuardian);
            guardianIndex++;
            updateGuardianNumbers();
        }

        function removeGuardian(button) {
            const guardianRow = button.closest('.guardian-row');
            guardianRow.remove();
            updateGuardianNumbers();
        }

        function updateGuardianNumbers() {
            const guardianRows = document.querySelectorAll('.guardian-row');
            guardianRows.forEach((row, index) => {
                const title = row.querySelector('h6');
                title.textContent = `Guardian ${index + 1}`;
            });
        }
        });

        // Handle section scrolling from overview page
        document.addEventListener('DOMContentLoaded', function() {
            const scrollToSection = sessionStorage.getItem('scrollToSection');
            if (scrollToSection) {
                setTimeout(() => {
                    const element = document.getElementById(scrollToSection);
                    if (element) {
                        element.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        // Add a subtle highlight effect
                        element.style.boxShadow = '0 0 0 3px rgba(79, 70, 229, 0.2)';
                        setTimeout(() => {
                            element.style.boxShadow = '';
                        }, 3000);
                    }
                    sessionStorage.removeItem('scrollToSection');
                }, 500);
            }
        });

        // Load states directly without API call for student page
        const stateSelect = document.getElementById('stateSelect');
        const lgaInput = document.getElementById('lgaInput');
        const savedState = @json(old('state_of_origin', $student->state_of_origin ?? ''));
        
        // Since we already have states in the HTML dropdown, we just need to update the LGA input placeholder
        
        // Add an event listener to update LGA input placeholder
        stateSelect.addEventListener('change', function() {
            if (this.value) {
                lgaInput.placeholder = 'Enter LGA for ' + this.value;
            } else {
                lgaInput.placeholder = 'Select a state first';
            }
        });
        
        // Set initial placeholder
        if (stateSelect.value) {
            lgaInput.placeholder = 'Enter LGA for ' + stateSelect.value;
        } else if (savedState) {
            lgaInput.placeholder = 'Enter LGA for ' + savedState;
        }
    </script>
@endpush
