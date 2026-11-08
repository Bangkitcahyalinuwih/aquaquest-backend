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
        Schema::create('kartu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biota_id')->unique()->constrained('biota')->cascadeOnDelete();
            $table->string('nama');
            $table->string('gambar');
            $table->unsignedInteger('xp_syarat');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kartu');
    }
};
