# Portal MTsN 1 Wonosobo

Aplikasi portal sekolah untuk MTsN 1 Wonosobo. Berisi informasi sekolah,
pengumuman, jadwal pelajaran, ekstrakurikuler, konten kesiswaan, layanan
konseling, dan asisten AI bernama STELA.

## Fitur

- Pengumuman sekolah.
- Jadwal pelajaran per kelas dan hari.
- Ekstrakurikuler beserta banner.
- Konten kesiswaan dengan gambar.
- Profil sekolah dan sambutan kepala sekolah.
- Konseling dari siswa ke guru BK, lengkap dengan status penanganan.
- Dashboard terpisah untuk siswa, guru, guru BK, dan admin.
- STELA, asisten AI yang menjawab pertanyaan seputar sekolah.

## Teknologi

- Laravel 10 dan PHP 8.3.
- Vercel sebagai hosting.
- TiDB Cloud sebagai database (kompatibel MySQL).
- Vercel Blob untuk menyimpan gambar unggahan.
- Google Gemini untuk STELA.

## Menjalankan di lokal

1. Clone repositori.
2. Jalankan `composer install`.
3. Salin `.env.example` menjadi `.env`, lalu jalankan `php artisan key:generate`.
4. Isi variabel database di `.env`.
5. Jalankan `php artisan migrate --seed`.
6. Jalankan `php artisan serve`.

Catatan:

- Tanpa `BLOB_READ_WRITE_TOKEN`, gambar disimpan di penyimpanan lokal.
- Tanpa `GEMINI_API_KEY`, STELA hanya membalas bahwa layanan belum
  dikonfigurasi.

## Deploy

Setiap push ke branch `main` akan memicu build otomatis di Vercel. Variabel
environment diatur melalui dashboard Vercel, bukan dari file `.env` di
repositori.

## Dokumentasi serah terima

Panduan serah terima ada di folder `docs/serah-terima`:

- `CHECKLIST.md` untuk langkah teknis sebelum diserahkan.
- `DOKUMEN-SERAH-TERIMA.md` untuk dokumen resmi yang ditandatangani.
- `SISA-PEKERJAAN.md` untuk daftar pekerjaan yang masih tersisa.
