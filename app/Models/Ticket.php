<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'created_by',
        'assigned_to',
    ];

    /* ── Enums ── */

    public const STATUSES = ['OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED'];
    public const PRIORITIES = ['LOW', 'MEDIUM', 'HIGH', 'URGENT'];

    /* ── Relations ── */

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class)->latest();
    }

    /* ── Scopes ── */

    public function scopeSearch($query, ?string $term)
    {
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('title', 'ilike', "%{$term}%")
                  ->orWhere('description', 'ilike', "%{$term}%");
            });
        }
    }

    public function scopeFilterStatus($query, ?string $status)
    {
        if ($status && in_array($status, self::STATUSES)) {
            $query->where('status', $status);
        }
    }

    public function scopeFilterPriority($query, ?string $priority)
    {
        if ($priority && in_array($priority, self::PRIORITIES)) {
            $query->where('priority', $priority);
        }
    }

    /* ── Accessors ── */

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'OPEN'        => 'blue',
            'IN_PROGRESS' => 'amber',
            'RESOLVED'    => 'emerald',
            'CLOSED'      => 'gray',
            default       => 'gray',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'LOW'    => 'slate',
            'MEDIUM' => 'sky',
            'HIGH'   => 'orange',
            'URGENT' => 'red',
            default  => 'gray',
        };
    }
}
