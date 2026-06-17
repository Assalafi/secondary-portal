<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    protected $fillable = [
        'title',
        'description',
        'subject_id',
        'class_arm_id',
        'academic_session_id',
        'term_id',
        'type',
        'total_marks',
        'assessment_date',
        'created_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_marks' => 'decimal:2',
            'assessment_date' => 'date',
        ];
    }

    /**
     * Get the subject that owns the assessment.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the class arm that owns the assessment.
     */
    public function classArm(): BelongsTo
    {
        return $this->belongsTo(ClassArm::class);
    }

    /**
     * Get the academic session that owns the assessment.
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the term that owns the assessment.
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Get the user who created the assessment.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the assessment results for the assessment.
     */
    public function assessmentResults(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }
}
