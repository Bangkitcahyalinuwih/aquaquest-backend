<?php

namespace App\Services;

use App\Models\Kartu;
use App\Models\KoleksiKartu;
use App\Models\User;

class KartuService
{
    public function bukaKartuBaru(User $user): array
    {
        $kartuBelumDimiliki = Kartu::where('xp_syarat', '<=', $user->total_xp)
            ->whereDoesntHave('koleksiKartu', fn ($q) => $q->where('user_id', $user->id))
            ->get();

        foreach ($kartuBelumDimiliki as $kartu) {
            KoleksiKartu::create([
                'user_id' => $user->id,
                'kartu_id' => $kartu->id,
                'didapat_at' => now(),
            ]);
        }

        return $kartuBelumDimiliki->all();
    }
}
