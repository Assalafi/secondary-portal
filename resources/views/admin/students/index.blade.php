@extends('layouts.admin')

@section('title', 'All Students')

@section('content')
<div class="container-fluid">
    <div class="mb-3">
        <h1 class="h3 mb-1 fw-bold text-dark">All Students</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 breadcrumb-soft">
                <li class="breadcrumb-item"><a href="{{ route('admin.students.overview') }}">Students</a></li>
                <li class="breadcrumb-item active" aria-current="page">All Students</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <!-- Actions Row -->
            <div class="d-flex flex-wrap gap-2 mb-3">
                <a href="{{ route('admin.students.enroll.step1') }}" class="btn btn-pill-dark d-flex align-items-center gap-1"><i class="ri-add-line"></i> Enroll New Students</a>
                <a href="{{ route('admin.students.import') }}" class="btn btn-success d-flex align-items-center gap-1"><i class="ri-file-excel-2-line"></i> Import Excel</a>
                <button class="btn btn-soft d-flex align-items-center gap-1"><i class="ri-download-2-line"></i> Export</button>
                <button class="btn btn-soft d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#filterModal"><i class="ri-filter-3-line"></i> Filter</button>
            </div>

            <!-- Search + Bulk Action -->
            <div class="d-flex align-items-center justify-content-between mb-3 gap-3 flex-wrap">
                <form method="GET" action="{{ route('admin.students.index') }}" class="input-group search-soft" style="max-width: 420px;">
                    <span class="input-group-text"><i class="ri-search-line"></i></span>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="e.g., John Doe or email">
                </form>
                <div id="bulkActionWrap" class="d-none">
                    <div class="btn-group">
                        <button class="btn btn-pill-dark dropdown-toggle" data-bs-toggle="dropdown">Bulk Action</button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" data-bulk="activate"><i class="ri-check-line me-2"></i>Activate</a></li>
                            <li><a class="dropdown-item" href="#" data-bulk="deactivate"><i class="ri-close-line me-2"></i>Deactivate</a></li>
                            <li><a class="dropdown-item" href="#" data-bulk="export"><i class="ri-download-2-line me-2"></i>Export selected</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Students Table -->
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:42px;">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                            </th>
                            <th class="text-uppercase small text-muted">#</th>
                            <th class="text-uppercase small text-muted">Full Name</th>
                            <th class="text-uppercase small text-muted">SESSION</th>
                            <th class="text-uppercase small text-muted">CLASS</th>
                            <th class="text-uppercase small text-muted">GENDER</th>
                            <th class="text-uppercase small text-muted">STATUS</th>
                            <th class="text-uppercase small text-muted text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody">
                        @forelse ($students as $student)
                        <tr>
                            <td>
                                <input class="form-check-input row-check" type="checkbox" value="{{ $student->id }}">
                            </td>
                            <td>{{ $loop->iteration + ($students->firstItem() - 1) }}.</td>
                            <td>{{ $student->full_name }}</td>
                            <td>{{ $student->session ?? ($student->academicSession->name ?? '-') }}</td>
                            <td>{{ ($student->classArm->schoolClass->name ?? '-') . ($student->classArm->name ?? '') }}</td>
                            <td>{{ $student->gender ?? '-' }}</td>
                            <td>
                                @php $st = $student->status ?? 'Inactive'; @endphp
                                <span class="status-badge {{ strtolower($st) == 'active' ? 'active' : 'inactive' }}">
                                    <span class="dot"></span> {{ ucfirst($st) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <a href="{{ route('admin.students.profile.overview', $student->id) }}" class="view-link">View</a>
                                    <div class="dropdown d-inline">
                                        <a class="btn btn-sm btn-outline-light border dropdown-toggle" data-bs-toggle="dropdown"></a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('admin.students.edit', $student->id) }}">Edit</a></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $student->id }})">Delete</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No students found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer: Pagination + Result count -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    {!! $students->withQueryString()->links() !!}
                </div>
                <div class="text-muted small">
                    Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteStudentForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
    <!-- Action will be set dynamically in JS -->
 </form>

