<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeSetup extends Model
{
    protected $fillable = [
        'payment_type',
        'level',
        'amount',
        'academic_session_id',
        'term_id',
        'description',
        'is_compulsory',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_compulsory' => 'boolean',
        ];
    }

    /**
     * Get the academic session that owns the fee setup.
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the term that owns the fee setup.
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Get the invoice items for the fee setup.
     */
    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
