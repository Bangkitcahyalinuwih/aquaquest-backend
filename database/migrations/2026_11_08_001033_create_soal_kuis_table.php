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
        Schema::create('soal_kuis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biota_id')->nullable()->constrained('biota')->nullOnDelete();
            $table->enum('tipe', ['teks', 'gambar'])->default('teks');
            $table->text('pertanyaan');
            $table->string('gambar')->nullable();
            $table->string('opsi_a');
            $table->string('opsi_b');
            $table->string('opsi_c');
            $table->string('opsi_d');
            $table->enum('jawaban_benar', ['a', 'b', 'c', 'd']);
            $table->unsignedInteger('poin')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal_kuis');
    }
};
