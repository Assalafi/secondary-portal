@extends('layouts.admin')

@section('title', 'Promote/Transfer Students - Class Selection')

@section('content')
<div class="main-content-container overflow-hidden">
    <!-- Page Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.students.promote.index') }}" class="btn btn-outline-secondary me-3 d-flex align-items-center gap-2">
                    <i class="ri-arrow-left-line"></i>
                    Back to Classes
                </a>
                <div>
                    <h3 class="fs-20 fw-semibold mb-1">Promote/Transfer Students</h3>
                    <p class="text-secondary mb-0">{{ $classArm->schoolClass->name ?? 'Class' }} {{ $classArm->name ?? '' }} - Academic Session {{ $classArm->students->first()->academicSession->name ?? 'Not Set' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="d-flex gap-2 justify-content-md-end">
                <button class="btn btn-outline-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#transferModal">
                    <i class="ri-exchange-line"></i>
                    Transfer Selected
                </button>
                <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#promoteModal">
                    <i class="ri-arrow-up-line"></i>
                    Promote Selected
                </button>
            </div>
        </div>
    </div>

    <!-- Selection Summary -->
    @php
        $totalStudents = $students->count();
        $eligibleStudents = $students->count(); // For now, all are eligible - could be based on scores
        $pendingReview = 0; // Could be calculated based on academic performance
        $eligibleRate = $totalStudents > 0 ? round(($eligibleStudents / $totalStudents) * 100, 1) : 0;
    @endphp
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 48px; height: 48px;">
                                <i class="ri-group-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">{{ $totalStudents }}</h6>
                            <p class="text-secondary mb-0 small">Total Students</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 48px; height: 48px;">
                                <i class="ri-checkbox-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold" id="selectedCount">{{ $totalStudents }}</h6>
                            <p class="text-secondary mb-0 small">Selected</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle" style="width: 48px; height: 48px;">
                                <i class="ri-alert-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">{{ $pendingReview }}</h6>
                            <p class="text-secondary mb-0 small">Pending Review</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="d-flex align-items-center justify-content-center bg-info-subtle text-info rounded-circle" style="width: 48px; height: 48px;">
                                <i class="ri-percent-line fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 fw-semibold">{{ $eligibleRate }}%</h6>
                            <p class="text-secondary mb-0 small">Eligible Rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card custom-shadow rounded-3 bg-white border">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <h6 class="fw-semibold mb-0">
                    <i class="ri-list-check-line me-2 text-primary"></i>{{ $classArm->schoolClass->name ?? 'Class' }} {{ $classArm->name ?? '' }} Students
                </h6>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAll" checked>
                    <label class="form-check-label fw-medium" for="selectAll">
                        Select All
                    </label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="ri-filter-line me-1"></i>Filter
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">All Students</a></li>
                        <li><a class="dropdown-item" href="#">Eligible Only</a></li>
                        <li><a class="dropdown-item" href="#">Pending Review</a></li>
                        <li><a class="dropdown-item" href="#">Male Students</a></li>
                        <li><a class="dropdown-item" href="#">Female Students</a></li>
                    </ul>
                </div>
                <button class="btn btn-sm btn-outline-primary" onclick="exportStudents()">
                    <i class="ri-download-line me-1"></i>Export
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold" style="width: 50px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAllTable">
                                </div>
                            </th>
                            <th class="fw-semibold">#</th>
                            <th class="fw-semibold">Student Name</th>
                            <th class="fw-semibold">Admission No.</th>
                            <th class="fw-semibold">Gender</th>
                            <th class="fw-semibold">Academic Status</th>
                            <th class="fw-semibold">Eligibility</th>
                            <th class="fw-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $student)
                            @php
                                $initials = collect(explode(' ', $student->full_name ?? ''))
                                    ->map(fn($name) => strtoupper(substr($name, 0, 1)))
                                    ->take(2)
                                    ->join('');
                                $genderBadge = strtolower($student->gender) === 'male' ? 'bg-primary-subtle text-primary' : 'bg-danger-subtle text-danger';
                                $avgScore = $student->scores->isNotEmpty() ? $student->scores->avg('total') : 0;
                                $statusBadge = $avgScore >= 70 ? 'bg-success-subtle text-success' : ($avgScore >= 50 ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger');
                                $statusText = $avgScore >= 70 ? 'Excellent' : ($avgScore >= 50 ? 'Good' : 'Needs Improvement');
                                $eligibleBadge = $avgScore >= 50 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning';
                                $eligibleText = $avgScore >= 50 ? 'Eligible' : 'Review';
                            @endphp
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input student-checkbox" type="checkbox" checked value="{{ $student->id }}">
                                    </div>
                                </td>
                                <td class="fw-medium">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                {{ $initials }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-medium">{{ $student->full_name ?? '-' }}</h6>
                                            <small class="text-muted">{{ $classArm->schoolClass->name ?? '' }} {{ $classArm->name ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-medium">{{ $student->admission_no ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $genderBadge }}">{{ ucfirst($student->gender ?? '-') }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $statusBadge }}">
                                        <i class="ri-check-line me-1"></i>{{ $statusText }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $eligibleBadge }}">
                                        <i class="{{ $avgScore >= 50 ? 'ri-check-circle-line' : 'ri-time-line' }} me-1"></i>{{ $eligibleText }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="ri-more-2-line"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('admin.students.profile.overview', $student->id) }}"><i class="ri-eye-line me-2"></i>View Profile</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="removeFromSelection({{ $student->id }})"><i class="ri-close-line me-2"></i>Remove from Selection</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-secondary py-4">No students found in this class.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Promote Modal -->
<div class="modal fade" id="promoteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-3">
                        <div class="avatar-title bg-primary text-white rounded-circle">
                            <i class="ri-arrow-up-line"></i>
                        </div>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fw-semibold">Promote Students</h5>
                        <small class="text-muted">Promote selected students to next class</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info d-flex align-items-center mb-4">
                    <i class="ri-information-line me-2"></i>
                    <span id="promoteAlert"><strong>{{ $totalStudents }} students</strong> selected for promotion from <strong>{{ $classArm->schoolClass->name ?? 'Class' }} {{ $classArm->name ?? '' }}</strong></span>
                </div>

                <form id="promoteForm" action="{{ route('admin.students.promote.execute') }}" method="POST">
                    @csrf
                    <input type="hidden" name="student_ids" id="promoteStudentIds">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-medium">
                                    <i class="ri-school-line me-2 text-primary"></i>Promote to Class
                                </label>
                                <select class="form-select" name="target_class_arm_id" required>
                                    <option value="">Select Class</option>
                                    @foreach($schoolClasses as $schoolClass)
                                        @foreach($schoolClass->classArms as $arm)
                                            <option value="{{ $arm->id }}">{{ $schoolClass->name }} {{ $arm->name }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-medium">
                                    <i class="ri-calendar-line me-2 text-primary"></i>Academic Session
                                </label>
                                <select class="form-select" name="academic_session_id" required>
                                    <option value="">Select Session</option>
                                    @foreach($academicSessions as $session)
                                        <option value="{{ $session->id }}" {{ $session->is_current ? 'selected' : '' }}>{{ $session->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">
                            <i class="ri-calendar-event-line me-2 text-primary"></i>Term
                        </label>
                        <select class="form-select" name="term_id" required>
                            <option value="">Select Term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ $term->is_current ? 'selected' : '' }}>{{ $term->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">
                            <i class="ri-file-text-line me-2 text-primary"></i>Promotion Notes
                        </label>
                        <textarea class="form-control" rows="3" name="notes" placeholder="Enter any notes about this promotion (optional)"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="submitPromotion()">
                    <i class="ri-arrow-up-line me-1"></i>Promote Students
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info-subtle">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-3">
                        <div class="avatar-title bg-info text-white rounded-circle">
                            <i class="ri-exchange-line"></i>
                        </div>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fw-semibold">Transfer Students</h5>
                        <small class="text-muted">Transfer selected students to different class</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning d-flex align-items-center mb-4">
                    <i class="ri-alert-line me-2"></i>
                    <span id="transferAlert"><strong>{{ $totalStudents }} students</strong> selected for transfer from <strong>{{ $classArm->schoolClass->name ?? 'Class' }} {{ $classArm->name ?? '' }}</strong></span>
                </div>

                <form id="transferForm" action="{{ route('admin.students.transfer.execute') }}" method="POST">
                    @csrf
                    <input type="hidden" name="student_ids" id="transferStudentIds">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-medium">
                                    <i class="ri-school-line me-2 text-info"></i>Transfer to Class
                                </label>
                                <select class="form-select" name="target_class_arm_id" required>
                                    <option value="">Select Class</option>
                                    @foreach($schoolClasses as $schoolClass)
                                        @foreach($schoolClass->classArms as $arm)
                                            @if($arm->id !== $classArm->id)
                                                <option value="{{ $arm->id }}">{{ $schoolClass->name }} {{ $arm->name }}</option>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-medium">
                                    <i class="ri-calendar-line me-2 text-info"></i>Effective Date
                                </label>
                                <input type="date" class="form-control" name="effective_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">
                            <i class="ri-file-text-line me-2 text-info"></i>Reason for Transfer <span class="text-danger">*</span>
                        </label>
                        <select class="form-select mb-2" name="reason" required>
                            <option value="">Select Reason</option>
                            <option value="academic">Academic Performance</option>
                            <option value="behavioral">Behavioral Issues</option>
                            <option value="parent_request">Parent Request</option>
                            <option value="medical">Medical Reasons</option>
                            <option value="other">Other</option>
                        </select>
                        <textarea class="form-control" rows="3" name="reason_details" placeholder="Provide additional details about the transfer reason"></textarea>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="notify_parents" id="notifyParents">
                        <label class="form-check-label" for="notifyParents">
                            Notify parents/guardians about this transfer
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="submitTransfer()">
                    <i class="ri-exchange-line me-1"></i>Transfer Students
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const className = "{{ $classArm->schoolClass->name ?? 'Class' }} {{ $classArm->name ?? '' }}";

document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectAllTableCheckbox = document.getElementById('selectAllTable');

    // Sync both select all checkboxes
    if (selectAllCheckbox && selectAllTableCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            selectAllTableCheckbox.checked = this.checked;
            toggleAllStudents(this.checked);
        });

        selectAllTableCheckbox.addEventListener('change', function() {
            selectAllCheckbox.checked = this.checked;
            toggleAllStudents(this.checked);
        });
    }

    // Individual checkbox functionality
    document.querySelectorAll('.student-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedCount();
            updateSelectAllState();
        });
    });

    function toggleAllStudents(checked) {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = checked;
        });
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.student-checkbox:checked').length;
        const selectedCountElement = document.getElementById('selectedCount');
        if (selectedCountElement) {
            selectedCountElement.textContent = selectedCount;
        }

        // Update modal alerts
        const promoteAlert = document.getElementById('promoteAlert');
        const transferAlert = document.getElementById('transferAlert');
        if (promoteAlert) {
            promoteAlert.innerHTML = `<strong>${selectedCount} students</strong> selected for promotion from <strong>${className}</strong>`;
        }
        if (transferAlert) {
            transferAlert.innerHTML = `<strong>${selectedCount} students</strong> selected for transfer from <strong>${className}</strong>`;
        }
    }

    function updateSelectAllState() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
        const selectAll = document.getElementById('selectAll');
        const selectAllTable = document.getElementById('selectAllTable');

        if (checkedCount === 0) {
            if (selectAll) {
                selectAll.indeterminate = false;
                selectAll.checked = false;
            }
            if (selectAllTable) {
                selectAllTable.indeterminate = false;
                selectAllTable.checked = false;
            }
        } else if (checkedCount === checkboxes.length) {
            if (selectAll) {
                selectAll.indeterminate = false;
                selectAll.checked = true;
            }
            if (selectAllTable) {
                selectAllTable.indeterminate = false;
                selectAllTable.checked = true;
            }
        } else {
            if (selectAll) {
                selectAll.indeterminate = true;
            }
            if (selectAllTable) {
                selectAllTable.indeterminate = true;
            }
        }
    }

    // Initialize counts
    updateSelectedCount();
    updateSelectAllState();
});

