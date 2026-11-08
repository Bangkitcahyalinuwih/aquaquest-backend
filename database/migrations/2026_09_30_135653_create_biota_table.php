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
        Schema::create('biota', function (Blueprint $table) {
            $table->id();                                          // kolom id, auto increment, primary key
            $table->string('nama');                                // nama umum biota, wajib diisi
            $table->string('nama_latin')->nullable();               // boleh kosong
            $table->string('slug')->unique();                       // untuk URL, misal /biota/penyu-hijau
            $table->string('kategori');                             // Ikan, Mamalia Laut, dst.
            $table->text('deskripsi');                              // teks panjang
            $table->string('habitat')->nullable();
            $table->string('status_konservasi')->nullable();        // LC, VU, EN, CR
            $table->string('gambar')->nullable();                   // nama file gambar
            $table->decimal('latitude', 10, 7)->nullable();         // untuk peta, diisi nanti
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();                                    // created_at & updated_at otomatis
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biota');   // kebalikan dari up(), untuk membatalkan migration ini
    }
    /**
     * Reverse the migrations.
     */
};
