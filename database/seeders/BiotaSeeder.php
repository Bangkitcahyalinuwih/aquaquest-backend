<?php

namespace Database\Seeders;

use App\Models\Biota;
use Database\Seeders\Concerns\SalinGambar;
use Illuminate\Database\Seeder;

class BiotaSeeder extends Seeder
{
    use SalinGambar;

    /**
     * Status konservasi mengacu pada IUCN Red List (global) dan dapat berubah saat penilaian diperbarui.
     * Koordinat bersifat perkiraan lokasi habitat/pengamatan yang dikenal, bukan titik survei presisi.
     * Verifikasi ulang di iucnredlist.org sebelum demo.
     */
    public function run(): void
    {
        $daftar = [
            [
                'slug' => 'penyu-hijau',
                'nama' => 'Penyu Hijau',
                'nama_latin' => 'Chelonia mydas',
                'kategori' => 'Reptil Laut',
                'status_konservasi' => 'LC',
                'habitat' => 'Perairan tropis dan subtropis, padang lamun, pantai peneluran',
                'deskripsi' => 'Penyu hijau adalah salah satu penyu laut terbesar dengan cangkang keras berwarna cokelat hingga hijau zaitun. Saat dewasa, penyu hijau memakan lamun dan alga sehingga ikut menjaga kesehatan padang lamun. Betina kembali ke pantai untuk bertelur. Pada tahun 2025 IUCN menurunkan status globalnya dari Endangered ke Least Concern, tetapi beberapa populasi lokal masih terancam.',
                'latitude' => 2.2867,
                'longitude' => 118.2425,
            ],
            [
                'slug' => 'hiu-paus',
                'nama' => 'Hiu Paus',
                'nama_latin' => 'Rhincodon typus',
                'kategori' => 'Ikan',
                'status_konservasi' => 'EN',
                'habitat' => 'Laut terbuka tropis dan hangat',
                'deskripsi' => 'Hiu paus adalah ikan terbesar di dunia. Meski berukuran raksasa, ia jinak dan makan dengan menyaring plankton serta ikan kecil dari air. Di Indonesia, hiu paus sering terlihat di Teluk Cenderawasih, Papua.',
                'latitude' => -3.2500,
                'longitude' => 134.9000,
            ],
            [
                'slug' => 'pari-manta-karang',
                'nama' => 'Pari Manta Karang',
                'nama_latin' => 'Mobula alfredi',
                'kategori' => 'Ikan',
                'status_konservasi' => 'VU',
                'habitat' => 'Terumbu karang dan perairan dangkal tropis',
                'deskripsi' => 'Pari manta karang adalah ikan bertulang rawan dengan sirip dada lebar menyerupai sayap. Ia menyaring plankton dan sering mengunjungi stasiun pembersihan di terumbu karang. Raja Ampat dikenal sebagai salah satu lokasi pengamatannya.',
                'latitude' => -0.5700,
                'longitude' => 130.5300,
            ],
            [
                'slug' => 'dugong',
                'nama' => 'Dugong',
                'nama_latin' => 'Dugong dugon',
                'kategori' => 'Mamalia Laut',
                'status_konservasi' => 'VU',
                'habitat' => 'Padang lamun di perairan dangkal pesisir',
                'deskripsi' => 'Dugong adalah mamalia laut herbivora yang hidupnya bergantung pada padang lamun. Hewan ini sering dikaitkan dengan legenda putri duyung. Hilangnya padang lamun dan terjerat jaring nelayan menjadi ancaman utamanya.',
                'latitude' => 1.1000,
                'longitude' => 104.5000,
            ],
            [
                'slug' => 'paus-biru',
                'nama' => 'Paus Biru',
                'nama_latin' => 'Balaenoptera musculus',
                'kategori' => 'Mamalia Laut',
                'status_konservasi' => 'EN',
                'habitat' => 'Laut terbuka, jalur migrasi samudra',
                'deskripsi' => 'Paus biru adalah hewan terbesar yang pernah diketahui hidup di Bumi. Ia menyaring krill dalam jumlah sangat besar sebagai makanannya. Perairan Laut Sawu di Nusa Tenggara Timur dikenal sebagai koridor migrasi paus.',
                'latitude' => -9.8000,
                'longitude' => 122.0000,
            ],
            [
                'slug' => 'kima-raksasa',
                'nama' => 'Kima Raksasa',
                'nama_latin' => 'Tridacna gigas',
                'kategori' => 'Moluska',
                'status_konservasi' => 'VU',
                'habitat' => 'Terumbu karang dangkal',
                'deskripsi' => 'Kima raksasa adalah kerang terbesar di dunia dan dapat hidup puluhan tahun. Sebagian makanannya berasal dari alga simbion (zooxanthellae) di dalam mantelnya, sisanya dari menyaring plankton. Pengambilan berlebihan membuat populasinya menurun.',
                'latitude' => -5.3000,
                'longitude' => 123.6000,
            ],
            [
                'slug' => 'ikan-badut',
                'nama' => 'Ikan Badut',
                'nama_latin' => 'Amphiprion ocellaris',
                'kategori' => 'Ikan',
                'status_konservasi' => 'LC',
                'habitat' => 'Terumbu karang, berlindung di anemon laut',
                'deskripsi' => 'Ikan badut hidup berdampingan dengan anemon laut dalam hubungan saling menguntungkan: anemon melindungi ikan, dan ikan menjaga serta membersihkan anemon. Warna oranye dengan garis putih membuatnya mudah dikenali.',
                'latitude' => 1.6200,
                'longitude' => 124.7600,
            ],
            [
                'slug' => 'ikan-napoleon',
                'nama' => 'Ikan Napoleon',
                'nama_latin' => 'Cheilinus undulatus',
                'kategori' => 'Ikan',
                'status_konservasi' => 'EN',
                'habitat' => 'Terumbu karang dan laguna',
                'deskripsi' => 'Ikan napoleon dikenali dari tonjolan di dahi dan bibirnya yang tebal. Ia dapat tumbuh sangat besar dan berumur panjang, tetapi lambat berkembang biak. Penangkapan untuk perdagangan ikan karang hidup menyebabkan populasinya menurun.',
                'latitude' => -6.5500,
                'longitude' => 121.0500,
            ],
            [
                'slug' => 'penyu-belimbing',
                'nama' => 'Penyu Belimbing',
                'nama_latin' => 'Dermochelys coriacea',
                'kategori' => 'Reptil Laut',
                'status_konservasi' => 'VU',
                'habitat' => 'Laut terbuka, bertelur di pantai tropis',
                'deskripsi' => 'Penyu belimbing adalah penyu terbesar dengan cangkang berkulit yang memiliki tonjolan memanjang, bukan sisik keras. Ia menyelam sangat dalam dan makanan utamanya ubur-ubur. Pantai Jamursba Medi di Papua Barat dikenal sebagai lokasi peneluran penting.',
                'latitude' => -0.4300,
                'longitude' => 132.3500,
            ],
            [
                'slug' => 'lumba-lumba-spinner',
                'nama' => 'Lumba-lumba Spinner',
                'nama_latin' => 'Stenella longirostris',
                'kategori' => 'Mamalia Laut',
                'status_konservasi' => 'LC',
                'habitat' => 'Perairan tropis, teluk, dan laut terbuka',
                'deskripsi' => 'Lumba-lumba spinner terkenal karena kebiasaannya melompat dan berputar di udara. Hewan cerdas ini hidup berkelompok dan memakai ekolokasi untuk menemukan mangsa. Teluk Kiluan di Lampung dikenal sebagai tempat pengamatannya.',
                'latitude' => -5.7700,
                'longitude' => 105.1700,
            ],
        ];

        foreach ($daftar as $data) {
            $data['gambar'] = $this->salinGambar('biota', $data['slug']);

            Biota::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
