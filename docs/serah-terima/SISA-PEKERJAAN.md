# Sisa Pekerjaan Sebelum Serah Terima

Terakhir diperbarui: 5 November 2026

Dokumen ini merangkum pekerjaan yang sudah selesai dan yang masih harus
dikerjakan sebelum portal diserahkan sepenuhnya ke client.

## Sudah selesai

- Deploy production di Vercel: https://webmtsn1wonosobo.vercel.app
- Migrasi TiDB: kolom `sudah_ditangani` ditambahkan, tabel
  `learning_materials` dihapus, data lama tetap utuh.
- Upload gambar tersimpan permanen di Vercel Blob. Sudah diuji unggah, tampil,
  dan hapus.
- `APP_DEBUG=false`, `APP_URL` sudah diatur, session aman, log production
  bersih.
- `.env.example` sudah memuat variabel TiDB, TLS, Blob, dan Gemini.
- Dokumen dan checklist serah terima sudah dibuat.
- STELA aktif di production dengan model `gemini-3.6-flash`, dibatasi 20
  permintaan per menit, dan jawaban tidak terpotong lagi.

## A. Keamanan akun

- [ ] Ganti password admin `admin001`. Password awalnya diberikan terpisah dan
      akun ini dipakai saat pengujian production.
- [ ] Hapus atau ganti password akun demo berikut. Password-nya mengikuti data
      seeder dan harus dianggap tidak aman.
  - [ ] `ahmad.setiawan@siswa.local`
  - [ ] `arif.rahman@guru.local`
  - [ ] `dewi.kusuma@guru.local`
- [ ] Rotasi password TiDB karena password lama pernah muncul di chat.
- [ ] Simpan semua kredensial di password manager client.

## B. Data dan backup

- [ ] Ganti data contoh dengan data asli sekolah:
  - [ ] Pengumuman
  - [ ] Jadwal pelajaran
  - [ ] Ekstrakurikuler
  - [ ] Konten kesiswaan
  - [ ] Profil sekolah
