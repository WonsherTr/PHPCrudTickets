<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* ── Helpers ── */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /* ── Relations ── */

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }
}
