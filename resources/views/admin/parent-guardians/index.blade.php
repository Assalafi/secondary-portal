@extends('layouts.admin')

@section('title', 'Parent/Guardian Directory')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.parent-guardians.overview') }}">Parent/Guardians</a></li>
                    <li class="breadcrumb-item active">Directory</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 fw-bold mt-2">Parent/Guardian Directory</h1>
        </div>
        <a href="{{ route('admin.parent-guardians.create') }}" class="btn btn-primary">
            <i class="ri-add-line me-1"></i> Add Parent/Guardian
        </a>
    </div>

    <!-- Search and Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.parent-guardians.index') }}" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">Search</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by name, email, or phone..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-search-line me-1"></i> Search
                    </button>
                    <a href="{{ route('admin.parent-guardians.index') }}" class="btn btn-outline-secondary">
                        <i class="ri-refresh-line me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Parents List Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
            <div>
                <h5 class="mb-0 fw-bold">All Parents/Guardians</h5>
                <small class="text-muted">{{ $parents->total() }} total records</small>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-danger" id="bulkDeleteBtn" style="display: none;">
                    <i class="ri-delete-bin-line me-1"></i> Delete Selected
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            @if($parents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="30">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>Parent/Guardian</th>
                                <th>Contact Information</th>
                                <th>Dependents</th>
                                <th>Last Login</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($parents as $parent)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input parent-checkbox" 
                                               value="{{ $parent->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary text-white me-2">
                                                {{ strtoupper(substr($parent->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $parent->name }}</div>
                                                <small class="text-muted">{{ $parent->occupation ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <i class="ri-mail-line text-muted me-1"></i>
                                            <small>{{ $parent->email }}</small>
                                        </div>
                                        @if($parent->phone)
                                            <div>
                                                <i class="ri-phone-line text-muted me-1"></i>
                                                <small>{{ $parent->phone }}</small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info">
                                            {{ $parent->dependents_count }} {{ Str::plural('student', $parent->dependents_count) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($parent->last_login_at)
                                            <small>{{ $parent->last_login_at->diffForHumans() }}</small>
                                        @else
                                            <small class="text-muted">Never</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($parent->last_login_at && $parent->last_login_at >= now()->subDays(30))
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.parent-guardians.show', $parent->id) }}">
                                                        <i class="ri-eye-line me-2"></i>View Profile
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.parent-guardians.edit', $parent->id) }}">
                                                        <i class="ri-edit-line me-2"></i>Edit
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.parent-guardians.reset-password', $parent->id) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-warning" 
                                                                onclick="return confirm('Reset password to default?')">
                                                            <i class="ri-key-line me-2"></i>Reset Password
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.parent-guardians.destroy', $parent->id) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" 
                                                                onclick="return confirm('Are you sure you want to delete this parent/guardian?')">
                                                            <i class="ri-delete-bin-line me-2"></i>Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white border-top">
                    {{ $parents->links() }}
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="ri-parent-line" style="font-size: 4rem;"></i>
                    <p class="mt-3">No parents/guardians found</p>
                    @if(request()->has('search') || request()->has('status'))
                        <a href="{{ route('admin.parent-guardians.index') }}" class="btn btn-outline-primary">
                            Clear Filters
                        </a>
                    @else
                        <a href="{{ route('admin.parent-guardians.create') }}" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i> Add First Parent
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Bulk Delete Form -->
    <form id="bulkDeleteForm" action="{{ route('admin.parent-guardians.bulk-action') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="parent_ids" id="bulkParentIds">
    </form>

    @push('styles')
    <style>
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Select All functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.parent-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            toggleBulkActions();
        });

        // Individual checkbox change
        document.querySelectorAll('.parent-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', toggleBulkActions);
        });

        function toggleBulkActions() {
            const checkedBoxes = document.querySelectorAll('.parent-checkbox:checked');
            const bulkBtn = document.getElementById('bulkDeleteBtn');
            
            if (checkedBoxes.length > 0) {
                bulkBtn.style.display = 'block';
            } else {
                bulkBtn.style.display = 'none';
            }
        }

        // Bulk delete
        document.getElementById('bulkDeleteBtn')?.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.parent-checkbox:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            
            if (confirm(`Delete ${ids.length} selected parent/guardian(s)?`)) {
                document.getElementById('bulkParentIds').value = JSON.stringify(ids);
                document.getElementById('bulkDeleteForm').submit();
            }
        });
    </script>
    @endpush
@endsection
