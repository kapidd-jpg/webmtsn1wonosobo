# Sisa Pekerjaan Sebelum Serah Terima

Terakhir diperbarui: 4 November 2026

Dokumen ini merangkum apa yang **sudah beres** dan apa yang **masih harus
dikerjakan** agar portal bisa diserahkan penuh ke client.

---

## Sudah beres

- [x] Deploy production di Vercel (`https://webmtsn1wonosobo.vercel.app`).
- [x] Migrasi TiDB: kolom `sudah_ditangani` ditambahkan, tabel
      `learning_materials` dihapus, data lama tetap utuh.
- [x] Upload gambar persisten di Vercel Blob (diuji: unggah, tampil, hapus).
- [x] `APP_DEBUG=false`, `APP_URL`, session aman, log production bersih.
- [x] `.env.example` lengkap dengan variabel TiDB/TLS dan Blob.
- [x] Dokumen dan checklist serah terima.

---

## A. Keamanan akun — wajib, cepat

- [ ] Ganti password admin `admin001` (password awal `admin123`).
      Akun ini dipakai saat pengujian production.
- [ ] Hapus atau ganti password akun demo:
  - [ ] `ahmad.setiawan@siswa.local` / `siswa123`
  - [ ] `arif.rahman@guru.local` / `guru123`
  - [ ] `dewi.kusuma@guru.local` / `gurubk123`
- [ ] Rotasi ulang password TiDB (password lama pernah muncul di chat).
- [ ] Simpan semua kredensial di password manager client.

## B. Data dan backup — wajib

- [ ] Ganti data seeder dengan data asli sekolah:
  - [ ] Pengumuman
  - [ ] Jadwal pelajaran
  - [ ] Ekstrakurikuler
  - [ ] Konten kesiswaan
  - [ ] Profil sekolah
- [ ] Pindahkan dump SQL dari folder temp laptop ke Google Drive client.
      Lokasi saat ini: `C:\Users\Khafidz\AppData\Local\Temp\opencode\`
      (`backup-counseling.sql`, `backup-learning-materials.sql`).
- [ ] Cek backup otomatis di TiDB Cloud; jika tidak ada, jadwalkan dump mingguan.
- [ ] Pastikan baris konseling di production adalah data asli, bukan sisa uji.

## C. Kepemilikan akun — inti serah terima

- [ ] Transfer repo GitHub ke akun/organisasi sekolah.
- [ ] Transfer project Vercel ke team sekolah (termasuk billing).
- [ ] Pastikan Vercel Blob store ikut pindah. Jika tidak, buat store baru di
      team client dan set `BLOB_READ_WRITE_TOKEN` ulang. **Jangan hapus store
      lama** sebelum gambar lama dimigrasi.
- [ ] Undang/pindahkan TiDB Cloud ke akun sekolah (invite member atau migrasi
      cluster).
- [ ] Daftarkan domain atas nama sekolah.
- [ ] Hapus akses developer setelah transfer selesai (kecuali ada kontrak
      maintenance).

## D. Konfigurasi yang belum final

- [ ] Ubah `APP_URL` ke domain final setelah domain aktif.
- [ ] Atur SMTP asli — **opsional**, karena saat ini belum ada fitur email
      aktif (tidak ada reset password via email).
- [ ] Aktifkan notifikasi email Vercel untuk deploy gagal ke alamat client.
- [ ] Uji upload file lebih dari 2 MB. Validasi banner mengizinkan 4 MB,
      tetapi batas PHP di Vercel perlu dipastikan (default PHP 2 MB).
- [ ] Uji rollback deployment sekali (Vercel → Promote to Production).

## E. Dokumen dan kesepakatan

- [ ] Isi bagian kosong pada `DOKUMEN-SERAH-TERIMA.md`: tanggal, nama, domain,
      kontak, dan biaya.
- [ ] Tanda tangan dua rangkap.
- [ ] Sepakati masa garansi (30 hari untuk bug) dan jalur dukungan.

---

## Urutan pengerjaan yang disarankan

1. **A** — keamanan akun (±10 menit, butuh password admin baru).
2. **B** — data asli dan backup (±1 jam, butuh konten dari sekolah).
3. **C** — transfer kepemilikan akun (butuh akses dashboard client).
4. **D** — konfigurasi domain dan pengujian tambahan.
5. **E** — tanda tangan dokumen.

Item di bagian **D** dan **E** tidak menghalangi serah terima teknis, tetapi
wajib diselesaikan agar client benar-benar mandiri.

---

## Catatan teknis

- **Jangan ubah `APP_KEY`** saat transfer. Jika berubah, semua sesi logout dan
  data terenkripsi tidak terbaca.
- **Jangan kirim file `.env`** bila menyerahkan source code. Cukup kirim repo
  GitHub tanpa file rahasia.
- **Blob store lama** harus tetap aktif selama masih ada gambar yang menunjuk
  ke URL store tersebut.
- **Paket Vercel Hobby** diperuntukkan bagi penggunaan non-komersial. Untuk
  portal resmi sekolah, pertimbangkan paket Pro.
- **Kuota gratis TiDB dan Vercel Blob** perlu dipantau; unggahan foto sekolah
  menambah pemakaian storage dan transfer.
