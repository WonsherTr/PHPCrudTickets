<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'file_path',
        'original_name',
        'mime_type',
        'size',
    ];

    /* ── Relations ── */

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /* ── Accessors ── */

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
