<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $globalSettings['school_name'] ?? 'School Portal' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --info-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            --dark-gradient: linear-gradient(135deg, #434343 0%, #000000 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Preloader */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .preloader.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .preloader-content {
            text-align: center;
            color: white;
        }

        .preloader-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Hero Section */
        .hero-section {
            min-height: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-gradient);
            color: white;
            padding: 80px 20px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-20px, 20px); }
        }

        .hero-content {
            text-align: center;
            z-index: 1;
            animation: fadeInUp 1s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .school-logo {
            width: 150px;
            height: 150px;
            margin: 0 auto 30px;
            background: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .school-logo img {
            max-width: 120px;
            max-height: 120px;
            object-fit: contain;
            border-radius: 15px;
        }

        .school-logo-placeholder {
            font-size: 48px;
            color: #667eea;
            font-weight: 700;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        .hero-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .hero-badges {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .hero-badge {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 10px 25px;
            border-radius: 30px;
            font-size: 0.9rem;
            border: 1px solid rgba(255,255,255,0.3);
        }

        /* Role Cards Section */
        .roles-section {
            padding: 80px 20px;
            background: white;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .section-subtitle {
            text-align: center;
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 60px;
        }

        .role-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid transparent;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
            position: relative;
            overflow: hidden;
        }

        .role-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .role-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            border-color: transparent;
        }

        .role-card:hover::before {
            opacity: 0.1;
        }

        .role-card .card-content {
            position: relative;
            z-index: 1;
        }

        .role-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: white;
            transition: transform 0.4s ease;
        }

        .role-card:hover .role-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .role-card.admin .role-icon { background: var(--primary-gradient); }
        .role-card.teacher .role-icon { background: var(--success-gradient); }
        .role-card.student .role-icon { background: var(--warning-gradient); }
        .role-card.parent .role-icon { background: var(--info-gradient); }
        .role-card.accountant .role-icon { background: var(--secondary-gradient); }
        .role-card.librarian .role-icon { background: var(--dark-gradient); }

        .role-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .role-card:hover .role-title {
            color: #667eea;
        }

        .role-description {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .role-arrow {
            color: #667eea;
            font-size: 24px;
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }

        .role-card:hover .role-arrow {
            opacity: 1;
            transform: translateX(0);
        }

        /* Footer */
        .footer {
            background: #1a1a2e;
            color: white;
            padding: 60px 20px 30px;
            text-align: center;
        }

        .footer-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .footer-logo {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .footer-info {
            color: rgba(255,255,255,0.7);
            margin-bottom: 30px;
            line-height: 1.8;
        }

        .footer-links {
            display: flex;
            gap: 30px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .footer-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
            color: rgba(255,255,255,0.5);
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .role-card {
                padding: 30px 20px;
            }

            .role-icon {
                width: 60px;
                height: 60px;
                font-size: 28px;
            }

            .role-title {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="preloader-content">
            <div class="preloader-spinner"></div>
            <p>Loading...</p>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="school-logo">
                @if ($globalSettings['school_logo'])
                    <img src="{{ asset('storage/' . $globalSettings['school_logo']) }}" alt="{{ $globalSettings['school_name'] }}">
                @else
                    <div class="school-logo-placeholder">{{ substr($globalSettings['school_name'] ?? 'S', 0, 2) }}</div>
                @endif
            </div>
            <h1 class="hero-title">{{ $globalSettings['school_name'] ?? 'School Portal' }}</h1>
            <p class="hero-subtitle">{{ $globalSettings['academic_session'] ?? 'Academic Session' }} • {{ $globalSettings['current_term'] ?? 'Current Term' }}</p>
            <div class="hero-badges">
                <span class="hero-badge"><i class="ri-graduation-cap-line me-2"></i>Excellence in Education</span>
                <span class="hero-badge"><i class="ri-shield-check-line me-2"></i>Secure Platform</span>
                <span class="hero-badge"><i class="ri-smartphone-line me-2"></i>Accessible Anywhere</span>
            </div>
        </div>
    </section>

    <!-- Role Cards Section -->
    <section class="roles-section">
        <div class="container">
            <h2 class="section-title">Select Your Role</h2>
            <p class="section-subtitle">Choose your role to access the appropriate portal</p>

            <div class="row g-4">
                <!-- Admin -->
                <div class="col-lg-4 col-md-6">
                    <a href="/login/admin" class="role-card admin">
                        <div class="card-content">
                            <div class="role-icon">
                                <i class="ri-admin-line"></i>
                            </div>
                            <h3 class="role-title">Admin</h3>
                            <p class="role-description">Manage school administration, settings, and oversee all operations</p>
                            <i class="ri-arrow-right-line role-arrow"></i>
                        </div>
                    </a>
                </div>

                <!-- Teacher -->
                <div class="col-lg-4 col-md-6">
                    <a href="/login/teacher" class="role-card teacher">
                        <div class="card-content">
                            <div class="role-icon">
                                <i class="ri-presentation-line"></i>
                            </div>
                            <h3 class="role-title">Teacher</h3>
                            <p class="role-description">Manage classes, upload results, take attendance, and track student progress</p>
                            <i class="ri-arrow-right-line role-arrow"></i>
                        </div>
                    </a>
                </div>

                <!-- Student -->
                <div class="col-lg-4 col-md-6">
                    <a href="/login/student" class="role-card student">
                        <div class="card-content">
                            <div class="role-icon">
                                <i class="ri-user-smile-line"></i>
                            </div>
                            <h3 class="role-title">Student</h3>
                            <p class="role-description">View results, check attendance, make payments, and access learning materials</p>
                            <i class="ri-arrow-right-line role-arrow"></i>
                        </div>
                    </a>
                </div>

                <!-- Parent -->
                <div class="col-lg-4 col-md-6">
                    <a href="/login/parent" class="role-card parent">
                        <div class="card-content">
                            <div class="role-icon">
                                <i class="ri-parent-line"></i>
                            </div>
                            <h3 class="role-title">Parent</h3>
                            <p class="role-description">Monitor ward's performance, view reports, manage payments, and communicate</p>
                            <i class="ri-arrow-right-line role-arrow"></i>
                        </div>
                    </a>
                </div>

                <!-- Accountant -->
                <div class="col-lg-4 col-md-6">
                    <a href="/login/accountant" class="role-card accountant">
                        <div class="card-content">
                            <div class="role-icon">
                                <i class="ri-money-dollar-circle-line"></i>
                            </div>
                            <h3 class="role-title">Accountant</h3>
                            <p class="role-description">Manage fee collections, generate invoices, and track financial records</p>
                            <i class="ri-arrow-right-line role-arrow"></i>
                        </div>
                    </a>
                </div>

                <!-- Librarian -->
                <div class="col-lg-4 col-md-6">
                    <a href="/login/librarian" class="role-card librarian">
                        <div class="card-content">
                            <div class="role-icon">
                                <i class="ri-book-3-line"></i>
                            </div>
                            <h3 class="role-title">Librarian</h3>
                            <p class="role-description">Manage library resources, track book borrowing, and maintain records</p>
                            <i class="ri-arrow-right-line role-arrow"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">{{ $globalSettings['school_name'] ?? 'School Portal' }}</div>
            <p class="footer-info">
                <i class="ri-map-pin-line me-2"></i>{{ $globalSettings['school_address'] ?? 'School Address' }}<br>
                <i class="ri-phone-line me-2"></i>{{ $globalSettings['phone_number'] ?? 'Phone Number' }}<br>
                <i class="ri-mail-line me-2"></i>{{ $globalSettings['email'] ?? 'Email Address' }}
            </p>
            <div class="footer-links">
                <a href="#"><i class="ri-facebook-fill me-1"></i>Facebook</a>
                <a href="#"><i class="ri-twitter-x-fill me-1"></i>Twitter</a>
                <a href="#"><i class="ri-instagram-fill me-1"></i>Instagram</a>
            </div>
            <div class="footer-bottom">
                &copy; {{ date('Y') }} {{ $globalSettings['school_name'] ?? 'School Portal' }}. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        // Preloader
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('preloader').classList.add('hidden');
            }, 500);
        });
    </script>
</body>

</html>
