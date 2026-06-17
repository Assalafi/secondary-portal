<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingProfile extends Model
{
    protected $fillable = [
        'name',
        'description',
        'level',
        'is_default',
        'status',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function scales()
    {
        return $this->hasMany(GradingScale::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }
}
