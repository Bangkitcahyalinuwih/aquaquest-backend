<?php

namespace App\Services;

class LevelService
{
    /**
     * Level = floor(total_xp / 100) + 1.
     * Contoh: 0-99 XP = level 1, 100-199 XP = level 2, dst.
     */
    public function hitungLevel(int $totalXp): int
    {
        return intdiv($totalXp, 100) + 1;
    }
}
