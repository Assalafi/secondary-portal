<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingScale extends Model
{
    protected $fillable = [
        'grading_profile_id',
        'grade',
        'min_score',
        'max_score',
        'remark',
        'grade_point',
        'sort_order',
    ];

    protected $casts = [
        'min_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'grade_point' => 'decimal:2',
    ];

    public function gradingProfile()
    {
        return $this->belongsTo(GradingProfile::class);
    }

    public function scopeByProfile($query, $profileId)
    {
        return $query->where('grading_profile_id', $profileId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