- [ ] Pindahkan dump SQL dari folder sementara laptop ke Google Drive client.
      Lokasi saat ini:
      `C:\Users\Khafidz\AppData\Local\Temp\opencode\` berisi
      `backup-counseling.sql` dan `backup-learning-materials.sql`.
- [ ] Cek backup otomatis di TiDB Cloud. Jika belum ada, jadwalkan dump
      mingguan.
- [ ] Pastikan baris konseling di production adalah data asli, bukan sisa
      pengujian.

## C. Kepemilikan akun

- [ ] Transfer repositori GitHub ke akun atau organisasi sekolah.
- [ ] Transfer project Vercel ke team sekolah, termasuk billing.
- [ ] Pastikan Blob store ikut pindah. Jika tidak, buat store baru di team
      client dan atur ulang `BLOB_READ_WRITE_TOKEN`. Jangan hapus store lama
      sebelum gambar lama dipindahkan.
- [ ] Pindahkan TiDB Cloud ke akun sekolah, baik dengan mengundang sebagai
      member maupun memindahkan data ke cluster baru.
- [ ] Daftarkan domain atas nama sekolah. Untuk `.sch.id`, siapkan surat
      permohonan berkop sekolah dan SK pendirian. Ini satu-satunya komponen
      yang perlu dibeli, dengan biaya sekitar Rp150.000 sampai Rp250.000 per
      tahun.
- [ ] Hapus akses developer setelah transfer selesai, kecuali ada kontrak
      maintenance.

## D. Konfigurasi yang belum final

- [ ] Ubah `APP_URL` ke domain final setelah domain aktif.
- [ ] Atur SMTP asli bila fitur email dibutuhkan. Saat ini belum ada fitur
      email aktif, jadi sifatnya opsional.
- [ ] Aktifkan notifikasi email Vercel untuk deploy yang gagal.
- [ ] Uji upload file berukuran besar. Validasi banner mengizinkan 4 MB,
      sedangkan Vercel membatasi ukuran request sekitar 4,5 MB. Sebaiknya
      turunkan batas unggah ke 3 MB atau gunakan upload langsung ke Blob.
- [ ] Uji rollback deployment sekali di Vercel.

## E. Dokumen dan kesepakatan

- [ ] Isi bagian kosong di `DOKUMEN-SERAH-TERIMA.md`: tanggal, nama, domain,
      kontak, dan biaya.
- [ ] Tanda tangan dua rangkap.
- [ ] Sepakati masa garansi 30 hari untuk bug dan jalur dukungan.

## F. STELA dan API key

- [x] API key aktif dan tersimpan sebagai Secret di Vercel untuk Production
      dan Preview.
- [x] STELA sudah diuji di production dengan beberapa pertanyaan dan semua
      jawaban lengkap.
- [x] Teks sampah pada konteks prompt sudah dihapus.
- [x] Rate limit 20 permintaan per menit per IP sudah aktif.
- [x] Model, batas panjang jawaban, dan pengaturan thinking bisa diatur lewat
      `GEMINI_MODEL`, `GEMINI_MAX_OUTPUT_TOKENS`, dan
      `GEMINI_THINKING_BUDGET`.
- [x] Thinking dimatikan agar jawaban tidak terpotong.
- [x] Semua bagian jawaban Gemini digabungkan. Sebelumnya hanya bagian pertama
      yang diambil, sehingga balasan tampak terpotong.
- [ ] Rotasi API key sebelum serah terima. Key yang dipakai sekarang pernah
      dikirim lewat chat, jadi harus dianggap bocor. Buat key baru di
      https://aistudio.google.com/apikey, ganti Secret `GEMINI_API_KEY` di
      Vercel, lalu redeploy.
- [ ] Pastikan key baru dibuat dan dimiliki akun Google sekolah, bukan akun
      pribadi developer.
- [ ] Catat di dokumen serah terima: pemilik key, cara rotasi, dan pihak yang
      menanggung biaya kuota.
- [ ] Pantau kuota Gemini dan aktifkan billing bila pemakaian meningkat.

## Urutan pengerjaan yang disarankan

1. Bagian A, keamanan akun, sekitar 10 menit dan membutuhkan password admin
   baru.
2. Bagian B, data asli dan backup, sekitar 1 jam dan membutuhkan konten dari
   sekolah.
3. Bagian C, perpindahan kepemilikan akun, membutuhkan akses dashboard client.
4. Bagian D, konfigurasi domain dan pengujian tambahan.
5. Bagian F, rotasi API key STELA.
6. Bagian E, tanda tangan dokumen.

Bagian D dan E tidak menghalangi serah terima teknis, tetapi perlu diselesaikan
agar client benar-benar mandiri.

## Catatan teknis

- Repositori GitHub bersifat publik. Jangan menulis password, API key, atau isi
  file `.env` di file mana pun di dalamnya.
- Jangan mengubah `APP_KEY` saat transfer. Jika berubah, semua sesi logout dan
  data terenkripsi tidak terbaca.
- Jangan mengirim file `.env` bila menyerahkan source code. Cukup serahkan
  repositori GitHub tanpa file rahasia.
- Blob store lama harus tetap aktif selama masih ada gambar yang menunjuk ke
  URL store tersebut.
- Paket Vercel Hobby ditujukan untuk penggunaan non-komersial. Untuk portal
  resmi sekolah, pertimbangkan paket Pro.
- Kuota gratis TiDB dan Vercel Blob perlu dipantau. Unggahan foto sekolah
  menambah pemakaian penyimpanan dan transfer.
- Database TiDB berada di region Singapore. Data konseling siswa bersifat
  sensitif, jadi keputusan menyimpan data di luar negeri sebaiknya
  dikonfirmasi ke pihak sekolah.
