<?php

namespace Database\Seeders;

use App\Models\KontenEdukasi;
use Database\Seeders\Concerns\SalinGambar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KontenEdukasiSeeder extends Seeder
{
    use SalinGambar;

    public function run(): void
    {
        $artikel = [
            [
                'judul' => 'Mengenal SDG 14: Ekosistem Lautan',
                'isi' => "SDG 14 atau Life Below Water adalah salah satu Tujuan Pembangunan Berkelanjutan PBB yang berfokus pada pelestarian dan pemanfaatan samudra, laut, dan sumber daya kelautan secara berkelanjutan.\n\nIndonesia sebagai negara kepulauan memiliki kepentingan besar pada tujuan ini. Laut menjadi sumber pangan, mata pencaharian, dan penyangga iklim bagi jutaan orang.\n\nMelalui AquaQuest, kamu belajar mengenali biota laut dan alasan mereka perlu dilindungi.",
            ],
            [
                'judul' => 'Mengapa Terumbu Karang Penting?',
                'isi' => "Terumbu karang hanya menutupi sebagian kecil dasar laut, tetapi menjadi rumah bagi sangat banyak jenis biota laut. Karena itu terumbu karang sering disebut hutan hujan lautan.\n\nTerumbu karang juga melindungi garis pantai dari hantaman ombak dan mendukung perikanan serta pariwisata.\n\nSuhu laut yang naik dapat menyebabkan pemutihan karang, ketika karang kehilangan alga simbionnya dan dapat mati bila kondisi buruk berlangsung lama.",
            ],
            [
                'judul' => 'Bahaya Sampah Plastik bagi Biota Laut',
                'isi' => "Sampah plastik yang berakhir di laut dapat dimakan atau melilit biota laut. Penyu, misalnya, sering mengira kantong plastik sebagai ubur-ubur yang menjadi makanannya.\n\nPlastik yang terurai menjadi serpihan kecil (mikroplastik) dapat masuk ke rantai makanan.\n\nLangkah sederhana seperti mengurangi plastik sekali pakai, membawa tas dan botol minum sendiri, serta membuang sampah pada tempatnya membantu mengurangi masalah ini.",
            ],
            [
                'judul' => 'Padang Lamun: Penjaga Pesisir yang Sering Terlupakan',
                'isi' => "Lamun adalah tumbuhan berbunga yang hidup di dasar perairan dangkal. Hamparannya disebut padang lamun.\n\nPadang lamun menjadi tempat mencari makan dan berlindung bagi banyak hewan, termasuk dugong dan penyu hijau. Akar lamun juga membantu menahan sedimen sehingga air lebih jernih.\n\nPembangunan pesisir dan pencemaran dapat merusak padang lamun, sehingga menjaga perairan dangkal sama pentingnya dengan menjaga terumbu karang.",
            ],
            [
                'judul' => 'Memahami Status Konservasi IUCN',
                'isi' => "IUCN Red List adalah daftar yang menilai risiko kepunahan suatu jenis makhluk hidup. Kategori yang sering ditemui antara lain Least Concern (LC, risiko rendah), Vulnerable (VU, rentan), Endangered (EN, genting), dan Critically Endangered (CR, kritis).\n\nStatus sebuah spesies dapat berubah ketika penilaian diperbarui. Misalnya, penyu hijau secara global dinilai ulang pada tahun 2025 dan masuk kategori Least Concern berkat upaya konservasi, meskipun beberapa populasi lokal tetap menghadapi ancaman.\n\nPerubahan status yang membaik bukan berarti ancaman hilang; perlindungan harus terus berjalan.",
            ],
            [
                'judul' => 'Cara Sederhana Menjaga Laut dari Rumah',
                'isi' => "Menjaga laut tidak harus dimulai dari pantai. Kebiasaan di rumah juga berpengaruh karena banyak sampah di laut berasal dari daratan.\n\nKurangi plastik sekali pakai, pilih makanan laut yang ditangkap secara bertanggung jawab, jangan membeli suvenir dari karang atau cangkang biota yang dilindungi, dan ikut kegiatan bersih pantai di daerahmu.\n\nBagikan pengetahuan yang kamu dapat di AquaQuest kepada teman dan keluarga.",
            ],
        ];

        foreach ($artikel as $a) {
            $slug = Str::slug($a['judul']);

            KontenEdukasi::updateOrCreate(
                ['slug' => $slug],
                [
                    'judul' => $a['judul'],
                    'isi' => $a['isi'],
                    'biota_id' => null,
                    'gambar' => $this->salinGambar('edukasi', $slug),
                ]
            );
        }
    }
}
