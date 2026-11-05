# Checklist Serah Terima Portal MTsN 1 Wonosobo

Dokumen ini dipakai secara internal. Kerjakan berurutan, centang setiap langkah,
dan simpan salinan setelah selesai. Perkiraan waktu 2 sampai 4 jam, di luar
waktu tunggu perpindahan akun.

- URL production: https://webmtsn1wonosobo.vercel.app
- Repositori: https://github.com/kapidd-jpg/webmtsn1wonosobo
- Teknologi: Laravel 10, PHP 8.3, Vercel, TiDB Cloud, Vercel Blob

## Fase A: Bersihkan akun dan data

- [ ] Login sebagai admin, lalu ganti password akun `admin001`. Password awal
      diberikan terpisah dan tidak dicatat di dokumen ini.
- [ ] Buat akun admin baru untuk client, lalu nonaktifkan atau ganti nama akun
      `admin001`.
- [ ] Ganti atau hapus akun demo berikut. Password-nya mengikuti data seeder
      dan harus dianggap tidak aman.
  - [ ] `ahmad.setiawan@siswa.local`
  - [ ] `arif.rahman@guru.local`
  - [ ] `dewi.kusuma@guru.local`
- [ ] Hapus data contoh yang tidak dipakai (pengumuman, jadwal,
      ekstrakurikuler, kesiswaan, konseling), atau ganti dengan data asli
      sekolah.
- [ ] Perbarui profil sekolah: nama, alamat, kontak, dan sambutan kepala
      sekolah.
- [ ] Pastikan tidak ada data pribadi siswa yang belum diverifikasi.

## Fase B: Backup dan pemulihan

- [ ] Buat dump terbaru seluruh database. Perintahnya ada di Lampiran A.
- [ ] Simpan dump di Google Drive milik client, bukan di folder sementara
      laptop.
- [ ] Salin juga `backup-counseling.sql` dan `backup-learning-materials.sql`
      dari `C:\Users\Khafidz\AppData\Local\Temp\opencode\` ke drive client.
- [ ] Uji restore ke database percobaan, bukan ke production, sampai berhasil.
- [ ] Cek TiDB Cloud Console apakah backup otomatis sudah aktif. Jika belum,
      jadwalkan dump manual setiap minggu.
- [ ] Catat tanggal backup terakhir di dokumen serah terima.

## Fase C: Pindahkan kepemilikan akun

- [ ] GitHub: buka Settings, General, lalu Danger Zone, pilih Transfer
      ownership ke akun atau organisasi client. Pastikan client bisa push ke
      branch `main`.
- [ ] Vercel: buat team milik client, lalu buka Project, Settings, General,
      dan pilih Transfer Project. Pastikan billing ikut pindah.
- [ ] Vercel Blob: setelah transfer, cek apakah Blob store ikut pindah. Jika
      tidak, buat store baru di team client dan atur ulang
      `BLOB_READ_WRITE_TOKEN`. Jangan hapus store lama sebelum semua gambar
      dipindahkan.
- [ ] TiDB Cloud: undang client sebagai member organisasi, atau buat cluster
      baru di akun client lalu pindahkan data dengan dump dan restore. Setelah
      itu perbarui variabel `DB_*` di Vercel.
- [ ] Domain: daftarkan atas nama sekolah. Untuk domain `.sch.id`, siapkan
      surat permohonan berkop sekolah, SK pendirian, dan dokumen lain yang
      diminta registrar. Setelah domain aktif, tambahkan di Vercel melalui
      Settings, Domains, atur DNS, ubah `APP_URL`, lalu redeploy.
- [ ] Simpan semua kredensial di password manager client. Jangan menaruhnya di
      chat, dokumen, atau repositori.

## Fase D: Konfigurasi final

- [ ] Rotasi password TiDB sekali lagi karena password lama pernah muncul di
      chat. Perbarui `DB_PASSWORD` di Production dan Preview, lalu redeploy.
- [ ] Pastikan `APP_DEBUG=false`, `APP_ENV=production`, dan `APP_URL` memakai
      domain final.
- [ ] Pastikan `SESSION_SECURE_COOKIE=true`.
- [ ] Atur SMTP asli bila fitur email dibutuhkan. Saat ini pengaturan email
      masih `mailpit`, sehingga email tidak terkirim di production.
- [ ] Pastikan `APP_KEY` tidak berubah saat transfer. Jika berubah, semua sesi
      logout dan data terenkripsi tidak terbaca.
- [ ] Pastikan file `.env` tidak ikut terkirim bila menyerahkan source code.
- [ ] Aktifkan notifikasi email Vercel untuk deploy yang gagal.

## Fase E: Verifikasi setelah transfer

Lakukan dari akun client.

- [ ] Deploy percobaan dari repositori client: ubah satu baris dokumen, push,
      lalu pastikan status Ready.
- [ ] Login admin berhasil.
- [ ] Tambah pengumuman, jadwal, ekstrakurikuler, dan kesiswaan, lalu pastikan
      tampil di portal.
- [ ] Unggah banner ekstrakurikuler, pastikan gambar muncul dan URL bisa
      dibuka.
- [ ] Unggah gambar kesiswaan, pastikan gambar muncul dan URL bisa dibuka.
- [ ] Hapus data uji, pastikan gambar ikut terhapus.
- [ ] Kirim konseling dari form publik, pastikan muncul di dashboard guru BK.
- [ ] Tandai konseling sebagai sudah ditangani, pastikan status tersimpan.
- [ ] Semua endpoint utama merespons 200, dan `/api/materi` merespons 404.
- [ ] Log Vercel bersih, tidak ada `SQLSTATE`, access denied, atau fatal error.
- [ ] Coba rollback deployment sekali di Vercel, lalu kembalikan ke versi
      terbaru.

## Fase F: Dokumen dan batas dukungan

- [ ] Isi `DOKUMEN-SERAH-TERIMA.md`: tanggal, nama, kontak, dan biaya.
- [ ] Cetak dan tanda tangani dua rangkap, atau gunakan tanda tangan digital.
- [ ] Jelaskan masa garansi 30 hari untuk bug, bukan untuk fitur baru.
- [ ] Sepakati jalur dukungan (WhatsApp atau email) dan jam kerja.
- [ ] Serahkan akses ke client, lalu hapus akses developer dari akun client
      kecuali ada kontrak maintenance.

## Lampiran A: Perintah operasional

### Backup database

Jalankan dari root project di PowerShell.

```powershell
& "D:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe" --no-defaults `
  --ssl-ca=database\certs\cacert.pem --ssl-mode=VERIFY_IDENTITY `
  -h <DB_HOST> -P 4000 -u <DB_USERNAME> -p `
  --skip-lock-tables --no-tablespaces --set-gtid-purged=OFF `
  --result-file=backup-portal-$(Get-Date -Format yyyyMMdd).sql `
  portal_kesiswaan
```

