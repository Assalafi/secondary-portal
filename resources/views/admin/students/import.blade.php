@extends('layouts.admin')

@section('title', 'Import Students')
@section('page-title', 'Import Students')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Import Form -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="ri-file-excel-2-line me-2 text-success"></i>Import Students from Excel
                    </h6>
                    <a href="{{ route('admin.students.import.template') }}" class="btn btn-outline-success btn-sm">
                        <i class="ri-download-line me-1"></i>Download Template
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.students.import.process') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf

                    <!-- Class Selection -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Class <span class="text-danger">*</span></label>
                            <select class="form-select" id="school_class_id" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Class Arm <span class="text-danger">*</span></label>
                            <select class="form-select" name="class_arm_id" id="class_arm_id" required disabled>
                                <option value="">Select Class First</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Academic Session <span class="text-danger">*</span></label>
                            <select class="form-select" name="academic_session_id" required>
                                <option value="">Select Session</option>
                                @foreach($academicSessions as $session)
                                    <option value="{{ $session->id }}" {{ $session->is_current ? 'selected' : '' }}>
                                        {{ $session->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">Excel File <span class="text-danger">*</span></label>
                        <div class="upload-area border-2 border-dashed rounded p-4 text-center" id="uploadArea">
                            <input type="file" name="file" id="fileInput" class="d-none" accept=".xlsx,.xls,.csv" required>
                            <div id="uploadPlaceholder">
                                <i class="ri-file-excel-2-line text-success" style="font-size: 48px;"></i>
                                <p class="mt-2 mb-1 fw-medium">Click to upload or drag and drop</p>
                                <small class="text-muted">Supports .xlsx, .xls, .csv (Max 10MB)</small>
                            </div>
                            <div id="fileInfo" class="d-none">
                                <i class="ri-file-excel-2-fill text-success" style="font-size: 36px;"></i>
                                <p class="mt-2 mb-0 fw-medium" id="fileName"></p>
                                <small class="text-muted" id="fileSize"></small>
                                <br>
                                <button type="button" class="btn btn-outline-danger btn-sm mt-2" id="removeFile">
                                    <i class="ri-close-line me-1"></i>Remove
                                </button>
                            </div>
                        </div>
                        @error('file')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="importBtn" disabled>
                            <i class="ri-upload-2-line me-1"></i>Import Students
                        </button>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                            <i class="ri-arrow-left-line me-1"></i>Back to List
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Import Results -->
        @if(session('success'))
            <div class="card border-0 shadow-sm mb-4 border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="ri-checkbox-circle-fill text-success me-2 fs-4"></i>
                        <h6 class="mb-0 fw-bold text-success">Import Completed</h6>
                    </div>
                    <p class="mb-0">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('import_errors') && count(session('import_errors')) > 0)
            <div class="card border-0 shadow-sm mb-4 border-start border-warning border-4">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex align-items-center">
                        <i class="ri-error-warning-fill text-warning me-2 fs-4"></i>
                        <h6 class="mb-0 fw-bold">Skipped Rows ({{ count(session('import_errors')) }})</h6>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                        @foreach(session('import_errors') as $error)
                            <div class="list-group-item py-2 px-3">
                                <small class="text-danger"><i class="ri-close-circle-line me-1"></i>{{ $error }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Instructions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="ri-information-line me-2 text-info"></i>Instructions
                </h6>
            </div>
            <div class="card-body">
                <ol class="ps-3 mb-0">
                    <li class="mb-2"><small>Download the Excel template using the button above.</small></li>
                    <li class="mb-2"><small>Fill in the student data following the column headers.</small></li>
                    <li class="mb-2"><small>Select the target class arm and academic session.</small></li>
                    <li class="mb-2"><small>Upload the file and click "Import Students".</small></li>
                </ol>
            </div>
        </div>

        <!-- Column Guide -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="ri-table-line me-2 text-primary"></i>Column Guide
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between py-2">
                        <small class="fw-medium">first_name</small>
                        <span class="badge bg-danger">Required</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2">
                        <small class="fw-medium">surname</small>
                        <span class="badge bg-danger">Required</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2">
                        <small class="fw-medium">middle_name</small>
                        <span class="badge bg-secondary">Optional</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2">
                        <small class="fw-medium">gender</small>
                        <span class="badge bg-danger">Required</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2">
                        <small class="fw-medium">date_of_birth</small>
                        <span class="badge bg-danger">Required</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2">
                        <small class="fw-medium">state_of_origin</small>
                        <span class="badge bg-secondary">Optional</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2">
                        <small class="fw-medium">lga</small>
                        <span class="badge bg-secondary">Optional</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2">
                        <small class="fw-medium">nationality</small>
                        <span class="badge bg-secondary">Optional</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2">
                        <small class="fw-medium">admission_no</small>
                        <span class="badge bg-secondary">Optional</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2">
                        <small class="fw-medium">email</small>
                        <span class="badge bg-secondary">Optional</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2">
                        <small class="fw-medium">admission_date</small>
                        <span class="badge bg-secondary">Optional</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h6 class="fw-bold mb-2"><i class="ri-lightbulb-line me-1 text-warning"></i>Notes</h6>
                <ul class="ps-3 mb-0 small text-muted">
                    <li class="mb-1">Gender accepts: Male, Female, M, F</li>
                    <li class="mb-1">Date formats: YYYY-MM-DD, DD/MM/YYYY, MM/DD/YYYY</li>
                    <li class="mb-1">If admission_no is empty, it will be auto-generated</li>
                    <li class="mb-1">If email is empty, it will be auto-generated from admission_no</li>
                    <li class="mb-1">Default password = admission number</li>
                    <li class="mb-1">Duplicate admission numbers will be skipped</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .upload-area {
        cursor: pointer;
        transition: all 0.3s;
        border-color: #dee2e6 !important;
    }
    .upload-area:hover, .upload-area.dragover {
        border-color: #198754 !important;
        background-color: #f8fdf9;
    }
</style>

@push('scripts')
<script>
// Class arm cascade
document.getElementById('school_class_id').addEventListener('change', function() {
    const classId = this.value;
    const classArmSelect = document.getElementById('class_arm_id');
    
    classArmSelect.innerHTML = '<option value="">Loading...</option>';
    classArmSelect.disabled = true;

    if (!classId) {
        classArmSelect.innerHTML = '<option value="">Select Class First</option>';
        return;
    }

    fetch(`/api/class-arms/${classId}`)
        .then(response => response.json())
        .then(data => {
            classArmSelect.innerHTML = '<option value="">Select Arm</option>';
            const arms = data.arms || data;
            arms.forEach(arm => {
                classArmSelect.innerHTML += `<option value="${arm.id}">${arm.name}</option>`;
            });
            classArmSelect.disabled = false;
        })
        .catch(() => {
            classArmSelect.innerHTML = '<option value="">Error loading arms</option>';
        });
});

// File upload handling
const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('fileInput');
const uploadPlaceholder = document.getElementById('uploadPlaceholder');
const fileInfo = document.getElementById('fileInfo');
const fileName = document.getElementById('fileName');
const fileSize = document.getElementById('fileSize');
const removeFile = document.getElementById('removeFile');
const importBtn = document.getElementById('importBtn');

uploadArea.addEventListener('click', () => fileInput.click());

uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    if (e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        showFileInfo(e.dataTransfer.files[0]);
    }
});

fileInput.addEventListener('change', function() {
    if (this.files.length) {
        showFileInfo(this.files[0]);
    }
});

removeFile.addEventListener('click', (e) => {
    e.stopPropagation();
    fileInput.value = '';
    uploadPlaceholder.classList.remove('d-none');
    fileInfo.classList.add('d-none');
    importBtn.disabled = true;
});

function showFileInfo(file) {
    fileName.textContent = file.name;
    fileSize.textContent = formatFileSize(file.size);
    uploadPlaceholder.classList.add('d-none');
    fileInfo.classList.remove('d-none');
    importBtn.disabled = false;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Form submission loading state
document.getElementById('importForm').addEventListener('submit', function() {
    importBtn.disabled = true;
    importBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Importing...';
});
</script>
@endpush
@endsection
