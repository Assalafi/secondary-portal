<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportCardItem extends Model
{
    protected $fillable = [
        'report_card_id',
        'subject_id',
        'subject_name',
        'ca_score',
        'exam_score',
        'total_score',
        'grade',
        'grade_point',
        'remark',
        'subject_position',
        'class_average',
        'highest_score',
        'lowest_score',
        'teacher_id',
        'teacher_initial',
    ];

    protected $casts = [
        'ca_score' => 'decimal:2',
        'exam_score' => 'decimal:2',
        'total_score' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'class_average' => 'decimal:2',
        'highest_score' => 'decimal:2',
        'lowest_score' => 'decimal:2',
    ];

    public function reportCard()
    {
        return $this->belongsTo(ReportCard::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }
}
