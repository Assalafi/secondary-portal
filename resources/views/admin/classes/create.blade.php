@extends('layouts.admin')

@section('title', 'Add New Class')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Add New Class</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}" class="text-primary">Classes & Subjects</a></li>
                    <li class="breadcrumb-item active text-gray-600" aria-current="page">Add New Class</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i>Back to Classes
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Class Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.classes.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label font-weight-bold">Class Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="e.g., JSS 1, SS 2" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="level" class="form-label font-weight-bold">Level <span class="text-danger">*</span></label>
                                    <select class="form-control @error('level') is-invalid @enderror" 
                                            id="level" name="level" required>
                                        <option value="">Select Level</option>
                                        <option value="JSS 1" {{ old('level') == 'JSS 1' ? 'selected' : '' }}>JSS 1</option>
                                        <option value="JSS 2" {{ old('level') == 'JSS 2' ? 'selected' : '' }}>JSS 2</option>
                                        <option value="JSS 3" {{ old('level') == 'JSS 3' ? 'selected' : '' }}>JSS 3</option>
                                        <option value="SS 1" {{ old('level') == 'SS 1' ? 'selected' : '' }}>SS 1</option>
                                        <option value="SS 2" {{ old('level') == 'SS 2' ? 'selected' : '' }}>SS 2</option>
                                        <option value="SS 3" {{ old('level') == 'SS 3' ? 'selected' : '' }}>SS 3</option>
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="numeric_level" class="form-label font-weight-bold">Numeric Level <span class="text-danger">*</span></label>
                                    <select class="form-control @error('numeric_level') is-invalid @enderror" 
                                            id="numeric_level" name="numeric_level" required>
                                        <option value="">Select Numeric Level</option>
                                        <option value="1" {{ old('numeric_level') == '1' ? 'selected' : '' }}>1</option>
                                        <option value="2" {{ old('numeric_level') == '2' ? 'selected' : '' }}>2</option>
                                        <option value="3" {{ old('numeric_level') == '3' ? 'selected' : '' }}>3</option>
                                        <option value="4" {{ old('numeric_level') == '4' ? 'selected' : '' }}>4</option>
                                        <option value="5" {{ old('numeric_level') == '5' ? 'selected' : '' }}>5</option>
                                        <option value="6" {{ old('numeric_level') == '6' ? 'selected' : '' }}>6</option>
                                    </select>
                                    @error('numeric_level')
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
                                      placeholder="Enter class description (optional)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Class Arms Section -->
                        <div class="card border-left-primary mb-4">
                            <div class="card-header py-2">
                                <h6 class="m-0 font-weight-bold text-primary">Class Arms</h6>
                                <small class="text-muted">Add arms for this class (e.g., A, B, C)</small>
                            </div>
                            <div class="card-body">
                                <div id="class-arms-container">
                                    <div class="row class-arm-row mb-2">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="arms[0][name]" 
                                                   placeholder="Arm Name (e.g., A)" value="A">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control" name="arms[0][capacity]" 
                                                   placeholder="Capacity" value="40" min="1">
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-control" name="arms[0][class_teacher_id]">
                                                <option value="">Select Class Teacher</option>
                                                <!-- Teachers will be populated dynamically -->
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-sm remove-arm" disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-success btn-sm" id="add-arm">
                                    <i class="fas fa-plus"></i> Add Another Arm
                                </button>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Create Class
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
    let armIndex = 1;
    
    // Add new arm
    $('#add-arm').click(function() {
        const newArm = `
            <div class="row class-arm-row mb-2">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="arms[${armIndex}][name]" 
                           placeholder="Arm Name (e.g., B)">
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" name="arms[${armIndex}][capacity]" 
                           placeholder="Capacity" value="40" min="1">
                </div>
                <div class="col-md-4">
                    <select class="form-control" name="arms[${armIndex}][class_teacher_id]">
                        <option value="">Select Class Teacher</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-arm">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('#class-arms-container').append(newArm);
        armIndex++;
        
        // Enable remove buttons if more than one arm
        if ($('.class-arm-row').length > 1) {
            $('.remove-arm').prop('disabled', false);
        }
    });
    
    // Remove arm
    $(document).on('click', '.remove-arm', function() {
        $(this).closest('.class-arm-row').remove();
        
        // Disable remove button if only one arm left
        if ($('.class-arm-row').length === 1) {
            $('.remove-arm').prop('disabled', true);
        }
    });
    
    // Auto-fill class name based on level selection
    $('#level').change(function() {
        const level = $(this).val();
        if (level) {
            $('#name').val(level);
            
            // Set numeric level based on selection
            const numericLevel = level.includes('JSS') ? level.split(' ')[1] : (parseInt(level.split(' ')[1]) + 3);
            $('#numeric_level').val(numericLevel);
        }
    });
});
</script>
@endpush
@endsection
