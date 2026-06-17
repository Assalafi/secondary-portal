<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultItem extends Model
{
    protected $fillable = [
        'result_id',
        'subject_id',
        'ca_score',
        'exam_score',
        'total_score',
        'grade',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'ca_score' => 'decimal:2',
            'exam_score' => 'decimal:2',
            'total_score' => 'decimal:2',
        ];
    }

    public function result(): BelongsTo
    {
        return $this->belongsTo(Result::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
