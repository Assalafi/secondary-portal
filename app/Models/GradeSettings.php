<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'grade',
        'min_score',
        'max_score',
        'gpa_point',
        'description',
        'status'
    ];

    protected $casts = [
        'min_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'gpa_point' => 'decimal:2'
    ];
}
