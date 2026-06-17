@extends('layouts.admin')

@section('title', 'Edit Attendance - ' . $reportCard->student->full_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Attendance Summary - {{ $reportCard->student->full_name }}</h5>
                    <a href="{{ route('admin.academic-management.report-cards.show', $reportCard->id) }}" class="btn btn-sm btn-secondary float-end">Back to Report Card</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.academic-management.report-cards.update-attendance', $reportCard->id) }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6>Student Information</h6>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <td><strong>Student:</strong> {{ $reportCard->student->full_name }}</td>
                                        <td><strong>Class:</strong> {{ $reportCard->class->name }}</td>
                                        <td><strong>Term:</strong> {{ $reportCard->term->name ?? 'Annual' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="alert alert-info mb-4">
                            <strong>Current Attendance Percentage:</strong> {{ number_format($reportCard->attendance_percentage, 2) }}%
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="attendance_opened" class="form-label">Times School Opened</label>
                                <input type="number" class="form-control" id="attendance_opened" name="attendance_opened" value="{{ $reportCard->attendance_opened }}" required min="0">
                                <small class="form-text text-muted">Total number of school days in the term</small>
                            </div>
                            <div class="col-md-6">
                                <label for="attendance_present" class="form-label">Times Present</label>
                                <input type="number" class="form-control" id="attendance_present" name="attendance_present" value="{{ $reportCard->attendance_present }}" required min="0">
                                <small class="form-text text-muted">Number of days student was present</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="attendance_absent" class="form-label">Times Absent</label>
                                <input type="number" class="form-control" id="attendance_absent" name="attendance_absent" value="{{ $reportCard->attendance_absent }}" required min="0">
                                <small class="form-text text-muted">Number of days student was absent</small>
                            </div>
                            <div class="col-md-6">
                                <label for="attendance_late" class="form-label">Times Late</label>
                                <input type="number" class="form-control" id="attendance_late" name="attendance_late" value="{{ $reportCard->attendance_late }}" required min="0">
                                <small class="form-text text-muted">Number of times student came late</small>
                            </div>
                        </div>

                        <div class="alert alert-warning mb-4">
                            <strong>Note:</strong> Attendance percentage will be automatically calculated as (Times Present / Times School Opened) × 100
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Save Attendance Summary</button>
                            <a href="{{ route('admin.academic-management.report-cards.show', $reportCard->id) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
