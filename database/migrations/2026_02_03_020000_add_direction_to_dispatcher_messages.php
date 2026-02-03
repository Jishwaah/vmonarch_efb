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
        Schema::table('dispatcher_messages', function (Blueprint $table) {
            $table->string('direction')->default('outbound')->after('status');
            $table->string('sender_label')->nullable()->after('direction');
            $table->timestamp('received_at')->nullable()->after('sent_at');
            $table->string('pilot_callsign')->nullable()->after('received_at');
            $table->string('discord_username')->nullable()->after('pilot_callsign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatcher_messages', function (Blueprint $table) {
            $table->dropColumn([
                'direction',
                'sender_label',
                'received_at',
                'pilot_callsign',
                'discord_username',
            ]);
        });
    }
};
