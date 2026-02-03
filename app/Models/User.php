<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'simbrief_id',
        'simbrief_payload',
        'simbrief_fetched_at',
        'discord_id',
        'discord_username',
        'discord_avatar',
        'discord_enabled',
        'discord_dm_enabled',
        'discord_roles',
        'discord_guild_id',
        'discord_roles_synced_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'simbrief_payload' => 'array',
            'simbrief_fetched_at' => 'datetime',
            'discord_enabled' => 'boolean',
            'discord_dm_enabled' => 'boolean',
            'discord_roles' => 'array',
            'discord_roles_synced_at' => 'datetime',
        ];
    }

    public function hasDiscordRole(string $roleId): bool
    {
        $roles = $this->discord_roles ?? [];

        return in_array($roleId, $roles, true);
    }

    public function isPilot(): bool
    {
        $pilotRole = config('services.discord.roles.pilot');
        $staffRoles = config('services.discord.roles.staff', []);

        if (! $pilotRole && empty($staffRoles)) {
            return false;
        }

        if ($pilotRole && $this->hasDiscordRole($pilotRole)) {
            return true;
        }

        foreach ($staffRoles as $roleId) {
            if ($this->hasDiscordRole($roleId)) {
                return true;
            }
        }

        return false;
    }

    public function isStaff(): bool
    {
        $staffRoles = config('services.discord.roles.staff', []);

        foreach ($staffRoles as $roleId) {
            if ($this->hasDiscordRole($roleId)) {
                return true;
            }
        }

        return false;
    }
}
