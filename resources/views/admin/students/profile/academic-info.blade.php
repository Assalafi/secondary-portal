@extends('layouts.admin')

@section('title', 'Student Profile - Academic Info')

@section('content')
<div class="main-content-container overflow-hidden">
    <!-- Student Profile Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary me-3 d-flex align-items-center gap-2">
                    <i class="ri-arrow-left-line"></i>
                    Back to Students
                </a>
                <div>
                    <h3 class="fs-20 fw-semibold mb-1">Student Profile</h3>
                    <p class="text-secondary mb-0">{{ $student->full_name ?? '-' }} - {{ $student->admission_no ?? '-' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="d-flex gap-2 justify-content-md-end">
                <button class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <i class="ri-download-line"></i>
                    Export Report
                </button>
                <button class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="ri-printer-line"></i>
                    Print Report
                </button>
            </div>
        </div>
    </div>
    
    <!-- Tab Navigation -->
    <div class="card custom-shadow rounded-3 bg-white border mb-4">
        <div class="card-body p-0">
            <ul class="nav nav-tabs border-0 px-4 pt-3" id="studentProfileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium" href="{{ route('admin.students.profile.overview', $student->id) }}">
                        <i class="ri-user-line me-2"></i>Overview
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link active fw-medium" href="{{ route('admin.students.profile.academic', $student->id) }}">
                        <i class="ri-graduation-cap-line me-2"></i>Academic Info
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium" href="{{ route('admin.students.profile.fees', $student->id) }}">
                        <i class="ri-money-dollar-circle-line me-2"></i>Fees & Payments
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium" href="{{ route('admin.students.profile.attendance', $student->id) }}">
                        <i class="ri-calendar-check-line me-2"></i>Attendance
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium" href="{{ route('admin.students.profile.documents', $student->id) }}">
                        <i class="ri-file-text-line me-2"></i>Documents
                    </a>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- Academic Content -->
    <div class="row g-4">
        <!-- Academic Status Cards -->
        <div class="col-lg-4">
            <div class="card custom-shadow rounded-3 bg-white border mb-4">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-semibold mb-0">
                        <i class="ri-graduation-cap-line me-2 text-primary"></i>Current Academic Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            @php
                                $cls = data_get($student, 'classArm.schoolClass.name');
                                $arm = data_get($student, 'classArm.name');
                                $className = trim(($cls ?: '-') . ' ' . ($arm ?: ''));
                                $currentTerm = null; // Not tracked; can be passed later
                            @endphp
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium text-secondary">Current Class:</span>
                                <span class="badge bg-primary-subtle text-primary">{{ $className }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium text-secondary">Academic Level:</span>
                                <span>{{ data_get($student, 'classArm.schoolClass.level', '—') }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium text-secondary">Shift:</span>
                                <span>—</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium text-secondary">Academic Year:</span>
                                <span>{{ data_get($student, 'academicSession.name', '—') }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium text-secondary">Current Term:</span>
                                <span class="badge bg-info-subtle text-info">{{ $currentTerm->name ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium text-secondary">Class Teacher:</span>
                                <span>Mrs. Sarah Johnson</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Summary -->
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-semibold mb-0">
                        <i class="ri-trophy-line me-2 text-warning"></i>Performance Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle mb-2" style="width: 60px; height: 60px;">
                            <span class="fw-bold fs-5">3.8</span>
                        </div>
                        <p class="mb-0 fw-medium">Current GPA</p>
                        <small class="text-secondary">Out of 4.0</small>
                    </div>
                    <hr>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium text-secondary">Class Position:</span>
                                <span class="badge bg-warning-subtle text-warning">5th of 45</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium text-secondary">Overall Grade:</span>
                                <span class="badge bg-success">A</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium text-secondary">Best Subject:</span>
                                <span>Mathematics</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="fw-medium text-secondary">Needs Improvement:</span>
                                <span>English Language</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Subject Performance Table -->
        <div class="col-lg-8">
            <div class="card custom-shadow rounded-3 bg-white border">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0">
                        <i class="ri-file-list-3-line me-2 text-primary"></i>Subject Performance - 2nd Term 2024/2025
                    </h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="ri-filter-line me-1"></i>Filter Term
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">1st Term</a></li>
                            <li><a class="dropdown-item active" href="#">2nd Term</a></li>
                            <li><a class="dropdown-item" href="#">3rd Term</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold">Subject</th>
                                    <th class="fw-semibold text-center">1st CA<br><small class="text-secondary">(20)</small></th>
                                    <th class="fw-semibold text-center">2nd CA<br><small class="text-secondary">(20)</small></th>
                                    <th class="fw-semibold text-center">Exam<br><small class="text-secondary">(60)</small></th>
                                    <th class="fw-semibold text-center">Total<br><small class="text-secondary">(100)</small></th>
                                    <th class="fw-semibold text-center">Grade</th>
                                    <th class="fw-semibold text-center">Position</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-medium">Mathematics</td>
                                    <td class="text-center">18</td>
                                    <td class="text-center">17</td>
                                    <td class="text-center">55</td>
                                    <td class="text-center fw-bold text-success">90</td>
                                    <td class="text-center"><span class="badge bg-success">A</span></td>
                                    <td class="text-center"><span class="badge bg-warning-subtle text-warning">2nd</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Basic Science</td>
                                    <td class="text-center">17</td>
                                    <td class="text-center">18</td>
                                    <td class="text-center">50</td>
                                    <td class="text-center fw-bold text-success">85</td>
                                    <td class="text-center"><span class="badge bg-success">A</span></td>
                                    <td class="text-center"><span class="badge bg-warning-subtle text-warning">4th</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Social Studies</td>
                                    <td class="text-center">16</td>
                                    <td class="text-center">15</td>
                                    <td class="text-center">48</td>
                                    <td class="text-center fw-bold text-primary">79</td>
                                    <td class="text-center"><span class="badge bg-primary">B</span></td>
                                    <td class="text-center"><span class="badge bg-secondary-subtle text-secondary">6th</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">English Language</td>
                                    <td class="text-center">15</td>
                                    <td class="text-center">16</td>
                                    <td class="text-center">45</td>
                                    <td class="text-center fw-bold text-primary">76</td>
                                    <td class="text-center"><span class="badge bg-primary">B</span></td>
                                    <td class="text-center"><span class="badge bg-secondary-subtle text-secondary">8th</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">French</td>
                                    <td class="text-center">14</td>
                                    <td class="text-center">16</td>
                                    <td class="text-center">42</td>
                                    <td class="text-center fw-bold text-warning">72</td>
                                    <td class="text-center"><span class="badge bg-warning">C</span></td>
                                    <td class="text-center"><span class="badge bg-secondary-subtle text-secondary">12th</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Civic Education</td>
                                    <td class="text-center">16</td>
                                    <td class="text-center">17</td>
                                    <td class="text-center">49</td>
                                    <td class="text-center fw-bold text-primary">82</td>
                                    <td class="text-center"><span class="badge bg-success">A</span></td>
                                    <td class="text-center"><span class="badge bg-warning-subtle text-warning">3rd</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Computer Studies</td>
                                    <td class="text-center">19</td>
                                    <td class="text-center">18</td>
                                    <td class="text-center">52</td>
                                    <td class="text-center fw-bold text-success">89</td>
                                    <td class="text-center"><span class="badge bg-success">A</span></td>
                                    <td class="text-center"><span class="badge bg-warning-subtle text-warning">1st</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">Physical & Health Education</td>
                                    <td class="text-center">17</td>
                                    <td class="text-center">16</td>
                                    <td class="text-center">46</td>
                                    <td class="text-center fw-bold text-primary">79</td>
                                    <td class="text-center"><span class="badge bg-primary">B</span></td>
                                    <td class="text-center"><span class="badge bg-secondary-subtle text-secondary">7th</span></td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th class="fw-bold">TOTAL/AVERAGE</th>
                                    <th class="text-center fw-bold">132/160</th>
                                    <th class="text-center fw-bold">133/160</th>
                                    <th class="text-center fw-bold">387/480</th>
                                    <th class="text-center fw-bold text-success">652/800</th>
                                    <th class="text-center"><span class="badge bg-success fs-6">A</span></th>
                                    <th class="text-center"><span class="badge bg-warning-subtle text-warning fs-6">5th</span></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Performance Chart -->
            <div class="card custom-shadow rounded-3 bg-white border mt-4">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-semibold mb-0">
                        <i class="ri-bar-chart-line me-2 text-primary"></i>Performance Trend
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <h5 class="text-primary mb-1">81.5%</h5>
                                <p class="mb-0 text-secondary">1st Term Average</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3 bg-success-subtle">
                                <h5 class="text-success mb-1">81.5%</h5>
                                <p class="mb-0 text-secondary">2nd Term Average</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <h5 class="text-secondary mb-1">--</h5>
                                <p class="mb-0 text-secondary">3rd Term Average</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex align-items-center">
                            <i class="ri-arrow-up-line text-success me-2"></i>
                            <span class="text-success fw-medium">Consistent performance maintained</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
