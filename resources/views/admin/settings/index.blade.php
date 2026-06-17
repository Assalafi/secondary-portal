@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
    <div class="container-fluid">
        <!-- Page Title & Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Settings</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Settings</h4>
                </div>
            </div>
        </div>

        <!-- Settings Grid -->
        <div class="row">
            <!-- School Information -->
            <div class="col-lg-4 col-md-6">
                <div class="card settings-card">
                    <div class="card-body">
                        <div class="settings-icon">
                            <i class="ri-building-line"></i>
                        </div>
                        <h5 class="card-title">School Information</h5>
                        <p class="card-text text-muted">Manage school details, logo, and contact information</p>
                        <a href="{{ route('admin.settings.school-info') }}" class="btn btn-soft-primary">
                            <i class="ri-settings-3-line me-1"></i>Configure
                        </a>
                    </div>
                </div>
            </div>

            <!-- Grading System -->
            <div class="col-lg-4 col-md-6">
                <div class="card settings-card">
                    <div class="card-body">
                        <div class="settings-icon">
                            <i class="ri-bar-chart-line"></i>
                        </div>
                        <h5 class="card-title">Grading System</h5>
                        <p class="card-text text-muted">Configure grade levels, score ranges, and GPA settings</p>
                        <a href="{{ route('admin.settings.grading-system') }}" class="btn btn-soft-primary">
                            <i class="ri-settings-3-line me-1"></i>Configure
                        </a>
                    </div>
                </div>
            </div>

            <!-- Session/Term Management -->
            <div class="col-lg-4 col-md-6">
                <div class="card settings-card">
                    <div class="card-body">
                        <div class="settings-icon">
                            <i class="ri-calendar-line"></i>
                        </div>
                        <h5 class="card-title">Session/Term</h5>
                        <p class="card-text text-muted">Manage academic sessions and terms</p>
                        <a href="{{ route('admin.settings.session-term') }}" class="btn btn-soft-primary">
                            <i class="ri-settings-3-line me-1"></i>Configure
                        </a>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="col-lg-4 col-md-6">
                <div class="card settings-card">
                    <div class="card-body">
                        <div class="settings-icon">
                            <i class="ri-shield-line"></i>
                        </div>
                        <h5 class="card-title">Security</h5>
                        <p class="card-text text-muted">Configure security policies and authentication settings</p>
                        <a href="{{ route('admin.settings.security') }}" class="btn btn-soft-primary">
                            <i class="ri-settings-3-line me-1"></i>Configure
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Management -->
            <div class="col-lg-4 col-md-6">
                <div class="card settings-card">
                    <div class="card-body">
                        <div class="settings-icon">
                            <i class="ri-user-settings-line"></i>
                        </div>
                        <h5 class="card-title">User Management</h5>
                        <p class="card-text text-muted">Manage system users, roles, and permissions</p>
                        <a href="{{ route('admin.settings.user-management') }}" class="btn btn-soft-primary">
                            <i class="ri-settings-3-line me-1"></i>Configure
                        </a>
                    </div>
                </div>
            </div>

            <!-- Role & Permissions -->
            <div class="col-lg-4 col-md-6">
                <div class="card settings-card">
                    <div class="card-body">
                        <div class="settings-icon">
                            <i class="ri-user-star-line"></i>
                        </div>
                        <h5 class="card-title">Role & Permissions</h5>
                        <p class="card-text text-muted">Configure user roles and access permissions</p>
                        <a href="{{ route('admin.settings.role-permissions') }}" class="btn btn-soft-primary">
                            <i class="ri-settings-3-line me-1"></i>Configure
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notification Preferences -->
            <div class="col-lg-4 col-md-6">
                <div class="card settings-card">
                    <div class="card-body">
                        <div class="settings-icon">
                            <i class="ri-notification-line"></i>
                        </div>
                        <h5 class="card-title">Notifications</h5>
                        <p class="card-text text-muted">Configure notification preferences and alerts</p>
                        <a href="{{ route('admin.settings.notification-preferences') }}" class="btn btn-soft-primary">
                            <i class="ri-settings-3-line me-1"></i>Configure
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Configuration -->
            <div class="col-lg-4 col-md-6">
                <div class="card settings-card">
                    <div class="card-body">
                        <div class="settings-icon">
                            <i class="ri-settings-2-line"></i>
                        </div>
                        <h5 class="card-title">System Config</h5>
                        <p class="card-text text-muted">General system configuration and preferences</p>
                        <a href="{{ route('admin.settings.system-config') }}" class="btn btn-soft-primary">
                            <i class="ri-settings-3-line me-1"></i>Configure
                        </a>
                    </div>
                </div>
            </div>

            <!-- Backup & Restore -->
            <div class="col-lg-4 col-md-6">
                <div class="card settings-card">
                    <div class="card-body">
                        <div class="settings-icon">
                            <i class="ri-database-line"></i>
                        </div>
                        <h5 class="card-title">Backup & Restore</h5>
                        <p class="card-text text-muted">Database backup and system restore options</p>
                        <a href="{{ route('admin.settings.backup-restore') }}" class="btn btn-soft-primary">
                            <i class="ri-settings-3-line me-1"></i>Configure
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .settings-card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 0.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .settings-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .settings-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(45deg, #5664d2, #677cdc);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .settings-icon i {
            font-size: 24px;
            color: white;
        }

        .card-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .card-text {
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
        }

        .btn-soft-primary {
            background-color: rgba(86, 100, 210, 0.1);
            border-color: rgba(86, 100, 210, 0.1);
            color: #5664d2;
        }

        .btn-soft-primary:hover {
            background-color: #5664d2;
            border-color: #5664d2;
            color: white;
        }

        .page-title {
            color: #495057;
            font-weight: 600;
            margin: 0;
        }

        .breadcrumb-item a {
            color: #6c757d;
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #5664d2;
        }
    </style>
@endpush
