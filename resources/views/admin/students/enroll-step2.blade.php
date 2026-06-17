@extends('layouts.admin')

@section('title', 'Enroll New Student - Step 2')

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
                <div class="step active">2</div>
                <div class="bar"></div>
                <div class="step">3</div>
                <div class="bar"></div>
                <div class="step">4</div>
            </div>
        </div>

        <div class="soft-card p-4 mb-4">
            <h5 class="fw-bold text-dark mb-3">Academic Placement</h5>
            <form method="POST" action="{{ route('admin.students.enroll.step3') }}" id="step2-form">
                @csrf
                <input type="hidden" name="class_id" value="{{ old('class_id', $step2Data['class_id'] ?? '') }}">
                <input type="hidden" name="class_arm_id"
                    value="{{ old('class_arm_id', $step2Data['class_arm_id'] ?? '') }}">
                <input type="hidden" name="class_group" value="{{ old('class_group', $step2Data['class_group'] ?? '') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-dark">Academic Level <span class="text-danger">*</span></label>
                        <select class="form-select input-soft" name="academic_level" required>
                            <option value="">Select academic level</option>
                            <option value="JSS"
                                {{ old('academic_level', $step2Data['academic_level'] ?? 'JSS') == 'JSS' ? 'selected' : '' }}>
                                Junior Secondary School (JSS)</option>
                            <option value="SS"
                                {{ old('academic_level', $step2Data['academic_level'] ?? '') == 'SS' ? 'selected' : '' }}>
                                Senior Secondary School (SS)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-dark">Class <span class="text-danger">*</span></label>
                        <select class="form-select input-soft" name="class" required>
                            <option value="">Select class</option>
                            @php $selClass = old('class', $step2Data['class'] ?? ''); @endphp
                            @if ($selClass)
                                <option value="{{ $selClass }}" selected>{{ $selClass }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-dark">Class Group</label>
                        <input type="text" class="form-control input-soft" name="class_group_display"
                            value="{{ old('class_group', $step2Data['class_group'] ?? '') }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-dark">Class Arm <span class="text-danger">*</span></label>
                        <select class="form-select input-soft" name="class_arm" required>
                            <option value="">Select class arm</option>
                            @php $selArm = old('class_arm', $step2Data['class_arm'] ?? ''); @endphp
                            @if ($selArm)
                                <option value="{{ $selArm }}" selected>{{ $selArm }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-dark">Session <span class="text-danger">*</span></label>
                        <div class="mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="session" value="Morning" id="morning"
                                    {{ old('session', $step2Data['session'] ?? 'Morning') == 'Morning' ? 'checked' : '' }}
                                    required>
                                <label class="form-check-label" for="morning">Morning</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="session" value="Afternoon"
                                    id="afternoon"
                                    {{ old('session', $step2Data['session'] ?? '') == 'Afternoon' ? 'checked' : '' }}
                                    required>
                                <label class="form-check-label" for="afternoon">Afternoon</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-dark">Academic Session <span class="text-danger">*</span></label>
                        <select class="form-select input-soft" name="academic_session" required>
                            <option value="">Select academic session</option>
                            @php $sel = old('academic_session', $step2Data['academic_session'] ?? null); @endphp
                            @if (isset($academicSessions) && count($academicSessions))
                                @foreach ($academicSessions as $sess)
                                    <option value="{{ $sess->name }}"
                                        {{ $sel === $sess->name || (!$sel && ($sess->is_current ?? false)) ? 'selected' : '' }}>
                                        {{ $sess->name }}</option>
                                @endforeach
                            @else
                                <option value="2023/2024" {{ $sel == '2023/2024' ? 'selected' : '' }}>2023/2024</option>
                                <option value="2024/2025" {{ $sel == '2024/2025' ? 'selected' : '' }}>2024/2025</option>
                                <option value="2025/2026" {{ $sel == '2025/2026' ? 'selected' : '' }}>2025/2026</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-dark">Admission Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control input-soft" name="admission_number"
                                value="{{ old('admission_number', $step2Data['admission_number'] ?? 'STU' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)) }}"
                                required>
                            <button class="btn btn-soft" type="button" onclick="generateAdmissionNumber()">
                                <i class="ri-refresh-line"></i>
                            </button>
                        </div>
                        <small class="text-secondary">Auto-generated admission number</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-dark">Enrollment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control input-soft" name="enrollment_date"
                            value="{{ old('enrollment_date', $step2Data['enrollment_date'] ?? date('Y-m-d')) }}" required>
                    </div>
                </div>
            </form>
            <div class="d-flex justify-content-start align-items-center gap-2 mt-4">
                <a href="{{ route('admin.students.enroll.step1') }}" class="btn btn-soft">Previous</a>
                <button type="submit" class="btn btn-pill-dark" form="step2-form">Next</button>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .breadcrumb-soft .breadcrumb-item+.breadcrumb-item::before {
                color: #9ca3af;
            }

            .breadcrumb-soft a {
                color: #6b7280;
                text-decoration: none;
            }

            .breadcrumb-soft .active {
                color: #9ca3af;
            }

            .soft-card {
                background: #f7f7f8;
                border: 0;
                border-radius: 18px;
            }

            .input-soft {
                background: #f7f7f8;
                border: 0;
                height: 52px;
                border-radius: 12px;
            }

            .input-soft:focus {
                background: #fff;
                box-shadow: 0 0 0 .25rem rgba(17, 24, 39, .06);
            }

            .btn-pill-dark {
                background: #111827;
                color: #fff;
                border: 0;
                border-radius: 9999px;
                padding: .6rem 1.1rem;
                font-weight: 600;
            }

            .btn-pill-dark:hover {
                background: #0b1220;
                color: #fff;
            }

            .btn-soft {
                background: #f1f1f1;
                color: #111827;
                border: 0;
                border-radius: 9999px;
                padding: .6rem 1.1rem;
                font-weight: 600;
            }

            .stepper {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .stepper .step {
                width: 36px;
                height: 36px;
                border-radius: 9999px;
                background: #e5e7eb;
                color: #111827;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
            }

            .stepper .step.active {
                background: #111827;
                color: #fff;
            }

            .stepper .step.done {
                background: #9ca3af;
                color: #fff;
            }

            .stepper .bar {
                flex: 1;
                height: 2px;
                background: #e5e7eb;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function generateAdmissionNumber() {
                const year = new Date().getFullYear();
                const randomNum = Math.floor(Math.random() * 9999) + 1;
                const admissionNumber = `STU${year}${randomNum.toString().padStart(4, '0')}`;
                document.querySelector('input[name="admission_number"]').value = admissionNumber;
            }

            // Dynamic Level -> Class -> Arm
            @php
                $selLevel = old('academic_level', isset($step2Data['academic_level']) ? $step2Data['academic_level'] : 'JSS');
                $selClassName = old('class', isset($step2Data['class']) ? $step2Data['class'] : '');
                $selArmVal = old('class_arm', isset($step2Data['class_arm']) ? $step2Data['class_arm'] : '');
                $selClassId = old('class_id', isset($step2Data['class_id']) ? $step2Data['class_id'] : '');
                $selArmId = old('class_arm_id', isset($step2Data['class_arm_id']) ? $step2Data['class_arm_id'] : '');
            @endphp
            document.addEventListener('DOMContentLoaded', function() {
                const classesAll = @json($classesLight);
                const levelSel = document.querySelector('select[name="academic_level"]');
                const classSel = document.querySelector('select[name="class"]');
                const armSel = document.querySelector('select[name="class_arm"]');
                const classIdInput = document.querySelector('input[name="class_id"]');
                const classArmIdInput = document.querySelector('input[name="class_arm_id"]');
                const classGroupHidden = document.querySelector('input[name="class_group"]');
                const classGroupDisplay = document.querySelector('input[name="class_group_display"]');

                const selectedLevel = @json($selLevel);
                const selectedClassName = @json($selClassName);
                const selectedArmVal = @json($selArmVal);
                const selectedClassId = @json($selClassId);
                const selectedArmId = @json($selArmId);

                function populateClasses(level) {
                    const options = [{
                        value: '',
                        text: 'Select class'
                    }];
                    classesAll.filter(c => c.level === level).forEach(c => {
                        options.push({
                            value: c.name,
                            text: c.name,
                            id: c.id
                        });
                    });
                    classSel.innerHTML = '';
                    options.forEach(opt => {
                        const o = document.createElement('option');
                        o.value = opt.value;
                        o.textContent = opt.text;
                        classSel.appendChild(o);
                        if (opt.value === selectedClassName) {
                            classSel.value = opt.value;
                            classIdInput.value = opt.id || '';
                        }
                    });
                }

                async function loadArmsForSelectedClass() {
                    armSel.innerHTML = '';
                    const defaultOpt = document.createElement('option');
                    defaultOpt.value = '';
                    defaultOpt.textContent = 'Select class arm';
                    armSel.appendChild(defaultOpt);

                    const className = classSel.value;
                    const cls = classesAll.find(c => c.name === className);
                    // set class group display/hidden
                    const grp = cls && cls.group ? cls.group : '';
                    if (classGroupHidden) classGroupHidden.value = grp || '';
                    if (classGroupDisplay) classGroupDisplay.value = grp || '';
                    const classId = cls ? cls.id : (classIdInput.value || selectedClassId);
                    classIdInput.value = classId || '';

                    if (!classId) return;

                    try {
                        const url = @json(route('admin.get-class-arms')) + '?class_id=' + encodeURIComponent(classId);
                        const res = await fetch(url);
                        const data = await res.json();
                        if (Array.isArray(data)) {
                            data.forEach(a => {
                                const o = document.createElement('option');
                                o.value = a.name; // keep name for review
                                o.textContent = a.name;
                                o.dataset.id = a.id;
                                armSel.appendChild(o);
                            });
                            // Preselect by id or name
                            if (selectedArmId) {
                                const matchById = [...armSel.options].find(op => op.dataset.id == selectedArmId);
                                if (matchById) {
                                    armSel.value = matchById.value;
                                    classArmIdInput.value = selectedArmId;
                                }
                            }
                            if (!armSel.value && selectedArmVal) {
                                armSel.value = selectedArmVal;
                                const matchByName = [...armSel.options].find(op => op.value === selectedArmVal);
                                if (matchByName) classArmIdInput.value = matchByName.dataset.id || '';
                            }
                        }
                    } catch (e) {
                        console.error('Failed to load class arms', e);
                    }
                }

                // Events
                levelSel.addEventListener('change', () => {
                    populateClasses(levelSel.value);
                    classArmIdInput.value = '';
                    armSel.innerHTML = '<option value="">Select class arm</option>';
                });

                classSel.addEventListener('change', () => {
                    classArmIdInput.value = '';
                    loadArmsForSelectedClass();
                });

                armSel.addEventListener('change', () => {
                    const opt = armSel.options[armSel.selectedIndex];
                    classArmIdInput.value = opt ? (opt.dataset.id || '') : '';
                });

                // Init
                if (selectedLevel) levelSel.value = selectedLevel;
                populateClasses(levelSel.value || selectedLevel);
                loadArmsForSelectedClass();
                // Initialize class group from preselected class if present
                const initCls = classesAll.find(c => c.name === selectedClassName);
                if (initCls) {
                    const grp = initCls.group || '';
                    if (classGroupHidden) classGroupHidden.value = grp;
                    if (classGroupDisplay) classGroupDisplay.value = grp;
                }
            });
        </script>
    @endpush
@endsection
