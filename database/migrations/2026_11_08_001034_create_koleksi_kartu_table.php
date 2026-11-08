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
        Schema::create('koleksi_kartu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kartu_id')->constrained('kartu')->cascadeOnDelete();
            $table->dateTime('didapat_at');
            $table->timestamps();
            $table->unique(['user_id', 'kartu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koleksi_kartu');
    }
};
