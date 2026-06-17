<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportSettings extends Model
{
    protected $fillable = [
        'default_grading_profile_id',
        'ca_max_score',
        'exam_max_score',
        'show_subject_position',
        'show_class_average',
        'show_highest_lowest',
        'show_affective_domain',
        'show_psychomotor_domain',
        'show_attendance',
        'show_next_term_fee',
        'show_outstanding_balance',
        'show_parent_signature',
        'show_qr_verification',
        'require_principal_approval',
        'allow_teacher_comment',
        'allow_parent_download',
        'pdf_template_name',
    ];

    protected $casts = [
        'ca_max_score' => 'integer',
        'exam_max_score' => 'integer',
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
    ];

    public function defaultGradingProfile()
    {
        return $this->belongsTo(GradingProfile::class, 'default_grading_profile_id');
    }

    public static function getSettings()
    {
        return self::firstOrCreate([], [
            'ca_max_score' => 30,
            'exam_max_score' => 70,
            'show_subject_position' => true,
            'show_class_average' => true,
            'show_highest_lowest' => true,
            'show_affective_domain' => true,
            'show_psychomotor_domain' => true,
            'show_attendance' => true,
            'show_next_term_fee' => true,
            'show_outstanding_balance' => true,
            'show_parent_signature' => true,
            'show_qr_verification' => true,
            'require_principal_approval' => true,
            'allow_teacher_comment' => true,
            'allow_parent_download' => true,
            'pdf_template_name' => 'nigerian_standard',
        ]);
    }
}
