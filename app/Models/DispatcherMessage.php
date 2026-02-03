<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispatcherMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'pilot_id',
        'discord_user_id',
        'message',
        'sent_by',
        'status',
        'booking_url',
        'error_message',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }
}
