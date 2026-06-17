<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'type',
        'priority',
        'sender_id',
        'recipient_ids',
        'recipient_roles',
        'recipient_classes',
        'is_global',
        'scheduled_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'recipient_ids' => 'array',
            'recipient_roles' => 'array',
            'recipient_classes' => 'array',
            'is_global' => 'boolean',
            'scheduled_at' => 'datetime',
        ];
    }

    /**
     * Get the user who sent the notification.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the notification reads for the notification.
     */
    public function notificationReads(): HasMany
    {
        return $this->hasMany(NotificationRead::class);
    }
}
