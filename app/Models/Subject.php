<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
    ];

    /**
     * Get the class arms that teach this subject.
     */
    public function classArms(): BelongsToMany
    {
        return $this->belongsToMany(ClassArm::class, 'class_subject')
                    ->withPivot('teacher_id')
                    ->withTimestamps();
    }

    /**
     * All teachers assigned to this subject across class arms (via pivot table).
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_subject', 'subject_id', 'teacher_id')
                    ->withTimestamps();
    }

    /**
     * Get the assessments for this subject.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}
