# Checklist Serah Terima — Portal MTsN 1 Wonosobo

Dokumen internal. Jalankan berurutan, centang tiap langkah, dan simpan salinan
setelah selesai. Estimasi total: 2–4 jam (di luar waktu tunggu transfer akun).

- **URL production:** https://webmtsn1wonosobo.vercel.app
- **Repo:** https://github.com/kapidd-jpg/webmtsn1wonosobo
- **Stack:** Laravel 10 · PHP 8.3 · Vercel · TiDB Cloud (MySQL) · Vercel Blob

---

## Fase A — Bersihkan akun & data (wajib sebelum diserahkan)

- [ ] Login sebagai admin, ganti password `admin001` (password awal `admin123`).
- [ ] Buat akun admin baru khusus client, lalu nonaktifkan/rename akun `admin001`.
- [ ] Ganti atau hapus akun demo:
  - [ ] `ahmad.setiawan@siswa.local` / `siswa123`
  - [ ] `arif.rahman@guru.local` / `guru123`
  - [ ] `dewi.kusuma@guru.local` / `gurubk123`
- [ ] Hapus data contoh yang tidak dipakai (pengumuman, jadwal, ekstrakurikuler,
      kesiswaan, konseling) atau ganti dengan data asli sekolah.
- [ ] Perbarui profil sekolah (nama, alamat, kontak, sambutan kepala sekolah).
- [ ] Pastikan tidak ada data pribadi/siswa asli yang belum diverifikasi.

## Fase B — Backup & pemulihan

- [ ] Buat dump terbaru seluruh database (lihat perintah di Lampiran A).
- [ ] Simpan dump di **Google Drive milik client**, bukan folder temp laptop.
- [ ] Salin juga `backup-counseling.sql` dan `backup-learning-materials.sql`
      dari `C:\Users\Khafidz\AppData\Local\Temp\opencode\` ke drive client.
- [ ] Uji restore ke database percobaan (bukan production) sampai berhasil.
- [ ] Cek TiDB Cloud Console: apakah backup otomatis aktif? Jika tidak, jadwalkan
      dump manual mingguan (bisa lewat GitHub Actions / cron di komputer client).
- [ ] Catat tanggal backup terakhir di dokumen serah terima.

## Fase C — Transfer kepemilikan akun

- [ ] **GitHub:** Settings → General → Danger Zone → Transfer ownership ke akun/org
      client. Pastikan client bisa push ke `main`.
- [ ] **Vercel:** buat team milik client, lalu Project → Settings → General →
      Transfer Project. Pastikan billing pindah ke client.
- [ ] **Vercel Blob:** setelah transfer, cek apakah Blob store ikut pindah.
      Jika tidak, buat store baru di team client dan set `BLOB_READ_WRITE_TOKEN`
      ulang. Perhatian: URL gambar lama menunjuk ke store lama — jangan hapus
      store lama sebelum semua gambar dimigrasi/diupload ulang.
- [ ] **TiDB Cloud:** undang client sebagai member organisasi, atau buat cluster
      baru di akun client lalu pindahkan data (dump + restore), kemudian update
      `DB_*` di Vercel.
- [ ] **Domain:** daftarkan atas nama sekolah. Tambahkan di Vercel → Settings →
      Domains, atur DNS, lalu ubah `APP_URL` dan redeploy.
- [ ] Simpan semua kredensial di **password manager client** (1Password/Bitwarden).
      Jangan pernah menaruhnya di chat, dokumen, atau repo.

## Fase D — Konfigurasi final

- [ ] Rotasi password TiDB sekali lagi (password lama pernah muncul di chat).
      Perbarui `DB_PASSWORD` di Production & Preview, lalu redeploy.
- [ ] `APP_DEBUG=false`, `APP_ENV=production`, `APP_URL=https://domain-final`.
- [ ] `SESSION_SECURE_COOKIE=true` (sudah aktif).
- [ ] Atur SMTP asli bila fitur email dibutuhkan (`MAIL_*`). Saat ini masih
      `mailpit` sehingga email tidak terkirim di production.
- [ ] Pastikan `APP_KEY` tidak berubah saat transfer (kalau berubah, sesi lama
      logout dan data terenkripsi tidak terbaca).
- [ ] Pastikan file `.env` tidak ikut terkirim bila menyerahkan source code.
- [ ] Aktifkan notifikasi email Vercel untuk deploy gagal ke alamat client.

