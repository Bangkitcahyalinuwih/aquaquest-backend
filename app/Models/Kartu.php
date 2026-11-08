<?php

namespace App\Models;

use App\Models\Concerns\HasGambarUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kartu extends Model
{
    use HasFactory, HasGambarUrl;

    protected $table = 'kartu';

    protected $fillable = ['biota_id', 'nama', 'gambar', 'xp_syarat'];

    public function biota(): BelongsTo
    {
        return $this->belongsTo(Biota::class);
    }

    public function koleksiKartu(): HasMany
    {
        return $this->hasMany(KoleksiKartu::class);
    }
}
