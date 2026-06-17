<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ClassArm extends Model
{
    protected $fillable = [
        'school_class_id',
        'name',
        'class_teacher_id',
        'capacity',
    ];

    /**
     * Get the school class that owns the class arm.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Get the class teacher for the class arm.
     */
    public function classTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'class_teacher_id');
    }

    /**
     * Get the students in the class arm.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'current_class_arm_id');
    }

    /**
     * Get the subjects for the class arm.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_subject')
                    ->withPivot('teacher_id')
                    ->withTimestamps();
    }

    /**
     * Get the assessments for the class arm.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * Get the attendances for the class arm.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
