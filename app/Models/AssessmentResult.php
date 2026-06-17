<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentResult extends Model
{
    protected $fillable = [
        'assessment_id',
        'student_id',
        'score',
        'grade',
        'remarks',
        'marked_by',
        'marked_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'marked_at' => 'datetime',
        ];
    }

    /**
     * Get the assessment that owns the result.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the student that owns the result.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user who marked the result.
     */
    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
