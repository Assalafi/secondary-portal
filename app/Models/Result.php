<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Result extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'academic_session_id',
        'term_id',
        'total_score',
        'maximum_score',
        'average_score',
        'final_grade',
        'position',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'total_score' => 'decimal:2',
            'maximum_score' => 'decimal:2',
            'average_score' => 'decimal:2',
            'published_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function resultItems(): HasMany
    {
        return $this->hasMany(ResultItem::class);
    }
}
