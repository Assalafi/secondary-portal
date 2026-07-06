@extends('layouts.parent')

@section('title', 'My Dependents')
@section('page-title', 'My Dependents')

@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active text-muted">My Dependents</li>
            </ol>
        </nav>

        @foreach (['success' => 'success', 'error' => 'danger', 'info' => 'info'] as $key => $type)
            @if(session($key))
                <div class="alert alert-{{ $type }} alert-dismissible fade show">
                    {{ session($key) }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        @endforeach

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>Please fix the following:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="text-white">
                        <h5 class="mb-2 fw-bold">Apply for New Admission</h5>
                        <p class="mb-0 opacity-90">Submit an admission application for a new student</p>
                    </div>
                    <a href="{{ route('parent.admission.create') }}" class="btn btn-light btn-lg px-4">
                        <i class="ri-add-line me-2"></i>Start New Application
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-xl-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="mb-1 fw-bold">Linked Students</h5>
                            <p class="text-muted mb-0 small">Students currently assigned to your parent account.</p>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary">{{ $dependents->count() }} linked</span>
                    </div>
                    <div class="card-body">
                        @if($dependents->isEmpty())
                            <div class="text-center py-5">
                                <i class="ri-user-line" style="font-size: 64px; color: #ccc;"></i>
                                <h5 class="mt-3 mb-2">No Dependents Found</h5>
                                <p class="text-muted mb-0">Search below and link an existing student to your account.</p>
                            </div>
                        @else
                            <div class="row g-4">
                                @foreach($dependents as $dependent)
                                    @php
                                        $student = $dependent['student'];
                                        $pivot = $student->pivot;
                                    @endphp
                                    <div class="col-12 col-lg-6">
                                        <div class="card border h-100">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-start gap-3 mb-4">
                                                    @if($student->user?->photo_path)
                                                        <img src="{{ asset('storage/' . $student->user->photo_path) }}"
                                                             alt="{{ $student->user->name }}"
                                                             class="rounded-circle"
                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center flex-shrink-0"
                                                             style="width: 60px; height: 60px;">
                                                            <i class="ri-user-line text-white" style="font-size: 30px;"></i>
                                                        </div>
                                                    @endif
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-1">{{ $student->user->name ?? $student->full_name }}</h5>
                                                        <p class="mb-1 text-muted small">{{ $student->admission_no ?? 'No admission number' }}</p>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <span class="badge bg-success bg-opacity-10 text-success">{{ $pivot->relationship ?? 'Guardian' }}</span>
                                                            @if($pivot->is_primary)
                                                                <span class="badge bg-warning bg-opacity-10 text-warning">Primary</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row g-3 mb-3">
                                                    <div class="col-6">
                                                        <p class="mb-1 text-muted small">Class</p>
                                                        <p class="mb-0 fw-bold">{{ optional(optional($student->classArm)->schoolClass)->name ?? 'N/A' }} {{ optional($student->classArm)->name ?? '' }}</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <p class="mb-1 text-muted small">Attendance</p>
                                                        <p class="mb-0 fw-bold">{{ $dependent['attendance'] }}%</p>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center gap-2">
                                                    <a href="{{ route('parent.dependents.show', $student->id) }}"
                                                       class="btn btn-sm btn-outline-dark">
                                                        View Details
                                                    </a>
                                                    <form method="POST"
                                                          action="{{ route('parent.dependents.remove', $student->id) }}"
                                                          onsubmit="return confirm('Unassign {{ addslashes($student->user->name ?? $student->full_name) }} from your account?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            Unassign
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-1 fw-bold">Assign Existing Student</h5>
                        <p class="text-muted mb-0 small">
                            Search by student name, admission number, or class. Assigned students are shown for clarity but cannot be selected.
                        </p>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="studentSearch" class="form-label fw-semibold">Search student</label>
                            <div class="position-relative">
                                <i class="ri-search-line position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <input type="search"
                                       id="studentSearch"
                                       class="form-control ps-5"
                                       placeholder="Type a name, admission no, or class...">
                            </div>
                        </div>

                        <div id="studentList" class="d-grid gap-3" style="max-height: 560px; overflow-y: auto;">
                            @forelse($students as $student)
                                @php
                                    $isLinked = $linkedStudentIds->contains($student->id);
                                    $isAssignedElsewhere = ! $isLinked && $assignedStudentIds->contains($student->id);
                                    $isUnavailable = $isLinked || $isAssignedElsewhere;
                                    $studentName = $student->user->name ?? $student->full_name;
                                    $className = trim((optional(optional($student->classArm)->schoolClass)->name ?? 'N/A') . ' ' . (optional($student->classArm)->name ?? ''));
                                @endphp
                                <div class="student-option border rounded p-3 {{ $isUnavailable ? 'bg-light' : '' }}"
                                     data-search="{{ strtolower($studentName . ' ' . $student->full_name . ' ' . $student->admission_no . ' ' . $className) }}">
                                    <div class="d-flex justify-content-between gap-3">
                                        <div>
                                            <h6 class="mb-1 fw-semibold">{{ $studentName }}</h6>
                                            <p class="mb-1 text-muted small">{{ $student->admission_no ?? 'No admission number' }}</p>
                                            <p class="mb-0 text-muted small">{{ $className }}</p>
                                        </div>
                                        <div class="text-end flex-shrink-0">
                                            @if($isLinked)
                                                <span class="badge bg-success bg-opacity-10 text-success">Linked to you</span>
                                            @elseif($isAssignedElsewhere)
                                                <span class="badge bg-warning bg-opacity-10 text-warning">Assigned to another parent</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">Available</span>
                                            @endif
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('parent.dependents.assign') }}" class="mt-3">
                                        @csrf
                                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                                        <div class="row g-2 align-items-end">
                                            <div class="col-12">
                                                <label class="form-label small fw-semibold">Relationship</label>
                                                <select name="relationship" class="form-select form-select-sm" {{ $isUnavailable ? 'disabled' : 'required' }}>
                                                    @foreach($relationshipOptions as $relationship)
                                                        <option value="{{ $relationship }}">{{ $relationship }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-sm btn-primary w-100" {{ $isUnavailable ? 'disabled' : '' }}>
                                                    <i class="ri-link me-1"></i>
                                                    @if($isLinked)
                                                        Already Assigned
                                                    @elseif($isAssignedElsewhere)
                                                        Assigned to Another Parent
                                                    @else
                                                        Assign Student
                                                    @endif
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @empty
                                <div class="text-center text-muted py-5">
                                    <i class="ri-search-eye-line d-block mb-2" style="font-size: 42px;"></i>
                                    No active students are available.
                                </div>
                            @endforelse
                        </div>

                        <div id="noStudentMatches" class="text-center text-muted py-5 d-none">
                            <i class="ri-search-line d-block mb-2" style="font-size: 42px;"></i>
                            No student matches your search.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('studentSearch');
            const rows = Array.from(document.querySelectorAll('.student-option'));
            const emptyState = document.getElementById('noStudentMatches');

            if (!searchInput) {
                return;
            }

            searchInput.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                let visibleCount = 0;

                rows.forEach(row => {
                    const matches = row.dataset.search.includes(query);
                    row.classList.toggle('d-none', !matches);

                    if (matches) {
                        visibleCount++;
                    }
                });

                emptyState.classList.toggle('d-none', visibleCount > 0);
            });
        });
    </script>
@endpush
@endsection
