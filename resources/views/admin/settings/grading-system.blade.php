@extends('layouts.admin')

@section('title', 'Grading System')

@section('content')
    <div class="container-fluid">
        <!-- Page Title & Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
                            <li class="breadcrumb-item active">Grading System</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Grading System</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm">
                                        <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                            <i class="ri-bar-chart-line"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">Grading System</h5>
                                    <p class="card-title-desc mb-0">Configure grade levels, score ranges, and GPA settings</p>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGradeModal">
                                <i class="ri-add-line me-1"></i>Add Grade
                            </button>
                        </div>
                    </div>
                    
                    <div class="card-body">
                            <!-- Level Tabs -->
                            <ul class="nav nav-pills nav-justified mb-4" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-bs-toggle="pill" href="#nursery-tab" role="tab">
                                        <i class="ri-home-smile-line d-block check-nav-icon mt-4 mb-2"></i>
                                        <p class="fw-normal mb-0">Nursery</p>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="pill" href="#primary-tab" role="tab">
                                        <i class="ri-graduation-cap-line d-block check-nav-icon mt-4 mb-2"></i>
                                        <p class="fw-normal mb-0">Primary</p>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="pill" href="#jss-tab" role="tab">
                                        <i class="ri-book-open-line d-block check-nav-icon mt-4 mb-2"></i>
                                        <p class="fw-normal mb-0">JSS</p>
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="pill" href="#ss-tab" role="tab">
                                        <i class="ri-school-line d-block check-nav-icon mt-4 mb-2"></i>
                                        <p class="fw-normal mb-0">SS</p>
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content">
                                <!-- Nursery Level -->
                                <div class="tab-pane active" id="nursery-tab" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>GRADE</th>
                                                    <th>RANGE</th>
                                                    <th>GPA</th>
                                                    <th>DESCRIPTION</th>
                                                    <th>ACTION</th>
                                                </tr>
                                            </thead>
                                            <tbody id="nurseryGrades">
                                                @if(isset($gradingSystems['Nursery']))
                                                    @foreach($gradingSystems['Nursery'] as $grade)
                                                        <tr>
                                                            <td>
                                                                <span class="badge bg-info-subtle text-info fs-12 fw-medium">{{ $grade->grade }}</span>
                                                            </td>
                                                            <td>{{ $grade->min_score }} - {{ $grade->max_score }}</td>
                                                            <td>{{ $grade->gpa_point }}</td>
                                                            <td>{{ $grade->description ?: 'N/A' }}</td>
                                                            <td>
                                                                <div class="dropdown">
                                                                    <button class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                                                                        <i class="ri-more-line"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <li><a class="dropdown-item edit-grade-btn" href="#" data-id="{{ $grade->id }}" data-level="{{ $grade->level }}" data-grade="{{ $grade->grade }}" data-min="{{ $grade->min_score }}" data-max="{{ $grade->max_score }}" data-gpa="{{ $grade->gpa_point }}" data-description="{{ $grade->description }}"><i class="ri-edit-line me-2"></i>Edit</a></li>
                                                                        <li><a class="dropdown-item text-danger delete-grade-btn" href="#" data-id="{{ $grade->id }}"><i class="ri-delete-bin-line me-2"></i>Delete</a></li>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <i class="ri-bar-chart-line display-6"></i>
                                                                <p class="mt-2">No nursery grades configured yet</p>
                                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#addGradeModal">
                                                                    Add First Grade
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Primary Level -->
                                <div class="tab-pane" id="primary-tab" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>GRADE</th>
                                                    <th>RANGE</th>
                                                    <th>GPA</th>
                                                    <th>DESCRIPTION</th>
                                                    <th>ACTION</th>
                                                </tr>
                                            </thead>
                                            <tbody id="primaryGrades">
                                                @if(isset($gradingSystems['Primary']))
                                                    @foreach($gradingSystems['Primary'] as $grade)
                                                        <tr>
                                                            <td>
                                                                <span class="badge bg-primary-subtle text-primary fs-12 fw-medium">{{ $grade->grade }}</span>
                                                            </td>
                                                            <td>{{ $grade->min_score }} - {{ $grade->max_score }}</td>
                                                            <td>{{ $grade->gpa_point }}</td>
                                                            <td>{{ $grade->description ?: 'N/A' }}</td>
                                                            <td>
                                                                <div class="dropdown">
                                                                    <button class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                                                                        <i class="ri-more-line"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <li><a class="dropdown-item edit-grade-btn" href="#" data-id="{{ $grade->id }}" data-level="{{ $grade->level }}" data-grade="{{ $grade->grade }}" data-min="{{ $grade->min_score }}" data-max="{{ $grade->max_score }}" data-gpa="{{ $grade->gpa_point }}" data-description="{{ $grade->description }}"><i class="ri-edit-line me-2"></i>Edit</a></li>
                                                                        <li><a class="dropdown-item text-danger delete-grade-btn" href="#" data-id="{{ $grade->id }}"><i class="ri-delete-bin-line me-2"></i>Delete</a></li>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <i class="ri-bar-chart-line display-6"></i>
                                                                <p class="mt-2">No primary grades configured yet</p>
                                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addGradeModal">
                                                                    Add First Grade
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- JSS Level -->
                                <div class="tab-pane" id="jss-tab" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>GRADE</th>
                                                    <th>RANGE</th>
                                                    <th>GPA</th>
                                                    <th>DESCRIPTION</th>
                                                    <th>ACTION</th>
                                                </tr>
                                            </thead>
                                            <tbody id="jssGrades">
                                                @if(isset($gradingSystems['JSS']))
                                                    @foreach($gradingSystems['JSS'] as $grade)
                                                        <tr>
                                                            <td>
                                                                <span class="badge bg-warning-subtle text-warning fs-12 fw-medium">{{ $grade->grade }}</span>
                                                            </td>
                                                            <td>{{ $grade->min_score }} - {{ $grade->max_score }}</td>
                                                            <td>{{ $grade->gpa_point }}</td>
                                                            <td>{{ $grade->description ?: 'N/A' }}</td>
                                                            <td>
                                                                <div class="dropdown">
                                                                    <button class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                                                                        <i class="ri-more-line"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <li><a class="dropdown-item edit-grade-btn" href="#" data-id="{{ $grade->id }}" data-level="{{ $grade->level }}" data-grade="{{ $grade->grade }}" data-min="{{ $grade->min_score }}" data-max="{{ $grade->max_score }}" data-gpa="{{ $grade->gpa_point }}" data-description="{{ $grade->description }}"><i class="ri-edit-line me-2"></i>Edit</a></li>
                                                                        <li><a class="dropdown-item text-danger delete-grade-btn" href="#" data-id="{{ $grade->id }}"><i class="ri-delete-bin-line me-2"></i>Delete</a></li>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <i class="ri-bar-chart-line display-6"></i>
                                                                <p class="mt-2">No JSS grades configured yet</p>
                                                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#addGradeModal">
                                                                    Add First Grade
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- SS Level -->
                                <div class="tab-pane" id="ss-tab" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>GRADE</th>
                                                    <th>RANGE</th>
                                                    <th>GPA</th>
                                                    <th>DESCRIPTION</th>
                                                    <th>ACTION</th>
                                                </tr>
                                            </thead>
                                            <tbody id="ssGrades">
                                                @if(isset($gradingSystems['SS']))
                                                    @foreach($gradingSystems['SS'] as $grade)
                                                        <tr>
                                                            <td>
                                                                <span class="badge bg-success-subtle text-success fs-12 fw-medium">{{ $grade->grade }}</span>
                                                            </td>
                                                            <td>{{ $grade->min_score }} - {{ $grade->max_score }}</td>
                                                            <td>{{ $grade->gpa_point }}</td>
                                                            <td>{{ $grade->description ?: 'N/A' }}</td>
                                                            <td>
                                                                <div class="dropdown">
                                                                    <button class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                                                                        <i class="ri-more-line"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <li><a class="dropdown-item edit-grade-btn" href="#" data-id="{{ $grade->id }}" data-level="{{ $grade->level }}" data-grade="{{ $grade->grade }}" data-min="{{ $grade->min_score }}" data-max="{{ $grade->max_score }}" data-gpa="{{ $grade->gpa_point }}" data-description="{{ $grade->description }}"><i class="ri-edit-line me-2"></i>Edit</a></li>
                                                                        <li><a class="dropdown-item text-danger delete-grade-btn" href="#" data-id="{{ $grade->id }}"><i class="ri-delete-bin-line me-2"></i>Delete</a></li>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <i class="ri-bar-chart-line display-6"></i>
                                                                <p class="mt-2">No SS grades configured yet</p>
                                                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addGradeModal">
                                                                    Add First Grade
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Grade Modal -->
<div class="modal fade" id="addGradeModal" tabindex="-1" aria-labelledby="addGradeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addGradeModalLabel">Add New Grade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addGradeForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Level <span class="text-danger">*</span></label>
                                <select class="form-select" name="level" required>
                                    <option value="">Select Level</option>
                                    <option value="Nursery">Nursery</option>
                                    <option value="Primary">Primary</option>
                                    <option value="JSS">JSS</option>
                                    <option value="SS">SS</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Grade <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="grade" placeholder="e.g., A, B, C" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Min Score <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="min_score" min="0" max="100" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Max Score <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="max_score" min="0" max="100" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">GPA Point <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="gpa_point" min="0" max="4" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Optional description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>Save Grade
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Grade Modal -->
<div class="modal fade" id="editGradeModal" tabindex="-1" aria-labelledby="editGradeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editGradeModalLabel">Edit Grade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editGradeForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="grade_id" id="editGradeId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Level <span class="text-danger">*</span></label>
                                <select class="form-select" name="level" id="editLevel" required>
                                    <option value="">Select Level</option>
                                    <option value="Nursery">Nursery</option>
                                    <option value="Primary">Primary</option>
                                    <option value="JSS">JSS</option>
                                    <option value="SS">SS</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Grade <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="grade" id="editGradeValue" placeholder="e.g., A, B, C" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Min Score <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="min_score" id="editMinScore" min="0" max="100" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Max Score <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="max_score" id="editMaxScore" min="0" max="100" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">GPA Point <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="gpa_point" id="editGpaPoint" min="0" max="4" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="editDescription" rows="2" placeholder="Optional description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>Update Grade
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-success mb-4">
                    <i class="ri-check-double-line display-4"></i>
                </div>
                <h5 class="mb-3" id="successMessage">Operation completed successfully!</h5>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .nav-pills .nav-link {
        border-radius: 8px;
        border: 1px solid #e9ecef;
        color: #6c757d;
        background-color: transparent;
    }
    
    .nav-pills .nav-link.active {
        background-color: #5664d2;
        border-color: #5664d2;
        color: white;
    }
    
    .check-nav-icon {
        font-size: 24px;
    }
    
    .bg-primary-subtle {
        background-color: rgba(86, 100, 210, 0.1) !important;
    }
    
    .text-primary {
        color: #5664d2 !important;
    }
    
    .bg-success-subtle {
        background-color: rgba(40, 167, 69, 0.1) !important;
    }
    
    .text-success {
        color: #28a745 !important;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .table th {
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #e9ecef;
    }
    
    .dropdown-toggle {
        border: 1px solid #e9ecef;
    }
    
    .dropdown-toggle:after {
        display: none;
    }
