<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'payment_setup_id',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Get the invoice that owns the invoice item.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the payment setup that owns the invoice item.
     */
    public function paymentSetup(): BelongsTo
    {
        return $this->belongsTo(PaymentSetup::class);
    }
    
    /**
     * Legacy method for backward compatibility
     */
    public function feeSetup(): BelongsTo
    {
        return $this->paymentSetup();
    }
}
