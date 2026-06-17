<!-- Add New Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content modal-soft">
      <form action="{{ route('admin.subjects.store') }}" method="POST">
        @csrf
        <div class="modal-header border-0 pb-0">
          <h4 class="modal-title fw-bold text-dark">Add New Subject</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-1">
          <div class="mb-3">
            <label class="form-label text-dark">Subject Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control input-soft" name="name" placeholder="Choose a subject name" required>
          </div>
          
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label text-dark">Level</label>
              <select class="form-select input-soft" id="sub_level" name="level">
                <option value="" selected>Choose level</option>
                @foreach(($levels ?? []) as $lvl)
                  <option value="{{ $lvl }}">{{ $lvl }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label text-dark">Assign to Class</label>
              <select class="form-select input-soft" id="sub_class_name" name="class_name">
                <option value="" selected>Choose class</option>
                @foreach(($classNames ?? []) as $cn)
                  <option value="{{ $cn }}">{{ $cn }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label text-dark">Group</label>
              <select class="form-select input-soft" id="sub_group" name="group">
                <option value="" selected>Choose group</option>
                @foreach(($groups ?? []) as $grp)
                  <option value="{{ $grp }}">{{ $grp }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label text-dark">Arm</label>
              <select class="form-select input-soft" id="sub_arm" name="arm">
                <option value="" selected>Choose Arm</option>
                @foreach(($arms ?? []) as $arm)
                  <option value="{{ $arm }}">{{ $arm }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label text-dark">Assign Teacher</label>
              <select class="form-select input-soft" id="sub_teacher_id" name="teacher_id">
                <option value="" selected>Choose Teacher to Assign</option>
                @foreach(($teachers ?? []) as $t)
                  <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0 d-flex justify-content-start gap-3">
          <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-confirm">Confirm</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Subject Modal -->
<div class="modal fade" id="editSubjectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content modal-soft">
      <form id="editSubjectForm" action="#" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header border-0 pb-0">
          <h4 class="modal-title fw-bold text-dark">Edit Subject</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-1">
          <div class="mb-3">
            <label class="form-label text-dark">Subject Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control input-soft" id="edit_name" name="name" placeholder="Choose a subject name" required>
          </div>

          <div class="row g-3">
            <div class="col-12">
              <label class="form-label text-dark">Level</label>
              <select class="form-select input-soft" id="edit_level" name="level">
                <option value="" selected>Choose level</option>
                @foreach(($levels ?? []) as $lvl)
                  <option value="{{ $lvl }}">{{ $lvl }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label text-dark">Assign to Class</label>
              <select class="form-select input-soft" id="edit_class_name" name="class_name">
                <option value="" selected>Choose class</option>
                @foreach(($classNames ?? []) as $cn)
                  <option value="{{ $cn }}">{{ $cn }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label text-dark">Group</label>
              <select class="form-select input-soft" id="edit_group" name="group">
                <option value="" selected>Choose group</option>
                @foreach(($groups ?? []) as $grp)
                  <option value="{{ $grp }}">{{ $grp }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label text-dark">Arm</label>
              <select class="form-select input-soft" id="edit_arm" name="arm">
                <option value="" selected>Choose Arm</option>
                @foreach(($arms ?? []) as $arm)
                  <option value="{{ $arm }}">{{ $arm }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label text-dark">Assign Teacher</label>
              <select class="form-select input-soft" id="edit_teacher_id" name="teacher_id">
                <option value="" selected>Choose Teacher to Assign</option>
                @foreach(($teachers ?? []) as $t)
                  <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0 d-flex justify-content-start gap-3">
          <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-confirm">Update</button>
        </div>
      </form>
    </div>
  </div>
  
</div>

<!-- Delete Subject Modal -->
<div class="modal fade" id="deleteSubjectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-soft">
      <div class="modal-body text-center p-4">
        <h4 class="mb-3">Delete Subject?</h4>
        <p class="text-muted">This action cannot be reversed. Are you sure you want to delete Math J-03 subject?</p>
        <div class="d-flex justify-content-center gap-2 mt-4">
          <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger">Confirm</button>
        </div>
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
  /* Modal wrapper */
  .modal-soft { border-radius: 1.25rem; border: 0; box-shadow: 0 24px 48px rgba(17, 24, 39, 0.12); position: relative; }
  .modal-soft::before { content: ""; position: absolute; top: -12px; left: 50%; transform: translateX(-50%); width: 72px; height: 10px; background: #111827; border-radius: 9999px; }
  .modal-title { font-size: 1.375rem; }
  .form-label { color: #111827; }

  /* Inputs */
  .input-soft { background: #f4f4f5; border: 0; color: #111827; border-radius: 12px; height: 52px; }
  .input-soft:focus { background: #fff; box-shadow: 0 0 0 .25rem rgba(17, 24, 39, 0.06); }
  .input-soft::placeholder { color: #9ca3af; }
  .form-select.input-soft { padding-right: 2.5rem; }

  /* Buttons */
  .btn-cancel { background: #efefef; color: #111827; border: 0; border-radius: 9999px; padding: .6rem 1.2rem; font-weight: 600; }
  .btn-cancel:hover { background: #e7e7e7; }
  .btn-confirm { background: #111827; color: #fff; border: 0; border-radius: 9999px; padding: .6rem 1.2rem; font-weight: 600; }
  .btn-confirm:hover { background: #0b1220; }
</style>
@endpush

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Dynamic class/group by level
    const levelSel = document.getElementById('sub_level');
    const classSel = document.getElementById('sub_class_name');
    const groupSel = document.getElementById('sub_group');
    if (levelSel) {
      levelSel.addEventListener('change', function () {
        const level = levelSel.value;
        if (!level) { return; }
        const url = "{{ route('admin.get-class-details-by-level') }}" + '?level=' + encodeURIComponent(level);
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
          .then(r => r.json())
          .then(data => {
            // Update class options
            if (classSel) {
              classSel.innerHTML = '<option value="" selected>Choose class</option>';
              (data.classNames || []).forEach(function (cn) {
                const opt = document.createElement('option');
                opt.value = cn; opt.textContent = cn; classSel.appendChild(opt);
              });
            }
            // Update group options
            if (groupSel) {
              groupSel.innerHTML = '<option value="" selected>Choose group</option>';
              (data.groups || []).forEach(function (g) {
                const opt = document.createElement('option');
                opt.value = g; opt.textContent = g; groupSel.appendChild(opt);
              });
            }
          })
          .catch(() => { /* silent */ });
      });
    }

    // Auto-open correct modal on validation errors
    @if ($errors->any())
      @if (session('openEditId'))
        (function(){
          const id = "{{ session('openEditId') }}";
          const trigger = document.querySelector('.edit-subject-trigger[data-id="' + id + '"]');
          if (trigger) { trigger.click(); }
        })();
      @else
        (function(){
          const modalEl = document.getElementById('addSubjectModal');
          if (modalEl) { const m = new bootstrap.Modal(modalEl); m.show(); }
        })();
      @endif
    @endif

    // Edit modal population
    const editTriggers = document.querySelectorAll('.edit-subject-trigger');
    const updateTemplate = "{{ route('admin.subjects.update', ['subject' => '__ID__']) }}";
    const editForm = document.getElementById('editSubjectForm');
    const editName = document.getElementById('edit_name');
    const editLevel = document.getElementById('edit_level');
    const editClass = document.getElementById('edit_class_name');
    const editGroup = document.getElementById('edit_group');
    const editArm = document.getElementById('edit_arm');
    const editTeacher = document.getElementById('edit_teacher_id');

    function setSelected(selectEl, value) {
      if (!selectEl) return;
      Array.from(selectEl.options).forEach(opt => {
        opt.selected = (value !== null && value !== undefined && opt.value == value);
      });
    }

    editTriggers.forEach(el => {
      el.addEventListener('click', function () {
        const id = this.dataset.id;
        const name = this.dataset.name || '';
        const level = this.dataset.level || '';
        const className = this.dataset.className || '';
        const group = this.dataset.group || '';
        const arm = this.dataset.arm || '';
        const teacherId = this.dataset.teacherId || '';

        if (editForm) editForm.action = updateTemplate.replace('__ID__', id);
        if (editName) editName.value = name;
        if (editLevel) setSelected(editLevel, level);
        if (editTeacher) setSelected(editTeacher, teacherId);

        // If level exists, refresh class/group options for that level then select
        if (level) {
          const url = "{{ route('admin.get-class-details-by-level') }}" + '?level=' + encodeURIComponent(level);
          fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
            .then(r => r.json())
            .then(data => {
              if (editClass) {
                editClass.innerHTML = '<option value="" selected>Choose class</option>';
                (data.classNames || []).forEach(function (cn) {
                  const opt = document.createElement('option');
                  opt.value = cn; opt.textContent = cn; editClass.appendChild(opt);
                });
                setSelected(editClass, className);
              }
              if (editGroup) {
                editGroup.innerHTML = '<option value="" selected>Choose group</option>';
                (data.groups || []).forEach(function (g) {
                  const opt = document.createElement('option');
                  opt.value = g; opt.textContent = g; editGroup.appendChild(opt);
                });
                setSelected(editGroup, group);
              }
            })
            .catch(() => { /* silent */ });
        } else {
          // No level provided; just try to set existing selections
          setSelected(editClass, className);
          setSelected(editGroup, group);
        }

        setSelected(editArm, arm);
      });
    });

    // Dynamic update for edit modal level change
    if (editLevel) {
      editLevel.addEventListener('change', function () {
        const level = editLevel.value;
        if (!level) { return; }
        const url = "{{ route('admin.get-class-details-by-level') }}" + '?level=' + encodeURIComponent(level);
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
          .then(r => r.json())
          .then(data => {
            if (editClass) {
              editClass.innerHTML = '<option value="" selected>Choose class</option>';
              (data.classNames || []).forEach(function (cn) {
                const opt = document.createElement('option');
                opt.value = cn; opt.textContent = cn; editClass.appendChild(opt);
              });
            }
            if (editGroup) {
              editGroup.innerHTML = '<option value="" selected>Choose group</option>';
              (data.groups || []).forEach(function (g) {
                const opt = document.createElement('option');
                opt.value = g; opt.textContent = g; editGroup.appendChild(opt);
              });
            }
          })
          .catch(() => { /* silent */ });
      });
    }

  });
</script>
@endpush
