<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $schoolSettings = \App\Models\SchoolSettings::first();
    @endphp

    @if($schoolSettings)
        @if($schoolSettings->favicon)
            @php $faviconVersion = time(); @endphp
            <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $schoolSettings->favicon) }}?v={{ $faviconVersion }}">
            <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/' . $schoolSettings->favicon) }}?v={{ $faviconVersion }}">
            <link rel="apple-touch-icon" href="{{ asset('storage/' . $schoolSettings->favicon) }}?v={{ $faviconVersion }}">
        @else
            <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
            <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        @endif
        @if($schoolSettings->meta_description)
            <meta name="description" content="{{ $schoolSettings->meta_description }}">
        @endif
        @if($schoolSettings->meta_keywords)
            <meta name="keywords" content="{{ $schoolSettings->meta_keywords }}">
        @endif
        @if($schoolSettings->meta_author)
            <meta name="author" content="{{ $schoolSettings->meta_author }}">
        @endif
        @if($schoolSettings->school_name)
            <meta property="og:title" content="{{ $schoolSettings->school_name }}">
            <meta property="og:site_name" content="{{ $schoolSettings->school_name }}">
            <title>@yield('title', $schoolSettings->school_name) - Student Portal</title>
        @else
            <title>@yield('title', 'School Management System') - Student Portal</title>
        @endif
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <title>@yield('title', 'School Management System') - Student Portal</title>
    @endif

    <!-- Links Of CSS File -->
    <link rel="stylesheet" href="{{ asset('assets/css/sidebar-menu.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/prism.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/rangeslider.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/quill.snow.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/google-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fullcalendar.main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jsvectormap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lightpick.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    @stack('styles')

    <style>
        .logo-img {
            width: 80px;
            height: 60px;
            object-fit: contain;
            border-radius: 12px;
            max-width: 100%;
        }
    </style>

    <title>@yield('title', $globalSettings['school_name']) - Student Portal</title>
</head>

