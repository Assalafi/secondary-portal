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
                    <p class="text-secondary mb-0">JSS 3A - Academic Session 2024/2025</p>
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
                            <h6 class="mb-0 fw-semibold">27</h6>
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
                            <h6 class="mb-0 fw-semibold" id="selectedCount">25</h6>
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
                            <h6 class="mb-0 fw-semibold">2</h6>
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
                            <h6 class="mb-0 fw-semibold">92.6%</h6>
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
                    <i class="ri-list-check-line me-2 text-primary"></i>JSS 3A Students
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
                <button class="btn btn-sm btn-outline-primary">
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
                        <!-- Student Rows -->
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input student-checkbox" type="checkbox" checked>
                                </div>
                            </td>
                            <td class="fw-medium">1</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3">
                                        <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                            JD
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-medium">John Doe</h6>
                                        <small class="text-muted">JSS 3A</small>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-medium">ADM/2023/001</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary">Male</span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success">
                                    <i class="ri-check-line me-1"></i>Excellent
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success">
                                    <i class="ri-check-circle-line me-1"></i>Eligible
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="ri-more-2-line"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="ri-eye-line me-2"></i>View Profile</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="ri-close-line me-2"></i>Remove from Selection</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input student-checkbox" type="checkbox" checked>
                                </div>
                            </td>
                            <td class="fw-medium">2</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3">
                                        <div class="avatar-title bg-info-subtle text-info rounded-circle">
                                            MD
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-medium">Mary Doe</h6>
                                        <small class="text-muted">JSS 3A</small>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-medium">ADM/2023/002</td>
                            <td>
                                <span class="badge bg-danger-subtle text-danger">Female</span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success">
                                    <i class="ri-check-line me-1"></i>Very Good
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success">
                                    <i class="ri-check-circle-line me-1"></i>Eligible
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="ri-more-2-line"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="ri-eye-line me-2"></i>View Profile</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="ri-close-line me-2"></i>Remove from Selection</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input student-checkbox" type="checkbox" checked>
                                </div>
                            </td>
                            <td class="fw-medium">3</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3">
                                        <div class="avatar-title bg-success-subtle text-success rounded-circle">
                                            AI
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-medium">Ahmed Ibrahim</h6>
                                        <small class="text-muted">JSS 3A</small>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-medium">ADM/2023/003</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary">Male</span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success">
                                    <i class="ri-check-line me-1"></i>Good
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success">
                                    <i class="ri-check-circle-line me-1"></i>Eligible
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="ri-more-2-line"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="ri-eye-line me-2"></i>View Profile</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="ri-close-line me-2"></i>Remove from Selection</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input student-checkbox" type="checkbox" checked>
                                </div>
                            </td>
                            <td class="fw-medium">4</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3">
                                        <div class="avatar-title bg-warning-subtle text-warning rounded-circle">
                                            FA
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-medium">Fatima Aliyu</h6>
                                        <small class="text-muted">JSS 3A</small>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-medium">ADM/2023/004</td>
                            <td>
                                <span class="badge bg-danger-subtle text-danger">Female</span>
                            </td>
                            <td>
                                <span class="badge bg-warning-subtle text-warning">
                                    <i class="ri-alert-line me-1"></i>Average
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-warning-subtle text-warning">
                                    <i class="ri-time-line me-1"></i>Review
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="ri-more-2-line"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="ri-eye-line me-2"></i>View Profile</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="ri-close-line me-2"></i>Remove from Selection</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input student-checkbox" type="checkbox" checked>
                                </div>
                            </td>
                            <td class="fw-medium">5</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3">
                                        <div class="avatar-title bg-secondary-subtle text-secondary rounded-circle">
                                            SJ
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-medium">Samuel Johnson</h6>
                                        <small class="text-muted">JSS 3A</small>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-medium">ADM/2023/005</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary">Male</span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success">
                                    <i class="ri-check-line me-1"></i>Very Good
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success">
                                    <i class="ri-check-circle-line me-1"></i>Eligible
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="ri-more-2-line"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="ri-eye-line me-2"></i>View Profile</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="ri-close-line me-2"></i>Remove from Selection</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
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
                    <span><strong>25 students</strong> selected for promotion from <strong>JSS 3A</strong></span>
                </div>
                
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-medium">
                                    <i class="ri-school-line me-2 text-primary"></i>Promote to Class
                                </label>
                                <select class="form-select">
                                    <option value="">Select Class</option>
                                    <option value="ss1a">SS 1A</option>
                                    <option value="ss1b">SS 1B</option>
                                    <option value="ss1c">SS 1C</option>
                                    <option value="ss1d">SS 1D</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-medium">
                                    <i class="ri-calendar-line me-2 text-primary"></i>Academic Session
                                </label>
                                <select class="form-select">
                                    <option value="2024/2025">2024/2025</option>
                                    <option value="2025/2026">2025/2026</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">
                            <i class="ri-calendar-event-line me-2 text-primary"></i>Term
                        </label>
                        <select class="form-select">
                            <option value="first">First Term</option>
                            <option value="second">Second Term</option>
                            <option value="third">Third Term</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">
                            <i class="ri-file-text-line me-2 text-primary"></i>Promotion Notes
                        </label>
                        <textarea class="form-control" rows="3" placeholder="Enter any notes about this promotion (optional)"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary">
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
                    <span><strong>25 students</strong> selected for transfer from <strong>JSS 3A</strong></span>
                </div>
                
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-medium">
                                    <i class="ri-school-line me-2 text-info"></i>Transfer to Class
                                </label>
                                <select class="form-select">
                                    <option value="">Select Class</option>
                                    <option value="jss3b">JSS 3B</option>
                                    <option value="jss3c">JSS 3C</option>
                                    <option value="jss3d">JSS 3D</option>
                                    <option value="jss2a">JSS 2A</option>
                                    <option value="jss2b">JSS 2B</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-medium">
                                    <i class="ri-calendar-line me-2 text-info"></i>Effective Date
                                </label>
                                <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">
                            <i class="ri-file-text-line me-2 text-info"></i>Reason for Transfer <span class="text-danger">*</span>
                        </label>
                        <select class="form-select mb-2">
                            <option value="">Select Reason</option>
                            <option value="academic">Academic Performance</option>
                            <option value="behavioral">Behavioral Issues</option>
                            <option value="parent_request">Parent Request</option>
                            <option value="medical">Medical Reasons</option>
                            <option value="other">Other</option>
                        </select>
                        <textarea class="form-control" rows="3" placeholder="Provide additional details about the transfer reason"></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notifyParents">
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
                <button type="button" class="btn btn-outline-primary">
                    <i class="ri-exchange-line me-1"></i>Transfer Students
                </button>
            </div>
        </div>
    </div>
</div>

<script>
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
        const promoteAlert = document.querySelector('#promoteModal .alert span');
        const transferAlert = document.querySelector('#transferModal .alert span');
        if (promoteAlert) {
            promoteAlert.innerHTML = `<strong>${selectedCount} students</strong> selected for promotion from <strong>JSS 3A</strong>`;
        }
        if (transferAlert) {
            transferAlert.innerHTML = `<strong>${selectedCount} students</strong> selected for transfer from <strong>JSS 3A</strong>`;
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
</script>
@endsection
