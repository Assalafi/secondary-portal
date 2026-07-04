<!DOCTYPE html>
<html lang="zxx">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $schoolSettings = \App\Models\SchoolSettings::first();
    @endphp

    <!-- SEO Meta Tags from School Settings -->
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
        @if($schoolSettings->meta_image)
            <meta property="og:image" content="{{ asset('storage/' . $schoolSettings->meta_image) }}">
            <meta property="og:image:width" content="1200">
            <meta property="og:image:height" content="630">
            <meta property="og:image:type" content="image/jpeg">
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:image" content="{{ asset('storage/' . $schoolSettings->meta_image) }}">
        @endif
        @if($schoolSettings->school_name)
            <meta property="og:title" content="{{ $schoolSettings->school_name }}">
            <meta property="og:site_name" content="{{ $schoolSettings->school_name }}">
            <title>@yield('title', $schoolSettings->school_name) - Admin</title>
        @else
            <title>@yield('title', 'School Management System') - Admin</title>
        @endif
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <title>@yield('title', 'School Management System') - Admin</title>
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

    <!-- Dynamic Logo Styles -->
    <style>
        .logo-img {
            width: 80px;
            height: 60px;
            object-fit: contain;
            border-radius: 12px;
            max-width: 100%;
        }
    </style>

    <!-- Title -->
    <title>@yield('title', $globalSettings['school_name'])</title>
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
            <a href="{{ route('admin.dashboard') }}"
                class="d-block text-decoration-none position-relative d-flex justify-content-center">
                @if ($globalSettings['school_logo'])
                    <img src="{{ asset('storage/' . $globalSettings['school_logo']) }}" alt="school-logo"
                        class="logo-img">
                @else
                    <img src="{{ asset('assets/images/logo-icon.png') }}" alt="logo-icon" class="logo-img">
                @endif
            </a>
            <button
                class="sidebar-burger-menu bg-transparent p-0 border-0 opacity-0 z-n1 position-absolute top-50 end-0 translate-middle-y"
                id="sidebar-burger-menu">
                <i data-feather="x"></i>
            </button>
        </div>

        <aside id="layout-menu" class="layout-menu menu-vertical menu active" data-simplebar>
            <ul class="menu-inner">
                <li class="menu-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">dashboard</span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('admin.students.overview') }}"
                        class="menu-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">school</span>
                        <span class="title">Students</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('admin.classes-subjects.overview') }}"
                        class="menu-link {{ request()->routeIs('admin.classes-subjects.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">class</span>
                        <span class="title">Classes & Subjects</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('admin.academic-management.index') }}"
                        class="menu-link {{ request()->routeIs('admin.academic-management.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">school</span>
                        <span class="title">Academic Management</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('admin.staff.overview') }}"
                        class="menu-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">person</span>
                        <span class="title">Staff</span>
                    </a>
                </li>
                {{-- Parent/Guardians --}}
                <li class="menu-item">
                    <a href="{{ route('admin.parent-guardians.overview') }}"
                        class="menu-link {{ request()->routeIs('admin.parent-guardians.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">person</span>
                        <span class="title">Parent/Guardians</span>
                    </a>
                </li>

                {{-- Admission Applications --}}
                <li class="menu-item">
                    <a href="{{ route('admin.admissions.index') }}"
                        class="menu-link {{ request()->routeIs('admin.admissions.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">description</span>
                        <span class="title">Admissions</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('admin.payments.overview') }}"
                        class="menu-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">payments</span>
                        <span class="title">Payment & Finance</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('admin.settings.index') }}"
                        class="menu-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">settings</span>
                        <span class="title">Settings</span>
                    </a>
                </li>

                {{-- Support Tickets --}}
                <li class="menu-item">
                    <a href="{{ route('admin.support.index') }}"
                        class="menu-link {{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">support_agent</span>
                        <span class="title">Support Tickets</span>
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
                            <ul
                                class="d-flex align-items-center ps-0 mb-0 list-unstyled justify-content-center justify-content-sm-start">
                                <li>
                                    <button class="header-burger-menu bg-transparent p-0 border-0"
                                        id="header-burger-menu">
                                        <span class="material-symbols-outlined">menu</span>
                                    </button>
                                </li>
                                <li>
                                    <form class="src-form position-relative">
                                        <input type="text" class="form-control" placeholder="Search here.....">
                                        <button type="submit"
                                            class="src-btn position-absolute top-50 end-0 translate-middle-y bg-transparent p-0 border-0">
                                            <span class="material-symbols-outlined">search</span>
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <div class="dropdown notifications apps">
                                        <button class="btn btn-secondary border-0 p-0 position-relative"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-symbols-outlined">apps</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-lg p-0 border-0 py-4 px-3 max-h-312"
                                            data-simplebar>
                                            <div
                                                class="notification-menu d-flex flex-wrap justify-content-between gap-4">
                                                <a href="{{ route('admin.students.overview') }}"
                                                    class="dropdown-item p-0 text-center">
                                                    <i class="material-symbols-outlined wh-25 text-primary">school</i>
                                                    <span>Students</span>
                                                </a>
                                                <a href="{{ route('admin.staff.overview') }}"
                                                    class="dropdown-item p-0 text-center">
                                                    <i class="material-symbols-outlined wh-25 text-success">person</i>
                                                    <span>Staff</span>
                                                </a>
                                                <a href="{{ route('admin.classes-subjects.overview') }}"
                                                    class="dropdown-item p-0 text-center">
                                                    <i class="material-symbols-outlined wh-25 text-warning">class</i>
                                                    <span>Classes</span>
                                                </a>
                                                <a href="#" class="dropdown-item p-0 text-center">
                                                    <i class="material-symbols-outlined wh-25 text-info">assessment</i>
                                                    <span>Exams</span>
                                                </a>
                                                <a href="{{ route('admin.payments.overview') }}"
                                                    class="dropdown-item p-0 text-center">
                                                    <i class="material-symbols-outlined wh-25 text-danger">payments</i>
                                                    <span>Finance</span>
                                                </a>
                                                <a href="#" class="dropdown-item p-0 text-center">
                                                    <i
                                                        class="material-symbols-outlined wh-25 text-secondary">library_books</i>
                                                    <span>Library</span>
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
                            <ul
                                class="d-flex align-items-center justify-content-center justify-content-sm-end ps-0 mb-0 list-unstyled">
                                <li class="header-right-item">
                                    <div class="light-dark">
                                        <button class="switch-toggle settings-btn dark-btn p-0 bg-transparent border-0"
                                            id="switch-toggle">
                                            <span class="dark"><i
                                                    class="material-symbols-outlined">light_mode</i></span>
                                            <span class="light"><i
                                                    class="material-symbols-outlined">dark_mode</i></span>
                                        </button>
                                    </div>
                                </li>
                                <li class="header-right-item">
                                    <div class="dropdown notifications language">
                                        <button
                                            class="btn btn-secondary dropdown-toggle border-0 p-0 position-relative"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-symbols-outlined">translate</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-lg p-0 border-0 dropdown-menu-end">
                                            <span class="fw-semibold fs-15 text-secondary title">Choose Language</span>
                                            <div class="max-h-275" data-simplebar>
                                                <div class="notification-menu">
                                                    <a href="javascript:void(0);" class="dropdown-item">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0">
                                                                <img src="{{ asset('assets/images/usa.svg') }}"
                                                                    class="wh-30 rounded-circle" alt="english">
                                                            </div>
                                                            <div class="flex-grow-1 ms-2">
                                                                <span
                                                                    class="text-secondary fw-medium fs-14">English</span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="header-right-item">
                                    <button class="fullscreen-btn bg-transparent p-0 border-0" id="fullscreen-button">
                                        <i class="material-symbols-outlined text-body">fullscreen</i>
                                    </button>
                                </li>
                                <li class="header-right-item">
                                    <div class="dropdown notifications noti">
                                        <button class="btn btn-secondary border-0 p-0 position-relative badge"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-symbols-outlined">notifications</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-lg p-0 border-0 p-0 dropdown-menu-end">
                                            <div class="d-flex justify-content-between align-items-center title">
                                                <span class="fw-semibold fs-15 text-secondary">Notifications <span
                                                        class="fw-normal text-body fs-14">(03)</span></span>
                                                <button
                                                    class="p-0 m-0 bg-transparent border-0 fs-14 text-primary">Clear
                                                    All</button>
                                            </div>

                                            <div class="max-h-217" data-simplebar>
                                                <div class="notification-menu">
                                                    <a href="#" class="dropdown-item">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0">
                                                                <i
                                                                    class="material-symbols-outlined text-primary">sms</i>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <p>New student registration</p>
                                                                <span class="fs-13">2 hrs ago</span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="notification-menu unseen">
                                                    <a href="#" class="dropdown-item">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0">
                                                                <i
                                                                    class="material-symbols-outlined text-info">person</i>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <p>A new teacher added</p>
                                                                <span class="fs-13">3 hrs ago</span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="notification-menu">
                                                    <a href="#" class="dropdown-item">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0">
                                                                <i
                                                                    class="material-symbols-outlined text-success">mark_email_unread</i>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <p>Fee payment received</p>
                                                                <span class="fs-13">1 day ago</span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>

                                            <a href="#"
                                                class="dropdown-item text-center text-primary d-block view-all fw-medium rounded-bottom-3">
                                                <span>See All Notifications </span>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                                <li class="header-right-item">
                                    <div class="dropdown admin-profile">
                                        <div class="d-xxl-flex align-items-center bg-transparent border-0 text-start p-0 cursor dropdown-toggle"
                                            data-bs-toggle="dropdown">
                                            <div class="flex-shrink-0">
                                                <img class="rounded-circle wh-40 administrator"
                                                    src="{{ asset('assets/images/administrator.jpg') }}"
                                                    alt="admin">
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
                                                    <img class="rounded-circle wh-30 administrator"
                                                        src="{{ asset('assets/images/administrator.jpg') }}"
                                                        alt="admin">
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h3 class="fw-medium">{{ Auth::user()->name }}</h3>
                                                    <span
                                                        class="fs-12">{{ Auth::user()->role->name ?? 'Administrator' }}</span>
                                                </div>
                                            </div>
                                            <ul class="admin-link ps-0 mb-0 list-unstyled">
                                                <li>
                                                    <a class="dropdown-item admin-item-link d-flex align-items-center text-body"
                                                        href="#">
                                                        <i class="material-symbols-outlined">account_circle</i>
                                                        <span class="ms-2">My Profile</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item admin-item-link d-flex align-items-center text-body"
                                                        href="#">
                                                        <i class="material-symbols-outlined">chat</i>
                                                        <span class="ms-2">Messages</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item admin-item-link d-flex align-items-center text-body"
                                                        href="#">
                                                        <i class="material-symbols-outlined">format_list_bulleted</i>
                                                        <span class="ms-2">My Task</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item admin-item-link d-flex align-items-center text-body"
                                                        href="#">
                                                        <i class="material-symbols-outlined">credit_card</i>
                                                        <span class="ms-2">Billing</span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <ul class="admin-link ps-0 mb-0 list-unstyled">
                                                <li>
                                                    <a class="dropdown-item admin-item-link d-flex align-items-center text-body"
                                                        href="{{ route('admin.settings.index') }}">
                                                        <i class="material-symbols-outlined">settings</i>
                                                        <span class="ms-2">Settings</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item admin-item-link d-flex align-items-center text-body"
                                                        href="#">
                                                        <i class="material-symbols-outlined">support</i>
                                                        <span class="ms-2">Support</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item admin-item-link d-flex align-items-center text-body"
                                                        href="#">
                                                        <i class="material-symbols-outlined">lock</i>
                                                        <span class="ms-2">Lock Screen</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <form method="POST" action="{{ route('logout') }}">
                                                        @csrf
                                                        <button type="submit"
                                                            class="dropdown-item admin-item-link d-flex align-items-center text-body border-0 bg-transparent w-100 text-start">
                                                            <i class="material-symbols-outlined">logout</i>
                                                            <span class="ms-2">Logout</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                                <li class="header-right-item">
                                    <button class="theme-settings-btn p-0 border-0 bg-transparent" type="button"
                                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling"
                                        aria-controls="offcanvasScrolling">
                                        <i class="material-symbols-outlined" data-bs-toggle="tooltip"
                                            data-bs-placement="left"
                                            data-bs-title="Click On Theme Settings">settings</i>
                                    </button>
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
                        <small class="text-muted">{{ $globalSettings['academic_session'] }} •
                            {{ $globalSettings['current_term'] }}</small>
                    </div>
                    @yield('breadcrumb')
                </div>

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

    <!-- Theme Settings Offcanvas -->
    <div class="offcanvas offcanvas-end" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1"
        id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Theme Settings</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="theme-settings">
                <h6 class="mb-3">Color Scheme</h6>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="colorScheme" id="lightMode" value="light"
                        checked>
                    <label class="form-check-label" for="lightMode">Light Mode</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="colorScheme" id="darkMode"
                        value="dark">
                    <label class="form-check-label" for="darkMode">Dark Mode</label>
                </div>

                <h6 class="mb-3">Sidebar</h6>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="sidebarType" id="sidebarDefault"
                        value="default" checked>
                    <label class="form-check-label" for="sidebarDefault">Default</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="sidebarType" id="sidebarCompact"
                        value="compact">
                    <label class="form-check-label" for="sidebarCompact">Compact</label>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Preloader Hide Script -->
    <script>
        window.addEventListener('load', function() {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                preloader.style.display = 'none';
            }
        });

        // Fallback - hide preloader after 2 seconds if window load doesn't fire
        setTimeout(function() {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                preloader.style.display = 'none';
            }
        }, 2000);
    </script>
    @stack('scripts')
</body>

</html>
