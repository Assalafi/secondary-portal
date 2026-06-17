@extends('layouts.admin')

@section('title', 'Classes & Subjects')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-2 text-gray-800 fw-bold">Classes & Subjects</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                <li class="breadcrumb-item text-muted" aria-current="page">Classes & Subjects</li>
            </ol>
        </nav>
    </div>
    @push('styles')
    <style>
        .quick-card {
            background-color: #f8f9fc;
            border: 1px solid #edf0f5;
            border-radius: 16px;
            padding: 18px 20px;
            transition: background-color .2s ease, transform .2s ease, box-shadow .2s ease;
        }
        .quick-card:hover {
            background-color: #f5f7fb;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(17, 24, 39, 0.06);
        }
        .icon-badge {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid #eceff3;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #111827;
        }
    </style>
    @endpush
    
    <!-- Quick Links -->
    <div class="mb-4">
        <h5 class="mb-3 fw-bold">Quick Links</h5>
        
        <div class="row g-4">
            <!-- Classes Management -->
            <div class="col-lg-6">
                <a href="{{ route('admin.classes.index') }}" class="text-decoration-none">
                    <div class="quick-card d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-badge">
                                <i class="ri-book-2-line"></i>
                            </div>
                            <h6 class="mb-0 text-dark fw-semibold">Classes Management</h6>
                        </div>
                        <i class="ri-arrow-right-s-line text-muted fs-5"></i>
                    </div>
                </a>
            </div>

            <!-- Assign Teacher to Classes -->
            <div class="col-lg-6">
                <a href="{{ route('admin.teachers.assign') }}" class="text-decoration-none">
                    <div class="quick-card d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-badge">
                                <i class="ri-clipboard-line"></i>
                            </div>
                            <h6 class="mb-0 text-dark fw-semibold">Assign Teacher to Classes</h6>
                        </div>
                        <i class="ri-arrow-right-s-line text-muted fs-5"></i>
                    </div>
                </a>
            </div>

            <!-- Subjects -->
            <div class="col-lg-6">
                <a href="{{ route('admin.subjects.index') }}" class="text-decoration-none">
                    <div class="quick-card d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-badge">
                                <i class="ri-ball-pen-line"></i>
                            </div>
                            <h6 class="mb-0 text-dark fw-semibold">Subjects</h6>
                        </div>
                        <i class="ri-arrow-right-s-line text-muted fs-5"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
