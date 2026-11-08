<?php

namespace App\Services;

use App\Models\GameHistory;
use App\Models\SoalKuis;
use App\Models\User;
use Illuminate\Support\Collection;

class KuisService
{
    private const JUMLAH_SOAL = 5;

    private const DURASI_DETIK = 300; // 5 menit

    public function __construct(
        private LevelService $levelService,
        private KartuService $kartuService,
    ) {}

    public function mulai(User $user): GameHistory
    {
        $soalIds = SoalKuis::inRandomOrder()->limit(self::JUMLAH_SOAL)->pluck('id')->toArray();

        if (empty($soalIds)) {
            abort(422, 'Belum ada soal kuis yang tersedia.');
        }

        return GameHistory::create([
            'user_id' => $user->id,
            'mulai_at' => now(),
            'deadline_at' => now()->addSeconds(self::DURASI_DETIK),
            'status' => 'berjalan',
            'soal_ids' => $soalIds,
            'jawaban' => [],
        ]);
    }

    public function soalUntukSesi(GameHistory $game): Collection
    {
        return SoalKuis::whereIn('id', $game->soal_ids)
            ->get(['id', 'tipe', 'pertanyaan', 'gambar', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d']);
    }

    public function jawab(GameHistory $game, int $soalId, string $pilihan): array
    {
        if ($game->status !== 'berjalan') {
            abort(422, 'Sesi kuis sudah berakhir.');
        }

        if (now()->greaterThan($game->deadline_at)) {
            $game->update(['status' => 'kedaluwarsa']);
            abort(422, 'Waktu kuis sudah habis.');
        }

        if (! in_array($soalId, $game->soal_ids)) {
            abort(403, 'Soal ini bukan bagian dari sesimu.');
        }

        $jawabanSekarang = $game->jawaban ?? [];

        if (array_key_exists($soalId, $jawabanSekarang)) {
            abort(422, 'Soal ini sudah pernah dijawab.');
        }

        $soal = SoalKuis::findOrFail($soalId);
        $benar = $soal->jawaban_benar === $pilihan;

        $jawabanSekarang[$soalId] = $pilihan;
        $game->update(['jawaban' => $jawabanSekarang]);

        return ['benar' => $benar, 'jawaban_benar' => $soal->jawaban_benar];
    }

    public function selesai(GameHistory $game): array
    {
        if ($game->status !== 'berjalan') {
            abort(422, 'Sesi kuis sudah berakhir sebelumnya.');
        }

        $soal = SoalKuis::whereIn('id', $game->soal_ids)->get()->keyBy('id');
        $jawaban = $game->jawaban ?? [];

        $jumlahBenar = 0;
        $poinDihasilkan = 0;

        foreach ($jawaban as $soalId => $pilihan) {
            $s = $soal->get((int) $soalId);
            if ($s && $s->jawaban_benar === $pilihan) {
                $jumlahBenar++;
                $poinDihasilkan += $s->poin;
            }
        }

        $sudahMainHariIni = GameHistory::where('user_id', $game->user_id)
            ->where('status', 'selesai')
            ->whereDate('selesai_at', today())
            ->where('id', '!=', $game->id)
            ->exists();

        $xpDikreditkan = $sudahMainHariIni ? 0 : $poinDihasilkan;

        $game->update([
            'status' => 'selesai',
            'selesai_at' => now(),
            'jumlah_benar' => $jumlahBenar,
            'xp_didapat' => $xpDikreditkan,
        ]);

        $kartuBaru = [];

        if ($xpDikreditkan > 0) {
            $user = $game->user;
            $user->increment('total_xp', $xpDikreditkan);
            $user->update(['level' => $this->levelService->hitungLevel($user->total_xp)]);
            $kartuBaru = $this->kartuService->bukaKartuBaru($user->fresh());
        }

        return [
            'jumlah_benar' => $jumlahBenar,
            'xp_didapat' => $xpDikreditkan,
            'sudah_main_hari_ini' => $sudahMainHariIni,
            'level' => $game->user->fresh()->level,
            'kartu_baru' => collect($kartuBaru)->map->nama->all(),
        ];
    }
}
