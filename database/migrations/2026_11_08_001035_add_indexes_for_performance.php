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
        Schema::table('game_history', function (Blueprint $table) {
            // dipakai query anti-farming: user + status + tanggal selesai
            $table->index(['user_id', 'status', 'selesai_at'], 'gh_user_status_selesai_idx');
        });

        Schema::table('biota', function (Blueprint $table) {
            $table->index('kategori');
        });

        Schema::table('kartu', function (Blueprint $table) {
            $table->index('xp_syarat');
        });
    }

    public function down(): void
    {
        Schema::table('game_history', fn (Blueprint $table) => $table->dropIndex('gh_user_status_selesai_idx'));
        Schema::table('biota', fn (Blueprint $table) => $table->dropIndex(['kategori']));
        Schema::table('kartu', fn (Blueprint $table) => $table->dropIndex(['xp_syarat']));
    }
};