</style>
@endpush

@push('scripts')
<script>
    // Add grade form submission
    document.getElementById('addGradeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="ri-loader-4-line spinner-border spinner-border-sm me-1"></i>Saving...';
        submitBtn.disabled = true;
        
        fetch('{{ route("admin.settings.grading-system.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('successMessage').textContent = data.message;
                new bootstrap.Modal(document.getElementById('successModal')).show();
                document.getElementById('addGradeModal').querySelector('.btn-close').click();
                this.reset();
                setTimeout(() => location.reload(), 1500);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Edit grade functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-grade-btn') || e.target.closest('.edit-grade-btn')) {
            const btn = e.target.classList.contains('edit-grade-btn') ? e.target : e.target.closest('.edit-grade-btn');
            e.preventDefault();
            
            document.getElementById('editGradeId').value = btn.dataset.id;
            document.getElementById('editLevel').value = btn.dataset.level;
            document.getElementById('editGradeValue').value = btn.dataset.grade;
            document.getElementById('editMinScore').value = btn.dataset.min;
            document.getElementById('editMaxScore').value = btn.dataset.max;
            document.getElementById('editGpaPoint').value = btn.dataset.gpa;
            document.getElementById('editDescription').value = btn.dataset.description;
            
            new bootstrap.Modal(document.getElementById('editGradeModal')).show();
        }
    });

    // Update grade form submission
    document.getElementById('editGradeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const gradeId = document.getElementById('editGradeId').value;
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="ri-loader-4-line spinner-border spinner-border-sm me-1"></i>Updating...';
        submitBtn.disabled = true;
        
        fetch(`/admin/settings/grading-system/${gradeId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('successMessage').textContent = data.message;
                new bootstrap.Modal(document.getElementById('successModal')).show();
                document.getElementById('editGradeModal').querySelector('.btn-close').click();
                setTimeout(() => location.reload(), 1500);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Delete grade functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-grade-btn') || e.target.closest('.delete-grade-btn')) {
            const btn = e.target.classList.contains('delete-grade-btn') ? e.target : e.target.closest('.delete-grade-btn');
            e.preventDefault();
            
            if (confirm('Are you sure you want to delete this grade? This action cannot be undone.')) {
                const gradeId = btn.dataset.id;
                
                fetch(`/admin/settings/grading-system/${gradeId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('successMessage').textContent = data.message;
                        new bootstrap.Modal(document.getElementById('successModal')).show();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }
    });
</script>
@endpush
