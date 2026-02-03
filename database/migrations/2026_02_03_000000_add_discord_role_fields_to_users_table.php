<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('discord_roles')->nullable()->after('discord_dm_enabled');
            $table->string('discord_guild_id')->nullable()->after('discord_roles');
            $table->timestamp('discord_roles_synced_at')->nullable()->after('discord_guild_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'discord_roles',
                'discord_guild_id',
                'discord_roles_synced_at',
            ]);
        });
    }
};
