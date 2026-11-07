# Dokumen Serah Terima

**Proyek:** Portal MTsN 1 Wonosobo
**Tanggal serah terima:** ____________________
**Diserahkan oleh:** ____________________
**Diterima oleh:** ____________________

Dokumen ini mencatat perpindahan kepemilikan dan tanggung jawab atas aplikasi
portal sekolah beserta layanan pendukungnya.

## 1. Ringkasan sistem

| Bagian | Keterangan |
|---|---|
| Aplikasi | Laravel 10 dengan PHP 8.3 |
| Hosting | Vercel, https://webmtsn1wonosobo.vercel.app |
| Database | TiDB Cloud, kompatibel MySQL, region Singapore |
| Penyimpanan gambar | Vercel Blob, akses publik |
| Source code | GitHub, https://github.com/<akun-client>/webmtsn1wonosobo |
| Domain | ____________________ |

Fitur utama: pengumuman, jadwal pelajaran, ekstrakurikuler, enam ruang
kesiswaan (Data Siswa, Kegiatan, Ekstrakurikuler, Layanan Beasiswa dan PIP,
Kemitraan, dan Giat Lain), profil sekolah, konseling dari siswa ke guru BK,
dashboard per peran, ubah password mandiri, reset password oleh admin, dan
asisten AI STELA.

## 2. Akses dan akun

| Layanan | Pemilik akun | Catatan |
|---|---|---|
| Vercel | Sekolah | Project sudah dipindahkan ke team sekolah |
| GitHub | Sekolah | Repositori sudah dipindahkan |
| TiDB Cloud | Sekolah | Kredensial tersimpan di password manager |
| Domain dan DNS | Sekolah | Terdaftar atas nama sekolah |
| Akun admin portal | Sekolah | Password diberikan terpisah dan wajib diganti |

Semua password diserahkan melalui password manager, bukan melalui dokumen ini,
chat, atau email.

## 3. Panduan singkat admin

1. Buka alamat portal, pilih Masuk, lalu gunakan akun admin.
2. Pada menu Dashboard Guru atau Admin tersedia:
   - Pengumuman, untuk menambah, mengubah, dan menghapus pengumuman.
   - Jadwal, untuk mengatur jadwal per kelas dan hari.
   - Ekstrakurikuler, untuk menambah kegiatan dan mengunggah banner.
   - Kesiswaan, untuk mengelola enam ruang layanan siswa beserta ikon, isi
     halaman detail, tautan, dan gambar.
   - Akun & Password, untuk melihat daftar akun dan mereset password pengguna.
   - Profil Sekolah, untuk mengubah identitas dan sambutan sekolah.
   - Konseling, untuk melihat pesan siswa dan menandainya sebagai sudah
     ditangani.
3. Gambar yang diunggah tersimpan di penyimpanan cloud dan tetap ada meskipun
   aplikasi diperbarui.
4. Setiap pengguna dapat mengganti password sendiri melalui tombol
   **Ubah Password** di dashboard. Password baru minimal 8 karakter. Jika lupa
   password, minta admin melakukan reset dari tab **Akun & Password**.

## 4. Operasional

### Memperbarui aplikasi

Setiap perubahan kode yang di-push ke branch `main` akan di-deploy otomatis
oleh Vercel dalam waktu sekitar 20 detik. Tidak perlu upload manual.

### Mengembalikan versi sebelumnya

Buka Vercel, masuk ke Deployments, pilih versi yang masih berjalan baik, lalu
pilih Promote to Production. Proses ini tidak menghapus data.

### Backup database

Dump database dilakukan minimal satu kali per minggu dan disimpan di Google
Drive sekolah. Perintah lengkapnya ada di lampiran checklist internal.

### Pemulihan data

Restore hanya dilakukan bila terjadi kerusakan data, menggunakan file dump
terakhir. Sebaiknya uji restore di database percobaan sebelum menyentuh
production.

## 5. Biaya dan tanggung jawab

| Layanan | Perkiraan biaya | Ditanggung oleh |
|---|---|---|
| Vercel | Gratis pada paket Hobby, sekitar USD 20 per bulan untuk paket Pro | Sekolah |
| TiDB Cloud | Kuota gratis, berbayar bila melewati kuota | Sekolah |
| Vercel Blob | Kuota gratis, berbayar bila melewati kuota | Sekolah |
| Domain | Sekitar Rp150.000 sampai Rp250.000 per tahun | Sekolah |

Catatan:

- Paket Hobby Vercel ditujukan untuk penggunaan non-komersial. Untuk portal
  resmi sekolah, paket Pro lebih disarankan.
- Pemakaian gambar yang banyak akan menambah kuota penyimpanan dan transfer.
- Harga dapat berubah. Selalu cek halaman resmi Vercel dan TiDB.

## 6. Garansi dan dukungan

- Masa garansi 30 hari sejak tanggal serah terima.
- Garansi mencakup perbaikan bug pada fitur yang sudah ada, tanpa biaya.
- Di luar garansi, pekerjaan dapat dilakukan dengan kesepakatan dan biaya
  terpisah. Cakupannya meliputi:
  - penambahan fitur baru atau perubahan alur kerja,
  - pengisian atau penggantian konten dan foto,
  - masalah akibat perubahan yang dilakukan pihak sekolah,
  - gangguan dari layanan pihak ketiga seperti Vercel, TiDB, domain, dan
    internet,
  - kehilangan data karena backup tidak dijalankan.
- Jalur dukungan: ____________________
- Jam dukungan: ____________________

## 7. Serah terima aset

| Aset | Status | Catatan |
|---|---|---|
| Source code di GitHub | | |
| Project Vercel | | |
| Database TiDB | | |
| Blob store untuk gambar | | |
| Domain dan DNS | | |
| Akun admin portal | | Password diberikan terpisah |
| Backup database terakhir | | Tanggal: __________ |
| Dokumen ini | | |

Dengan menandatangani dokumen ini, kedua pihak menyetujui isi dan batas
tanggung jawab di atas.

| Diserahkan oleh | Diterima oleh |
|---|---|
| Nama: | Nama: |
| Jabatan: | Jabatan: |
| Tanda tangan: | Tanda tangan: |
| Tanggal: | Tanggal: |

## Lampiran: Variabel environment production

Nilai rahasia tidak dicantumkan di sini. Semua nilai tersimpan di Vercel dan
password manager sekolah.

| Variabel | Fungsi |
|---|---|
| `APP_ENV` | Mode aplikasi, yaitu `production` |
| `APP_KEY` | Kunci enkripsi aplikasi, jangan diubah |
| `APP_DEBUG` | Harus `false` di production |
| `APP_URL` | Alamat domain portal |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | Alamat server database |
| `DB_PORT` | `4000` |
| `DB_DATABASE` | `portal_kesiswaan` |
| `DB_USERNAME` | User database |
| `DB_PASSWORD` | Password database |
| `MYSQL_ATTR_SSL_CA` | Sertifikat TLS, opsional |
| `BLOB_READ_WRITE_TOKEN` | Token penyimpanan gambar |
| `BLOB_ACCESS` | `public` |
| `GEMINI_API_KEY` | API key untuk STELA |
| `GEMINI_MODEL` | Model AI, default `gemini-3.6-flash` |
| `GEMINI_MAX_OUTPUT_TOKENS` | Batas panjang jawaban, default `2048` |
| `GEMINI_THINKING_BUDGET` | Default `0` |
| `SESSION_SECURE_COOKIE` | `true` |
| `LOG_CHANNEL` | `stderr` |
