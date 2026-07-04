@extends('layouts.staff')

@section('title', 'My Support Tickets')
@section('page-title', 'My Support Tickets')

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
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                            <a href="{{ route('teacher.support.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                                        <td><small class="text-muted">{{ $ticket->created_at->format('M d, Y') }}</small></td>
                                        <td>
                                            <a href="{{ route('teacher.support.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary">
                                                View <i class="ri-arrow-right-line"></i>
                                            </a>
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
                        <h5 class="text-muted mt-3">No Tickets Assigned</h5>
                        <p class="text-muted">You don't have any support tickets assigned to you yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
