<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    protected $fillable = [
        'level',
        'name',
        'numeric_level',
        'group',
        'status',
    ];

    /**
     * Get the class arms for the school class.
     */
    public function classArms(): HasMany
    {
        return $this->hasMany(ClassArm::class);
    }
}
