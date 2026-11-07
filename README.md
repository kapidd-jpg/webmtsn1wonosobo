# Portal MTsN 1 Wonosobo

Portal sekolah yang menyatukan informasi resmi, layanan kesiswaan, dan asisten
AI **STELA** dalam satu aplikasi web. Aplikasi dipakai oleh siswa, guru, guru
BK, dan administrator sekolah.

| Keterangan | Nilai |
|---|---|
| Status | Produksi |
| Alamat portal | <https://webmtsn1wonosobo.vercel.app> |
| Repositori | <https://github.com/kapidd-jpg/webmtsn1wonosobo> |
| Dokumentasi serah terima | [`docs/serah-terima`](docs/serah-terima/README.md) |
| Terakhir diperbarui | 7 November 2026 |

> Dokumen ini dapat diunggah ke Google Drive sekolah bersama folder
> `docs/serah-terima`. Jangan pernah menyertakan file `.env`, password, API key,
> atau isi `docs/serah-terima/AKUN.md` ke dalam dokumen publik.

## Daftar isi

- [Tentang aplikasi](#tentang-aplikasi)
- [Fitur](#fitur)
- [Peran pengguna](#peran-pengguna)
- [Teknologi](#teknologi)
- [Struktur proyek](#struktur-proyek)
- [Menjalankan di lokal](#menjalankan-di-lokal)
- [Konfigurasi environment](#konfigurasi-environment)
- [Deploy ke Vercel](#deploy-ke-vercel)
- [Operasional](#operasional)
- [Dokumentasi serah terima](#dokumentasi-serah-terima)
- [Keamanan dan catatan penting](#keamanan-dan-catatan-penting)
- [Kontak](#kontak)

## Tentang aplikasi

Portal MTsN 1 Wonosobo adalah aplikasi Laravel yang menyajikan profil sekolah,
pengumuman, jadwal pelajaran, jadwal ujian, ekstrakurikuler, dan konten
kesiswaan. Pengguna yang login mendapatkan dashboard sesuai perannya. Siswa
dapat mengirim permohonan konseling kepada guru BK, sedangkan guru BK dan admin
dapat memantau serta menandai permohonan sebagai sudah ditangani.

STELA adalah asisten AI berbasis Google Gemini. Ia menjawab pertanyaan seputar
sekolah menggunakan data portal (profil, jadwal, ekstrakurikuler, dan
pengumuman terbaru), serta mengarahkan siswa yang membutuhkan bantuan pribadi
ke layanan konseling atau guru BK.

## Fitur

### Portal publik

- Profil sekolah, visi, misi, dan sambutan kepala sekolah.
- Daftar pengumuman sekolah.
- Daftar ekstrakurikuler beserta banner dan jadwal kegiatan.
- Enam ruang kesiswaan dengan ikon: Data Siswa, Kegiatan, Ekstrakurikuler,
  Layanan Beasiswa dan PIP, Kemitraan, serta Giat Lain. Setiap ruang punya
  halaman detail sendiri.
- Form permohonan konseling untuk siswa.
- STELA, asisten AI yang dapat ditanyai tanpa login.

### Setelah login

- **Siswa**: dashboard pribadi berisi agenda hari ini, jadwal pelajaran sesuai
  kelas, jadwal ujian, serta akses ke STELA dan form konseling. Siswa dapat
  mengganti password sendiri dari dashboard.
- **Guru**: dashboard kesiswaan berisi statistik, daftar siswa, dan daftar wali
  kelas per kelas.
- **Guru BK**: dashboard konseling, daftar permohonan masuk, dan pembaruan
  status penanganan.
- **Admin**: pengelolaan seluruh konten portal, data siswa, profil sekolah,
  pemantauan konseling, serta daftar akun dan reset password.
- **Semua peran**: tombol **Ubah Password** tersedia di dashboard masing-masing.
  Password baru minimal 8 karakter. Setelah password diubah, perangkat lain
  yang masih login akan otomatis keluar.

### Pengelolaan konten

Admin dapat menambah, mengubah, dan menghapus:

- pengumuman;
- jadwal pelajaran per kelas dan hari;
- jadwal ujian (ulangan harian, UTS, UAS);
- ekstrakurikuler beserta banner;
- enam ruang kesiswaan beserta ikon, isi halaman detail, tautan, dan gambar;
- data siswa dan profil sekolah;
- akun pengguna, termasuk reset password lewat tab **Akun & Password**.

Gambar unggahan disimpan di Vercel Blob saat token Blob tersedia, atau di
penyimpanan lokal saat dijalankan tanpa token.

## Peran pengguna

| Kemampuan | Siswa | Guru | Guru BK | Admin |
|---|:---:|:---:|:---:|:---:|
| Melihat portal publik | ✓ | ✓ | ✓ | ✓ |
| Melihat jadwal dan ujian | ✓ | ✓ | ✓ | ✓ |
| Mengirim permohonan konseling (form publik) | ✓ | ✓ | ✓ | ✓ |
| Melihat dashboard kesiswaan | – | ✓ | ✓ | ✓ |
| Melihat daftar siswa | – | ✓ | – | ✓ |
| Menangani permohonan konseling | – | – | ✓ | ✓ |
| Mengelola konten portal | – | – | – | ✓ |
| Mengelola akun dan reset password | – | – | – | ✓ |
| Mengubah password sendiri | ✓ | ✓ | ✓ | ✓ |
| Mengubah profil sekolah | – | – | – | ✓ |

## Teknologi

| Bagian | Teknologi |
|---|---|
| Backend | Laravel 10, PHP 8.3 |
| Frontend | Blade, CSS, dan JavaScript vanilla |
| Database lokal | MySQL atau MariaDB |
| Database produksi | TiDB Cloud (kompatibel MySQL, region Singapore) |
| Hosting | Vercel dengan `vercel-php` |
| Penyimpanan gambar | Vercel Blob (akses publik) |
| Asisten AI | Google Gemini, model `gemini-3.6-flash` |
| Autentikasi | Session Laravel dengan middleware peran |

Catatan: aset CSS dan JavaScript dimuat langsung dari `public/`, sehingga
`npm install` tidak wajib untuk menjalankan aplikasi.

## Struktur proyek

```text
.
├── api/index.php                 # Entry point untuk Vercel
├── app/
│   ├── Http/Controllers/         # Controller portal, auth, dan API
│   ├── Http/Middleware/          # Middleware autentikasi dan peran
│   ├── Models/                   # Model Eloquent
│   └── Services/                 # Layanan penyimpanan gambar dan Vercel Blob
├── database/
│   ├── certs/cacert.pem          # Sertifikat TLS untuk TiDB Cloud
│   ├── migrations/               # Skema database
│   └── seeders/                  # Data contoh untuk pengembangan
├── docs/serah-terima/            # Checklist, dokumen resmi, dan sisa pekerjaan
├── public/
│   ├── css/portal.css            # Gaya antarmuka
│   └── js/script1.js             # Interaksi portal dan pemanggilan API
├── resources/views/home.blade.php # Halaman utama portal
├── routes/web.php                # Routing halaman dan API
└── vercel.json                   # Konfigurasi build dan routing Vercel
```

## Menjalankan di lokal

### Prasyarat

- PHP 8.1 atau lebih baru (disarankan 8.3).
- Composer.
- MySQL atau MariaDB.
- Ekstensi PHP yang dibutuhkan Laravel, termasuk `pdo_mysql` dan `openssl`.

### Langkah

1. Clone repositori, lalu masuk ke folder proyek.

   ```bash
   git clone https://github.com/kapidd-jpg/webmtsn1wonosobo.git
   cd webmtsn1wonosobo
   ```

2. Pasang dependensi PHP.

   ```bash
   composer install
   ```

3. Salin konfigurasi contoh dan buat application key.

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Buat database `portal_kesiswaan`, lalu sesuaikan `DB_HOST`, `DB_PORT`,
   `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di `.env`.

5. Jalankan migrasi dan data contoh.

   ```bash
   php artisan migrate --seed
   ```

6. Jalankan server pengembangan.

   ```bash
   php artisan serve
   ```

7. Buka <http://127.0.0.1:8000>.

### Catatan lokal

- Tanpa `BLOB_READ_WRITE_TOKEN`, gambar disimpan di penyimpanan lokal dan perlu
  `php artisan storage:link` agar dapat diakses dari browser.
- Tanpa `GEMINI_API_KEY`, STELA hanya membalas bahwa layanan belum
  dikonfigurasi.
- Akun contoh dibuat oleh `database/seeders/UserSeeder.php`. Ganti seluruh
  password akun contoh sebelum dipakai di production.

## Konfigurasi environment

Seluruh variabel dasar tersedia di `.env.example`. Nilai rahasia hanya diisi di
`.env` lokal atau di dashboard Vercel, tidak pernah di-commit.

| Variabel | Fungsi |
|---|---|
| `APP_ENV` | `local` saat pengembangan, `production` saat daring |
| `APP_KEY` | Kunci enkripsi aplikasi; jangan diubah saat transfer |
| `APP_DEBUG` | `true` di lokal, wajib `false` di production |
| `APP_URL` | Alamat portal |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST`, `DB_PORT` | Alamat dan port database |
| `DB_DATABASE` | Nama database, default `portal_kesiswaan` |
| `DB_USERNAME`, `DB_PASSWORD` | Kredensial database |
| `MYSQL_ATTR_SSL_CA` | Opsional; default memakai `database/certs/cacert.pem` untuk TiDB |
| `BLOB_READ_WRITE_TOKEN` | Token Vercel Blob untuk menyimpan gambar |
| `BLOB_ACCESS` | Akses blob, gunakan `public` |
| `GEMINI_API_KEY` | API key STELA dari Google AI Studio |
| `GEMINI_MODEL` | Opsional, default `gemini-3.6-flash` |
| `GEMINI_MAX_OUTPUT_TOKENS` | Opsional, default `2048` |
| `GEMINI_THINKING_BUDGET` | Opsional, default `0` agar jawaban tidak terpotong |
| `SESSION_SECURE_COOKIE` | `true` di production |

## Deploy ke Vercel

1. Hubungkan repositori GitHub ke project Vercel.
2. Atur seluruh variabel environment untuk Production dan Preview. Simpan
   `APP_KEY`, `DB_PASSWORD`, dan `GEMINI_API_KEY` sebagai Secret.
3. Hubungkan Vercel Blob store agar `BLOB_READ_WRITE_TOKEN` terisi otomatis.
4. Pastikan database TiDB dapat diakses dan `DB_PORT` diisi `4000`.
5. Deploy. Setiap push ke branch `main` akan memicu build otomatis.
6. Setelah domain aktif, ubah `APP_URL` ke domain final lalu redeploy.

Konfigurasi build berada di `vercel.json`, dengan `api/index.php` sebagai entry
point dan folder `public/` sebagai aset statis.

## Operasional

### Memperbarui aplikasi

```bash
git add .
git commit -m "Deskripsi perubahan"
git push origin main
```

Vercel akan membangun dan menerbitkan versi baru secara otomatis.

### Mengubah atau mereset password

- Pengguna yang masih bisa login dapat membuka dashboard, lalu menekan
  **Ubah Password**. Masukkan password lama, password baru minimal 8 karakter,
  dan konfirmasinya.
- Admin dapat mereset password pengguna lain dari dashboard, tab
  **Akun & Password**. Cari nama atau NIS/NIP, tekan **Reset Password**, lalu
  tentukan password baru.
- Setelah password diubah, sesi di perangkat lain otomatis berakhir. Pengguna
  perlu login ulang di perangkat tersebut.
- NIS/NIP tidak dapat diubah sendiri oleh pengguna. Perbaikan NIS/NIP dilakukan
  oleh admin melalui data siswa.

### Mengembalikan versi sebelumnya

Buka dashboard Vercel, masuk ke **Deployments**, pilih deployment yang masih
stabil, lalu pilih **Promote to Production**. Proses ini tidak menghapus data.

### Backup database

Lakukan dump minimal satu kali per minggu dan simpan di Google Drive sekolah.
Perintah lengkap untuk TiDB ada di
[`docs/serah-terima/CHECKLIST.md`](docs/serah-terima/CHECKLIST.md).

### Log dan pemantauan

- Log production tersedia di dashboard Vercel dan sebaiknya memakai
  `LOG_CHANNEL=stderr`.
- Pantau kuota TiDB, Vercel Blob, dan Gemini secara berkala.
- Aktifkan notifikasi email untuk deployment yang gagal.

## Dokumentasi serah terima

Folder [`docs/serah-terima`](docs/serah-terima/README.md) berisi:

- [`CHECKLIST.md`](docs/serah-terima/CHECKLIST.md): langkah teknis sebelum
  aplikasi diserahkan.
- [`DOKUMEN-SERAH-TERIMA.md`](docs/serah-terima/DOKUMEN-SERAH-TERIMA.md):
  dokumen resmi untuk ditandatangani kedua pihak.
- [`SISA-PEKERJAAN.md`](docs/serah-terima/SISA-PEKERJAAN.md): daftar pekerjaan
  yang masih tersisa beserta urutan pengerjaannya.
- `AKUN.md`: catatan kredensial lokal yang **tidak boleh** di-commit atau
  diunggah ke Google Drive publik.

Urutan penggunaan: kerjakan checklist, isi dokumen serah terima, tanda tangani,
lalu simpan salinan beserta backup database di Google Drive sekolah.

## Keamanan dan catatan penting

- Jangan menaruh password, API key, atau isi `.env` di repositori, dokumen, atau
  chat. Repositori GitHub bersifat publik.
- Ganti seluruh akun dan password contoh sebelum production.
- Password baru minimal 8 karakter. Gunakan password manager sekolah dan jangan
  membagikan password lewat chat atau dokumen.
- Jika pengguna lupa password, admin mereset dari tab **Akun & Password**.
  Setelah reset, minta pengguna segera mengganti password sendiri.
- Rotasi `GEMINI_API_KEY` dan password database bila pernah terbuka di chat.
- Jangan mengubah `APP_KEY` saat proses transfer. Perubahan akan membuat semua
  sesi logout dan data terenkripsi tidak terbaca.
- Jangan menghapus Vercel Blob store lama sebelum semua gambar dipindahkan.
- Pastikan akun Vercel, GitHub, TiDB, dan domain terdaftar atas nama sekolah,
  bukan atas nama developer.
- Data konseling bersifat sensitif. Batasi akses sesuai peran dan konfirmasikan
  kebijakan penyimpanan data di TiDB Cloud kepada pihak sekolah.
- Paket Vercel Hobby ditujukan untuk penggunaan non-komersial. Untuk portal
  resmi sekolah, pertimbangkan paket Pro.

## Kontak

| Keperluan | Kontak |
|---|---|
| Pengelola portal | MTsN 1 Wonosobo |
| Dukungan teknis | ____________________ |
| Email / WhatsApp | ____________________ |

Isi bagian kontak sebelum dokumen dibagikan. Untuk pertanyaan teknis, sertakan
alamat halaman, waktu kejadian, dan tangkapan layar pesan kesalahan.
