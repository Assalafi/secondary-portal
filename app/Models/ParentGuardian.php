<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ParentGuardian extends Model
{
    protected $table = 'parents_guardians';
    protected $fillable = [
        'user_id',
        'full_name',
        'relationship_to_student',
        'occupation',
        'present_address',
        'permanent_address',
        'phone_residence',
        'phone_office',
        'email',
    ];

    /**
     * Get the user that owns the parent/guardian.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the students for the parent/guardian.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_parent')
                    ->withPivot('is_primary_contact')
                    ->withTimestamps();
    }

    /**
     * Accessor: primary phone (residence preferred, fallback office)
     */
    public function getPhoneAttribute(): ?string
    {
        return $this->phone_residence ?: $this->phone_office;
    }

    /**
     * Accessor: simple alias for relationship_to_student
     */
    public function getRelationshipAttribute(): ?string
    {
        return $this->relationship_to_student;
    }
}
