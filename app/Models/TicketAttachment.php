<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAttachment extends Model
{
    protected $fillable = [
        'ticket_message_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    /**
     * Get the ticket message that owns the attachment.
     */
    public function ticketMessage(): BelongsTo
    {
        return $this->belongsTo(TicketMessage::class);
    }

    /**
     * Get the file size in human-readable format.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the file icon based on file type.
     */
    public function getFileIconAttribute(): string
    {
        $type = strtolower($this->file_type);

        if (str_contains($type, 'image')) {
            return 'ri-image-line';
        } elseif (str_contains($type, 'pdf')) {
            return 'ri-file-pdf-line';
        } elseif (str_contains($type, 'word') || str_contains($type, 'document')) {
            return 'ri-file-word-line';
        } elseif (str_contains($type, 'excel') || str_contains($type, 'spreadsheet')) {
            return 'ri-file-excel-line';
        } elseif (str_contains($type, 'zip') || str_contains($type, 'rar')) {
            return 'ri-file-zip-line';
        }

        return 'ri-file-line';
    }
}
