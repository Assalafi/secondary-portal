@extends('layouts.admin')

@section('title', 'Salary Setup')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Salary Setup</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.payments.overview') }}" class="text-muted text-decoration-none">Payment & Finance</a>
                            </li>
                            <li class="breadcrumb-item active text-muted" aria-current="page">Salary Setup</li>
                        </ol>
                    </nav>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSalaryModal">
                    <i class="ri-add-line"></i> Add New Salary Setup
                </button>
            </div>
        </div>
    </div>

    <!-- Salary Structures -->
    <div class="row">
        <div class="col-12">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-semibold mb-0">Salary Structures</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>STRUCTURE TITLE</th>
                                    <th>ROLE/LEVEL</th>
                                    <th>BASE SALARY</th>
                                    <th>ALLOWANCE</th>
                                    <th>DEDUCTION</th>
                                    <th>NET SALARY</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salaryStructures as $structure)
                                    <tr>
                                        <td class="fw-semibold">{{ $structure->structure_title }}</td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary">{{ $structure->role_level }}</span>
                                        </td>
                                        <td class="fw-bold">₦{{ number_format($structure->base_salary) }}</td>
                                        <td class="text-success">₦{{ number_format($structure->allowance) }}</td>
                                        <td class="text-danger">₦{{ number_format($structure->deduction) }}</td>
                                        <td class="fw-bold text-success">₦{{ number_format($structure->base_salary + $structure->allowance - $structure->deduction) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $structure->status === 'Active' ? 'success' : 'secondary' }}-subtle text-{{ $structure->status === 'Active' ? 'success' : 'secondary' }}">
                                                {{ $structure->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                    <i class="ri-more-line"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item edit-btn" href="#" data-id="{{ $structure->id }}" data-title="{{ $structure->structure_title }}" data-role="{{ $structure->role_level }}" data-base="{{ $structure->base_salary }}" data-allowance="{{ $structure->allowance }}" data-deduction="{{ $structure->deduction }}" data-status="{{ $structure->status }}" data-description="{{ $structure->description }}"><i class="ri-edit-line me-2"></i>Edit</a></li>
                                                    <li><a class="dropdown-item text-danger delete-btn" href="#" data-id="{{ $structure->id }}" data-title="{{ $structure->structure_title }}"><i class="ri-delete-bin-line me-2"></i>Delete</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ri-money-cny-box-line display-6"></i>
                                                <p class="mt-2">No salary structures found</p>
                                                <p class="small">Create salary structures to manage staff compensation</p>
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

<!-- Add Salary Setup Modal -->
<div class="modal fade" id="addSalaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Salary Setup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="salarySetupForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Structure Title</label>
                            <input type="text" class="form-control" name="structure_title" placeholder="e.g., Standard Teacher Package" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Role/Level</label>
                            <select class="form-select" name="role_level" required>
                                <option value="">Choose role/level</option>
                                <option value="Principal">Principal</option>
                                <option value="Vice Principal">Vice Principal</option>
                                <option value="Head Teacher">Head Teacher</option>
                                <option value="Senior Teacher">Senior Teacher</option>
                                <option value="Class Teacher">Class Teacher</option>
                                <option value="Subject Teacher">Subject Teacher</option>
                                <option value="Teacher">Teacher</option>
                                <option value="Admin Staff">Admin Staff</option>
                                <option value="Librarian">Librarian</option>
                                <option value="Security Guard">Security Guard</option>
                                <option value="Cleaner">Cleaner</option>
                                <option value="Driver">Driver</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Base Salary</label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="number" class="form-control" name="base_salary" step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Allowance</label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="number" class="form-control" name="allowance" step="0.01" min="0" value="0">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Deduction</label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="number" class="form-control" name="deduction" step="0.01" min="0" value="0">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Optional description about this salary structure"></textarea>
                        </div>

                        <!-- Net Salary Preview -->
                        <div class="col-12">
                            <div class="alert alert-info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Net Salary Preview:</span>
                                    <span class="fw-bold fs-5" id="netSalaryPreview">₦0</span>
                                </div>
                                <small class="text-muted">Base Salary + Allowance - Deduction</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Salary Setup Modal -->
<div class="modal fade" id="editSalaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Salary Setup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSalarySetupForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editSalaryId" name="id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Structure Title</label>
                            <input type="text" class="form-control" id="editStructureTitle" name="structure_title" placeholder="e.g., Standard Teacher Package" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Role/Level</label>
                            <select class="form-select" id="editRoleLevel" name="role_level" required>
                                <option value="">Choose role/level</option>
                                <option value="Principal">Principal</option>
                                <option value="Vice Principal">Vice Principal</option>
                                <option value="Head Teacher">Head Teacher</option>
                                <option value="Senior Teacher">Senior Teacher</option>
                                <option value="Class Teacher">Class Teacher</option>
                                <option value="Subject Teacher">Subject Teacher</option>
                                <option value="Teacher">Teacher</option>
                                <option value="Admin Staff">Admin Staff</option>
                                <option value="Librarian">Librarian</option>
                                <option value="Security Guard">Security Guard</option>
                                <option value="Cleaner">Cleaner</option>
                                <option value="Driver">Driver</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Base Salary</label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="number" class="form-control" id="editBaseSalary" name="base_salary" step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Allowance</label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="number" class="form-control" id="editAllowance" name="allowance" step="0.01" min="0" value="0">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Deduction</label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="number" class="form-control" id="editDeduction" name="deduction" step="0.01" min="0" value="0">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="editStatus" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="editDescription" name="description" rows="3" placeholder="Optional description about this salary structure"></textarea>
                        </div>

                        <!-- Net Salary Preview -->
                        <div class="col-12">
                            <div class="alert alert-info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Net Salary Preview:</span>
                                    <span class="fw-bold fs-5" id="editNetSalaryPreview">₦0</span>
                                </div>
                                <small class="text-muted">Base Salary + Allowance - Deduction</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success Message Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content text-center">
            <div class="modal-body p-4">
                <div class="text-success mb-3">
                    <i class="ri-check-double-line display-4"></i>
                </div>
                <h6 class="fw-semibold mb-2">Salary Structure has been added successfully!</h6>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Calculate net salary preview
    function calculateNetSalary() {
        const baseSalary = parseFloat(document.querySelector('input[name="base_salary"]').value) || 0;
        const allowance = parseFloat(document.querySelector('input[name="allowance"]').value) || 0;
        const deduction = parseFloat(document.querySelector('input[name="deduction"]').value) || 0;
        
        const netSalary = baseSalary + allowance - deduction;
        document.getElementById('netSalaryPreview').textContent = '₦' + netSalary.toLocaleString();
    }

    // Add event listeners for real-time calculation
    document.addEventListener('DOMContentLoaded', function() {
        const salaryInputs = document.querySelectorAll('input[name="base_salary"], input[name="allowance"], input[name="deduction"]');
        salaryInputs.forEach(input => {
            input.addEventListener('input', calculateNetSalary);
        });
    });

    // Handle form submission
    document.getElementById('salarySetupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("admin.payments.salary-setup.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide add modal
                const addModal = bootstrap.Modal.getInstance(document.getElementById('addSalaryModal'));
                addModal.hide();
                
                // Show success modal
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                
                // Reload page after success modal is closed
                document.getElementById('successModal').addEventListener('hidden.bs.modal', function() {
                    location.reload();
                });
            } else {
                alert('Error: ' + (data.message || 'Failed to create salary structure'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating salary structure');
        });
    });

    // Reset form when add modal is closed
    document.getElementById('addSalaryModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('salarySetupForm').reset();
        document.getElementById('netSalaryPreview').textContent = '₦0';
    });

    // Edit functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-btn') || e.target.closest('.edit-btn')) {
            const btn = e.target.classList.contains('edit-btn') ? e.target : e.target.closest('.edit-btn');
            e.preventDefault();
            
            // Get data from button attributes
            const id = btn.getAttribute('data-id');
            const title = btn.getAttribute('data-title');
            const role = btn.getAttribute('data-role');
            const baseSalary = btn.getAttribute('data-base');
            const allowance = btn.getAttribute('data-allowance');
            const deduction = btn.getAttribute('data-deduction');
            const status = btn.getAttribute('data-status');
            const description = btn.getAttribute('data-description');
            
            // Populate edit form
            document.getElementById('editSalaryId').value = id;
            document.getElementById('editStructureTitle').value = title;
            document.getElementById('editRoleLevel').value = role;
            document.getElementById('editBaseSalary').value = baseSalary;
            document.getElementById('editAllowance').value = allowance;
            document.getElementById('editDeduction').value = deduction;
            document.getElementById('editStatus').value = status;
            document.getElementById('editDescription').value = description;
            
            // Calculate and show net salary preview
            calculateEditNetSalary();
            
            // Show edit modal
            const editModal = new bootstrap.Modal(document.getElementById('editSalaryModal'));
            editModal.show();
        }
    });

    // Calculate net salary preview for edit form
    function calculateEditNetSalary() {
        const baseSalary = parseFloat(document.getElementById('editBaseSalary').value) || 0;
        const allowance = parseFloat(document.getElementById('editAllowance').value) || 0;
        const deduction = parseFloat(document.getElementById('editDeduction').value) || 0;
        
        const netSalary = baseSalary + allowance - deduction;
        document.getElementById('editNetSalaryPreview').textContent = '₦' + netSalary.toLocaleString();
    }

    // Add event listeners for edit form real-time calculation
    document.addEventListener('DOMContentLoaded', function() {
        const editSalaryInputs = document.querySelectorAll('#editBaseSalary, #editAllowance, #editDeduction');
        editSalaryInputs.forEach(input => {
            input.addEventListener('input', calculateEditNetSalary);
        });
    });

    // Handle edit form submission
    document.getElementById('editSalarySetupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const salaryId = document.getElementById('editSalaryId').value;
        
        fetch(`{{ url('admin/payments/salary-setup') }}/${salaryId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-HTTP-Method-Override': 'PUT'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Hide edit modal
                const editModal = bootstrap.Modal.getInstance(document.getElementById('editSalaryModal'));
                editModal.hide();
                
                // Update success modal message
                document.querySelector('#successModal .fw-semibold').textContent = 'Salary Structure has been updated successfully!';
                
                // Show success modal
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                
                // Reload page after success modal is closed
                document.getElementById('successModal').addEventListener('hidden.bs.modal', function() {
                    location.reload();
                });
            } else {
                alert(`❌ Error: ${data.message || 'Failed to update salary structure'}`);
            }
        })
        .catch(error => {
            console.error('Edit salary structure error:', error);
            alert(`❌ Network Error: Unable to update salary structure. Please check your connection and try again.`);
        });
    });

    // Reset edit form when modal is closed
    document.getElementById('editSalaryModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('editSalarySetupForm').reset();
        document.getElementById('editNetSalaryPreview').textContent = '₦0';
    });

    // Delete functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-btn') || e.target.closest('.delete-btn')) {
            const btn = e.target.classList.contains('delete-btn') ? e.target : e.target.closest('.delete-btn');
            e.preventDefault();
            
            const id = btn.getAttribute('data-id');
            const title = btn.getAttribute('data-title');
            
            if (confirm(`⚠️ Are you sure you want to delete "${title}"?\n\nThis action cannot be undone. If this salary structure is being used by staff members, the deletion will be prevented.`)) {
                fetch(`{{ url('admin/payments/salary-setup') }}/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-HTTP-Method-Override': 'DELETE',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || `HTTP error! status: ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert(`✅ Success: ${data.message}`);
                        location.reload();
                    } else {
                        alert(`❌ Error: ${data.message || 'Failed to delete salary structure'}`);
                    }
                })
                .catch(error => {
                    console.error('Delete salary structure error:', error);
                    alert(`❌ Error: ${error.message}`);
                });
            }
        }
    });
</script>
@endpush

@push('styles')
<style>
    .custom-shadow { 
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); 
    }
    .table-hover tbody tr:hover { 
        background-color: rgba(79, 70, 229, 0.05); 
    }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
    .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
    .bg-secondary-subtle { background-color: rgba(108, 117, 125, 0.1) !important; }
</style>
@endpush
