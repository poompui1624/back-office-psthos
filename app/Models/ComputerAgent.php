<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComputerAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'token_hash',
        'is_active',
        'last_seen_at',
        'last_ip_address',
        'last_user_agent',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }
}
