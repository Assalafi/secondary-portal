@extends('layouts.teacher')

@section('title', 'Mark Attendance')
@section('page-title', 'Mark Attendance')

@push('styles')
<style>
    .attendance-btn {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
    }
    .attendance-btn.present { background: rgba(16, 185, 129, 0.1); color: #10b981; border-color: #10b981; }
    .attendance-btn.absent { background: rgba(239, 68, 68, 0.1); color: #ef4444; border-color: #ef4444; }
    .attendance-btn.late { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border-color: #f59e0b; }
    .attendance-btn.inactive { opacity: 0.3; border-color: #e5e7eb; background: transparent; }
    .student-row:hover { background-color: #f8fafc; }
</style>
@endpush

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="mb-0 fw-bold">
                    <i class="ri-checkbox-multiple-line me-2 text-primary"></i>
                    {{ $classArm->schoolClass->level ?? '' }} {{ $classArm->schoolClass->name ?? '' }} {{ $classArm->name }}
                </h6>
                <small class="text-muted">{{ \Carbon\Carbon::parse($today)->format('l, F d, Y') }} &bull; {{ $classArm->students->count() }} students</small>
            </div>
            <a href="{{ route('teacher.attendance.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="ri-arrow-left-line me-1"></i>Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('teacher.attendance.store') }}" method="POST">
            @csrf
            <input type="hidden" name="class_arm_id" value="{{ $classArm->id }}">
            <input type="hidden" name="date" value="{{ $today }}">

            <!-- Quick Actions -->
            <div class="mb-4 d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-outline-success" id="markAllPresent">
                    <i class="ri-checkbox-circle-line me-1"></i>Mark All Present
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" id="markAllAbsent">
                    <i class="ri-close-circle-line me-1"></i>Mark All Absent
                </button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4" style="width: 50px;">#</th>
                            <th class="border-0">Student Name</th>
                            <th class="border-0 text-center" style="width: 200px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classArm->students->sortBy('user.name') as $student)
                            @php
                                $existing = $existingAttendance[$student->id] ?? null;
                                $currentStatus = $existing ? $existing->status : 'Present';
                            @endphp
                            <tr class="student-row">
                                <td class="px-4">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <span class="text-white fw-bold" style="font-size: 11px;">{{ strtoupper(substr($student->user->name ?? 'N', 0, 2)) }}</span>
                                        </div>
                                        <span class="fw-medium">{{ $student->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <input type="hidden" name="attendance[{{ $student->id }}]" id="status-{{ $student->id }}" value="{{ $currentStatus }}">
                                        <button type="button" class="attendance-btn {{ $currentStatus === 'Present' ? 'present' : 'inactive' }}" data-student="{{ $student->id }}" data-status="Present" title="Present">
                                            <i class="ri-check-line"></i>
                                        </button>
                                        <button type="button" class="attendance-btn {{ $currentStatus === 'Absent' ? 'absent' : 'inactive' }}" data-student="{{ $student->id }}" data-status="Absent" title="Absent">
                                            <i class="ri-close-line"></i>
                                        </button>
                                        <button type="button" class="attendance-btn {{ $currentStatus === 'Late' ? 'late' : 'inactive' }}" data-student="{{ $student->id }}" data-status="Late" title="Late">
                                            <i class="ri-time-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="ri-save-line me-1"></i>Save Attendance
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.attendance-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const studentId = this.dataset.student;
        const status = this.dataset.status;

        // Update hidden input
        document.getElementById(`status-${studentId}`).value = status;

        // Update button states
        const buttons = document.querySelectorAll(`[data-student="${studentId}"]`);
        buttons.forEach(b => {
            b.classList.remove('present', 'absent', 'late');
            b.classList.add('inactive');
        });
        this.classList.remove('inactive');
        this.classList.add(status.toLowerCase());
    });
});

document.getElementById('markAllPresent').addEventListener('click', function() {
    document.querySelectorAll('.attendance-btn[data-status="Present"]').forEach(btn => btn.click());
});

document.getElementById('markAllAbsent').addEventListener('click', function() {
    document.querySelectorAll('.attendance-btn[data-status="Absent"]').forEach(btn => btn.click());
});
</script>
@endpush
