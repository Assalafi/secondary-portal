@extends('layouts.parent')

@section('title', 'Parent Dashboard')
@section('page-title', 'Parent/Guardian Dashboard')

@section('content')
    <!-- Session Info -->
    <div class="mb-3">
        <small class="text-muted">{{ $globalSettings['academic_session'] }} - {{ $globalSettings['current_term'] }}</small>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Total Dependents</p>
                        <h3>{{ $stats['total_dependents'] }}</h3>
                        <small class="text-muted">Active Students</small>
                    </div>
                    <div class="stat-icon">
                        <i class="ri-group-line" style="font-size: 32px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Average Attendance</p>
                        <h3>{{ $stats['average_attendance'] }}%</h3>
                        <small class="text-muted">This Month</small>
                    </div>
                    <div class="stat-icon">
                        <i class="ri-calendar-check-line" style="font-size: 32px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Pending Payments</p>
                        <h3>{{ $stats['pending_payments'] }}</h3>
                        <small class="text-muted">Due next month</small>
                    </div>
                    <div class="stat-icon">
                        <i class="ri-wallet-3-line" style="font-size: 32px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Students/Children -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">My Students</h5>
            <a href="{{ route('parent.dependents.index') }}" class="text-decoration-none small">View All</a>
        </div>
        
        @if($dependents->count() > 0)
            <div class="row g-3">
                @foreach($dependents as $student)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    @if($student->user && $student->user->photo_path)
                                        <img src="{{ asset('storage/' . $student->user->photo_path) }}" 
                                             alt="{{ $student->user->name }}" 
                                             class="rounded-circle me-3"
                                             style="width: 60px; height: 60px; object-fit: cover; border: 2px solid #667eea;">
                                    @else
                                        <div class="rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <i class="ri-user-line text-white" style="font-size: 28px;"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $student->user->name ?? $student->first_name . ' ' . $student->last_name }}</h6>
                                        <p class="text-muted small mb-1">
                                            <i class="ri-book-line me-1"></i>{{ $student->classArm->schoolClass->name ?? 'N/A' }} {{ $student->classArm->name ?? '' }}
                                        </p>
                                        <p class="text-muted small mb-0">
                                            <i class="ri-barcode-line me-1"></i>{{ $student->admission_no }}
                                        </p>
                                    </div>
                                    <span class="badge {{ $student->status === 'Active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $student->status }}
                                    </span>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <a href="{{ route('parent.dependents.show', $student->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="ri-eye-line me-1"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="ri-user-line text-muted" style="font-size: 64px;"></i>
                    <h6 class="mt-3 mb-2">No Students Yet</h6>
                    <p class="text-muted mb-3">You don't have any admitted students at the moment.</p>
                    <a href="{{ route('parent.admission.index') }}" class="btn btn-primary">
                        <i class="ri-add-line me-2"></i>Submit New Application
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Admission Applications -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Admission Applications</h5>
            <a href="{{ route('parent.admission.index') }}" class="text-decoration-none small">View All</a>
        </div>
        
        @if($applications->count() > 0)
            <div class="row g-3">
                @foreach($applications as $app)
                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">{{ $app->full_name }}</h6>
                                    <span class="badge {{ $app->getStatusBadgeClass() }}">{{ $app->status }}</span>
                                </div>
                                <p class="text-muted small mb-2">
                                    <i class="ri-file-text-line me-1"></i>{{ $app->application_number }}
                                </p>
                                <p class="text-muted small mb-2">
                                    <i class="ri-book-line me-1"></i>{{ $app->proposedClass->name ?? 'N/A' }} - {{ $app->academicSession->name ?? 'N/A' }}
                                </p>
                                <p class="text-muted small mb-3">
                                    <i class="ri-calendar-line me-1"></i>{{ $app->submitted_at ? $app->submitted_at->format('M d, Y') : 'Not submitted' }}
                                </p>
                                
                                <div class="d-grid gap-2">
                                    @if($app->status === 'Draft' || $app->status === 'Pending Payment')
                                        <a href="{{ route('parent.admission.form', $app->id) }}" class="btn btn-sm btn-primary">
                                            <i class="ri-edit-line me-1"></i>Continue Application
                                        </a>
                                    @elseif($app->status === 'Approved')
                                        <a href="{{ route('parent.admission.show', $app->id) }}" class="btn btn-sm btn-success">
                                            <i class="ri-checkbox-circle-line me-1"></i>View Admission
                                        </a>
                                    @else
                                        <a href="{{ route('parent.admission.show', $app->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ri-eye-line me-1"></i>View Details
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="ri-file-list-line text-muted" style="font-size: 64px;"></i>
                    <h6 class="mt-3 mb-2">No Applications Yet</h6>
                    <p class="text-muted mb-3">Start your child's admission journey today.</p>
                    <a href="{{ route('parent.admission.create') }}" class="btn btn-primary">
                        <i class="ri-add-line me-2"></i>New Admission Application
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Links -->
    <div class="mb-4">
        <h5 class="mb-3">Quick Links</h5>
        <div class="row g-3">
            <div class="col-12 col-sm-6 col-md-4">
                <a href="{{ route('parent.dependents.index') }}" class="text-decoration-none">
                    <div class="stat-card hover-effect">
                        <div class="d-flex align-items-center justify-content-between">
                            <span>View Dependents</span>
                            <i class="ri-arrow-right-line"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <a href="{{ route('parent.payments.index') }}" class="text-decoration-none">
                    <div class="stat-card hover-effect">
                        <div class="d-flex align-items-center justify-content-between">
                            <span>Make Payment</span>
                            <i class="ri-arrow-right-line"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <a href="{{ route('parent.dependents.index') }}" class="text-decoration-none">
                    <div class="stat-card hover-effect">
                        <div class="d-flex align-items-center justify-content-between">
                            <span>Manage Dependents</span>
                            <i class="ri-arrow-right-line"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activities & Upcoming Events -->
    <div class="row g-3">
        <!-- Recent Activities -->
        <div class="col-12 col-lg-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Recent Activities</h5>
                    <a href="#" class="text-decoration-none small">View All</a>
                </div>

                @if ($recentActivities->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach ($recentActivities as $activity)
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-start">
                                    <i class="{{ $activity['icon'] }} me-3 mt-1"></i>
                                    <div class="flex-grow-1">
                                        <p class="mb-1">{{ $activity['message'] }}</p>
                                        <small class="text-muted">{{ $activity['time']->diffForHumans() }}</small>
                                    </div>
                                    <a href="#" class="text-decoration-none">
                                        <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="ri-inbox-line" style="font-size: 48px;"></i>
                        <p class="mt-2">No recent activities</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="col-12 col-lg-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Upcoming Events</h5>
                    <a href="#" class="text-decoration-none small">View All</a>
                </div>

                @if ($upcomingEvents->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach ($upcomingEvents as $event)
                            <div class="list-group-item px-0">
                                <div class="d-flex align-items-start">
                                    <i class="ri-calendar-event-line me-3 mt-1"></i>
                                    <div class="flex-grow-1">
                                        <p class="mb-1 fw-medium">{{ $event['title'] }}</p>
                                        <small class="text-muted">{{ $event['date']->format('M d, Y') }} -
                                            {{ $event['date']->diffForHumans() }}</small>
                                    </div>
                                    <a href="#" class="text-decoration-none">
                                        <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="ri-calendar-line" style="font-size: 48px;"></i>
                        <p class="mt-2">No upcoming events</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .hover-effect {
                cursor: pointer;
                transition: all 0.2s;
            }

            .hover-effect:hover {
                background-color: var(--bg-light);
                transform: translateY(-2px);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .list-group-item {
                border-left: 0;
                border-right: 0;
            }

            .list-group-item:first-child {
                border-top: 0;
            }

            .list-group-item:last-child {
                border-bottom: 0;
            }
        </style>
    @endpush
@endsection