@push('styles')
<style>
  .breadcrumb-soft .breadcrumb-item + .breadcrumb-item::before { color: #9ca3af; }
  .breadcrumb-soft a { color: #6b7280; text-decoration: none; }
  .breadcrumb-soft .active { color: #9ca3af; }

  .btn-pill-dark { background: #111827; color: #fff; border: 0; border-radius: 9999px; padding: .6rem 1.1rem; font-weight: 600; }
  .btn-pill-dark:hover { background: #0b1220; color: #fff; }
  .btn-soft { background: #f7f7f8; color: #111827; border: 0; border-radius: 12px; padding: .6rem 1.1rem; font-weight: 600; }
  .btn-soft:hover { background: #efefef; }

  .search-soft .input-group-text { background: #f7f7f8; border: 0; border-top-left-radius: 12px; border-bottom-left-radius: 12px; color: #6b7280; }
  .search-soft .form-control { background: #f7f7f8; border: 0; border-top-right-radius: 12px; border-bottom-right-radius: 12px; height: 44px; }
  .search-soft .form-control:focus { background: #fff; box-shadow: 0 0 0 .25rem rgba(17,24,39,.06); }

  thead th { background: #f7f7f8; }
  .table > :not(caption) > * > * { padding: .9rem 1rem; }
  .table tbody tr + tr { border-top: 1px solid #f1f3f5; }
  .view-link { color: #111827; font-weight: 600; text-decoration: none; }
  .view-link:hover { text-decoration: underline; }
  .status-badge { display: inline-flex; align-items: center; gap: .35rem; padding: .2rem .6rem; border-radius: 9999px; font-size: .85rem; }
  .status-badge .dot { width: .5rem; height: .5rem; border-radius: 9999px; display: inline-block; }
  .status-badge.active { background: #dcfce7; color: #065f46; }
  .status-badge.active .dot { background: #10b981; }
  .status-badge.inactive { background: #fee2e2; color: #991b1b; }
  .status-badge.inactive .dot { background: #ef4444; }
  .card.shadow-sm { border: 0; border-radius: 18px; }
</style>
@endpush

@push('scripts')
<script>
  function confirmDelete(studentId) {
    if (!confirm('Are you sure you want to delete this student? This action cannot be undone.')) return;
    const form = document.getElementById('deleteStudentForm');
    // Point to the destroy route: /admin/students/{id}
    form.action = `{{ url('admin/students') }}/${studentId}`;
    form.submit();
  }

  // Selection handling
  const selectAll = document.getElementById('selectAll');
  const tableBody = document.getElementById('studentsTableBody');
  const bulkWrap = document.getElementById('bulkActionWrap');
  const bulkForm = document.createElement('form');
  bulkForm.method = 'POST';
  bulkForm.action = '{{ route('admin.students.bulk-action') }}';
  bulkForm.style.display = 'none';
  bulkForm.innerHTML = `@csrf <input type="hidden" name="action" value="">`;
  document.body.appendChild(bulkForm);
  function updateBulkVisibility() {
    const selected = document.querySelectorAll('.row-check:checked').length;
    if (selected > 0) {
      bulkWrap.classList.remove('d-none');
    } else {
      bulkWrap.classList.add('d-none');
    }
  }
  if (selectAll) {
    selectAll.addEventListener('change', function() {
      document.querySelectorAll('.row-check').forEach(cb => { cb.checked = selectAll.checked; });
      updateBulkVisibility();
    });
  }
  document.addEventListener('change', function(e) {
    if (e.target.classList && e.target.classList.contains('row-check')) {
      updateBulkVisibility();
    }
  });

  // Filter modal actions (placeholder)
  document.addEventListener('click', function(e){
    const bulkAction = e.target.closest('[data-bulk]');
    if (bulkAction) {
      e.preventDefault();
      const action = bulkAction.getAttribute('data-bulk');
      if (action === 'export') {
        alert('Export selected: CSV export will be implemented.');
        return;
      }
      const ids = [...document.querySelectorAll('.row-check:checked')].map(cb => cb.value);
      if (ids.length === 0) return;
      bulkForm.querySelector('input[name="action"]').value = action;
      // Clear previous inputs
      [...bulkForm.querySelectorAll('input[name="student_ids[]"]')].forEach(n => n.remove());
      ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'student_ids[]';
        input.value = id;
        bulkForm.appendChild(input);
      });
      bulkForm.submit();
    }
  });
</script>
@endpush

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border:0;border-radius:18px;background:#f7f7f8;">
      <div class="modal-header" style="border:0;">
        <h5 class="modal-title">Filter block</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <div class="fw-semibold mb-2">Filter by:</div>
        </div>
        <div class="border-top pt-3">
          <div class="mb-3">
            <div class="fw-semibold mb-2">Session</div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="sess1"><label class="form-check-label" for="sess1">Morning</label></div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="sess2"><label class="form-check-label" for="sess2">Afternoon</label></div>
          </div>
          <hr>
          <div class="mb-3">
            <div class="fw-semibold mb-2">Level</div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="lvl1"><label class="form-check-label" for="lvl1">Nursery</label></div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="lvl2"><label class="form-check-label" for="lvl2">Primary</label></div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="lvl3"><label class="form-check-label" for="lvl3">JSS</label></div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="lvl4"><label class="form-check-label" for="lvl4">SS</label></div>
          </div>
          <hr>
          <div class="mb-3">
            <div class="fw-semibold mb-2">Class</div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="cls1"><label class="form-check-label" for="cls1">JSS1</label></div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="cls2"><label class="form-check-label" for="cls2">JSS2</label></div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="cls3"><label class="form-check-label" for="cls3">JSS3</label></div>
          </div>
          <hr>
          <div class="mb-3">
            <div class="fw-semibold mb-2">Class group</div>
            @for ($i=65; $i<=70; $i++)
              <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="grp{{ chr($i) }}"><label class="form-check-label" for="grp{{ chr($i) }}">{{ chr($i) }}</label></div>
            @endfor
          </div>
          <hr>
          <div class="mb-3">
            <div class="fw-semibold mb-2">Gender</div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="g1"><label class="form-check-label" for="g1">Male</label></div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="g2"><label class="form-check-label" for="g2">Female</label></div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="border:0;">
        <button type="button" class="btn btn-soft" data-bs-dismiss="modal">Clear Filter</button>
        <button type="button" class="btn btn-pill-dark" data-bs-dismiss="modal">Apply Filter</button>
      </div>
    </div>
  </div>
</div>
@endsection
