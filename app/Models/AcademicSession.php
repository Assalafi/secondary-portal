<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicSession extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    /**
     * Get the students for the academic session.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get the fee setups for the academic session.
     */
    public function feeSetups(): HasMany
    {
        return $this->hasMany(FeeSetup::class);
    }

    /**
     * Get the invoices for the academic session.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the assessments for the academic session.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}
