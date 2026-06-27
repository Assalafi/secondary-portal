@extends('layouts.student')

@section('title', 'Student Dashboard')
@section('page-title', 'My Dashboard')

@section('content')
    @if(!$student)
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="ri-error-warning-line text-warning" style="font-size: 64px;"></i>
                <h5 class="mt-3 mb-2">Student Profile Not Found</h5>
                <p class="text-muted">Your student profile has not been set up yet. Please contact the school administrator.</p>
            </div>
        </div>
    @else
        <!-- Welcome Banner -->
        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body py-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="text-white mb-1">Welcome back, {{ explode(' ', Auth::user()->name)[0] }}! 👋</h4>
                        <p class="text-white-50 mb-0">
                            {{ $student->classArm->schoolClass->name ?? 'N/A' }} {{ $student->classArm->name ?? '' }}
                            &bull; Admission No: {{ $student->admission_no }}
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <span class="badge bg-white text-dark px-3 py-2 fs-14">
                            <i class="ri-trophy-line me-1"></i>Position: {{ $stats['class_position'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width:40px;height:40px;background:rgba(102,126,234,0.1);">
                                <i class="ri-percent-line text-primary" style="font-size:20px;"></i>
                            </div>
                        </div>
                        <h3 class="mb-0">{{ $stats['attendance_rate'] }}%</h3>
                        <small class="text-muted">Attendance Rate</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width:40px;height:40px;background:rgba(40,167,69,0.1);">
                                <i class="ri-bar-chart-box-line text-success" style="font-size:20px;"></i>
                            </div>
                        </div>
                        <h3 class="mb-0">{{ $stats['average_score'] }}%</h3>
                        <small class="text-muted">Average Score</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width:40px;height:40px;background:rgba(23,162,184,0.1);">
                                <i class="ri-book-open-line text-info" style="font-size:20px;"></i>
                            </div>
                        </div>
                        <h3 class="mb-0">{{ $stats['total_subjects'] }}</h3>
                        <small class="text-muted">Subjects</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width:40px;height:40px;background:rgba(255,193,7,0.1);">
                                <i class="ri-wallet-3-line text-warning" style="font-size:20px;"></i>
                            </div>
                        </div>
                        <h3 class="mb-0">{{ $stats['pending_payments'] }}</h3>
                        <small class="text-muted">Pending Fees</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <!-- Attendance Chart -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">Attendance Overview</h6>
                            <a href="{{ route('student.attendance.index') }}" class="text-decoration-none small">View All</a>
                        </div>
                        <div id="attendanceChart"></div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Quick Actions</h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('student.results.index') }}" class="btn btn-outline-primary btn-sm text-start">
                                <i class="ri-file-chart-line me-2"></i>View My Results
                            </a>
                            <a href="{{ route('student.report-cards') }}" class="btn btn-outline-success btn-sm text-start">
                                <i class="ri-file-text-line me-2"></i>View Report Cards
                            </a>
                            <a href="{{ route('student.assignments.index') }}" class="btn btn-outline-info btn-sm text-start">
                                <i class="ri-clipboard-line me-2"></i>My Assignments
                            </a>
                            <a href="{{ route('student.payments.index') }}" class="btn btn-outline-warning btn-sm text-start">
                                <i class="ri-bank-card-line me-2"></i>Make Payment
                            </a>
                            <a href="{{ route('student.attendance.index') }}" class="btn btn-outline-secondary btn-sm text-start">
                                <i class="ri-calendar-check-line me-2"></i>Check Attendance
                            </a>
                            <a href="{{ route('student.timetable') }}" class="btn btn-outline-dark btn-sm text-start">
                                <i class="ri-calendar-todo-line me-2"></i>My Timetable
                            </a>
                            <a href="{{ route('student.profile') }}" class="btn btn-outline-primary btn-sm text-start">
                                <i class="ri-user-settings-line me-2"></i>My Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <!-- Pending Payments -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">Pending Payments</h6>
                            <a href="{{ route('student.payments.index') }}" class="text-decoration-none small">View All</a>
                        </div>
                        @if($pendingPayments->count() > 0)
                            @foreach($pendingPayments as $invoice)
                                @php
                                    $paymentTitle = 'Invoice #' . $invoice->invoice_number;
                                    if ($invoice->invoiceItems && $invoice->invoiceItems->count() > 0) {
                                        $paymentTitle = optional($invoice->invoiceItems->first()->feeSetup)->payment_type ?? 'School Fees';
                                    }
                                @endphp
                                <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div>
                                        <p class="mb-0 fw-medium">{{ $paymentTitle }}</p>
                                        <small class="text-muted">{{ optional($invoice->term)->name ?? 'N/A' }} &bull; Due: {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'N/A' }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold text-danger">&#8358;{{ number_format($invoice->balance ?? $invoice->total_amount, 2) }}</span>
                                        <br>
                                        <span class="badge bg-warning text-dark small">{{ $invoice->status }}</span>
                                    </div>
                                </div>
                            @endforeach
                            @if($stats['outstanding_balance'] > 0)
                                <div class="mt-3 p-2 rounded" style="background: rgba(255,193,7,0.1);">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">Total Outstanding</span>
                                        <span class="fw-bold text-danger fs-5">&#8358;{{ number_format($stats['outstanding_balance'], 2) }}</span>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <i class="ri-check-double-line text-success" style="font-size: 48px;"></i>
                                <p class="mt-2 text-muted mb-0">All payments are up to date!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Results -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">Recent Results</h6>
                            <a href="{{ route('student.results.index') }}" class="text-decoration-none small">View All</a>
                        </div>
                        @if($recentResults->count() > 0)
                            @foreach($recentResults as $score)
                                @php
                                    $subjectName = optional(optional($score->scoreBatch)->subject)->name ?? 'N/A';
                                    $termName = optional(optional($score->scoreBatch)->term)->name ?? '';
                                    $totalScore = $score->total ?? 0;
                                    $pct = min(round($totalScore), 100);
                                    $color = $pct >= 70 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
                                @endphp
                                <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div>
                                        <p class="mb-0 fw-medium">{{ $subjectName }}</p>
                                        <small class="text-muted">{{ $termName }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold">{{ $totalScore }}%</span>
                                        <div class="progress mt-1" style="width: 80px; height: 5px;">
                                            <div class="progress-bar bg-{{ $color }}" style="width: {{ $pct }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="ri-file-list-line text-muted" style="font-size: 48px;"></i>
                                <p class="mt-2 text-muted mb-0">No results available yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <!-- My Subjects -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">My Subjects</h6>
                            <span class="badge bg-primary">{{ $subjects->count() }} Subjects</span>
                        </div>
                        @if($subjects->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Subject</th>
                                            <th>Teacher</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subjects as $subject)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <i class="ri-book-line me-1 text-primary"></i>
                                                    {{ $subject->name ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    <i class="ri-user-line me-1 text-muted"></i>
                                                    @php $teacher = $subject->pivot->teacher_id ? \App\Models\User::find($subject->pivot->teacher_id) : null; @endphp
                                                    {{ $teacher->name ?? 'TBA' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-3 text-muted">
                                <p class="mb-0">No subjects assigned yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Upcoming Events</h6>
                        @foreach($upcomingEvents as $event)
                            <div class="d-flex align-items-start mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-3"
                                     style="width:40px;height:40px;background:{{ $event['type'] === 'exam' ? 'rgba(220,53,69,0.1)' : ($event['type'] === 'event' ? 'rgba(40,167,69,0.1)' : 'rgba(23,162,184,0.1)') }};">
                                    <i class="{{ $event['type'] === 'exam' ? 'ri-edit-2-line text-danger' : ($event['type'] === 'event' ? 'ri-flag-line text-success' : 'ri-group-line text-info') }}" style="font-size:18px;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-medium">{{ $event['title'] }}</p>
                                    <small class="text-muted">{{ $event['date']->format('D, M d Y') }} &bull; {{ $event['date']->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($student && !empty($attendanceChart))
        var options = {
            chart: { type: 'area', height: 250, toolbar: { show: false }, fontFamily: 'inherit' },
            series: [{ name: 'Attendance Rate', data: {!! json_encode(array_column($attendanceChart, 'rate')) !!} }],
            xaxis: { categories: {!! json_encode(array_column($attendanceChart, 'month')) !!} },
            yaxis: { max: 100, labels: { formatter: function(val) { return val + '%'; } } },
            colors: ['#667eea'],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } },
            stroke: { curve: 'smooth', width: 3 },
            dataLabels: { enabled: false },
            tooltip: { y: { formatter: function(val) { return val + '%'; } } },
            grid: { borderColor: '#f1f1f1' },
        };
        var chart = new ApexCharts(document.querySelector("#attendanceChart"), options);
        chart.render();
        @endif
    });
</script>
@endpush
@endsection
