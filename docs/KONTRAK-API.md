# Kontrak API AquaQuest (v1)

Dokumen ini adalah **sumber kebenaran** antara backend dan frontend. Backend membangun API persis seperti ini; frontend
membuat data tiruan (mock) persis seperti ini. Jika ada yang perlu diubah, ubah dokumen ini **lebih dulu** (lewat PR
yang direview dua tim), baru kode. Koleksi Postman (`postman/`) adalah bentuk yang dapat dijalankan dari dokumen ini.

## 1. Aturan umum

| Hal | Aturan |
|---|---|
| Alamat dasar | `/api/v1` (development: `http://localhost:8000/api/v1`; frontend memakai alamat relatif `/api/v1`) |
| Format | JSON. Header yang selalu dikirim: `Accept: application/json` |
| Autentikasi | Token Sanctum: `Authorization: Bearer <token>`. Masa berlaku 7 hari |
| Pembungkus | Semua respons sukses berbentuk `{ "data": ... }` |
| Paginasi | `data` (array) + `links` + `meta`. Parameter `?page=N`. Ukuran halaman 9 (daftar publik) dan 10 (admin) |
| Field | Bahasa Indonesia, snake_case (`nama_latin`, `xp_syarat`) |
| Tanggal | ISO 8601 (`2026-11-05T10:05:00+07:00`) |
| Gambar | Field `gambar_url` berisi URL lengkap atau `null` |
| Unggah file | `multipart/form-data`. Untuk **ubah** data yang memuat file, kirim `POST` dengan field `_method=PUT` |
| Penghapusan | `204 No Content` tanpa isi |

**Pagination (bentuk)**
```json
{
  "data": [ ... ],
  "links": { "first": "http://localhost:8000/api/v1/biota?page=1", "last": "...?page=2", "prev": null, "next": "...?page=2" },
  "meta": { "current_page": 1, "from": 1, "last_page": 2, "per_page": 9, "to": 9, "total": 10 }
}
```
Frontend hanya memakai `meta.current_page`, `meta.last_page`, `meta.total`, dan `meta.per_page`.

## 2. Kode status dan bentuk error

| Status | Arti | Bentuk isi | Tindakan frontend |
|---|---|---|---|
| 200 / 201 / 204 | Berhasil / dibuat / dihapus | lihat tiap endpoint | |
| 401 | Belum login atau token tidak valid | `{ "message": "Unauthenticated." }` | Hapus sesi, arahkan ke `/login` |
| 403 | Login tetapi tidak berhak | `{ "message": "..." }` | Halaman akses ditolak |
| 404 | Data tidak ada | `{ "message": "..." }` | Halaman 404 |
| 422 | Validasi gagal | di bawah | Tampilkan pesan di bawah kolom |
| 429 | Terlalu banyak permintaan | `{ "message": "..." }` | Toast |
| 500 | Kesalahan server | `{ "message": "Server Error" }` | Toast |

Teks `message` pada 401, 403, 404, dan 500 **bukan patokan** logika frontend (hanya status). Yang boleh dipakai logika
hanyalah `errors` pada 422.

```json
{
  "message": "Data tidak valid.",
  "errors": { "email": ["Email atau kata sandi salah."], "password": ["Kata sandi minimal 8 karakter."] }
}
```

## 3. Objek bersama

**User**
```json
{ "id": 2, "name": "Demo Pengguna", "email": "demo@aquaquest.test", "role": "user", "level": 1, "total_xp": 80 }
```
`role`: `"user"` atau `"admin"`.

**BiotaRingkas** (isi daftar)
```json
{
  "slug": "penyu-hijau", "nama": "Penyu Hijau", "nama_latin": "Chelonia mydas",
  "kategori": "Reptil Laut", "status_konservasi": "LC",
  "gambar_url": "http://localhost:8000/storage/biota/penyu-hijau.jpg",
  "deskripsi_singkat": "Penyu hijau adalah salah satu penyu laut terbesar dengan cangkang keras..."
}
```
**Biota** (detail) = semua field BiotaRingkas kecuali `deskripsi_singkat`, ditambah:
```json
{ "id": 1, "habitat": "Perairan tropis dan subtropis, padang lamun", "deskripsi": "Teks lengkap...", "latitude": 2.2867, "longitude": 118.2425 }
```
`latitude`/`longitude` berupa angka atau `null`. `status_konservasi`, `nama_latin`, `habitat` boleh `null`.

