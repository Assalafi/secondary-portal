<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $globalSettings['school_name'] ?? 'School Portal' }} - Complete School Management System</title>
    <meta name="description" content="Complete school management portal for Nursery, Primary, JSS and SSS. Manage admissions, results, attendance, payments and more.">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --primary-light: #818cf8;
            --secondary: #0ea5e9;
            --accent: #f59e0b;
            --dark: #1e293b;
            --gray: #64748b;
            --light: #f8fafc;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; color: var(--dark); overflow-x: hidden; background: var(--light); }
        a { text-decoration: none; color: inherit; }
        .section-pad { padding: 100px 0; }

        /* Navbar */
        .navbar-custom {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            padding: 18px 0; transition: all 0.3s ease;
            background: transparent;
        }
        .navbar-custom.scrolled {
            background: rgba(255,255,255,0.97); box-shadow: 0 2px 20px rgba(0,0,0,0.08); padding: 12px 0;
        }
        .navbar-custom .nav-brand { display: flex; align-items: center; gap: 12px; font-weight: 700; font-size: 1.1rem; color: white; }
        .navbar-custom.scrolled .nav-brand { color: var(--dark); }
        .navbar-custom .nav-brand img { width: 40px; height: 40px; border-radius: 10px; object-fit: contain; background: white; padding: 2px; }
        .navbar-custom .nav-links { display: flex; align-items: center; gap: 30px; }
        .navbar-custom .nav-links a { color: rgba(255,255,255,0.85); font-weight: 500; font-size: 0.95rem; transition: color 0.3s; }
        .navbar-custom.scrolled .nav-links a { color: var(--gray); }
        .navbar-custom .nav-links a:hover { color: white; }
        .navbar-custom.scrolled .nav-links a:hover { color: var(--primary); }
        .btn-login-nav { background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); color: white !important; padding: 10px 28px; border-radius: 50px; font-weight: 600; transition: all 0.3s; border: 1px solid rgba(255,255,255,0.3); }
        .navbar-custom.scrolled .btn-login-nav { background: var(--primary); color: white !important; border-color: var(--primary); }
        .btn-login-nav:hover { background: white; color: var(--primary) !important; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .navbar-custom.scrolled .btn-login-nav:hover { background: var(--primary-dark); color: white !important; }

        /* Mobile menu toggle */
        .nav-toggle { display: none; background: none; border: none; color: white; font-size: 28px; cursor: pointer; }
        .navbar-custom.scrolled .nav-toggle { color: var(--dark); }

        /* Hero */
        .hero {
            min-height: 100vh; display: flex; align-items: center;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 25%, #4f46e5 50%, #6366f1 75%, #4338ca 100%);
            color: white; position: relative; overflow: hidden; padding: 120px 0 80px;
        }
        .hero::before {
            content: ''; position: absolute; top: -200px; right: -200px; width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(99,102,241,0.3) 0%, transparent 70%);
            border-radius: 50%; pointer-events: none;
        }
        .hero::after {
            content: ''; position: absolute; bottom: -150px; left: -100px; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(139,92,246,0.2) 0%, transparent 70%);
            border-radius: 50%; pointer-events: none;
        }

        .hero-inner { position: relative; z-index: 2; padding-top: 20px; }

        .hero .school-crest {
            width: 90px; height: 90px; background: rgba(255,255,255,0.12);
            backdrop-filter: blur(12px); border-radius: 22px;
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 28px; border: 1px solid rgba(255,255,255,0.2);
        }
        .hero .school-crest img { max-width: 62px; max-height: 62px; object-fit: contain; border-radius: 12px; }
        .hero .school-crest-text { font-size: 1.8rem; font-weight: 800; color: white; }

        .hero h1 { font-size: 3.8rem; font-weight: 900; line-height: 1.08; margin-bottom: 24px; letter-spacing: -0.02em; }
        .hero h1 .accent { background: linear-gradient(90deg, #fbbf24, #f59e0b, #fbbf24); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .hero .hero-desc { font-size: 1.15rem; color: rgba(255,255,255,0.8); line-height: 1.75; margin-bottom: 36px; max-width: 520px; }

        .hero-cta { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 0; }
        .btn-hero {
            padding: 16px 34px; border-radius: 50px; font-weight: 700; font-size: 1rem;
            border: none; cursor: pointer; transition: all 0.3s ease;
            display: inline-flex; align-items: center; gap: 10px; text-decoration: none;
        }
        .btn-hero.primary { background: white; color: #4f46e5; }
        .btn-hero.primary:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,0.25); background: #f8fafc; }
        .btn-hero.outline { background: rgba(255,255,255,0.08); color: white; border: 2px solid rgba(255,255,255,0.3); }
        .btn-hero.outline:hover { background: rgba(255,255,255,0.15); border-color: rgba(255,255,255,0.6); }

        .hero-stats {
            display: flex; gap: 0; margin-top: 50px;
            border-top: 1px solid rgba(255,255,255,0.15); padding-top: 30px;
        }
        .hero-stats .stat {
            text-align: left; padding-right: 40px; margin-right: 40px;
            border-right: 1px solid rgba(255,255,255,0.15);
        }
        .hero-stats .stat:last-child { border-right: none; margin-right: 0; padding-right: 0; }
        .hero-stats .stat-num { font-size: 1.6rem; font-weight: 800; letter-spacing: -0.01em; }
        .hero-stats .stat-label { font-size: 0.8rem; color: rgba(255,255,255,0.6); margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Right side floating cards */
        .hero-right { position: relative; z-index: 2; min-height: 400px; }
        .hero-right .info-card {
            background: rgba(255,255,255,0.1); backdrop-filter: blur(16px);
            border-radius: 16px; padding: 22px 26px;
            border: 1px solid rgba(255,255,255,0.15); color: white;
            display: flex; align-items: center; gap: 16px;
            margin-bottom: 20px; transition: all 0.3s ease;
            animation: slideIn 0.8s ease forwards; opacity: 0;
        }
        .hero-right .info-card:hover { background: rgba(255,255,255,0.18); transform: translateX(5px); }
        .info-card:nth-child(1) { animation-delay: 0.2s; }
        .info-card:nth-child(2) { animation-delay: 0.4s; }
        .info-card:nth-child(3) { animation-delay: 0.6s; }
        .info-card:nth-child(4) { animation-delay: 0.8s; }
        @keyframes slideIn { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
        .info-card .ic-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
        .info-card .ic-text .ic-label { font-size: 0.78rem; color: rgba(255,255,255,0.6); text-transform: uppercase; letter-spacing: 0.5px; }
        .info-card .ic-text .ic-value { font-size: 1.05rem; font-weight: 700; margin-top: 2px; }

        /* Level Strip */
        .level-strip { background: white; padding: 28px 0; border-bottom: 1px solid #e2e8f0; }
        .level-strip .levels { display: flex; justify-content: center; align-items: center; gap: 10px; flex-wrap: wrap; }
        .level-strip .level-tag { padding: 8px 20px; border-radius: 50px; font-size: 0.82rem; font-weight: 600; background: #f1f5f9; color: var(--gray); transition: all 0.3s; cursor: default; }
        .level-strip .level-tag:hover, .level-strip .level-tag.active { background: var(--primary); color: white; }
        .level-strip .level-divider { width: 5px; height: 5px; border-radius: 50%; background: #cbd5e1; }

        /* Features */
        .features { background: var(--light); }
        .section-header { text-align: center; margin-bottom: 60px; }
        .section-header .badge-label { display: inline-block; background: rgba(79,70,229,0.1); color: var(--primary); padding: 6px 18px; border-radius: 50px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 16px; }
        .section-header h2 { font-size: 2.5rem; font-weight: 800; color: var(--dark); margin-bottom: 16px; letter-spacing: -0.02em; }
        .section-header p { font-size: 1.05rem; color: var(--gray); max-width: 600px; margin: 0 auto; line-height: 1.6; }
        .feature-card { background: white; border-radius: 16px; padding: 32px 26px; height: 100%; transition: all 0.35s ease; border: 1px solid #e2e8f0; }
        .feature-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); border-color: transparent; }
        .feature-card .f-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 18px; color: white; }
        .feature-card h5 { font-weight: 700; font-size: 1.05rem; margin-bottom: 10px; color: var(--dark); }
        .feature-card p { font-size: 0.88rem; color: var(--gray); line-height: 1.65; margin-bottom: 0; }

        /* Portal Cards */
        .portals { background: white; }
        .portal-card {
            border-radius: 20px; padding: 36px 28px; text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid #e2e8f0; display: block; height: 100%; background: white;
        }
        .portal-card:hover { transform: translateY(-8px); box-shadow: 0 20px 50px rgba(0,0,0,0.1); border-color: var(--primary-light); }
        .portal-icon { width: 72px; height: 72px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 30px; color: white; margin: 0 auto 22px; transition: transform 0.4s; }
        .portal-card:hover .portal-icon { transform: scale(1.1) rotate(5deg); }
        .portal-card h4 { font-weight: 700; font-size: 1.2rem; margin-bottom: 10px; color: var(--dark); }
        .portal-card p { font-size: 0.88rem; color: var(--gray); line-height: 1.6; margin-bottom: 18px; }
        .portal-arrow { display: inline-flex; align-items: center; gap: 6px; color: var(--primary); font-weight: 600; font-size: 0.88rem; opacity: 0; transform: translateY(5px); transition: all 0.3s; }
        .portal-card:hover .portal-arrow { opacity: 1; transform: translateY(0); }

        /* Why Choose */
        .why-choose { background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4f46e5 100%); color: white; }
        .why-choose .section-header .badge-label { background: rgba(255,255,255,0.15); color: white; }
        .why-choose .section-header h2 { color: white; }
        .why-choose .section-header p { color: rgba(255,255,255,0.75); }
        .why-card { background: rgba(255,255,255,0.08); backdrop-filter: blur(10px); border-radius: 16px; padding: 30px 24px; border: 1px solid rgba(255,255,255,0.12); height: 100%; transition: all 0.3s; }
        .why-card:hover { background: rgba(255,255,255,0.15); transform: translateY(-5px); }
        .why-card i { font-size: 34px; margin-bottom: 16px; color: #fbbf24; }
        .why-card h5 { font-weight: 700; margin-bottom: 10px; font-size: 1.05rem; }
        .why-card p { font-size: 0.88rem; color: rgba(255,255,255,0.75); line-height: 1.65; margin: 0; }

        /* Footer */
        .footer { background: #0f172a; color: white; padding: 70px 0 30px; }
        .footer h6 { font-weight: 700; margin-bottom: 20px; font-size: 1rem; }
        .footer p, .footer a { color: #94a3b8; font-size: 0.9rem; }
        .footer a:hover { color: white; }
        .footer ul { list-style: none; padding: 0; }
        .footer li { margin-bottom: 10px; }
        .footer .footer-brand { font-size: 1.3rem; font-weight: 800; color: white; margin-bottom: 14px; }
        .footer-bottom { border-top: 1px solid #1e293b; margin-top: 50px; padding-top: 25px; text-align: center; color: #475569; font-size: 0.85rem; }

        /* Responsive */
        @media (max-width: 991px) {
            .hero h1 { font-size: 2.8rem; }
            .hero-right { display: none; }
            .navbar-custom .nav-links { display: none; }
            .nav-toggle { display: block; }
        }
        @media (max-width: 576px) {
            .hero { padding: 100px 0 60px; }
            .hero h1 { font-size: 2.2rem; }
            .hero .hero-desc { font-size: 1rem; }
            .hero-stats { flex-direction: column; gap: 15px; }
            .hero-stats .stat { border-right: none; padding-right: 0; margin-right: 0; padding-bottom: 15px; margin-bottom: 0; border-bottom: 1px solid rgba(255,255,255,0.1); }
            .hero-stats .stat:last-child { border-bottom: none; padding-bottom: 0; }
            .btn-hero { padding: 14px 28px; font-size: 0.95rem; width: 100%; justify-content: center; }
            .section-header h2 { font-size: 1.8rem; }
            .section-pad { padding: 60px 0; }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar-custom" id="navbar">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ route('landing') }}" class="nav-brand">
                @if ($globalSettings['school_logo'])
                    <img src="{{ asset('storage/' . $globalSettings['school_logo']) }}" alt="Logo">
                @endif
                {{ $globalSettings['school_name'] ?? 'School Portal' }}
            </a>
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="#portals">Portals</a>
                <a href="#why-us">Why Us</a>
                <a href="#contact">Contact</a>
                <a href="{{ route('login') }}" class="btn-login-nav">Login</a>
            </div>
            <button class="nav-toggle" onclick="document.querySelector('.nav-links').classList.toggle('d-flex')">
                <i class="ri-menu-line"></i>
            </button>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 hero-inner">
                    <div class="school-crest">
                        @if ($globalSettings['school_logo'])
                            <img src="{{ asset('storage/' . $globalSettings['school_logo']) }}" alt="Logo">
                        @else
                            <span class="school-crest-text">{{ substr($globalSettings['school_name'] ?? 'SP', 0, 2) }}</span>
                        @endif
                    </div>
                    <h1>
                        Complete School<br>Management<br>
                        <span class="accent">Made Simple.</span>
                    </h1>
                    <p class="hero-desc">
                        An all-in-one platform for managing your school from Nursery to Senior Secondary.
                        Admissions, results, attendance, payments, report cards &mdash; everything in one place.
                    </p>
                    <div class="hero-cta">
                        <a href="{{ route('login') }}" class="btn-hero primary">
                            <i class="ri-login-box-line"></i> Login to Portal
                        </a>
                        <a href="#features" class="btn-hero outline">
                            <i class="ri-arrow-down-line"></i> Explore Features
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div class="stat">
                            <div class="stat-num">Nursery &ndash; SS3</div>
                            <div class="stat-label">All Levels Covered</div>
                        </div>
                        <div class="stat">
                            <div class="stat-num">6+</div>
                            <div class="stat-label">User Portals</div>
                        </div>
                        <div class="stat">
                            <div class="stat-num">100%</div>
                            <div class="stat-label">Online Access</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 hero-right d-none d-lg-block">
                    <div class="info-card">
                        <div class="ic-icon" style="background:rgba(251,191,36,0.2)">
                            <i class="ri-graduation-cap-fill" style="color:#fbbf24"></i>
                        </div>
                        <div class="ic-text">
                            <div class="ic-label">Results Management</div>
                            <div class="ic-value">CA Scores, Exams & Report Cards</div>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="ic-icon" style="background:rgba(16,185,129,0.2)">
                            <i class="ri-money-dollar-circle-fill" style="color:#10b981"></i>
                        </div>
                        <div class="ic-text">
                            <div class="ic-label">Online Payments</div>
                            <div class="ic-value">Fees, Invoices & Remita Gateway</div>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="ic-icon" style="background:rgba(14,165,233,0.2)">
                            <i class="ri-calendar-check-fill" style="color:#0ea5e9"></i>
                        </div>
                        <div class="ic-text">
                            <div class="ic-label">Attendance Tracking</div>
                            <div class="ic-value">Daily Records & History Reports</div>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="ic-icon" style="background:rgba(168,85,247,0.2)">
                            <i class="ri-user-add-fill" style="color:#a855f7"></i>
                        </div>
                        <div class="ic-text">
                            <div class="ic-label">Online Admissions</div>
                            <div class="ic-value">Apply, Track & Get Enrolled</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Level Strip -->
    <div class="level-strip">
        <div class="container">
            <div class="levels">
                <span class="level-tag active">Nursery 1</span>
                <span class="level-divider"></span>
                <span class="level-tag">Nursery 2</span>
                <span class="level-divider"></span>
                <span class="level-tag">Nursery 3</span>
                <span class="level-divider"></span>
                <span class="level-tag">Primary 1-6</span>
                <span class="level-divider"></span>
                <span class="level-tag">JSS 1</span>
                <span class="level-divider"></span>
                <span class="level-tag">JSS 2</span>
                <span class="level-divider"></span>
                <span class="level-tag">JSS 3</span>
                <span class="level-divider"></span>
                <span class="level-tag">SS 1</span>
                <span class="level-divider"></span>
                <span class="level-tag">SS 2</span>
                <span class="level-divider"></span>
                <span class="level-tag active">SS 3</span>
            </div>
        </div>
    </div>

    <!-- Features -->
    <section class="features section-pad" id="features">
        <div class="container">
            <div class="section-header">
                <span class="badge-label">Features</span>
                <h2>Everything Your School Needs</h2>
                <p>Powerful modules designed for Nigerian schools, from nursery to senior secondary</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="f-icon" style="background:linear-gradient(135deg,#4f46e5,#7c3aed)"><i class="ri-user-add-line"></i></div>
                        <h5>Student Enrollment</h5>
                        <p>Multi-step enrollment wizard with admission number generation, class assignment, and guardian linking for all levels.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="f-icon" style="background:linear-gradient(135deg,#0ea5e9,#06b6d4)"><i class="ri-file-list-3-line"></i></div>
                        <h5>Results &amp; Report Cards</h5>
                        <p>Upload CA and exam scores, auto-generate termly and annual report cards with grades, positions, and promotion decisions.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="f-icon" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="ri-calendar-check-line"></i></div>
                        <h5>Attendance Tracking</h5>
                        <p>Daily attendance recording per class with history, statistics, and reports. Track present, absent, late, and excused statuses.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="f-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706)"><i class="ri-bank-card-line"></i></div>
                        <h5>Fee Management &amp; Payments</h5>
                        <p>Create fee structures, generate invoices, accept online payments via Remita, track balances, and download receipts.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="f-icon" style="background:linear-gradient(135deg,#ef4444,#dc2626)"><i class="ri-building-line"></i></div>
                        <h5>Class &amp; Subject Management</h5>
                        <p>Create classes from Nursery to SS3 with arms (A, B, C), assign subjects, and link teachers to specific class-subject combinations.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="f-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)"><i class="ri-file-paper-2-line"></i></div>
                        <h5>Online Admissions</h5>
                        <p>Parents can apply for admission online with document uploads, tracking, and payment. Admins review, approve, or reject applications.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="f-icon" style="background:linear-gradient(135deg,#ec4899,#db2777)"><i class="ri-team-line"></i></div>
                        <h5>Staff &amp; Teacher Management</h5>
                        <p>Manage all staff records, assign roles, handle salary setup, generate payroll, and track teacher subject assignments.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="f-icon" style="background:linear-gradient(135deg,#14b8a6,#0d9488)"><i class="ri-parent-line"></i></div>
                        <h5>Parent Portal</h5>
                        <p>Parents monitor dependents' results, attendance, assignments, and make payments. Self-registration and multi-ward support included.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="f-icon" style="background:linear-gradient(135deg,#64748b,#475569)"><i class="ri-settings-3-line"></i></div>
                        <h5>School Settings</h5>
                        <p>Configure school info, academic sessions, terms, grading systems, report card layouts, notification preferences, and backup settings.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Portals -->
    <section class="portals section-pad" id="portals">
        <div class="container">
            <div class="section-header">
                <span class="badge-label">Access Portals</span>
                <h2>Login to Your Portal</h2>
                <p>Select your role to access the appropriate dashboard</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <a href="/login/admin" class="portal-card">
                        <div class="portal-icon" style="background:linear-gradient(135deg,#4f46e5,#7c3aed)"><i class="ri-shield-star-line"></i></div>
                        <h4>Administrator</h4>
                        <p>Full control over school operations, settings, staff, students, and academic management</p>
                        <span class="portal-arrow">Access Portal <i class="ri-arrow-right-line"></i></span>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <a href="/login/teacher" class="portal-card">
                        <div class="portal-icon" style="background:linear-gradient(135deg,#0ea5e9,#06b6d4)"><i class="ri-presentation-line"></i></div>
                        <h4>Teacher</h4>
                        <p>Manage your classes, upload scores, take attendance, and monitor student progress</p>
                        <span class="portal-arrow">Access Portal <i class="ri-arrow-right-line"></i></span>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <a href="/login/student" class="portal-card">
                        <div class="portal-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706)"><i class="ri-graduation-cap-line"></i></div>
                        <h4>Student</h4>
                        <p>View your results, attendance, report cards, timetable, and make fee payments online</p>
                        <span class="portal-arrow">Access Portal <i class="ri-arrow-right-line"></i></span>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <a href="/login/parent" class="portal-card">
                        <div class="portal-icon" style="background:linear-gradient(135deg,#10b981,#059669)"><i class="ri-parent-line"></i></div>
                        <h4>Parent / Guardian</h4>
                        <p>Monitor your ward's performance, manage payments, apply for admission, and view reports</p>
                        <span class="portal-arrow">Access Portal <i class="ri-arrow-right-line"></i></span>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <a href="/login/accountant" class="portal-card">
                        <div class="portal-icon" style="background:linear-gradient(135deg,#ec4899,#db2777)"><i class="ri-money-dollar-circle-line"></i></div>
                        <h4>Accountant</h4>
                        <p>Handle fee collections, generate invoices, process payroll, and manage financial reports</p>
                        <span class="portal-arrow">Access Portal <i class="ri-arrow-right-line"></i></span>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6">
                    <a href="/login/librarian" class="portal-card">
                        <div class="portal-icon" style="background:linear-gradient(135deg,#64748b,#475569)"><i class="ri-book-open-line"></i></div>
                        <h4>Librarian</h4>
                        <p>Manage library catalogue, track book borrowing and returns, and maintain library records</p>
                        <span class="portal-arrow">Access Portal <i class="ri-arrow-right-line"></i></span>
                    </a>
                </div>
            </div>
            <div class="text-center mt-5">
                <p class="text-muted mb-3">New parent? Register to track your child's progress</p>
                <a href="{{ route('parent.register') }}" class="btn btn-outline-primary btn-lg rounded-pill px-5">
                    <i class="ri-user-add-line me-2"></i>Register as Parent
                </a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="why-choose section-pad" id="why-us">
        <div class="container">
            <div class="section-header">
                <span class="badge-label">Why Choose Us</span>
                <h2>Built for Nigerian Schools</h2>
                <p>Designed specifically for the Nigerian education system and school workflow</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="why-card">
                        <i class="ri-shield-check-line"></i>
                        <h5>Secure &amp; Reliable</h5>
                        <p>Role-based access control ensures each user sees only what they need. Your data is safe and secure.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="why-card">
                        <i class="ri-smartphone-line"></i>
                        <h5>Access Anywhere</h5>
                        <p>Fully responsive design works perfectly on phones, tablets, and computers. No app installation needed.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="why-card">
                        <i class="ri-bank-card-2-line"></i>
                        <h5>Remita Integration</h5>
                        <p>Accept fee payments online through Remita payment gateway. Parents and students can pay from anywhere.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="why-card">
                        <i class="ri-file-pdf-2-line"></i>
                        <h5>PDF Report Cards</h5>
                        <p>Generate professional report cards with QR verification codes, affective and psychomotor domains.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand">{{ $globalSettings['school_name'] ?? 'School Portal' }}</div>
                    <p>A comprehensive school management system covering Nursery 1 through SS 3. Empowering schools with modern technology for better education management.</p>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6>Quick Links</h6>
                    <ul>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#portals">Portals</a></li>
                        <li><a href="#why-us">Why Us</a></li>
                        <li><a href="{{ route('login') }}">Login</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h6>Portals</h6>
                    <ul>
                        <li><a href="/login/admin">Admin Portal</a></li>
                        <li><a href="/login/teacher">Teacher Portal</a></li>
                        <li><a href="/login/student">Student Portal</a></li>
                        <li><a href="/login/parent">Parent Portal</a></li>
                        <li><a href="{{ route('parent.register') }}">Parent Registration</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h6>Contact</h6>
                    <ul>
                        <li><i class="ri-map-pin-line me-2"></i>{{ $globalSettings['school_address'] ?? '' }}</li>
                        <li><i class="ri-phone-line me-2"></i>{{ $globalSettings['phone_number'] ?? '' }}</li>
                        <li><i class="ri-mail-line me-2"></i>{{ $globalSettings['email'] ?? '' }}</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; {{ date('Y') }} {{ $globalSettings['school_name'] ?? 'School Portal' }}. All rights reserved.
                <br><small>{{ $globalSettings['academic_session'] ?? '' }} &bull; {{ $globalSettings['current_term'] ?? '' }}</small>
            </div>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('navbar');
            nav.classList.toggle('scrolled', window.scrollY > 50);
        });
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', function(e) {
                e.preventDefault();
                const el = document.querySelector(this.getAttribute('href'));
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    </script>
</body>

</html>
