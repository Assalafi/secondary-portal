@extends('layouts.admin')

@section('title', 'Session/Term Management')

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
                            <li class="breadcrumb-item active">Session/Term</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Session/Term Management</h4>
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
                                            <i class="ri-calendar-line"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">Session/Term Management</h5>
                                    <p class="card-title-desc mb-0">Manage academic sessions and terms</p>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSessionModal">
                                <i class="ri-add-line me-1"></i>Add Session/Term
                            </button>
                        </div>
                    </div>
                    
                    <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ACADEMIC YEAR</th>
                                            <th>TERM</th>
                                            <th>DURATION</th>
                                            <th>STATUS</th>
                                            <th>CURRENT</th>
                                            <th>ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sessions as $session)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 avatar-xs me-3">
                                                            <div class="avatar-title bg-soft-success text-success rounded-circle">
                                                                <i class="ri-calendar-2-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0">{{ $session->academic_year }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info-subtle text-info">{{ $session->term_name }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">
                                                        {{ $session->start_date->format('M d, Y') }} - {{ $session->end_date->format('M d, Y') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($session->status === 'Active')
                                                        <span class="badge bg-success-subtle text-success">Active</span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($session->is_current)
                                                        <span class="badge bg-warning-subtle text-warning">
                                                            <i class="ri-star-line me-1"></i>Current
                                                        </span>
                                                    @else
                                                        <span class="text-muted">No</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                                                            <i class="ri-more-line"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item edit-session-btn" href="#" 
                                                                data-id="{{ $session->id }}"
                                                                data-year="{{ $session->academic_year }}"
                                                                data-term="{{ $session->term_name }}"
                                                                data-start="{{ $session->start_date->format('Y-m-d') }}"
                                                                data-end="{{ $session->end_date->format('Y-m-d') }}"
                                                                data-current="{{ $session->is_current ? '1' : '0' }}">
                                                                <i class="ri-edit-line me-2"></i>Edit
                                                            </a></li>
                                                            @if(!$session->is_current)
                                                                <li><a class="dropdown-item set-current-btn" href="#" data-id="{{ $session->id }}"><i class="ri-star-line me-2"></i>Set as Current</a></li>
                                                            @endif
                                                            <li><a class="dropdown-item text-danger delete-session-btn" href="#" data-id="{{ $session->id }}"><i class="ri-delete-bin-line me-2"></i>Delete</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="ri-calendar-line display-6"></i>
                                                        <p class="mt-2">No sessions/terms configured yet</p>
                                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSessionModal">
                                                            Add First Session/Term
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Session Modal -->
<div class="modal fade" id="addSessionModal" tabindex="-1" aria-labelledby="addSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSessionModalLabel">Add New Session/Term</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addSessionForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="academic_year" placeholder="e.g., 2023/2024" required>
                                <small class="text-muted">Format: YYYY/YYYY</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Term <span class="text-danger">*</span></label>
                                <select class="form-select" name="term_name" required>
                                    <option value="">Select Term</option>
                                    <option value="1st Term">1st Term</option>
                                    <option value="2nd Term">2nd Term</option>
                                    <option value="3rd Term">3rd Term</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_current" value="1" id="is_current">
                            <label class="form-check-label" for="is_current">
                                Set as current session/term
                            </label>
                            <small class="d-block text-muted">This will deactivate any existing current session/term</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>Save Session/Term
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Session Modal -->
<div class="modal fade" id="editSessionModal" tabindex="-1" aria-labelledby="editSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSessionModalLabel">Edit Session/Term</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSessionForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="session_id" id="editSessionId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="academic_year" id="editAcademicYear" placeholder="e.g., 2023/2024" required>
                                <small class="text-muted">Format: YYYY/YYYY</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Term <span class="text-danger">*</span></label>
                                <select class="form-select" name="term_name" id="editTermName" required>
                                    <option value="">Select Term</option>
                                    <option value="1st Term">1st Term</option>
                                    <option value="2nd Term">2nd Term</option>
                                    <option value="3rd Term">3rd Term</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" id="editStartDate" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="end_date" id="editEndDate" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_current" value="1" id="editIsCurrent">
                            <label class="form-check-label" for="editIsCurrent">
                                Set as current session/term
                            </label>
                            <small class="d-block text-muted">This will deactivate any existing current session/term</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>Update Session/Term
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
    .bg-soft-success {
        background-color: rgba(40, 167, 69, 0.1) !important;
    }
    
    .bg-info-subtle {
        background-color: rgba(13, 202, 240, 0.1) !important;
    }
    
    .text-info {
        color: #0dcaf0 !important;
    }
    
    .bg-success-subtle {
        background-color: rgba(40, 167, 69, 0.1) !important;
    }
    
    .bg-secondary-subtle {
        background-color: rgba(108, 117, 125, 0.1) !important;
    }
    
    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
    
    .text-warning {
        color: #ffc107 !important;
    }
    
    .avatar-xs {
        height: 2rem;
        width: 2rem;
    }
    
    .avatar-title {
        align-items: center;
        background-color: #5664d2;
        color: #fff;
        display: flex;
        font-weight: 500;
        height: 100%;
        justify-content: center;
        width: 100%;
    }
    
    .form-check-input:checked {
        background-color: #5664d2;
        border-color: #5664d2;
    }
</style>
@endpush

@push('scripts')
<script>
    async function parseJsonResponse(response) {
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || Object.values(data.errors || {}).flat().join('\n') ||
                `HTTP error! status: ${response.status}`);
        }

        return data;
    }

    // Add session form submission
    document.getElementById('addSessionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="ri-loader-4-line spinner-border spinner-border-sm me-1"></i>Saving...';
        submitBtn.disabled = true;
        
        fetch('{{ route("admin.settings.session-term.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(parseJsonResponse)
        .then(data => {
            if (data.success) {
                document.getElementById('successMessage').textContent = data.message;
                new bootstrap.Modal(document.getElementById('successModal')).show();
                document.getElementById('addSessionModal').querySelector('.btn-close').click();
                this.reset();
                setTimeout(() => location.reload(), 1500);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'An error occurred. Please try again.');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Edit session functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-session-btn') || e.target.closest('.edit-session-btn')) {
            const btn = e.target.classList.contains('edit-session-btn') ? e.target : e.target.closest('.edit-session-btn');
            e.preventDefault();
            
            document.getElementById('editSessionId').value = btn.dataset.id;
            document.getElementById('editAcademicYear').value = btn.dataset.year;
            document.getElementById('editTermName').value = btn.dataset.term;
            document.getElementById('editStartDate').value = btn.dataset.start;
            document.getElementById('editEndDate').value = btn.dataset.end;
            document.getElementById('editIsCurrent').checked = btn.dataset.current === '1';
            
            new bootstrap.Modal(document.getElementById('editSessionModal')).show();
        }
    });

    // Edit session form submission
    document.getElementById('editSessionForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const sessionId = document.getElementById('editSessionId').value;
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.innerHTML = '<i class="ri-loader-4-line spinner-border spinner-border-sm me-1"></i>Updating...';
        submitBtn.disabled = true;

        fetch(`/admin/settings/session-term/${sessionId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-HTTP-Method-Override': 'PUT',
                'Accept': 'application/json',
            }
        })
        .then(parseJsonResponse)
        .then(data => {
            if (data.success) {
                document.getElementById('successMessage').textContent = data.message;
                new bootstrap.Modal(document.getElementById('successModal')).show();
                document.getElementById('editSessionModal').querySelector('.btn-close').click();
                setTimeout(() => location.reload(), 1500);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'An error occurred. Please try again.');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Set current session functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('set-current-btn') || e.target.closest('.set-current-btn')) {
            const btn = e.target.classList.contains('set-current-btn') ? e.target : e.target.closest('.set-current-btn');
            e.preventDefault();
            
            if (confirm('Are you sure you want to set this as the current session/term?')) {
                const sessionId = btn.dataset.id;
                
                fetch(`/admin/settings/session-term/${sessionId}/set-current`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                })
                .then(parseJsonResponse)
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
                    alert(error.message || 'An error occurred. Please try again.');
                });
            }
        }
    });

    // Delete session functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-session-btn') || e.target.closest('.delete-session-btn')) {
            const btn = e.target.classList.contains('delete-session-btn') ? e.target : e.target.closest('.delete-session-btn');
            e.preventDefault();

            if (confirm('Delete this session/term from settings? Historical invoices/results will remain untouched.')) {
                fetch(`/admin/settings/session-term/${btn.dataset.id}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-HTTP-Method-Override': 'DELETE',
                        'Accept': 'application/json',
                    }
                })
                .then(parseJsonResponse)
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
                    alert(error.message || 'An error occurred. Please try again.');
                });
            }
        }
    });

    // Date validation
    document.addEventListener('change', function(e) {
        if (e.target.name === 'start_date') {
            const endDateInput = e.target.closest('form').querySelector('input[name="end_date"]');
            endDateInput.min = e.target.value;
        }
        if (e.target.name === 'end_date') {
            const startDateInput = e.target.closest('form').querySelector('input[name="start_date"]');
            if (startDateInput.value && new Date(e.target.value) <= new Date(startDateInput.value)) {
                alert('End date must be after start date');
                e.target.value = '';
            }
        }
    });
</script>
@endpush
