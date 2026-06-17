<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingSystem extends Model
{
    protected $fillable = [
        'level',
        'grade',
        'remark',
        'min_score',
        'max_score',
        'gpa_point',
        'description',
        'is_active',
    ];

    protected $casts = [
        'min_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the grade and remark for a given score.
     */
    public static function getGradeForScore($score, $level)
    {
        return self::where('level', $level)
            ->where('is_active', true)
            ->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();
    }

    /**
     * Scope to get active grading systems for a specific level.
     */
    public function scopeForLevel($query, $level)
    {
        return $query->where('level', $level)->where('is_active', true);
    }

    /**
     * Scope to get active records only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
