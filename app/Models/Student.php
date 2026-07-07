<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use Auditable;

    protected $auditModule = 'students';
    protected $auditIdentifier = 'admission_no';

    protected $fillable = [
        'user_id',
        'admission_no',
        'admission_date',
        'surname',
        'first_name',
        'middle_name',
        'gender',
        'dob',
        'place_of_birth_town',
        'place_of_birth_lga',
        'place_of_birth_state',
        'nationality',
        'state_of_origin',
        'lga',
        'health_status',
        'disability_details',
        'previous_school_details',
        'current_class_arm_id',
        'academic_session_id',
        'status',
        'photo_path',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'admission_date' => 'date',
        ];
    }

    /**
     * Get the user that owns the student.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the class arm that owns the student.
     */
    public function classArm(): BelongsTo
    {
        return $this->belongsTo(ClassArm::class, 'current_class_arm_id');
    }

    /**
     * Get the academic session that owns the student.
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the parents/guardians for the student.
     */
    public function parentsGuardians(): BelongsToMany
    {
        return $this->belongsToMany(ParentGuardian::class, 'student_parent')
                    ->withPivot('is_primary_contact')
                    ->withTimestamps();
    }

    /**
     * Get the invoices for the student.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the attendances for the student.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the assessment results for the student.
     */
    public function assessmentResults(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    /**
     * Get the scores for the student.
     */
    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->surname);
    }
}
