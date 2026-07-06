<?php

namespace App\Http\Controllers;

use App\Models\GradingProfile;
use App\Models\ReportSettings;
use Illuminate\Http\Request;

class ReportSettingsController extends Controller
{
    public function index()
    {
        $settings = ReportSettings::getSettings();
        $gradingProfiles = GradingProfile::active()->get();

        return view('admin.report-settings.index', compact('settings', 'gradingProfiles'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'default_grading_profile_id' => 'nullable|exists:grading_profiles,id',
            'ca_max_score' => 'required|integer|min:0|max:100',
            'exam_max_score' => [
                'required',
                'integer',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($request) {
                    if ((int) $request->ca_max_score + (int) $value !== 100) {
                        $fail('The CA and examination maximum scores must add up to 100.');
                    }
                },
            ],
            'show_subject_position' => 'boolean',
            'show_class_average' => 'boolean',
            'show_highest_lowest' => 'boolean',
            'show_affective_domain' => 'boolean',
            'show_psychomotor_domain' => 'boolean',
            'show_attendance' => 'boolean',
            'show_next_term_fee' => 'boolean',
            'show_outstanding_balance' => 'boolean',
            'show_parent_signature' => 'boolean',
            'show_qr_verification' => 'boolean',
            'require_principal_approval' => 'boolean',
            'allow_teacher_comment' => 'boolean',
            'allow_parent_download' => 'boolean',
            'pdf_template_name' => 'required|string',
        ]);

        $settings = ReportSettings::getSettings();
        $settings->update([
            'default_grading_profile_id' => $request->default_grading_profile_id,
            'ca_max_score' => $request->ca_max_score,
            'exam_max_score' => $request->exam_max_score,
            'show_subject_position' => $request->has('show_subject_position'),
            'show_class_average' => $request->has('show_class_average'),
            'show_highest_lowest' => $request->has('show_highest_lowest'),
            'show_affective_domain' => $request->has('show_affective_domain'),
            'show_psychomotor_domain' => $request->has('show_psychomotor_domain'),
            'show_attendance' => $request->has('show_attendance'),
            'show_next_term_fee' => $request->has('show_next_term_fee'),
            'show_outstanding_balance' => $request->has('show_outstanding_balance'),
            'show_parent_signature' => $request->has('show_parent_signature'),
            'show_qr_verification' => $request->has('show_qr_verification'),
            'require_principal_approval' => $request->has('require_principal_approval'),
            'allow_teacher_comment' => $request->has('allow_teacher_comment'),
            'allow_parent_download' => $request->has('allow_parent_download'),
            'pdf_template_name' => $request->pdf_template_name,
        ]);

        return redirect()->back()
            ->with('success', 'Report settings updated successfully.');
    }
}
