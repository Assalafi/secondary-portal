@extends('layouts.parent')

@section('title', 'Ticket Details')
@section('page-title', 'Support Ticket')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Back Button -->
        <a href="{{ route('parent.support.index') }}" class="btn btn-outline-dark mb-3">
            <i class="ri-arrow-left-line me-2"></i>Back to Tickets
        </a>

        <!-- Ticket Header -->
        <div class="stat-card mb-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h4 class="mb-2">{{ $ticket->title }}</h4>
                    <div class="d-flex gap-3 text-muted small">
                        <span><i class="ri-ticket-line me-1"></i>{{ $ticket->ticket_number }}</span>
                        <span><i class="ri-folder-line me-1"></i>{{ $ticket->category }}</span>
                        <span><i class="ri-time-line me-1"></i>{{ $ticket->created_at->format('M d, Y - h:i A') }}</span>
                    </div>
                </div>
                <div>
                    @if($ticket->status === 'Open')
                        <span class="badge badge-danger">Need Attention</span>
                    @elseif($ticket->status === 'Awaiting Response')
                        <span class="badge badge-warning">Awaiting Response</span>
                    @elseif($ticket->status === 'Resolved')
                        <span class="badge badge-success">Resolved</span>
                    @else
                        <span class="badge bg-secondary">{{ $ticket->status }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Messages Thread -->
        <div class="stat-card">
            <h5 class="mb-4">Conversation</h5>
            
            <div class="messages-container">
                @foreach($ticket->messages as $message)
                    <div class="message-item {{ $message->is_staff_reply ? 'staff-message' : 'user-message' }} mb-4">
                        <div class="d-flex gap-3">
                            <!-- Avatar -->
                            <div class="message-avatar">
                                <div class="rounded-circle {{ $message->is_staff_reply ? 'bg-primary' : 'bg-secondary' }} text-white d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px; font-size: 16px;">
                                    {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                </div>
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
                                <div class="message-content p-3 rounded" style="background-color: {{ $message->is_staff_reply ? '#f0f8ff' : '#f8f8f8' }}">
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
                    <h6 class="mb-3">Add Reply</h6>
                    <form id="replyForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <textarea class="form-control" name="message" rows="4" placeholder="Type your message here..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Add Attachment <span class="text-muted">(Optional - Max 5 files, 10MB each)</span></label>
                            <div class="border rounded p-3 bg-light">
                                <input type="file" class="d-none" id="attachmentInput" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('attachmentInput').click()">
                                    <i class="ri-add-line me-1"></i>Add File
                                </button>
                                <div class="form-text mt-2">Supported formats: Images, PDF, Word, Excel, ZIP, RAR</div>
                                <div id="filePreview" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <span class="submit-text"><i class="ri-send-plane-line me-2"></i>Send Reply</span>
                                <span class="submit-loading d-none">
                                    <span class="spinner-border spinner-border-sm me-2"></span>Sending...
                                </span>
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

@push('scripts')
<script>
document.getElementById('replyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const submitText = submitBtn.querySelector('.submit-text');
    const submitLoading = submitBtn.querySelector('.submit-loading');
    
    // Show loading state
    submitText.classList.add('d-none');
    submitLoading.classList.remove('d-none');
    submitBtn.disabled = true;
    
    const formData = new FormData(form);
    
    fetch('{{ route("parent.support.reply", $ticket->id) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to show new message
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while sending the reply');
    })
    .finally(() => {
        // Reset button state
        submitText.classList.remove('d-none');
        submitLoading.classList.add('d-none');
        submitBtn.disabled = false;
    });
});

// File preview for attachments
let selectedFiles = [];

document.getElementById('attachmentInput').addEventListener('change', function(e) {
    const newFiles = Array.from(e.target.files);
    
    if (newFiles.length > 0) {
        // Add new files to the array
        selectedFiles = selectedFiles.concat(newFiles);
        
        // Update preview
        updateFilePreview();
    }
    
    // Clear the input so it can be used again
    e.target.value = '';
});

function updateFilePreview() {
    const preview = document.getElementById('filePreview');
    preview.innerHTML = '';
    
    if (selectedFiles.length > 0) {
        const fileList = document.createElement('div');
        fileList.className = 'd-flex flex-wrap gap-2';
        
        for (let i = 0; i < selectedFiles.length; i++) {
            const file = selectedFiles[i];
            const fileBadge = document.createElement('span');
            fileBadge.className = 'badge bg-secondary d-flex align-items-center';
            fileBadge.innerHTML = `
                <i class="ri-file-line me-1"></i>
                ${file.name}
                <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 10px;" onclick="removeFile(${i})"></button>
            `;
            fileList.appendChild(fileBadge);
        }
        
        preview.appendChild(fileList);
    }
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    updateFilePreview();
}

// Before form submission, add selected files to the input
document.getElementById('replyForm').addEventListener('submit', function(e) {
    const input = document.getElementById('attachmentInput');
    const dt = new DataTransfer();
    
    for (let i = 0; i < selectedFiles.length; i++) {
        dt.items.add(selectedFiles[i]);
    }
    
    input.files = dt.files;
});
</script>
@endpush
@endsection
