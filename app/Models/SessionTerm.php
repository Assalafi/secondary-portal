<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year',
        'term_name',
        'start_date',
        'end_date',
        'is_current',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean'
    ];
}
