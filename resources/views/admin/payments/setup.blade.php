@extends('layouts.admin')

@section('title', 'Payment Setup')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-1">Payment Setup</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="text-muted text-decoration-none">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.payments.overview') }}"
                                        class="text-muted text-decoration-none">Payment & Finance</a>
                                </li>
                                <li class="breadcrumb-item active text-muted" aria-current="page">Payment Setup</li>
                            </ol>
                        </nav>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                        <i class="ri-add-line"></i> Add New Payment Setup
                    </button>
                </div>
            </div>
        </div>

        <!-- School Fees Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card custom-shadow rounded-3 bg-white border">
                    <div class="card-header bg-transparent border-0">
                        <h6 class="fw-semibold mb-0">School Fees</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>PAYMENT TYPE</th>
                                        <th>LEVEL</th>
                                        <th>TERM</th>
                                        <th>AMOUNT</th>
                                        <th>LAST UPDATED</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($paymentSetups->where('payment_type', 'School Fees') as $setup)
                                        <tr>
                                            <td class="fw-semibold">{{ $setup->payment_type }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $setup->level === 'Nursery' ? 'info' : ($setup->level === 'Primary' ? 'success' : 'primary') }}-subtle text-{{ $setup->level === 'Nursery' ? 'info' : ($setup->level === 'Primary' ? 'success' : 'primary') }}">
                                                    {{ $setup->level }}
                                                </span>
                                            </td>
                                            <td>{{ $setup->term }}</td>
                                            <td class="fw-bold text-success">₦{{ number_format($setup->amount) }}</td>
                                            <td>{{ $setup->last_updated->format('M d, Y') }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $setup->status === 'Active' ? 'success' : 'secondary' }}-subtle text-{{ $setup->status === 'Active' ? 'success' : 'secondary' }}">
                                                    {{ $setup->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                        <i class="ri-more-line"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item edit-btn" href="#"
                                                                data-id="{{ $setup->id }}"
                                                                data-type="{{ $setup->payment_type }}"
                                                                data-level="{{ $setup->level }}"
                                                                data-term="{{ $setup->term }}"
                                                                data-amount="{{ $setup->amount }}"
                                                                data-date="{{ $setup->effective_date->format('Y-m-d') }}"
                                                                data-status="{{ $setup->status }}"
                                                                data-description="{{ $setup->description }}"><i
                                                                    class="ri-edit-line me-2"></i>Edit</a></li>
                                                        <li><a class="dropdown-item text-danger delete-btn" href="#"
                                                                data-id="{{ $setup->id }}"
                                                                data-type="{{ $setup->payment_type }}"><i
                                                                    class="ri-delete-bin-line me-2"></i>Delete</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Other Payments Section -->
        <div class="row">
            <div class="col-12">
                <div class="card custom-shadow rounded-3 bg-white border">
                    <div class="card-header bg-transparent border-0">
                        <h6 class="fw-semibold mb-0">Other Payments</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>PAYMENT TYPE</th>
                                        <th>LEVEL</th>
                                        <th>TERM</th>
                                        <th>AMOUNT</th>
                                        <th>LAST UPDATED</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($paymentSetups->where('payment_type', '!=', 'School Fees') as $setup)
                                        <tr>
                                            <td class="fw-semibold">{{ $setup->payment_type }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary">{{ $setup->level }}</span>
                                            </td>
                                            <td>{{ $setup->term }}</td>
                                            <td class="fw-bold text-success">₦{{ number_format($setup->amount) }}</td>
                                            <td>{{ $setup->last_updated->format('M d, Y') }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $setup->status === 'Active' ? 'success' : 'secondary' }}-subtle text-{{ $setup->status === 'Active' ? 'success' : 'secondary' }}">
                                                    {{ $setup->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                        <i class="ri-more-line"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item edit-btn" href="#"
                                                                data-id="{{ $setup->id }}"
                                                                data-type="{{ $setup->payment_type }}"
                                                                data-level="{{ $setup->level }}"
                                                                data-term="{{ $setup->term }}"
                                                                data-amount="{{ $setup->amount }}"
                                                                data-date="{{ $setup->effective_date->format('Y-m-d') }}"
                                                                data-status="{{ $setup->status }}"
                                                                data-description="{{ $setup->description }}"><i
                                                                    class="ri-edit-line me-2"></i>Edit</a></li>
                                                        <li><a class="dropdown-item text-danger delete-btn" href="#"
                                                                data-id="{{ $setup->id }}"
                                                                data-type="{{ $setup->payment_type }}"><i
                                                                    class="ri-delete-bin-line me-2"></i>Delete</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Payment Setup Modal -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Payment Setup</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="paymentSetupForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Payment Type</label>
                                <select class="form-select" name="payment_type" required>
                                    <option value="">Choose payment type</option>
                                    <option value="Application">Application</option>
                                    <option value="School Fees">School Fees</option>
                                    <option value="ID card">ID card</option>
                                    <option value="Uniform">Uniform</option>
                                    <option value="Books">Books</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Feeding">Feeding</option>
                                    <option value="Registration">Registration</option>
                                    <option value="Examination">Examination</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Level</label>
                                <select class="form-select" name="level" required>
                                    <option value="All">All</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level }}">{{ $level }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Term</label>
                                <select class="form-select" name="term" required>
                                    @foreach ($terms as $term)
                                        <option value="{{ $term }}">{{ $term }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" class="form-control" name="amount" step="0.01"
                                        min="0" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Effective Date</label>
                                <input type="date" class="form-control" name="effective_date" required>
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
                                <textarea class="form-control" name="description" rows="3" placeholder="Optional description"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Payment Setup Modal -->
    <div class="modal fade" id="editPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Payment Setup</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editPaymentSetupForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editPaymentId" name="id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Payment Type</label>
                                <select class="form-select" id="editPaymentType" name="payment_type" required>
                                    <option value="">Choose payment type</option>
                                    <option value="Application">Application</option>
                                    <option value="School Fees">School Fees</option>
                                    <option value="ID card">ID card</option>
                                    <option value="Uniform">Uniform</option>
                                    <option value="Books">Books</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Feeding">Feeding</option>
                                    <option value="Registration">Registration</option>
                                    <option value="Examination">Examination</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Level</label>
                                <select class="form-select" id="editLevel" name="level" required>
                                    <option value="All">All</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level }}">{{ $level }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Term</label>
                                <select class="form-select" id="editTerm" name="term" required>
                                    @foreach ($terms as $term)
                                        <option value="{{ $term }}">{{ $term }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" class="form-control" id="editAmount" name="amount"
                                        step="0.01" min="0" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Effective Date</label>
                                <input type="date" class="form-control" id="editEffectiveDate" name="effective_date"
                                    required>
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
                                <textarea class="form-control" id="editDescription" name="description" rows="3"
                                    placeholder="Optional description"></textarea>
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
                    <h6 class="fw-semibold mb-2">New Payment Setup has been added successfully!</h6>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

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

        // Handle form submission
        document.getElementById('paymentSetupForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('{{ route('admin.payments.setup.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(parseJsonResponse)
                .then(data => {
                    if (data.success) {
                        // Hide add modal
                        const addModal = bootstrap.Modal.getInstance(document.getElementById(
                            'addPaymentModal'));
                        addModal.hide();

                        // Show success modal
                        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();

                        // Reload page after success modal is closed
                        document.getElementById('successModal').addEventListener('hidden.bs.modal', function() {
                            location.reload();
                        });
                    } else {
                        alert('Error: ' + (data.message || 'Failed to create payment setup'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message || 'An error occurred while creating payment setup');
                });
        });

        // Set default effective date to today
        document.querySelector('input[name="effective_date"]').value = new Date().toISOString().split('T')[0];

        // Edit functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-btn') || e.target.closest('.edit-btn')) {
                const btn = e.target.classList.contains('edit-btn') ? e.target : e.target.closest('.edit-btn');
                e.preventDefault();

                // Get data from button attributes
                const id = btn.getAttribute('data-id');
                const type = btn.getAttribute('data-type');
                const level = btn.getAttribute('data-level');
                const term = btn.getAttribute('data-term');
                const amount = btn.getAttribute('data-amount');
                const date = btn.getAttribute('data-date');
                const status = btn.getAttribute('data-status');
                const description = btn.getAttribute('data-description');

                // Populate edit form
                document.getElementById('editPaymentId').value = id;
                document.getElementById('editPaymentType').value = type;
                document.getElementById('editLevel').value = level;
                document.getElementById('editTerm').value = term;
                document.getElementById('editAmount').value = amount;
                document.getElementById('editEffectiveDate').value = date;
                document.getElementById('editStatus').value = status;
                document.getElementById('editDescription').value = description || '';

                // Show edit modal
                const editModal = new bootstrap.Modal(document.getElementById('editPaymentModal'));
                editModal.show();
            }
        });

        // Handle edit form submission
        document.getElementById('editPaymentSetupForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const paymentId = document.getElementById('editPaymentId').value;

            fetch(`{{ url('admin/payments/setup') }}/${paymentId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-HTTP-Method-Override': 'PUT',
                        'Accept': 'application/json'
                    }
                })
                .then(parseJsonResponse)
                .then(data => {
                    if (data.success) {
                        // Hide edit modal
                        const editModal = bootstrap.Modal.getInstance(document.getElementById(
                            'editPaymentModal'));
                        editModal.hide();

                        // Update success modal message
                        document.querySelector('#successModal .fw-semibold').textContent =
                            'Payment Setup has been updated successfully!';

                        // Show success modal
                        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();

                        // Reload page after success modal is closed
                        document.getElementById('successModal').addEventListener('hidden.bs.modal', function() {
                            location.reload();
                        });
                    } else {
                        alert(`❌ Error: ${data.message || 'Failed to update payment setup'}`);
                    }
                })
                .catch(error => {
                    console.error('Edit payment setup error:', error);
                    alert(`❌ Error: ${error.message || 'Unable to update payment setup'}`);
                });
        });

        // Delete functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-btn') || e.target.closest('.delete-btn')) {
                const btn = e.target.classList.contains('delete-btn') ? e.target : e.target.closest('.delete-btn');
                e.preventDefault();

                const id = btn.getAttribute('data-id');
                const type = btn.getAttribute('data-type');

                if (confirm(
                        `⚠️ Are you sure you want to delete "${type}" payment setup?\n\nThis action cannot be undone. If this payment setup is being used in transactions, the deletion will be prevented.`
                    )) {
                    fetch(`{{ url('admin/payments/setup') }}/${id}`, {
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
                                    throw new Error(data.message ||
                                        `HTTP error! status: ${response.status}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                alert(`✅ Success: ${data.message}`);
                                location.reload();
                            } else {
                                alert(`❌ Error: ${data.message || 'Failed to delete payment setup'}`);
                            }
                        })
                        .catch(error => {
                            console.error('Delete payment setup error:', error);
                            alert(`❌ Error: ${error.message}`);
                        });
                }
            }
        });

        // Reset edit form when modal is closed
        document.getElementById('editPaymentModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('editPaymentSetupForm').reset();
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

        .bg-primary-subtle {
            background-color: rgba(13, 110, 253, 0.1) !important;
        }

        .bg-success-subtle {
            background-color: rgba(25, 135, 84, 0.1) !important;
        }

        .bg-info-subtle {
            background-color: rgba(13, 202, 240, 0.1) !important;
        }

        .bg-warning-subtle {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }

        .bg-danger-subtle {
            background-color: rgba(220, 53, 69, 0.1) !important;
        }

        .bg-secondary-subtle {
            background-color: rgba(108, 117, 125, 0.1) !important;
        }
    </style>
@endpush