## Fase E — Verifikasi pasca-transfer (dari akun client)

- [ ] Deploy percobaan dari repo client (ubah 1 baris dokumen → push → cek Ready).
- [ ] Login admin berhasil.
- [ ] Tambah pengumuman, jadwal, ekstrakurikuler, kesiswaan → tampil di portal.
- [ ] Upload banner ekstrakurikuler → gambar muncul dan URL bisa dibuka.
- [ ] Upload gambar kesiswaan → gambar muncul dan URL bisa dibuka.
- [ ] Hapus data uji → gambar ikut terhapus (tidak ada file menggantung).
- [ ] Kirim konseling dari form publik → muncul di dashboard guru BK.
- [ ] Tandai konseling "sudah ditangani" → status tersimpan.
- [ ] Semua endpoint utama 200, `/api/materi` 404.
- [ ] Log Vercel bersih (tidak ada `SQLSTATE`, access denied, fatal error).
- [ ] Rollback deployment dicoba sekali di Vercel (lalu kembalikan ke versi terbaru).

## Fase F — Dokumen & batas dukungan

- [ ] Isi `DOKUMEN-SERAH-TERIMA.md` (tanggal, nama, kontak, biaya).
- [ ] Cetak/tanda tangan 2 rangkap (atau tanda tangan digital).
- [ ] Jelaskan masa garansi: **30 hari untuk bug**, bukan fitur baru.
- [ ] Sepakati jalur dukungan (WhatsApp/email) dan jam kerja.
- [ ] Serahkan akses ke client, lalu **hapus akses Anda** dari akun client
      (kecuali ada kontrak maintenance).

---

## Lampiran A — Perintah operasional

### Backup database (PowerShell, dari root project)

```powershell
& "D:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe" --no-defaults `
  --ssl-ca=database\certs\cacert.pem --ssl-mode=VERIFY_IDENTITY `
  -h <DB_HOST> -P 4000 -u <DB_USERNAME> -p `
  --skip-lock-tables --no-tablespaces --set-gtid-purged=OFF `
  --result-file=backup-portal-$(Get-Date -Format yyyyMMdd).sql `
  portal_kesiswaan
```

`-p` tanpa nilai akan meminta password secara interaktif (aman, tidak tersimpan
di riwayat perintah).

### Restore database

```powershell
Get-Content backup-portal-20261104.sql -Raw | `
  & "D:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" --no-defaults `
  --ssl-ca=database\certs\cacert.pem --ssl-mode=VERIFY_IDENTITY `
  -h <DB_HOST> -P 4000 -u <DB_USERNAME> -p portal_kesiswaan
```

### Rotasi password database

```powershell
# 1. Reset password di TiDB Cloud Console
# 2. Perbarui di Vercel
npx vercel env rm DB_PASSWORD production --yes
"<password-baru>" | npx vercel env add DB_PASSWORD production
npx vercel env rm DB_PASSWORD preview --yes
"<password-baru>" | npx vercel env add DB_PASSWORD preview
# 3. Redeploy
npx vercel --prod --yes
```

### Deploy & rollback

- Deploy: `git push origin main` → Vercel otomatis build.
- Rollback: Vercel → Deployments → pilih versi lama → **Promote to Production**.

## Lampiran B — Variabel environment production

| Variabel | Keterangan |
|---|---|
| `APP_ENV` | `production` |
| `APP_KEY` | Jangan diubah saat transfer |
| `APP_DEBUG` | `false` |
| `APP_URL` | URL domain final |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | Host TiDB |
| `DB_PORT` | `4000` |
| `DB_DATABASE` | `portal_kesiswaan` |
| `DB_USERNAME` | User TiDB |
| `DB_PASSWORD` | Password TiDB (Secret) |
| `MYSQL_ATTR_SSL_CA` | Opsional; default `database/certs/cacert.pem` |
| `BLOB_READ_WRITE_TOKEN` | Otomatis dari Vercel Blob |
| `BLOB_ACCESS` | `public` |
| `GEMINI_API_KEY` | API key asisten AI STELA (Secret) |
| `GEMINI_MODEL` | Opsional; default `gemini-3.6-flash` |
| `GEMINI_MAX_OUTPUT_TOKENS` | Opsional; default `2048` |
| `GEMINI_THINKING_BUDGET` | Opsional; default `0` (thinking dimatikan) |
| `SESSION_SECURE_COOKIE` | `true` |
| `LOG_CHANNEL` | `stderr` |
