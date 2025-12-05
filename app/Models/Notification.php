<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'type',
        'recipient_type',
        'recipient_id',
        'created_by',
        'is_read',
        'read_at',
        'metadata',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the admin who created this notification
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Management::class, 'created_by');
    }

    /**
     * Get the recipient (user or shop)
     */
    public function recipient(): MorphTo
    {
        return $this->morphTo('recipient', 'recipient_type', 'recipient_id');
    }

    /**
     * Get the recipient user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Get the recipient shop
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'recipient_id');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for notifications by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for notifications by recipient type
     */
    public function scopeForRecipientType($query, $recipientType)
    {
        return $query->where('recipient_type', $recipientType);
    }

    /**
     * Get notification type badge class
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->type) {
            'info' => 'bg-blue-100 text-blue-800',
            'success' => 'bg-green-100 text-green-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'error' => 'bg-red-100 text-red-800',
            'announcement' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get notification icon
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'info' => 'info-circle',
            'success' => 'check-circle',
            'warning' => 'exclamation-triangle',
            'error' => 'x-circle',
            'announcement' => 'megaphone',
            default => 'bell',
        };
    }
}