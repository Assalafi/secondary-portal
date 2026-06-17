@extends('layouts.admin')

@section('title', 'Students Overview')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 mb-1 fw-bold text-dark">Students</h1>
    </div>

    <!-- Overview -->
    <h6 class="fw-bold text-dark mb-3">Overview</h6>
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="soft-card p-4 d-flex align-items-center justify-content-between">
                <div>
                    <div class="label-soft">Students Enrolled</div>
                    <div class="stat-number">{{ number_format($totalStudents ?? 0) }}</div>
                </div>
                <div class="icon-circle"><i class="ri-graduation-cap-line"></i></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="soft-card p-4 d-flex align-items-center justify-content-between">
                <div>
                    <div class="label-soft">New Admissions</div>
                    <div class="stat-number">{{ number_format($newAdmissions ?? 0) }}</div>
                </div>
                <div class="icon-circle"><i class="ri-graduation-cap-line"></i></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="soft-card p-4 d-flex align-items-center justify-content-between">
                <div>
                    <div class="label-soft">Pending Promotions</div>
                    <div class="stat-number">{{ number_format($pendingPromotions ?? 0) }}</div>
                </div>
                <div class="icon-circle"><i class="ri-graduation-cap-line"></i></div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <h6 class="fw-bold text-dark mb-3">Quick Links</h6>
    <div class="row g-4">
        <div class="col-lg-6">
            <a href="{{ route('admin.students.index') }}" class="link-card d-flex align-items-center p-4">
                <div class="icon-circle me-3"><i class="ri-book-2-line"></i></div>
                <div class="flex-grow-1 fw-semibold text-dark">All Students</div>
                <i class="ri-arrow-right-s-line text-muted"></i>
            </a>
        </div>
        <div class="col-lg-6">
            <a href="{{ route('admin.students.enroll.step1') }}" class="link-card d-flex align-items-center p-4">
                <div class="icon-circle me-3"><i class="ri-clipboard-line"></i></div>
                <div class="flex-grow-1 fw-semibold text-dark">Enroll New Student</div>
                <i class="ri-arrow-right-s-line text-muted"></i>
            </a>
        </div>
        <div class="col-lg-6">
            <a href="{{ route('admin.students.promote.index') }}" class="link-card d-flex align-items-center p-4">
                <div class="icon-circle me-3"><i class="ri-edit-2-line"></i></div>
                <div class="flex-grow-1 fw-semibold text-dark">Promote/Transfer</div>
                <i class="ri-arrow-right-s-line text-muted"></i>
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
  .soft-card { background: #f7f7f8; border: 0; border-radius: 18px; }
  .label-soft { color: #6b7280; font-weight: 500; }
  .stat-number { font-size: 2rem; font-weight: 700; color: #111827; line-height: 1.1; }
  .icon-circle { width: 56px; height: 56px; border-radius: 9999px; background: #fff; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #eee; color: #111827; font-size: 22px; }

  .link-card { display: flex; align-items: center; background: #f7f7f8; border-radius: 18px; text-decoration: none; color: inherit; }
  .link-card:hover { background: #efefef; }
</style>
@endpush
@endsection
