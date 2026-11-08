<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameHistory extends Model
{
    protected $table = 'game_history';

    protected $fillable = [
        'user_id',
        'mulai_at',
        'deadline_at',
        'selesai_at',
        'status',
        'soal_ids',
        'jawaban',
        'jumlah_benar',
        'xp_didapat',
    ];

    // "casts" memberi tahu Laravel cara menerjemahkan kolom ini saat dibaca/ditulis.
    // Tanpa ini, $game->soal_ids akan jadi teks JSON mentah, bukan array PHP biasa.
    protected $casts = [
        'mulai_at' => 'datetime',
        'deadline_at' => 'datetime',
        'selesai_at' => 'datetime',
        'soal_ids' => 'array',
        'jawaban' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
