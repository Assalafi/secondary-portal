<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    protected $fillable = [
        'user_id',
        'staff_id',
        'designation',
        'department',
        'date_of_employment',
        'date_of_retirement',
        'salary',
        'qualifications',
        'employment_type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_of_employment' => 'date',
            'date_of_retirement' => 'date',
            'salary' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns the staff.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payroll records for the staff.
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(PayrollRecord::class);
    }

    /**
     * Get the class arms where this staff is a class teacher.
     */
    public function assignedClasses(): HasMany
    {
        return $this->hasMany(ClassArm::class, 'class_teacher_id', 'user_id');
    }

    /**
     * Get the subjects taught by this staff.
     */
    public function assignedSubjects(): HasMany
    {
        return $this->hasMany(ClassSubject::class, 'teacher_id', 'user_id');
    }
}
