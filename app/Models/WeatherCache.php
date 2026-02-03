<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherCache extends Model
{
    protected $table = 'weather_cache';

    protected $fillable = [
        'icao',
        'payload',
        'fetched_at',
        'expires_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'fetched_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
}
