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
            $table->string('simbrief_id')->nullable()->after('email');
            $table->string('discord_id')->nullable()->after('simbrief_id');
            $table->string('discord_username')->nullable()->after('discord_id');
            $table->string('discord_avatar')->nullable()->after('discord_username');
            $table->boolean('discord_enabled')->default(false)->after('discord_avatar');
            $table->boolean('discord_dm_enabled')->default(false)->after('discord_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'simbrief_id',
                'discord_id',
                'discord_username',
                'discord_avatar',
                'discord_enabled',
                'discord_dm_enabled',
            ]);
        });
    }
};