**EdukasiRingkas**
```json
{ "slug": "mengenal-sdg-14-ekosistem-lautan", "judul": "Mengenal SDG 14: Ekosistem Lautan",
  "ringkasan": "SDG 14 atau Life Below Water adalah ...", "gambar_url": null, "created_at": "2026-11-01T08:00:00+07:00" }
```
**Edukasi** (detail) = EdukasiRingkas tanpa `ringkasan`, ditambah `"isi": "Teks lengkap..."` dan
`"biota": { "slug": "penyu-hijau", "nama": "Penyu Hijau" }` atau `"biota": null`.

## 4. Auth

| Endpoint | Akses | Request | Respons sukses |
|---|---|---|---|
| `POST /auth/register` | publik | `name`, `email`, `password`, `password_confirmation` | **201** `{ "data": { "token": "1\|abc...", "user": User } }` |
| `POST /auth/login` | publik (maks. 10/menit) | `email`, `password` | **200** `{ "data": { "token": "...", "user": User } }` |
| `GET /auth/me` | login | | **200** `{ "data": User }` |
| `POST /auth/logout` | login | | **204** (token yang dipakai dihapus) |
| `PUT /profil` | login | `name` (wajib); opsional `current_password`, `password`, `password_confirmation` (ketiganya bersama) | **200** `{ "data": User }` |

Aturan validasi: `name` wajib maks. 255; `email` valid dan unik; `password` minimal 8 karakter dan cocok dengan
konfirmasi. Login gagal menghasilkan **422** dengan `errors.email` (bukan 401), supaya tampil sebagai galat form.

## 5. Endpoint publik

| Endpoint | Query | Respons sukses |
|---|---|---|
| `GET /beranda` | | `{ "data": { "statistik": { "jumlah_biota": 10, "jumlah_soal": 24, "jumlah_pemain": 9 }, "biota_pilihan": [BiotaRingkas x3], "edukasi_terbaru": [EdukasiRingkas x3] } }` |
| `GET /biota` | `q` (nama atau nama latin), `kategori`, `page` | Paginasi berisi BiotaRingkas, urut nama |
| `GET /biota/kategori` | | `{ "data": ["Ikan", "Mamalia Laut", "Moluska", "Reptil Laut"] }` |
| `GET /biota/{slug}` | | `{ "data": Biota }`, 404 jika tidak ada |
| `GET /edukasi` | `q` (judul atau isi), `page` | Paginasi berisi EdukasiRingkas, terbaru dahulu |
| `GET /edukasi/{slug}` | | `{ "data": Edukasi }`, 404 jika tidak ada |
| `GET /leaderboard` | | `{ "data": [ { "peringkat": 1, "id": 7, "name": "Budi Santoso", "level": 5, "total_xp": 420 } ] }` (20 teratas, hanya role user) |

Catatan: rute `/biota/kategori` harus didefinisikan **sebelum** `/biota/{slug}`.

## 6. Endpoint pengguna (wajib login)

**Kuis** (dibatasi 60 permintaan per menit)

`POST /kuis/mulai` → **200**
```json
{ "data": {
  "game_id": 12, "deadline_at": "2026-11-05T10:05:00+07:00",
  "soal": [ { "id": 3, "tipe": "teks", "pertanyaan": "Apa nama ilmiah penyu hijau?", "gambar_url": null,
              "opsi_a": "Chelonia mydas", "opsi_b": "...", "opsi_c": "...", "opsi_d": "..." } ]
} }
```
Isi `soal` **tidak boleh** memuat `jawaban_benar`. Jika belum ada soal: **422** `{ "message": "Belum ada soal kuis yang tersedia." }`.

`POST /kuis/{game_id}/jawab` dengan `{ "soal_id": 3, "pilihan": "a" }` → **200**
`{ "data": { "benar": true, "jawaban_benar": "a" } }`
Error: 403 (sesi orang lain atau soal bukan bagian sesi), 422 (sesi berakhir, waktu habis, soal sudah dijawab, `pilihan` bukan a/b/c/d).
Pesan waktu habis memuat kata "Waktu" (dipakai frontend untuk menampilkan layar waktu habis).

