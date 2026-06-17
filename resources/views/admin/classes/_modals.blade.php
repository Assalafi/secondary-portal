<!-- Create New Class Modal -->
<div class="modal fade" id="createClassModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('admin.classes.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Create New Class</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Level</label>
            <select class="form-select" name="level" required>
              <option value="">Choose level</option>
              <option value="Nursery">Nursery</option>
              <option value="Primary">Primary</option>
              <option value="JSS">Junior Secondary</option>
              <option value="SS">Senior Secondary</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Class</label>
            <select class="form-select" name="class_name" required>
              <option value="">Choose class</option>
              <option value="NUR 1">NUR 1</option>
              <option value="NUR 2">NUR 2</option>
              <option value="Primary 1">Primary 1</option>
              <option value="Primary 2">Primary 2</option>
              <option value="Primary 3">Primary 3</option>
              <option value="Primary 4">Primary 4</option>
              <option value="Primary 5">Primary 5</option>
              <option value="Primary 6">Primary 6</option>
              <option value="JSS 1">JSS 1</option>
              <option value="JSS 2">JSS 2</option>
              <option value="JSS 3">JSS 3</option>
              <option value="SS 1">SS 1</option>
              <option value="SS 2">SS 2</option>
              <option value="SS 3">SS 3</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Group</label>
            <select class="form-select" name="group">
              <option value="">Choose group</option>
              <option value="">None</option>
              <option value="Science">Science</option>
              <option value="Arts">Arts</option>
              <option value="Commercial">Commercial</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Arm</label>
            <select class="form-select" name="arm" required>
              <option value="">Choose Arm</option>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Class Teacher</label>
            <select class="form-select" name="class_teacher_id">
              <option value="">Select Teacher</option>
              @foreach(\App\Models\User::all() as $teacher)
                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
              @endforeach
            </select>
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

<!-- Delete Class Modal -->
<div class="modal fade" id="deleteClassModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center p-4">
        <h4 class="mb-3">Delete Class?</h4>
        <p class="text-muted">This action cannot be reversed. Are you sure you want to delete class - JSS3D?</p>
        <div class="d-flex justify-content-center gap-2 mt-4">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger">Delete</button>
        </div>
      </div>
    </div>
  </div>
</div>
