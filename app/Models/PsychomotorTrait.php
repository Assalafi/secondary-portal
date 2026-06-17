<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PsychomotorTrait extends Model
{
    protected $fillable = [
        'name',
        'description',
        'sort_order',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
