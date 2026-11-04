# Dokumen Serah Terima

**Proyek:** Portal MTsN 1 Wonosobo
**Tanggal serah terima:** ____________________
**Diserahkan oleh:** ____________________
**Diterima oleh:** ____________________

Dokumen ini mencatat perpindahan kepemilikan dan tanggung jawab atas aplikasi
portal sekolah beserta seluruh layanan pendukungnya.

---

## 1. Ringkasan sistem

| Bagian | Keterangan |
|---|---|
| Aplikasi | Laravel 10 (PHP 8.3) |
| Hosting | Vercel — https://webmtsn1wonosobo.vercel.app |
| Database | TiDB Cloud (kompatibel MySQL), region Singapore |
| Penyimpanan gambar | Vercel Blob (public) |
| Source code | GitHub — https://github.com/<akun-client>/webmtsn1wonosobo |
| Domain | ____________________ (isi setelah domain aktif) |

Fitur utama: pengumuman, jadwal pelajaran, ekstrakurikuler, konten kesiswaan,
profil sekolah, konseling (siswa → guru BK), serta dashboard per peran
(siswa, guru, guru BK, admin).

## 2. Akses dan akun

| Layanan | Pemilik akun | Catatan |
|---|---|---|
| Vercel (hosting) | Sekolah | Project sudah dipindahkan ke team sekolah |
| GitHub (source code) | Sekolah | Repo sudah dipindahkan |
| TiDB Cloud (database) | Sekolah | Kredensial tersimpan di password manager |
| Domain & DNS | Sekolah | Atas nama sekolah |
| Akun admin portal | Sekolah | Password awal diberikan terpisah, **wajib diganti** |

> Semua password diserahkan melalui password manager, **bukan** melalui dokumen
> ini, chat, atau email.

## 3. Panduan singkat admin

1. Buka URL portal → **Masuk** → gunakan akun admin.
2. Menu **Dashboard Guru/Admin**:
   - **Pengumuman** — tambah/edit/hapus pengumuman.
   - **Jadwal** — atur jadwal per kelas/hari.
   - **Ekstrakurikuler** — tambah kegiatan dan unggah banner.
   - **Kesiswaan** — tambah konten kesiswaan beserta gambar.
   - **Profil Sekolah** — ubah identitas dan sambutan sekolah.
   - **Konseling** — guru BK melihat pesan siswa dan menandai
     **sudah ditangani**.
3. Gambar yang diunggah otomatis tersimpan di penyimpanan cloud dan tetap ada
   meskipun aplikasi diperbarui.

## 4. Operasional

### Memperbarui aplikasi
Setiap perubahan kode yang di-push ke branch `main` akan otomatis di-deploy
oleh Vercel (sekitar 20 detik). Tidak perlu upload manual.

### Mengembalikan versi sebelumnya (rollback)
Vercel → **Deployments** → pilih versi yang masih bagus →
**Promote to Production**. Proses ini tidak menghapus data.

### Backup database
Dump database dilakukan minimal **1× per minggu** dan disimpan di Google Drive
sekolah. Perintah lengkap ada di lampiran checklist internal.

### Pemulihan (restore)
Restore hanya dilakukan bila terjadi kerusakan data, menggunakan file dump
terakhir. Disarankan uji restore di database percobaan sebelum menyentuh
production.

## 5. Biaya dan tanggung jawab

| Layanan | Perkiraan biaya | Ditanggung oleh |
|---|---|---|
| Vercel (hosting) | Gratis (Hobby) / ±USD 20 per bulan (Pro) | Sekolah |
| TiDB Cloud | Kuota gratis, berbayar bila melewati kuota | Sekolah |
| Vercel Blob | Kuota gratis, berbayar bila melewati kuota | Sekolah |
| Domain | ±Rp150.000–250.000 per tahun | Sekolah |

Catatan:
- Paket **Hobby Vercel** diperuntukkan bagi penggunaan non-komersial. Untuk
  portal resmi sekolah, disarankan paket **Pro**.
- Pemakaian gambar yang banyak akan menambah kuota penyimpanan/transfer.
- Harga dapat berubah; selalu cek halaman resmi Vercel dan TiDB.

## 6. Garansi dan dukungan

- **Masa garansi:** 30 hari sejak tanggal serah terima.
- **Cakupan garansi:** perbaikan bug pada fitur yang sudah ada, tanpa biaya.
- **Di luar garansi** (dapat dikerjakan dengan kesepakatan/biaya terpisah):
  - penambahan fitur baru atau perubahan alur kerja,
  - pengisian/penggantian konten dan foto,
  - masalah akibat perubahan yang dilakukan pihak sekolah,
  - gangguan dari layanan pihak ketiga (Vercel, TiDB, domain, internet),
  - kehilangan data karena backup tidak dijalankan.
- **Jalur dukungan:** ____________________ (WhatsApp/email)
- **Jam dukungan:** ____________________

## 7. Serah terima aset

| Aset | Status | Catatan |
|---|---|---|
| Source code (GitHub) | ☐ diserahkan | |
| Project Vercel | ☐ diserahkan | |
| Database TiDB | ☐ diserahkan | |
| Blob store (gambar) | ☐ diserahkan | |
| Domain & DNS | ☐ diserahkan | |
| Akun admin portal | ☐ diserahkan | password terpisah |
| Backup database terakhir | ☐ diserahkan | tanggal: __________ |
| Dokumen ini | ☐ diserahkan | |

Dengan menandatangani dokumen ini, kedua pihak menyetujui isi dan batas
tanggung jawab di atas.

| Diserahkan oleh | Diterima oleh |
|---|---|
| Nama: | Nama: |
| Jabatan: | Jabatan: |
| Tanda tangan: | Tanda tangan: |
| Tanggal: | Tanggal: |

---

## Lampiran — Daftar environment variable production

Nilai rahasia tidak dicantumkan di sini; tersimpan di Vercel dan password
manager sekolah.

| Variabel | Fungsi |
|---|---|
| `APP_ENV` | Mode aplikasi (`production`) |
| `APP_KEY` | Kunci enkripsi aplikasi — **jangan diubah** |
| `APP_DEBUG` | Harus `false` di production |
| `APP_URL` | Alamat domain portal |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | Alamat server database |
| `DB_PORT` | `4000` |
| `DB_DATABASE` | `portal_kesiswaan` |
| `DB_USERNAME` | User database |
| `DB_PASSWORD` | Password database |
| `MYSQL_ATTR_SSL_CA` | Sertifikat TLS (opsional) |
| `BLOB_READ_WRITE_TOKEN` | Token penyimpanan gambar |
| `BLOB_ACCESS` | `public` |
| `SESSION_SECURE_COOKIE` | `true` |
| `LOG_CHANNEL` | `stderr` |
