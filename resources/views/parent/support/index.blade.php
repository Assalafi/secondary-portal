@extends('layouts.parent')

@section('title', 'Support')
@section('page-title', 'Support')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Support Tickets</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                <i class="ri-add-line me-2"></i>Add New Ticket
            </button>
        </div>

        @if($tickets->isEmpty())
            <div class="stat-card text-center py-5">
                <i class="ri-customer-service-line" style="font-size: 64px; color: #ccc;"></i>
                <h5 class="mt-3 mb-2">No tickets yet</h5>
                <p class="text-muted">You haven't created any support tickets yet.</p>
                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                    Create First Ticket
                </button>
            </div>
        @else
            <!-- Tickets List -->
            <div class="stat-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>MY TICKET</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr>
                                <td>
                                    <div>
                                        <h6 class="mb-1">{{ $ticket->title }}</h6>
                                        <small class="text-muted">Ticket #{{ $ticket->ticket_number }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if($ticket->status === 'Open')
                                        <span class="badge badge-danger">Need Attention</span>
                                    @elseif($ticket->status === 'Awaiting Response')
                                        <span class="badge badge-warning">Awaiting Response</span>
                                    @elseif($ticket->status === 'Resolved')
                                        <span class="badge badge-success">Resolved</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $ticket->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('parent.support.show', $ticket->id) }}" class="btn btn-sm btn-outline-dark">
                                        View <i class="ri-arrow-right-line"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

                @if($tickets->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- New Ticket Modal -->
<div class="modal fade" id="newTicketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">New Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="newTicketForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Ticket title/ title here</label>
                        <input type="text" class="form-control" name="title" placeholder="Enter ticket title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category" required>
                            <option value="">Choose category</option>
                            <option value="Academic">Academic</option>
                            <option value="Payment">Payment</option>
                            <option value="Technical">Technical</option>
                            <option value="Account">Account</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" name="message" rows="5" placeholder="Describe your issue..." required></textarea>
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

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-dark flex-grow-1" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <span class="submit-text">Send</span>
                            <span class="submit-loading d-none">
                                <span class="spinner-border spinner-border-sm me-2"></span>Sending...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-success mb-3">
                    <i class="ri-checkbox-circle-line" style="font-size: 64px;"></i>
                </div>
                <h5 class="mb-3">Support - new ticket added</h5>
                <p class="text-muted">Your ticket has been submitted successfully</p>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('newTicketForm').addEventListener('submit', function(e) {
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
    
    fetch('{{ route("parent.support.store") }}', {
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
            // Hide new ticket modal
            const newTicketModal = bootstrap.Modal.getInstance(document.getElementById('newTicketModal'));
            newTicketModal.hide();
            
            // Show success modal
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            
            // Reload page after closing success modal
            document.getElementById('successModal').addEventListener('hidden.bs.modal', function() {
                location.reload();
            });
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the ticket');
    })
    .finally(() => {
        // Reset button state
        submitText.classList.remove('d-none');
        submitLoading.classList.add('d-none');
        submitBtn.disabled = false;
    });
});

// File preview for attachments
document.getElementById('attachmentInput').addEventListener('change', function(e) {
    const newFiles = e.target.files;
    const preview = document.getElementById('filePreview');
    
    if (newFiles.length > 0) {
        // Get existing files
        const input = document.getElementById('attachmentInput');
        const dt = new DataTransfer();
        
        // Add existing files
        const existingFiles = input.files;
        for (let i = 0; i < existingFiles.length; i++) {
            dt.items.add(existingFiles[i]);
        }
        
        // Add new files
        for (let i = 0; i < newFiles.length; i++) {
            dt.items.add(newFiles[i]);
        }
        
        // Update input
        input.files = dt.files;
        
        // Update preview
        updateFilePreview();
    }
});

function updateFilePreview() {
    const input = document.getElementById('attachmentInput');
    const files = input.files;
    const preview = document.getElementById('filePreview');
    preview.innerHTML = '';
    
    if (files.length > 0) {
        const fileList = document.createElement('div');
        fileList.className = 'd-flex flex-wrap gap-2';
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
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
    const input = document.getElementById('attachmentInput');
    const dt = new DataTransfer();
    const files = input.files;
    
    for (let i = 0; i < files.length; i++) {
        if (i !== index) {
            dt.items.add(files[i]);
        }
    }
    
    input.files = dt.files;
    updateFilePreview();
}
</script>
@endpush
@endsection
