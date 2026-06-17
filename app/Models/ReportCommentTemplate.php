<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportCommentTemplate extends Model
{
    protected $fillable = [
        'category',
        'min_average',
        'max_average',
        'template_text',
        'status',
    ];

    protected $casts = [
        'min_average' => 'decimal:2',
        'max_average' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeForAverage($query, $average)
    {
        return $query->where('min_average', '<=', $average)
                     ->where('max_average', '>=', $average);
    }
}
