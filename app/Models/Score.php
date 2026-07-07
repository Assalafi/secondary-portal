<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Score extends Model
{
    use Auditable;

    protected $auditModule = 'scores';

    protected $fillable = [
        'score_batch_id',
        'student_id',
        'first_ca',
        'second_ca',
        'third_ca',
        'exam',
        'total',
        'grade',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'first_ca' => 'decimal:2',
            'second_ca' => 'decimal:2',
            'third_ca' => 'decimal:2',
            'exam' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function scoreBatch(): BelongsTo
    {
        return $this->belongsTo(ScoreBatch::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
