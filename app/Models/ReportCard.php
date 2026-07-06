<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
{
    protected $appends = [
        'session_name',
        'term_name',
    ];

    protected $fillable = [
        'student_id',
        'class_id',
        'academic_session_id',
        'term_id',
        'result_id',
        'report_type',
        'status',
        'total_score',
        'maximum_score',
        'average_score',
        'final_grade',
        'final_remark',
        'class_position',
        'number_in_class',
        'class_highest_average',
        'class_lowest_average',
        'class_average',
        'attendance_opened',
        'attendance_present',
        'attendance_absent',
        'attendance_late',
        'attendance_percentage',
        'promotion_decision',
        'next_class_id',
        'vacation_date',
        'next_term_begins',
        'next_term_fee',
        'outstanding_balance',
        'class_teacher_comment',
        'principal_comment',
        'parent_comment',
        'class_teacher_id',
        'approved_by',
        'published_by',
        'published_at',
        'pdf_url',
        'verification_code',
        'verification_url',
        'qr_code_url',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'maximum_score' => 'integer',
        'average_score' => 'decimal:2',
        'class_highest_average' => 'decimal:2',
        'class_lowest_average' => 'decimal:2',
        'class_average' => 'decimal:2',
        'attendance_percentage' => 'decimal:2',
        'next_term_fee' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'vacation_date' => 'date',
        'next_term_begins' => 'date',
        'published_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function academicSession()
    {
        return $this->belongsTo(SessionTerm::class, 'academic_session_id');
    }

    public function term()
    {
        return $this->belongsTo(SessionTerm::class, 'term_id');
    }

    public function result()
    {
        return $this->belongsTo(Result::class);
    }

    public function nextClass()
    {
        return $this->belongsTo(SchoolClass::class, 'next_class_id');
    }

    public function classTeacher()
    {
        return $this->belongsTo(Staff::class, 'class_teacher_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function publishedBy()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function items()
    {
        return $this->hasMany(ReportCardItem::class);
    }

    public function affectiveRatings()
    {
        return $this->hasMany(StudentAffectiveRating::class);
    }

    public function psychomotorRatings()
    {
        return $this->hasMany(StudentPsychomotorRating::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeBySession($query, $sessionId)
    {
        return $query->where('academic_session_id', $sessionId);
    }

    public function scopeByTerm($query, $termId)
    {
        return $query->where('term_id', $termId);
    }

    public function getSessionNameAttribute(): string
    {
        return $this->academicSession?->academic_year ?? 'N/A';
    }

    public function getTermNameAttribute(): string
    {
        return $this->report_type === 'annual'
            ? 'Annual'
            : ($this->term?->term_name ?? 'N/A');
    }
}
