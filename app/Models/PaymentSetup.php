<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetup extends Model
{
    protected $fillable = [
        'payment_type',
        'level',
        'term',
        'amount',
        'effective_date',
        'last_updated',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'last_updated' => 'date',
            'amount' => 'decimal:2',
        ];
    }
}
