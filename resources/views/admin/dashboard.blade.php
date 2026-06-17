@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<style>
    .card {
        border-radius: 8px;
    }
    .card-body h6 {
        font-size: 13px;
        color: #6c757d;
        font-weight: 400;
    }
    .card-body h2 {
        font-size: 32px;
        color: #000;
    }
    .btn {
        border-radius: 6px;
        padding: 10px 20px;
    }
    .list-group-item {
        border-left: 0;
        border-right: 0;
    }
    .list-group-item:first-child {
        border-top: 0;
    }
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
</style>
@endpush

@section('content')
    {{-- Page Header --}}
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Admin Dashboard</h1>
        <p class="text-muted">
            {{ $currentSession->name ?? '2024/2025' }} Academic Session – {{ $currentTerm->name ?? '3rd term' }}
        </p>
    </div>

    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2 small">Students Enrolled</h6>
                    <h2 class="fw-bold mb-3">{{ number_format($stats['total_students']) }}</h2>
                    <a href="{{ route('admin.students.index') }}" class="text-decoration-none text-dark small">
                        View All Students <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2 small">Staff</h6>
                    <h2 class="fw-bold mb-3">{{ number_format($stats['total_staff']) }}</h2>
                    <a href="{{ route('admin.staff.index') }}" class="text-decoration-none text-dark small">
                        View All Staff <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2 small">Active Classes</h6>
                    <h2 class="fw-bold mb-3">{{ number_format($stats['total_classes']) }}</h2>
                    <a href="#" class="text-decoration-none text-dark small">
                        View All Classes <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-2 small">Fees Collected</h6>
                    <h2 class="fw-bold mb-3">₦{{ number_format($stats['total_fees_collected']) }}</h2>
                    <a href="{{ route('admin.payments.fees-income') }}" class="text-decoration-none text-dark small">
                        View Fees Record <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3 fw-bold">Quick Link</h5>
            <a href="{{ route('admin.students.enroll.step1') }}" class="btn btn-primary me-2">
                <i class="ri-add-line me-1"></i> Enroll New Student
            </a>
            <a href="{{ route('admin.staff.enroll.step1') }}" class="btn btn-light border me-2">
                <i class="ri-add-line me-1"></i> Add Staff
            </a>
            <a href="{{ route('admin.classes.create') }}" class="btn btn-light border">
                <i class="ri-add-line me-1"></i> Create New Class
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <h5 class="mb-3 fw-bold">Report Summary</h5>
        </div>

        <div class="col-xl-5 col-lg-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="m-0 fw-bold">Fees Collected vs Outstanding</h6>
                        <a href="{{ route('admin.payments.fees-income') }}" class="text-decoration-none text-dark small">
                            View All <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                    <div class="d-flex align-items-center justify-content-center" style="min-height: 280px;">
                        <div style="position: relative; height:280px; width:280px">
                            <canvas id="feesPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-7 col-lg-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="m-0 fw-bold">Student Attendance (Last 5 Days)</h6>
                        <a href="#" class="text-decoration-none text-dark small">
                            View All <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                    <div style="height: 280px;">
                        <canvas id="attendanceLineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="m-0 fw-bold">Recent Activities</h6>
                    <a href="#" class="text-decoration-none text-dark small">View All</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($recentActivities as $activity)
                        <a href="{{ $activity['url'] }}"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-start py-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="{{ $activity['icon'] }} fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $activity['title'] }}</div>
                                    @if(isset($activity['description']))
                                        <small class="text-muted">{{ $activity['description'] }}</small>
                                    @endif
                                </div>
                            </div>
                            <small class="text-muted">{{ $activity['time']->diffForHumans() }}</small>
                        </a>
                    @empty
                        <div class="list-group-item text-center text-muted py-4">
                            No recent activities
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Fees Collected Pie Chart with REAL DATA
            const collectedPercent = {{ $stats['fees_collected_percent'] }};
            const outstandingPercent = {{ $stats['fees_outstanding_percent'] }};
            
            new Chart(document.getElementById('feesPieChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Collected Fees', 'Outstanding Fees'],
                    datasets: [{
                        data: [collectedPercent, outstandingPercent],
                        backgroundColor: ['#1e40af', '#ff9900'],
                        borderColor: '#fff',
                        borderWidth: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '0%',
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            const value = data.datasets[0].data[i];
                                            return {
                                                text: `${label}`,
                                                fillStyle: data.datasets[0].backgroundColor[i],
                                                hidden: false,
                                                index: i
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => `${context.label}: ${context.parsed}%`
                            }
                        },
                        datalabels: {
                            formatter: (value) => value.toFixed(1) + '%',
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 16
                            }
                        }
                    }
                },
                plugins: [{
                    afterDatasetsDraw: function(chart) {
                        const ctx = chart.ctx;
                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach((element, index) => {
                                ctx.fillStyle = '#fff';
                                const fontSize = 14;
                                const fontStyle = 'bold';
                                const fontFamily = 'Arial';
                                ctx.font = fontStyle + ' ' + fontSize + 'px ' + fontFamily;
                                
                                const dataString = dataset.data[index].toFixed(1) + '%';
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';
                                
                                const position = element.tooltipPosition();
                                ctx.fillText(dataString, position.x, position.y);
                            });
                        });
                    }
                }]
            });

            // Student Attendance Line Chart with REAL DATA
            const attendanceLabels = @json($attendanceLabels);
            const attendanceData = @json($attendanceData);
            
            new Chart(document.getElementById('attendanceLineChart'), {
                type: 'line',
                data: {
                    labels: attendanceLabels,
                    datasets: [{
                        label: "Attendance",
                        data: attendanceData,
                        borderColor: '#1e40af',
                        backgroundColor: 'rgba(30, 64, 175, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: (value) => value + '%'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
