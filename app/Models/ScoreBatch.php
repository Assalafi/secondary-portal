<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScoreBatch extends Model
{
    protected $fillable = [
        'class_id',
        'subject_id',
        'academic_session_id',
        'term_id',
        'status',
        'uploaded_by',
        'uploaded_at',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }
}
