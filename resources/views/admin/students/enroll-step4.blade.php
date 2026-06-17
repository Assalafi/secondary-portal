@extends('layouts.admin')

@section('title', 'Enroll New Student - Step 4')

@section('content')
<div class="container-fluid">
    <div class="mb-3">
        <h1 class="h3 mb-1 fw-bold text-dark">Enroll New Student</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 breadcrumb-soft">
                <li class="breadcrumb-item"><a href="{{ route('admin.students.overview') }}">Students</a></li>
                <li class="breadcrumb-item active" aria-current="page">Enroll New Student</li>
            </ol>
        </nav>
    </div>

    <!-- Stepper -->
    <div class="soft-card p-4 mb-4">
        <div class="stepper">
            <div class="step done">1</div>
            <div class="bar"></div>
            <div class="step done">2</div>
            <div class="bar"></div>
            <div class="step done">3</div>
            <div class="bar"></div>
            <div class="step active">4</div>
        </div>
    </div>

    <div class="soft-card p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3">Guardian Information</h5>
        @php
          $s1 = $step1Data ?? [];
          $s2 = $step2Data ?? [];
          $s3 = $step3Data ?? [];
        @endphp
        
        <!-- Student Informations -->
        <div class="row g-3 mb-3">
            <div class="col-lg-6">
                <div class="soft-card p-4 h-100">
                    <div class="d-flex justify-content-between mb-2"><div class="fw-semibold">Student Informations</div><a href="{{ route('admin.students.enroll.step1') }}" class="btn btn-soft btn-sm">Edit</a></div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted me-2" style="min-width:130px;">First Name:</span>
                                <span>{{ $s1['first_name'] ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted me-2" style="min-width:130px;">Surname:</span>
                                <span>{{ $s1['middle_name'] ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted me-2" style="min-width:130px;">Gender:</span>
                                <span>{{ $s1['gender'] ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted me-2" style="min-width:130px;">Date of Birth:</span>
                                <span>{{ !empty($s1['date_of_birth']) ? \Carbon\Carbon::parse($s1['date_of_birth'])->format('jS F Y') : '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted me-2" style="min-width:130px;">State of Origin:</span>
                                <span>{{ $s1['state_of_origin'] ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted me-2" style="min-width:130px;">LGA:</span>
                                <span>{{ $s1['lga'] ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="soft-card p-4 h-100">
                    <div class="d-flex justify-content-between mb-2"><div class="fw-semibold">Academic Placement</div><a href="{{ route('admin.students.enroll.step2.show') }}" class="btn btn-soft btn-sm">Edit</a></div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted me-2" style="min-width:130px;">Class:</span>
                                <span>{{ trim(($s2['class'] ?? '-') . ' ' . ($s2['class_arm'] ?? '')) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted me-2" style="min-width:130px;">Class group:</span>
                                <span>{{ $s2['class_group'] ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted me-2" style="min-width:130px;">Session:</span>
                                <span>{{ $s2['session'] ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted me-2" style="min-width:130px;">Admission Number:</span>
                                <span>{{ $s2['admission_number'] ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted me-2" style="min-width:130px;">Enrollment Date:</span>
                                <span>{{ !empty($s2['enrollment_date']) ? \Carbon\Carbon::parse($s2['enrollment_date'])->format('jS F Y') : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="soft-card p-4 h-100">
                    <div class="d-flex justify-content-between mb-2"><div class="fw-semibold">Guardian Informations</div><a href="{{ route('admin.students.enroll.step3.show') }}" class="btn btn-soft btn-sm">Edit</a></div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted me-2" style="min-width:150px;">Guardian Name:</span>
                                <span>{{ $s3['guardian_name'] ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted me-2" style="min-width:150px;">Relationship:</span>
                                <span>{{ $s3['relationship'] ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted me-2" style="min-width:150px;">Phone Number:</span>
                                <span>{{ $s3['phone_number'] ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted me-2" style="min-width:150px;">Email Address:</span>
                                <span>{{ $s3['email_address'] ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <span class="text-muted me-2" style="min-width:150px;">Emergency Contact:</span>
                                <span>{{ $s3['emergency_contact'] ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex">
                                <span class="text-muted me-2" style="min-width:150px;">Address:</span>
                                <span>{{ $s3['address'] ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-start align-items-center gap-2 mt-3">
            <a href="{{ route('admin.students.enroll.step3.show') }}" class="btn btn-soft">Previous</a>
            <form method="POST" action="{{ route('admin.students.enroll.complete') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-pill-dark">Enroll Student</button>
            </form>
        </div>
    </div>
</div>
@push('styles')
<style>
  .breadcrumb-soft .breadcrumb-item + .breadcrumb-item::before { color: #9ca3af; }
  .breadcrumb-soft a { color: #6b7280; text-decoration: none; }
  .breadcrumb-soft .active { color: #9ca3af; }
  .soft-card { background: #f7f7f8; border: 0; border-radius: 18px; }
  .btn-pill-dark { background: #111827; color: #fff; border: 0; border-radius: 9999px; padding: .6rem 1.1rem; font-weight: 600; }
  .btn-pill-dark:hover { background: #0b1220; color: #fff; }
  .btn-soft { background: #f1f1f1; color: #111827; border: 0; border-radius: 9999px; padding: .6rem 1.1rem; font-weight: 600; }
  .stepper { display: flex; align-items: center; gap: 10px; }
  .stepper .step { width: 36px; height: 36px; border-radius: 9999px; background: #e5e7eb; color: #111827; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; }
  .stepper .step.active { background: #111827; color: #fff; }
  .stepper .step.done { background: #9ca3af; color: #fff; }
  .stepper .bar { flex: 1; height: 2px; background: #e5e7eb; }
</style>
@endpush

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function(){
    @if(session('enrolled_success'))
      const m = new bootstrap.Modal(document.getElementById('enrollSuccessModal'));
      m.show();
    @endif
  });
</script>
@endpush

<!-- Success Modal -->
<div class="modal fade" id="enrollSuccessModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content soft-card p-4">
      <div class="text-center">
        <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;border-radius:9999px;border:2px solid #10b981;color:#10b981;">
          <i class="ri-check-line fs-3"></i>
        </div>
        <h5 class="fw-bold text-dark mb-4">Student enrolled successfully!</h5>
        @php $enrolled = session('enrolled_success'); @endphp
        <div class="d-flex justify-content-center gap-2">
          @if($enrolled && !empty($enrolled['student_id']))
            <a href="{{ route('admin.students.profile.overview', $enrolled['student_id']) }}" class="btn btn-soft">View Student Record</a>
          @endif
          <a href="{{ route('admin.students.enroll.step1') }}" class="btn btn-pill-dark">Enroll Another Student</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
