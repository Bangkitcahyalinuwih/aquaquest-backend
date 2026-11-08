<?php

namespace Database\Seeders;

use App\Models\Biota;
use App\Models\SoalKuis;
use Illuminate\Database\Seeder;

class SoalKuisSeeder extends Seeder
{
    public function run(): void
    {
        $idBiota = Biota::pluck('id', 'slug');

        // [slug biota | null, pertanyaan, jawaban benar, [3 pengecoh]]
        $teks = [
            ['penyu-hijau', 'Apa nama ilmiah penyu hijau?', 'Chelonia mydas', ['Dermochelys coriacea', 'Eretmochelys imbricata', 'Caretta caretta']],
            ['penyu-hijau', 'Saat dewasa, penyu hijau terutama memakan apa?', 'Lamun dan alga', ['Ubur-ubur', 'Ikan karang', 'Kepiting']],
            ['hiu-paus', 'Bagaimana hiu paus mendapatkan makanan?', 'Menyaring plankton dan ikan kecil dari air', ['Memburu mamalia laut', 'Menerkam ikan besar', 'Memakan bangkai di dasar laut']],
            ['hiu-paus', 'Di perairan mana di Indonesia hiu paus sering terlihat?', 'Teluk Cenderawasih', ['Selat Malaka', 'Laut Jawa', 'Selat Sunda']],
            ['pari-manta-karang', 'Pari manta termasuk kelompok hewan apa?', 'Ikan bertulang rawan', ['Mamalia laut', 'Reptil laut', 'Moluska']],
            ['pari-manta-karang', 'Apa makanan utama pari manta?', 'Plankton', ['Ikan besar', 'Karang keras', 'Penyu muda']],
            ['dugong', 'Dugong menggantungkan hidupnya pada ekosistem apa?', 'Padang lamun', ['Hutan bakau', 'Laut dalam', 'Gua bawah laut']],
            ['dugong', 'Dugong termasuk kelompok hewan apa?', 'Mamalia laut', ['Ikan bertulang rawan', 'Reptil laut', 'Krustasea']],
            ['paus-biru', 'Apa keistimewaan paus biru?', 'Hewan terbesar yang pernah diketahui hidup di Bumi', ['Hewan tercepat di laut', 'Satu-satunya paus bergigi', 'Hewan laut paling berbisa']],
            ['paus-biru', 'Paus biru terutama memakan apa?', 'Krill', ['Cumi raksasa', 'Ikan tuna', 'Penyu']],
            ['kima-raksasa', 'Kima raksasa termasuk kelompok hewan apa?', 'Moluska (kerang)', ['Ikan', 'Mamalia laut', 'Karang']],
            ['kima-raksasa', 'Apa yang hidup di dalam mantel kima raksasa dan membantu menyediakan makanan?', 'Alga simbion (zooxanthellae)', ['Cacing parasit', 'Bakteri pemakan karang', 'Anemon kecil']],
            ['ikan-badut', 'Ikan badut hidup berdampingan dengan hewan apa?', 'Anemon laut', ['Hiu paus', 'Kima raksasa', 'Penyu hijau']],
            ['ikan-badut', 'Hubungan saling menguntungkan antara ikan badut dan anemon laut disebut?', 'Simbiosis mutualisme', ['Parasitisme', 'Kompetisi', 'Predasi']],
            ['ikan-napoleon', 'Apa ciri khas ikan napoleon?', 'Tonjolan di dahi dan bibir yang tebal', ['Duri beracun di punggung', 'Tubuh pipih seperti cakram', 'Sirip ekor bercabang dua panjang']],
            ['ikan-napoleon', 'Mengapa populasi ikan napoleon menurun?', 'Penangkapan berlebihan untuk perdagangan ikan karang hidup', ['Kekurangan oksigen di laut dalam', 'Ia hanya hidup di sungai', 'Terlalu banyak predator darat']],
            ['penyu-belimbing', 'Apa yang membedakan penyu belimbing dari penyu lain?', 'Cangkangnya berkulit dengan tonjolan memanjang, bukan sisik keras', ['Cangkangnya berduri tajam', 'Hanya hidup di air tawar', 'Tidak memiliki sirip']],
            ['penyu-belimbing', 'Makanan utama penyu belimbing adalah?', 'Ubur-ubur', ['Lamun', 'Terumbu karang', 'Kerang batu']],
            ['lumba-lumba-spinner', 'Mengapa disebut lumba-lumba spinner?', 'Karena sering melompat dan berputar di udara', ['Karena berenang berputar di dasar laut', 'Karena ekornya berbentuk spiral', 'Karena suaranya berdengung']],
            ['lumba-lumba-spinner', 'Lumba-lumba menemukan mangsa dengan bantuan apa?', 'Ekolokasi', ['Penciuman tajam', 'Sengatan listrik', 'Cahaya dari tubuhnya']],
            [null, 'SDG 14 "Life Below Water" berkaitan dengan apa?', 'Pelestarian ekosistem lautan', ['Pengelolaan hutan hujan', 'Energi bersih', 'Pendidikan berkualitas']],
            [null, 'Apa salah satu ancaman utama bagi terumbu karang?', 'Pemutihan karang akibat naiknya suhu laut', ['Terlalu banyak ikan kecil', 'Cahaya matahari yang terlalu sedikit', 'Ombak yang terlalu tenang']],
            [null, 'Mengapa sampah plastik berbahaya bagi penyu?', 'Penyu dapat mengira plastik sebagai ubur-ubur dan memakannya', ['Plastik membuat air laut menjadi manis', 'Plastik membuat penyu terlalu cepat berenang', 'Plastik membuat pasir terlalu panas']],
            [null, 'Dalam daftar IUCN, kategori "Endangered" (EN) berarti apa?', 'Risiko kepunahan sangat tinggi di alam liar', ['Spesies sudah punah', 'Spesies aman dan melimpah', 'Spesies belum pernah diteliti']],
        ];

        foreach ($teks as [$slug, $pertanyaan, $benar, $salah]) {
            $this->buatSoal($slug ? ($idBiota[$slug] ?? null) : null, 'teks', $pertanyaan, null, $benar, $salah);
        }

        // Soal gambar jika tidak ada skippp
        $semuaNama = Biota::pluck('nama');

        foreach (Biota::whereNotNull('gambar')->take(5)->get() as $biota) {
            $pengecoh = $semuaNama->reject(fn ($n) => $n === $biota->nama)->shuffle()->take(3)->values()->all();

            $this->buatSoal($biota->id, 'gambar', 'Hewan laut apakah yang terlihat pada gambar ini?', $biota->gambar, $biota->nama, $pengecoh);
        }
    }

    private function buatSoal(?int $biotaId, string $tipe, string $pertanyaan, ?string $gambar, string $benar, array $salah): void
    {
        // Posisi jawaban benar diacak supaya tidak selalu pada huruf yang sama.
        $opsi = collect(array_merge([$benar], $salah))->shuffle()->values();
        $huruf = ['a', 'b', 'c', 'd'];

        SoalKuis::updateOrCreate(
            ['pertanyaan' => $pertanyaan, 'gambar' => $gambar],
            [
                'biota_id' => $biotaId,
                'tipe' => $tipe,
                'opsi_a' => $opsi[0],
                'opsi_b' => $opsi[1],
                'opsi_c' => $opsi[2],
                'opsi_d' => $opsi[3],
                'jawaban_benar' => $huruf[$opsi->search($benar)],
                'poin' => 10,
            ]
        );
    }
}
