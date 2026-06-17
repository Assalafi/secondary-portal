<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPsychomotorRating extends Model
{
    protected $fillable = [
        'report_card_id',
        'student_id',
        'trait_id',
        'rating_value',
        'rated_by',
    ];

    public function reportCard()
    {
        return $this->belongsTo(ReportCard::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function trait()
    {
        return $this->belongsTo(PsychomotorTrait::class, 'trait_id');
    }

    public function ratedBy()
    {
        return $this->belongsTo(User::class, 'rated_by');
    }
}
