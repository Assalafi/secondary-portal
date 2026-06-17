@extends('layouts.admin')

@section('title', 'Subject Details - ' . $subject->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="h3 mb-1 fw-bold text-dark">{{ $subject->name }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0 breadcrumb-soft">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.classes-subjects.overview') }}">Classes & Subjects</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.subjects.index') }}">Subjects</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $subject->name }}</li>
            </ol>
        </nav>
    </div>

    <!-- Overview -->
    <h6 class="fw-bold text-dark mb-3">Overview</h6>
    <div class="row g-4 mb-4">
        <!-- Subject Summary -->
        <div class="col-lg-6">
            <div class="soft-card p-4 d-flex flex-column gap-2">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div class="fw-semibold text-muted small">&nbsp;</div>
                    <a href="#" class="btn btn-pill-dark edit-subject-trigger"
                       data-bs-toggle="modal" data-bs-target="#editSubjectModal"
                       data-id="{{ $subject->id }}"
                       data-name="{{ $subject->name }}"
                       data-level="{{ $summary['level'] ?? '' }}"
                       data-class-name="{{ $summary['class_name'] ?? '' }}"
                       data-group="{{ $summary['group'] ?? '' }}"
                       data-arm="{{ $summary['arm'] ?? '' }}"
                       data-teacher-id="{{ $summary['teacher']?->id ?? '' }}">
                        Update
                    </a>
                </div>
                <div class="row gy-2">
                    <div class="col-12"><span class="label-soft">Subject:</span> <span class="value-soft">{{ $summary['level'] ?? '-' }}</span></div>
                    <div class="col-12"><span class="label-soft">Level:</span> <span class="value-soft">{{ $summary['class_name'] ?? '-' }}</span></div>
                    <div class="col-12"><span class="label-soft">Arm:</span> <span class="value-soft">{{ $summary['arm'] ?? '-' }}</span></div>
                    <div class="col-12"><span class="label-soft">Class Group:</span> <span class="value-soft">{{ $summary['group'] ?? '-' }}</span></div>
                    <div class="col-12"><span class="label-soft">Current Enrollment:</span> <span class="value-soft">{{ $summary['enrollment'] ?? 0 }}</span></div>
                </div>
            </div>
        </div>

        <!-- Teacher + Subjects stack -->
        <div class="col-lg-6">
            <div class="soft-card p-4 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold text-dark mb-1">Teacher</div>
                        <div class="small"><span class="label-soft">Teacher:</span> <span class="value-soft">{{ $summary['teacher']?->name ?? 'Not Assigned' }}</span></div>
                    </div>
                    <a href="#" class="btn btn-pill-dark edit-subject-trigger"
                       data-bs-toggle="modal" data-bs-target="#editSubjectModal"
                       data-id="{{ $subject->id }}"
                       data-name="{{ $subject->name }}"
                       data-level="{{ $summary['level'] ?? '' }}"
                       data-class-name="{{ $summary['class_name'] ?? '' }}"
                       data-group="{{ $summary['group'] ?? '' }}"
                       data-arm="{{ $summary['arm'] ?? '' }}"
                       data-teacher-id="{{ $summary['teacher']?->id ?? '' }}">Update</a>
                </div>
            </div>

            <div class="soft-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold text-dark mb-1">Subjects</div>
                        <div class="small"><span class="label-soft">Subjects:</span> <span class="value-soft">{{ $summary['total_subjects'] }}</span></div>
                    </div>
                    <a href="#" class="btn btn-pill-dark edit-subject-trigger"
                       data-bs-toggle="modal" data-bs-target="#editSubjectModal"
                       data-id="{{ $subject->id }}"
                       data-name="{{ $subject->name }}"
                       data-level="{{ $summary['level'] ?? '' }}"
                       data-class-name="{{ $summary['class_name'] ?? '' }}"
                       data-group="{{ $summary['group'] ?? '' }}"
                       data-arm="{{ $summary['arm'] ?? '' }}"
                       data-teacher-id="{{ $summary['teacher']?->id ?? '' }}">Update</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <h6 class="fw-bold text-dark mb-3">Quick Links</h6>
    <div class="row g-4">
        <div class="col-lg-6">
            <a href="#" class="link-card d-flex align-items-center p-4">
                <div class="icon-circle me-3"><i class="ri-book-2-line"></i></div>
                <div class="flex-grow-1 fw-semibold text-dark">Students</div>
                <i class="ri-arrow-right-s-line text-muted"></i>
            </a>
        </div>
        <div class="col-lg-6">
            <a href="#" class="link-card d-flex align-items-center p-4">
                <div class="icon-circle me-3"><i class="ri-task-line"></i></div>
                <div class="flex-grow-1 fw-semibold text-dark">Assignment</div>
                <i class="ri-arrow-right-s-line text-muted"></i>
            </a>
        </div>
        <div class="col-lg-6">
            <a href="#" class="link-card d-flex align-items-center p-4">
                <div class="icon-circle me-3"><i class="ri-calendar-todo-line"></i></div>
                <div class="flex-grow-1 fw-semibold text-dark">Test/Exam Schedule</div>
                <i class="ri-arrow-right-s-line text-muted"></i>
            </a>
        </div>
        <div class="col-lg-6">
            <a href="#" class="link-card d-flex align-items-center p-4">
                <div class="icon-circle me-3"><i class="ri-bar-chart-2-line"></i></div>
                <div class="flex-grow-1 fw-semibold text-dark">Score Upload</div>
                <i class="ri-arrow-right-s-line text-muted"></i>
            </a>
        </div>
    </div>
</div>

@include('admin.subjects._modals')
@endsection

@push('styles')
<style>
  .breadcrumb-soft .breadcrumb-item + .breadcrumb-item::before { color: #9ca3af; }
  .breadcrumb-soft a { color: #6b7280; text-decoration: none; }
  .breadcrumb-soft .active { color: #9ca3af; }

  .soft-card { background: #f7f7f8; border: 0; border-radius: 18px; }
  .label-soft { color: #6b7280; margin-right: .25rem; }
  .value-soft { color: #111827; font-weight: 600; }

  .btn-pill-dark { background: #111827; color: #fff; border: 0; border-radius: 9999px; padding: .45rem 1rem; font-weight: 600; }
  .btn-pill-dark:hover { background: #0b1220; color: #fff; }

  .link-card { display: flex; align-items: center; background: #f7f7f8; border-radius: 18px; text-decoration: none; color: inherit; }
  .link-card:hover { background: #efefef; }
  .icon-circle { width: 48px; height: 48px; border-radius: 9999px; background: #fff; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #eee; color: #111827; font-size: 20px; }
</style>
@endpush
