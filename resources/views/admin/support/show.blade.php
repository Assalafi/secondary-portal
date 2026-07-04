@extends('layouts.admin')

@section('title', 'Ticket Details')
@section('page-title', 'Support Ticket Details')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Back Button -->
        <a href="{{ route('admin.support.index') }}" class="btn btn-outline-secondary mb-3">
            <i class="ri-arrow-left-line me-1"></i>Back to Tickets
        </a>

        <!-- Ticket Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="ri-customer-service-line text-white" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-1 fw-bold">{{ $ticket->title }}</h5>
                                <div class="d-flex gap-3 text-muted small mb-2">
                                    <span><i class="ri-ticket-line me-1"></i>{{ $ticket->ticket_number }}</span>
                                    <span><i class="ri-folder-line me-1"></i>{{ $ticket->category }}</span>
                                    <span><i class="ri-time-line me-1"></i>{{ $ticket->created_at->format('M d, Y - h:i A') }}</span>
                                </div>
                                <div class="d-flex gap-2">
                                    @php
                                        $statusColors = [
                                            'Open' => 'danger',
                                            'Awaiting Response' => 'warning',
                                            'In Progress' => 'info',
                                            'Resolved' => 'success',
                                            'Closed' => 'secondary'
                                        ];
                                        $priorityColors = [
                                            'Low' => 'secondary',
                                            'Medium' => 'info',
                                            'High' => 'warning',
                                            'Urgent' => 'danger'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$ticket->status] ?? 'secondary' }}">{{ $ticket->status }}</span>
                                    <span class="badge bg-{{ $priorityColors[$ticket->priority] ?? 'secondary' }}">{{ $ticket->priority }} Priority</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-2 justify-content-end">
                            @if($ticket->status !== 'Closed')
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignModal">
                                    <i class="ri-user-add-line me-1"></i>Assign
                                </button>
                                <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal">
                                    <i class="ri-settings-3-line me-1"></i>Status
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    @if($ticket->user->photo_path)
                        <img src="{{ asset('storage/' . $ticket->user->photo_path) }}" alt="{{ $ticket->user->name }}" class="rounded-circle" style="width: 48px; height: 48px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="ri-user-line text-white" style="font-size: 24px;"></i>
                        </div>
                    @endif
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $ticket->user->name }}</h6>
                        <small class="text-muted">{{ $ticket->user->email ?? 'N/A' }}</small>
                    </div>
                    <div class="ms-auto">
                        @if($ticket->assignedStaff)
                            <span class="badge bg-success">Assigned to {{ $ticket->assignedStaff->name }}</span>
                        @else
                            <span class="badge bg-warning">Unassigned</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages Thread -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="ri-message-3-line me-2 text-primary"></i>Conversation</h6>
            </div>
            <div class="card-body">
                <div class="messages-container">
                    @foreach($ticket->messages as $message)
                        <div class="message-item {{ $message->is_staff_reply ? 'staff-message' : 'user-message' }} mb-4">
                            <div class="d-flex gap-3">
                                <!-- Avatar -->
                                <div class="message-avatar">
                                    @if($message->user->photo_path)
                                        <img src="{{ asset('storage/' . $message->user->photo_path) }}" alt="{{ $message->user->name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle {{ $message->is_staff_reply ? 'bg-primary' : 'bg-secondary' }} text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 16px;">
                                            {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Message Content -->
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <strong>{{ $message->user->name }}</strong>
                                            @if($message->is_staff_reply)
                                                <span class="badge bg-primary ms-2" style="font-size: 10px;">Support Team</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="message-content p-3 rounded" style="background-color: {{ $message->is_staff_reply ? '#e3f2fd' : '#f5f5f5' }}; border-left: 3px solid {{ $message->is_staff_reply ? '#2196f3' : '#9e9e9e' }};">
                                        <p class="mb-0">{{ $message->message }}</p>
                                        @if($message->attachments && $message->attachments->count() > 0)
                                            <div class="mt-3">
                                                <small class="text-muted fw-bold">Attachments:</small>
                                                <div class="d-flex flex-wrap gap-2 mt-2">
                                                    @foreach($message->attachments as $attachment)
                                                        <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary d-flex align-items-center">
                                                            <i class="{{ $attachment->file_icon }} me-1"></i>
                                                            <span class="text-truncate" style="max-width: 150px;">{{ $attachment->file_name }}</span>
                                                            <small class="text-muted ms-1">({{ $attachment->formatted_size }})</small>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($ticket->status !== 'Closed')
                    <!-- Reply Form -->
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="mb-3 fw-bold"><i class="ri-reply-line me-2 text-primary"></i>Add Reply</h6>
                        <form action="{{ route('admin.support.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <textarea class="form-control" name="message" rows="4" placeholder="Type your response here..." required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Attachments <span class="text-muted">(Optional - Max 5 files, 10MB each)</span></label>
                                <input type="file" class="form-control" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar">
                                <div class="form-text">Supported formats: Images, PDF, Word, Excel, ZIP, RAR</div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-send-plane-line me-1"></i>Send Reply
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="alert alert-info mt-4">
                        <i class="ri-information-line me-2"></i>This ticket has been closed and cannot receive new replies.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Assign Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.support.assign', $ticket->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Assign To</label>
                        <select class="form-select" name="assigned_to">
                            <option value="">Unassigned</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}" {{ $ticket->assigned_to == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Update Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.support.updateStatus', $ticket->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Status</label>
                        <select class="form-select" name="status">
                            <option value="Open" {{ $ticket->status === 'Open' ? 'selected' : '' }}>Open</option>
                            <option value="Awaiting Response" {{ $ticket->status === 'Awaiting Response' ? 'selected' : '' }}>Awaiting Response</option>
                            <option value="In Progress" {{ $ticket->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Resolved" {{ $ticket->status === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="Closed" {{ $ticket->status === 'Closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Priority</label>
                        <select class="form-select" name="priority">
                            <option value="Low" {{ $ticket->priority === 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ $ticket->priority === 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ $ticket->priority === 'High' ? 'selected' : '' }}>High</option>
                            <option value="Urgent" {{ $ticket->priority === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .message-item {
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush
@endsection
