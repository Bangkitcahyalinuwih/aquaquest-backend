<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KoleksiKartu extends Model
{
    protected $table = 'koleksi_kartu';

    protected $fillable = ['user_id', 'kartu_id', 'didapat_at'];

    protected $casts = [
        'didapat_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kartu(): BelongsTo
    {
        return $this->belongsTo(Kartu::class);
    }
}