`POST /kuis/{game_id}/selesai` → **200**
```json
{ "data": { "jumlah_benar": 4, "xp_didapat": 40, "sudah_main_hari_ini": false, "level": 1, "kartu_baru": ["Kartu Penyu Hijau"] } }
```
Menyelesaikan sesi dua kali: **422**.

**Dashboard dan koleksi**

`GET /dashboard` → **200**
```json
{ "data": { "user": User, "kartu_berikutnya": { "nama": "Kartu Hiu Paus", "xp_syarat": 100 }, "jumlah_kartu_dimiliki": 1,
            "pernah_main": true, "sudah_main_hari_ini": false } }
```
`kartu_berikutnya` bernilai `null` jika semua kartu sudah terbuka.

`GET /koleksi` → **200**
```json
{ "data": { "total_xp": 80, "kartu": [ { "id": 1, "nama": "Kartu Penyu Hijau", "xp_syarat": 50,
            "gambar_url": null, "biota": { "slug": "penyu-hijau", "nama": "Penyu Hijau" }, "terbuka": true } ] } }
```
Kartu diurutkan menurut `xp_syarat`. Tampilan kartu terkunci (menyembunyikan nama) diatur frontend.

Aturan permainan (tidak berubah dari v1): 5 soal acak, 5 menit, XP = jumlah poin soal benar, hanya sesi selesai
pertama setiap hari yang menambah XP, `level = floor(total_xp / 100) + 1`, kartu terbuka otomatis saat XP mencapai syaratnya.

## 7. Endpoint admin (role `admin`, selain itu 403)

Semua memakai **`id` numerik**, bukan slug. Daftar dipaginasi 10 per halaman.

| Modul | Endpoint | Isi request (`multipart/form-data`) |
|---|---|---|
| Dashboard | `GET /admin/dashboard` | |
| Biota | `GET/POST /admin/biota`, `GET/POST(_method=PUT)/DELETE /admin/biota/{id}` | `nama`*, `nama_latin`, `kategori`*, `deskripsi`*, `habitat`, `status_konservasi`, `latitude`, `longitude`, `gambar` |
| Edukasi | `/admin/edukasi` (pola sama) | `judul`*, `isi`*, `biota_id` (boleh kosong), `gambar` |
| Soal | `/admin/soal` (pola sama) | `tipe`* (`teks`/`gambar`), `pertanyaan`*, `opsi_a`..`opsi_d`*, `jawaban_benar`* (`a`-`d`), `poin`* (1-100), `biota_id`, `gambar` (wajib bila tipe `gambar` dan belum ada) |
| Kartu | `/admin/kartu` (pola sama) | `biota_id`* (unik), `nama`*, `xp_syarat`*, `gambar` (wajib saat tambah) |

(* = wajib.) Respons tambah **201** dan ubah **200** berisi `{ "data": objek }` dengan field `id` dan seluruh field di atas
serta `gambar_url`. Hapus **204**. Validasi gagal **422**.

`GET /admin/dashboard` → **200**
```json
{ "data": { "jumlah_biota": 10, "jumlah_edukasi": 6, "jumlah_soal": 24, "jumlah_kartu": 10, "jumlah_pemain": 9, "sesi_selesai": 3,
            "pemain_teratas": [ { "id": 7, "name": "Budi Santoso", "level": 5, "total_xp": 420 } ],
            "sesi_terbaru": [ { "pemain": "Demo Pengguna", "jumlah_benar": 4, "selesai_at": "2026-11-05T10:03:00+07:00" } ] } }
```

## 8. Status implementasi (diperbarui tiap minggu)

| Kelompok | Minggu | Pemilik |
|---|---|---|
| Auth, profil | 1 (auth), 2 (profil) | Bangkit |
| Beranda, biota, edukasi | 1 | Dila |
| Kuis, dashboard, koleksi, leaderboard | 2 | Bangkit (kuis, koleksi), Dila (leaderboard, dashboard) |
| Admin | 3 | Dila |

## 9. Cara mengubah kontrak
1. Buka PR di repo backend yang mengubah dokumen ini (dan koleksi Postman bila perlu).
2. Tim frontend dan backend sama-sama me-review.
3. Setelah merge, backend dan frontend menyesuaikan kode dan mock masing-masing.
