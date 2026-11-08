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
        Schema::create('game_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('mulai_at');
            $table->dateTime('deadline_at');
            $table->dateTime('selesai_at')->nullable();
            $table->enum('status', ['berjalan', 'selesai', 'kedaluwarsa'])->default('berjalan');
            $table->json('soal_ids');
            $table->json('jawaban')->nullable();
            $table->unsignedInteger('jumlah_benar')->default(0);
            $table->unsignedInteger('xp_didapat')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_history');
    }
};
