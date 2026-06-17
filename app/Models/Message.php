<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    protected $fillable = [
        'subject',
        'body',
        'sender_id',
        'recipient_ids',
        'priority',
        'is_draft',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'recipient_ids' => 'array',
            'is_draft' => 'boolean',
            'sent_at' => 'datetime',
        ];
    }

    /**
     * Get the user who sent the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the message reads for the message.
     */
    public function messageReads(): HasMany
    {
        return $this->hasMany(MessageRead::class);
    }
}
