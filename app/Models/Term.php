<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Term extends Model
{
    protected $fillable = [
        'name',
        'number',
    ];

    /**
     * Get the fee setups for the term.
     */
    public function feeSetups(): HasMany
    {
        return $this->hasMany(FeeSetup::class);
    }

    /**
     * Get the invoices for the term.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the assessments for the term.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}
