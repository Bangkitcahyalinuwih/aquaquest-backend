<?php

namespace App\Models;

use App\Models\Concerns\HasGambarUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoalKuis extends Model
{
    use HasFactory, HasGambarUrl;

    protected $table = 'soal_kuis';

    protected $fillable = [
        'biota_id',
        'tipe',
        'pertanyaan',
        'gambar',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'jawaban_benar',
        'poin',
    ];

    public function biota(): BelongsTo
    {
        return $this->belongsTo(Biota::class);
    }
}
