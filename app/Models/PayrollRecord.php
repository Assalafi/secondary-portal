<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollRecord extends Model
{
    protected $fillable = [
        'staff_id',
        'payroll_month',
        'payroll_year',
        'base_pay',
        'allowances',
        'deductions',
        'gross_pay',
        'net_pay',
        'status',
        'generated_date',
        'paid_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'base_pay' => 'decimal:2',
            'allowances' => 'decimal:2',
            'deductions' => 'decimal:2',
            'gross_pay' => 'decimal:2',
            'net_pay' => 'decimal:2',
            'generated_date' => 'date',
            'paid_date' => 'date',
            'payroll_year' => 'integer',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
