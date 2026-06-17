@extends('layouts.admin')

@section('title', 'Add New Subject')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Add New Subject</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}" class="text-primary">Classes & Subjects</a></li>
                    <li class="breadcrumb-item active text-gray-600" aria-current="page">Add New Subject</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i>Back to Subjects
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subject Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.subjects.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label font-weight-bold">Subject Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="e.g., Mathematics, English Language" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="code" class="form-label font-weight-bold">Subject Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code') }}" 
                                           placeholder="e.g., MATH, ENG, PHY" maxlength="10" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="category" class="form-label font-weight-bold">Category <span class="text-danger">*</span></label>
                                    <select class="form-control @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="Core" {{ old('category') == 'Core' ? 'selected' : '' }}>Core</option>
                                        <option value="Elective" {{ old('category') == 'Elective' ? 'selected' : '' }}>Elective</option>
                                        <option value="Vocational" {{ old('category') == 'Vocational' ? 'selected' : '' }}>Vocational</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label font-weight-bold">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="description" class="form-label font-weight-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Enter subject description (optional)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Class Assignment Section -->
                        <div class="card border-left-success mb-4">
                            <div class="card-header py-2">
                                <h6 class="m-0 font-weight-bold text-success">Class Assignment</h6>
                                <small class="text-muted">Select which classes will take this subject</small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="select-all-classes">
                                            <label class="form-check-label font-weight-bold" for="select-all-classes">
                                                Select All Classes
                                            </label>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold text-primary mb-2">Junior Secondary</h6>
                                        <div class="form-check">
                                            <input class="form-check-input class-checkbox" type="checkbox" name="classes[]" value="1" id="jss1">
                                            <label class="form-check-label" for="jss1">JSS 1</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input class-checkbox" type="checkbox" name="classes[]" value="2" id="jss2">
                                            <label class="form-check-label" for="jss2">JSS 2</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input class-checkbox" type="checkbox" name="classes[]" value="3" id="jss3">
                                            <label class="form-check-label" for="jss3">JSS 3</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h6 class="font-weight-bold text-info mb-2">Senior Secondary</h6>
                                        <div class="form-check">
                                            <input class="form-check-input class-checkbox" type="checkbox" name="classes[]" value="4" id="ss1">
                                            <label class="form-check-label" for="ss1">SS 1</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input class-checkbox" type="checkbox" name="classes[]" value="5" id="ss2">
                                            <label class="form-check-label" for="ss2">SS 2</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input class-checkbox" type="checkbox" name="classes[]" value="6" id="ss3">
                                            <label class="form-check-label" for="ss3">SS 3</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Teacher Assignment Section -->
                        <div class="card border-left-warning mb-4">
                            <div class="card-header py-2">
                                <h6 class="m-0 font-weight-bold text-warning">Teacher Assignment</h6>
                                <small class="text-muted">Assign teachers to this subject (optional)</small>
                            </div>
                            <div class="card-body">
                                <div id="teacher-assignments-container">
                                    <div class="row teacher-assignment-row mb-2">
                                        <div class="col-md-5">
                                            <select class="form-control" name="teachers[0][teacher_id]">
                                                <option value="">Select Teacher</option>
                                                <!-- Teachers will be populated dynamically -->
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <select class="form-control" name="teachers[0][classes][]" multiple>
                                                <option value="">Select Classes for this Teacher</option>
                                                <option value="1">JSS 1</option>
                                                <option value="2">JSS 2</option>
                                                <option value="3">JSS 3</option>
                                                <option value="4">SS 1</option>
                                                <option value="5">SS 2</option>
                                                <option value="6">SS 3</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-sm remove-teacher" disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-warning btn-sm" id="add-teacher">
                                    <i class="fas fa-plus"></i> Add Another Teacher
                                </button>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Create Subject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let teacherIndex = 1;
    
    // Auto-generate subject code from name
    $('#name').on('input', function() {
        const name = $(this).val();
        let code = '';
        
        // Generate code from first letters of words
        const words = name.split(' ');
        words.forEach(word => {
            if (word.length > 0) {
                code += word.charAt(0).toUpperCase();
            }
        });
        
        // Limit to 10 characters
        code = code.substring(0, 10);
        $('#code').val(code);
    });
    
    // Select/Deselect all classes
    $('#select-all-classes').change(function() {
        $('.class-checkbox').prop('checked', $(this).is(':checked'));
    });
    
    // Update select all checkbox based on individual selections
    $('.class-checkbox').change(function() {
        const totalCheckboxes = $('.class-checkbox').length;
        const checkedCheckboxes = $('.class-checkbox:checked').length;
        
        $('#select-all-classes').prop('checked', totalCheckboxes === checkedCheckboxes);
    });
    
    // Add new teacher assignment
    $('#add-teacher').click(function() {
        const newTeacher = `
            <div class="row teacher-assignment-row mb-2">
                <div class="col-md-5">
                    <select class="form-control" name="teachers[${teacherIndex}][teacher_id]">
                        <option value="">Select Teacher</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <select class="form-control" name="teachers[${teacherIndex}][classes][]" multiple>
                        <option value="">Select Classes for this Teacher</option>
                        <option value="1">JSS 1</option>
                        <option value="2">JSS 2</option>
                        <option value="3">JSS 3</option>
                        <option value="4">SS 1</option>
                        <option value="5">SS 2</option>
                        <option value="6">SS 3</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-teacher">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('#teacher-assignments-container').append(newTeacher);
        teacherIndex++;
        
        // Enable remove buttons if more than one teacher
        if ($('.teacher-assignment-row').length > 1) {
            $('.remove-teacher').prop('disabled', false);
        }
    });
    
    // Remove teacher assignment
    $(document).on('click', '.remove-teacher', function() {
        $(this).closest('.teacher-assignment-row').remove();
        
        // Disable remove button if only one teacher left
        if ($('.teacher-assignment-row').length === 1) {
            $('.remove-teacher').prop('disabled', true);
        }
    });
    
    // Set default selections based on category
    $('#category').change(function() {
        const category = $(this).val();
        
        // Clear all selections first
        $('.class-checkbox').prop('checked', false);
        $('#select-all-classes').prop('checked', false);
        
        if (category === 'Core') {
            // Core subjects are for all classes
            $('.class-checkbox').prop('checked', true);
            $('#select-all-classes').prop('checked', true);
        } else if (category === 'Elective') {
            // Elective subjects typically for senior secondary
            $('#ss1, #ss2, #ss3').prop('checked', true);
        }
    });
});
</script>
@endpush
@endsection