`-p` tanpa nilai akan meminta password secara interaktif, sehingga password
tidak tersimpan di riwayat perintah.

### Restore database

```powershell
Get-Content backup-portal-20261104.sql -Raw | `
  & "D:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" --no-defaults `
  --ssl-ca=database\certs\cacert.pem --ssl-mode=VERIFY_IDENTITY `
  -h <DB_HOST> -P 4000 -u <DB_USERNAME> -p portal_kesiswaan
```

### Rotasi password database

```powershell
# 1. Reset password di TiDB Cloud Console.
# 2. Perbarui variabel di Vercel.
npx vercel env rm DB_PASSWORD production --yes
"<password-baru>" | npx vercel env add DB_PASSWORD production
npx vercel env rm DB_PASSWORD preview --yes
"<password-baru>" | npx vercel env add DB_PASSWORD preview
# 3. Redeploy.
npx vercel --prod --yes
```

### Deploy dan rollback

- Deploy: `git push origin main`, lalu Vercel build otomatis.
- Rollback: buka Vercel, masuk ke Deployments, pilih versi lama, lalu pilih
  Promote to Production.

## Lampiran B: Variabel environment production

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
| `DB_PASSWORD` | Password TiDB, disimpan sebagai Secret |
| `MYSQL_ATTR_SSL_CA` | Opsional, default `database/certs/cacert.pem` |
| `BLOB_READ_WRITE_TOKEN` | Diisi otomatis oleh Vercel Blob |
| `BLOB_ACCESS` | `public` |
| `GEMINI_API_KEY` | API key STELA, disimpan sebagai Secret |
| `GEMINI_MODEL` | Opsional, default `gemini-3.6-flash` |
| `GEMINI_MAX_OUTPUT_TOKENS` | Opsional, default `2048` |
| `GEMINI_THINKING_BUDGET` | Opsional, default `0` |
| `SESSION_SECURE_COOKIE` | `true` |
| `LOG_CHANNEL` | `stderr` |
