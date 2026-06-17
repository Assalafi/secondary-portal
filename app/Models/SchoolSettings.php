<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'school_logo',
        'school_address',
        'phone_number',
        'email',
        'website',
        'established_year',
        'academic_session',
        'current_term',
        'favicon',
        'meta_image',
        'meta_description',
        'meta_keywords',
        'meta_author',
        'security_settings',
        'notification_settings',
        'finance_settings',
        'system_settings'
    ];

    protected $casts = [
        'security_settings' => 'array',
        'notification_settings' => 'array',
        'finance_settings' => 'array',
        'system_settings' => 'array'
    ];
}