<body class="boxed-size bg-white">
    <!-- Start Preloader Area -->
    <div class="preloader" id="preloader">
        <div class="preloader">
            <div class="loading-text">Loading...</div>
        </div>
    </div>
    <!-- End Preloader Area -->

    <!-- Start Sidebar Area -->
    <div class="sidebar-area border-end" id="sidebar-area">
        <div class="logo position-relative">
            <a href="{{ route('student.dashboard') }}" class="d-block text-decoration-none position-relative d-flex justify-content-center">
                @if ($globalSettings['school_logo'])
                    <img src="{{ asset('storage/' . $globalSettings['school_logo']) }}" alt="school-logo" class="logo-img">
                @else
                    <img src="{{ asset('assets/images/logo-icon.png') }}" alt="logo-icon" class="logo-img">
                @endif
            </a>
            <button class="sidebar-burger-menu bg-transparent p-0 border-0 opacity-0 z-n1 position-absolute top-50 end-0 translate-middle-y" id="sidebar-burger-menu">
                <i data-feather="x"></i>
            </button>
        </div>

        <aside id="layout-menu" class="layout-menu menu-vertical menu active" data-simplebar>
            <ul class="menu-inner">
                <li class="menu-item">
                    <a href="{{ route('student.dashboard') }}" class="menu-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">dashboard</span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('student.results.index') }}" class="menu-link {{ request()->routeIs('student.results.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">assessment</span>
                        <span class="title">My Results</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('student.report-cards') }}" class="menu-link {{ request()->routeIs('student.report-cards*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">description</span>
                        <span class="title">Report Cards</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('student.attendance.index') }}" class="menu-link {{ request()->routeIs('student.attendance.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">fact_check</span>
                        <span class="title">Attendance</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('student.assignments.index') }}" class="menu-link {{ request()->routeIs('student.assignments.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">assignment</span>
                        <span class="title">Assignments</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('student.timetable') }}" class="menu-link {{ request()->routeIs('student.timetable') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">calendar_month</span>
                        <span class="title">Timetable</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('student.payments.index') }}" class="menu-link {{ request()->routeIs('student.payments.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">payments</span>
                        <span class="title">Payments</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('student.profile') }}" class="menu-link {{ request()->routeIs('student.profile') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">person</span>
                        <span class="title">My Profile</span>
                    </a>
                </li>
            </ul>
        </aside>
    </div>
    <!-- End Sidebar Area -->

    <!-- Start Main Content Area -->
    <div class="container-fluid">
        <div class="main-content d-flex flex-column">
            <!-- Start Header Area -->
            <header class="header-area bg-white mb-4 rounded-bottom-15 px-0" id="header-area">
                <div class="row align-items-center">
                    <div class="col-lg-4 col-sm-6">
                        <div class="left-header-content">
                            <ul class="d-flex align-items-center ps-0 mb-0 list-unstyled justify-content-center justify-content-sm-start">
                                <li>
                                    <button class="header-burger-menu bg-transparent p-0 border-0" id="header-burger-menu">
                                        <span class="material-symbols-outlined">menu</span>
                                    </button>
                                </li>
                                <li>
                                    <form class="src-form position-relative">
                                        <input type="text" class="form-control" placeholder="Search here.....">
                                        <button type="submit" class="src-btn position-absolute top-50 end-0 translate-middle-y bg-transparent p-0 border-0">
                                            <span class="material-symbols-outlined">search</span>
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <div class="dropdown notifications apps">
                                        <button class="btn btn-secondary border-0 p-0 position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-symbols-outlined">apps</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-lg p-0 border-0 py-4 px-3 max-h-312" data-simplebar>
                                            <div class="notification-menu d-flex flex-wrap justify-content-between gap-4">
                                                <a href="{{ route('student.results.index') }}" class="dropdown-item p-0 text-center">
                                                    <i class="material-symbols-outlined wh-25 text-primary">assessment</i>
                                                    <span>Results</span>
                                                </a>
                                                <a href="{{ route('student.payments.index') }}" class="dropdown-item p-0 text-center">
                                                    <i class="material-symbols-outlined wh-25 text-success">payments</i>
                                                    <span>Payments</span>
                                                </a>
                                                <a href="{{ route('student.attendance.index') }}" class="dropdown-item p-0 text-center">
                                                    <i class="material-symbols-outlined wh-25 text-warning">fact_check</i>
                                                    <span>Attendance</span>
                                                </a>
                                                <a href="{{ route('student.profile') }}" class="dropdown-item p-0 text-center">
                                                    <i class="material-symbols-outlined wh-25 text-info">account_circle</i>
                                                    <span>Profile</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-8 col-sm-6">
                        <div class="right-header-content mt-2 mt-sm-0">
                            <ul class="d-flex align-items-center justify-content-center justify-content-sm-end ps-0 mb-0 list-unstyled">
                                <li class="header-right-item">
                                    <div class="dropdown notifications noti">
                                        <button class="btn btn-secondary border-0 p-0 position-relative badge" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-symbols-outlined">notifications</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-lg p-0 border-0 p-0 dropdown-menu-end">
                                            <div class="d-flex justify-content-between align-items-center title">
                                                <span class="fw-semibold fs-15 text-secondary">Notifications</span>
                                                <button class="p-0 m-0 bg-transparent border-0 fs-14 text-primary">Clear All</button>
                                            </div>
                                            <div class="max-h-217" data-simplebar>
                                                <div class="notification-menu">
                                                    <a href="#" class="dropdown-item">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0">
                                                                <i class="material-symbols-outlined text-primary">school</i>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <p>Welcome to your student portal</p>
                                                                <span class="fs-13">Just now</span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <a href="#" class="dropdown-item text-center text-primary d-block view-all fw-medium rounded-bottom-3">
                                                <span>See All Notifications</span>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                                <li class="header-right-item">
                                    <div class="dropdown admin-profile">
                                        <div class="d-xxl-flex align-items-center bg-transparent border-0 text-start p-0 cursor dropdown-toggle" data-bs-toggle="dropdown">
                                            <div class="flex-shrink-0">
                                                @if(Auth::user()->photo_path)
                                                    <img class="rounded-circle wh-40" src="{{ asset('storage/' . Auth::user()->photo_path) }}" alt="student">
                                                @else
                                                    <div class="rounded-circle wh-40 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                        <span class="text-white fw-bold" style="font-size: 14px;">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-none d-xxl-block">
                                                        <div class="d-flex align-content-center">
                                                            <h3>{{ Auth::user()->name }}</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="dropdown-menu border-0 bg-white dropdown-menu-end">
                                            <div class="d-flex align-items-center info">
                                                <div class="flex-shrink-0">
                                                    @if(Auth::user()->photo_path)
                                                        <img class="rounded-circle wh-30" src="{{ asset('storage/' . Auth::user()->photo_path) }}" alt="student">
                                                    @else
                                                        <div class="rounded-circle wh-30 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                            <span class="text-white fw-bold" style="font-size: 11px;">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h3 class="fw-medium">{{ Auth::user()->name }}</h3>
                                                    <span class="fs-12">Student</span>
                                                </div>
                                            </div>
                                            <ul class="admin-link ps-0 mb-0 list-unstyled">
                                                <li>
                                                    <a class="dropdown-item admin-item-link d-flex align-items-center text-body" href="{{ route('student.profile') }}">
                                                        <i class="material-symbols-outlined">account_circle</i>
                                                        <span class="ms-2">My Profile</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item admin-item-link d-flex align-items-center text-body" href="{{ route('student.results.index') }}">
                                                        <i class="material-symbols-outlined">assessment</i>
                                                        <span class="ms-2">My Results</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item admin-item-link d-flex align-items-center text-body" href="{{ route('student.payments.index') }}">
                                                        <i class="material-symbols-outlined">payments</i>
                                                        <span class="ms-2">Payments</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <ul class="admin-link ps-0 mb-0 list-unstyled">
                                                <li>
                                                    <form method="POST" action="{{ route('logout') }}">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item admin-item-link d-flex align-items-center text-body border-0 bg-transparent w-100 text-start">
                                                            <i class="material-symbols-outlined">logout</i>
                                                            <span class="ms-2">Logout</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>
            <!-- End Header Area -->

            <div class="main-content-container overflow-hidden">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                    <div>
                        <h3 class="mb-0">@yield('page-title', 'Dashboard')</h3>
                        <small class="text-muted">{{ $globalSettings['academic_session'] }} &bull; {{ $globalSettings['current_term'] }}</small>
                    </div>
                    @yield('breadcrumb')
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')

                <!-- Footer -->
                <footer class="mt-5 pt-4 border-top">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="mb-0 text-muted">
                                &copy; {{ date('Y') }} {{ $globalSettings['school_name'] }}. All rights reserved.
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-0 text-muted">
                                <i class="ri-phone-line me-1"></i>{{ $globalSettings['phone_number'] }} |
                                <i class="ri-mail-line me-1"></i>{{ $globalSettings['email'] }}
                            </p>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>
    <!-- End Main Content Area -->

    <!-- Links of JS files -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sidebar-menu.js') }}"></script>
    <script src="{{ asset('assets/js/dragdrop.js') }}"></script>
    <script src="{{ asset('assets/js/rangeslider.min.js') }}"></script>
    <script src="{{ asset('assets/js/quill.min.js') }}"></script>
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script src="{{ asset('assets/js/prism.js') }}"></script>
    <script src="{{ asset('assets/js/clipboard.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/fullcalendar.main.js') }}"></script>
    <script src="{{ asset('assets/js/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/js/world-merc.js') }}"></script>
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/lightpick.js') }}"></script>
    <script src="{{ asset('assets/js/custom/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/js/custom/echarts.js') }}"></script>
    <script src="{{ asset('assets/js/custom/custom.js') }}"></script>

    <script>
        window.addEventListener('load', function() {
            const preloader = document.getElementById('preloader');
            if (preloader) preloader.style.display = 'none';
        });
        setTimeout(function() {
            const preloader = document.getElementById('preloader');
            if (preloader) preloader.style.display = 'none';
        }, 2000);
    </script>
    @stack('scripts')
</body>

</html>
