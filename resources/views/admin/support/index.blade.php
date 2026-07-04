@extends('layouts.admin')

@section('title', 'Support Tickets')
@section('page-title', 'Support Tickets')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-3">
                        <h6 class="text-muted small mb-1">Total</h6>
                        <h4 class="mb-0 fw-bold">{{ $stats['total'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-3">
                        <h6 class="text-muted small mb-1">Open</h6>
                        <h4 class="mb-0 fw-bold text-danger">{{ $stats['open'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-3">
                        <h6 class="text-muted small mb-1">Awaiting</h6>
                        <h4 class="mb-0 fw-bold text-warning">{{ $stats['awaiting'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-3">
                        <h6 class="text-muted small mb-1">In Progress</h6>
                        <h4 class="mb-0 fw-bold text-info">{{ $stats['in_progress'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-3">
                        <h6 class="text-muted small mb-1">Resolved</h6>
                        <h4 class="mb-0 fw-bold text-success">{{ $stats['resolved'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-3">
                        <h6 class="text-muted small mb-1">Closed</h6>
                        <h4 class="mb-0 fw-bold text-secondary">{{ $stats['closed'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search" placeholder="Search tickets..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="status">
                            <option value="">All Status</option>
                            <option value="Open" {{ request('status') === 'Open' ? 'selected' : '' }}>Open</option>
                            <option value="Awaiting Response" {{ request('status') === 'Awaiting Response' ? 'selected' : '' }}>Awaiting Response</option>
                            <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Resolved" {{ request('status') === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="Closed" {{ request('status') === 'Closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="category">
                            <option value="">All Categories</option>
                            <option value="Academic" {{ request('category') === 'Academic' ? 'selected' : '' }}>Academic</option>
                            <option value="Payment" {{ request('category') === 'Payment' ? 'selected' : '' }}>Payment</option>
                            <option value="Technical" {{ request('category') === 'Technical' ? 'selected' : '' }}>Technical</option>
                            <option value="Account" {{ request('category') === 'Account' ? 'selected' : '' }}>Account</option>
                            <option value="Other" {{ request('category') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="priority">
                            <option value="">All Priority</option>
                            <option value="Low" {{ request('priority') === 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ request('priority') === 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ request('priority') === 'High' ? 'selected' : '' }}>High</option>
                            <option value="Urgent" {{ request('priority') === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                            <a href="{{ route('admin.support.index') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tickets Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                @if($tickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 px-4">Ticket #</th>
                                    <th class="border-0">Title</th>
                                    <th class="border-0">User</th>
                                    <th class="border-0">Category</th>
                                    <th class="border-0">Priority</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Assigned To</th>
                                    <th class="border-0">Created</th>
                                    <th class="border-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $ticket)
                                    <tr>
                                        <td class="px-4"><span class="fw-medium text-primary">{{ $ticket->ticket_number }}</span></td>
                                        <td>{{ $ticket->title }}</td>
                                        <td>{{ $ticket->user->name ?? 'N/A' }}</td>
                                        <td>{{ $ticket->category }}</td>
                                        <td>
                                            @php
                                                $priorityColors = [
                                                    'Low' => 'secondary',
                                                    'Medium' => 'info',
                                                    'High' => 'warning',
                                                    'Urgent' => 'danger'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $priorityColors[$ticket->priority] ?? 'secondary' }}">{{ $ticket->priority }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'Open' => 'danger',
                                                    'Awaiting Response' => 'warning',
                                                    'In Progress' => 'info',
                                                    'Resolved' => 'success',
                                                    'Closed' => 'secondary'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$ticket->status] ?? 'secondary' }}">{{ $ticket->status }}</span>
                                        </td>
                                        <td>{{ $ticket->assignedStaff->name ?? 'Unassigned' }}</td>
                                        <td><small class="text-muted">{{ $ticket->created_at->format('M d, Y') }}</small></td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.support.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                                <button onclick="deleteTicket({{ $ticket->id }})" class="btn btn-sm btn-outline-danger">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">
                        {{ $tickets->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ri-customer-service-line text-muted" style="font-size: 64px;"></i>
                        <h5 class="text-muted mt-3">No Tickets Found</h5>
                        <p class="text-muted">There are no support tickets matching your criteria.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteTicket(id) {
    if (confirm('Are you sure you want to delete this ticket? This action cannot be undone.')) {
        fetch(`{{ route('admin.support.destroy', ':id') }}`.replace(':id', id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred while deleting the ticket');
        });
    }
}
</script>
@endpush
@endsection