function submitPromotion() {
    const selectedStudents = [];
    document.querySelectorAll('.student-checkbox:checked').forEach(checkbox => {
        selectedStudents.push(checkbox.value);
    });

    if (selectedStudents.length === 0) {
        alert('Please select at least one student to promote.');
        return;
    }

    document.getElementById('promoteStudentIds').value = JSON.stringify(selectedStudents);
    document.getElementById('promoteForm').submit();
}

function submitTransfer() {
    const selectedStudents = [];
    document.querySelectorAll('.student-checkbox:checked').forEach(checkbox => {
        selectedStudents.push(checkbox.value);
    });

    if (selectedStudents.length === 0) {
        alert('Please select at least one student to transfer.');
        return;
    }

    document.getElementById('transferStudentIds').value = JSON.stringify(selectedStudents);
    document.getElementById('transferForm').submit();
}

function removeFromSelection(studentId) {
    const checkbox = document.querySelector(`.student-checkbox[value="${studentId}"]`);
    if (checkbox) {
        checkbox.checked = false;
        updateSelectedCount();
        updateSelectAllState();
    }
}

function exportStudents() {
    const students = [];
    document.querySelectorAll('.student-checkbox:checked').forEach(checkbox => {
        const row = checkbox.closest('tr');
        const name = row.querySelector('td:nth-child(3) h6').textContent;
        const admissionNo = row.querySelector('td:nth-child(4)').textContent;
        const gender = row.querySelector('td:nth-child(5) span').textContent;
        const status = row.querySelector('td:nth-child(6) span').textContent;
        const eligibility = row.querySelector('td:nth-child(7) span').textContent;

        students.push({
            name: name,
            admission_no: admissionNo,
            gender: gender,
            academic_status: status,
            eligibility: eligibility
        });
    });

    if (students.length === 0) {
        alert('Please select at least one student to export.');
        return;
    }

    let csvContent = '"NAME","ADMISSION NO","GENDER","ACADEMIC STATUS","ELIGIBILITY"\n';
    students.forEach(s => {
        csvContent += `"${s.name}","${s.admission_no}","${s.gender}","${s.academic_status}","${s.eligibility}"\n`;
    });

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `promotion_list_${className.replace(/\s+/g, '_')}.csv`;
    link.click();
}
</script>
@endsection
