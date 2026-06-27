<div class="modal fade" id="createClassModal2" tabindex="-1" aria-labelledby="createClassModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createClassModalLabel">Create New Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.classes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="level" class="form-label">Level</label>
                        <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                            <option value="" disabled selected>Choose level</option>
                            @foreach($levels as $level)
                                <option value="{{ $level }}" {{ old('level') == $level ? 'selected' : '' }}>{{ $level }}</option>
                            @endforeach
                        </select>
                        @error('level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="class_name" class="form-label">Class</label>
                        <select class="form-select @error('class_name') is-invalid @enderror" id="class_name" name="class_name" required>
                            <option value="" disabled selected>Choose class</option>
                        </select>
                        @error('class_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="group" class="form-label">Group</label>
                        <select class="form-select @error('group') is-invalid @enderror" id="group" name="group">
                            <option value="" disabled selected>Choose group</option>
                        </select>
                        @error('group')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="arm" class="form-label">Arm</label>
                        <select class="form-select @error('arm') is-invalid @enderror" id="arm" name="arm"
                            required>
                            <option value="" disabled selected>Choose Arm</option>
                            @foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $armLetter)
                                <option value="{{ $armLetter }}" {{ old('arm') == $armLetter ? 'selected' : '' }}>
                                    {{ $armLetter }}</option>
                            @endforeach
                        </select>
                        @error('arm')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacity</label>
                        <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity"
                            name="capacity" value="{{ old('capacity') ?? 40 }}" min="1" max="100" required>
                        <div class="form-text">Maximum number of students for this class arm</div>
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="class_teacher" class="form-label">Class Teacher</label>
                        <select class="form-select @error('teacher_id') is-invalid @enderror" id="class_teacher"
                            name="teacher_id">
                            <option value="" selected>Select Teacher (Optional)</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}"
                                    {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>
