@extends('layouts.admin')

@section('title', 'Report Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Report Card Settings</h5>
                    <p class="mb-0 text-muted">Configure Nigerian report card system settings</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.report-settings.update') }}" method="POST">
                        @csrf
                        
                        <!-- Score Configuration -->
                        <h6 class="mt-4 mb-3">Score Configuration</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="ca_max_score" class="form-label">CA Maximum Score</label>
                                <input type="number" class="form-control" id="ca_max_score" name="ca_max_score" value="{{ $settings->ca_max_score }}" required min="0" max="100">
                                <small class="form-text text-muted">Maximum score for Continuous Assessment (default: 30)</small>
                            </div>
                            <div class="col-md-6">
                                <label for="exam_max_score" class="form-label">Exam Maximum Score</label>
                                <input type="number" class="form-control" id="exam_max_score" name="exam_max_score" value="{{ $settings->exam_max_score }}" required min="0" max="100">
                                <small class="form-text text-muted">Maximum score for Examination (default: 70)</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="default_grading_profile_id" class="form-label">Default Grading Profile</label>
                                <select class="form-select" id="default_grading_profile_id" name="default_grading_profile_id">
                                    <option value="">Select Grading Profile</option>
                                    @foreach($gradingProfiles as $profile)
                                    <option value="{{ $profile->id }}" {{ $settings->default_grading_profile_id == $profile->id ? 'selected' : '' }}>{{ $profile->name }} ({{ $profile->level }})</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Default grading system for report cards</small>
                            </div>
                            <div class="col-md-6">
                                <label for="pdf_template_name" class="form-label">PDF Template</label>
                                <select class="form-select" id="pdf_template_name" name="pdf_template_name" required>
                                    <option value="nigerian_standard" {{ $settings->pdf_template_name == 'nigerian_standard' ? 'selected' : '' }}>Nigerian Standard</option>
                                    <option value="nigerian_detailed" {{ $settings->pdf_template_name == 'nigerian_detailed' ? 'selected' : '' }}>Nigerian Detailed</option>
                                    <option value="simple" {{ $settings->pdf_template_name == 'simple' ? 'selected' : '' }}>Simple Format</option>
                                </select>
                                <small class="form-text text-muted">PDF template style for report cards</small>
                            </div>
                        </div>

                        <!-- Display Settings -->
                        <h6 class="mt-4 mb-3">Display Settings</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="show_subject_position" name="show_subject_position" {{ $settings->show_subject_position ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_subject_position">Show Subject Position</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="show_class_average" name="show_class_average" {{ $settings->show_class_average ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_class_average">Show Class Average</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="show_highest_lowest" name="show_highest_lowest" {{ $settings->show_highest_lowest ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_highest_lowest">Show Highest/Lowest Scores</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="show_affective_domain" name="show_affective_domain" {{ $settings->show_affective_domain ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_affective_domain">Show Affective Domain</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="show_psychomotor_domain" name="show_psychomotor_domain" {{ $settings->show_psychomotor_domain ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_psychomotor_domain">Show Psychomotor Skills</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="show_attendance" name="show_attendance" {{ $settings->show_attendance ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_attendance">Show Attendance Summary</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="show_next_term_fee" name="show_next_term_fee" {{ $settings->show_next_term_fee ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_next_term_fee">Show Next Term Fee</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="show_outstanding_balance" name="show_outstanding_balance" {{ $settings->show_outstanding_balance ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_outstanding_balance">Show Outstanding Balance</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="show_parent_signature" name="show_parent_signature" {{ $settings->show_parent_signature ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_parent_signature">Show Parent Signature</label>
                                </div>
                            </div>
                        </div>

                        <!-- Workflow Settings -->
                        <h6 class="mt-4 mb-3">Workflow Settings</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="show_qr_verification" name="show_qr_verification" {{ $settings->show_qr_verification ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_qr_verification">Show QR Verification</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="require_principal_approval" name="require_principal_approval" {{ $settings->require_principal_approval ? 'checked' : '' }}>
                                    <label class="form-check-label" for="require_principal_approval">Require Principal Approval</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="allow_teacher_comment" name="allow_teacher_comment" {{ $settings->allow_teacher_comment ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_teacher_comment">Allow Teacher Comments</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="allow_parent_download" name="allow_parent_download" {{ $settings->allow_parent_download ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_parent_download">Allow Parent Download</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
