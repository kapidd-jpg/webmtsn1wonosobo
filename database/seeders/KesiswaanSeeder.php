<?php

namespace Database\Seeders;

use App\Models\KesiswaanItem;
use Illuminate\Database\Seeder;

class KesiswaanSeeder extends Seeder
{
    public function run(): void
    {
        $ruang = [
            [
                'slug'      => 'data-siswa',
                'judul'     => 'Data Siswa',
                'deskripsi' => 'Data siswa, kelas, wali kelas, dan ekstrakurikuler dalam satu tempat.',
                'ikon'      => 'data-siswa',
                'tautan'    => 'guru-dashboard',
                'tautan_label' => 'Buka Data Siswa',
                'urutan'    => 1,
                'isi'       => "Ruang Data Siswa memuat informasi siswa, kelas, wali kelas, dan ekstrakurikuler yang diikuti.\n\nDaftar siswa hanya dapat dibuka oleh guru, guru BK, dan admin yang sudah login. Siswa dapat melihat ringkasan profil dan kegiatannya melalui dashboard masing-masing.\n\nJika ada data yang perlu diperbaiki, silakan hubungi wali kelas atau bagian kesiswaan.",
            ],
            [
                'slug'      => 'kegiatan',
                'judul'     => 'Kegiatan',
                'deskripsi' => 'Agenda, dokumentasi, dan cerita kegiatan siswa sepanjang tahun.',
                'ikon'      => 'kegiatan',
                'tautan'    => 'galeri',
                'tautan_label' => 'Lihat Galeri Kegiatan',
                'urutan'    => 2,
                'isi'       => "Ruang Kegiatan menghimpun agenda dan dokumentasi kegiatan siswa, mulai dari kegiatan rutin sekolah, peringatan hari besar, hingga kegiatan bersama masyarakat.\n\nGunakan ruang ini untuk mengikuti jadwal kegiatan terbaru dan melihat dokumentasi yang sudah dipublikasikan.\n\nInformasi kegiatan resmi juga diumumkan melalui halaman Pengumuman.",
            ],
            [
                'slug'      => 'ekstrakurikuler',
                'judul'     => 'Ekstrakurikuler',
                'deskripsi' => 'Pilihan kegiatan pengembangan minat, bakat, dan organisasi siswa.',
                'ikon'      => 'ekstrakurikuler',
                'tautan'    => 'ekstra',
                'tautan_label' => 'Lihat Ekstrakurikuler',
                'urutan'    => 3,
                'isi'       => "Ruang Ekstrakurikuler berisi daftar kegiatan pengembangan minat dan bakat yang tersedia di madrasah, lengkap dengan jadwal dan lokasi kegiatan.\n\nSiswa dapat memilih ekstrakurikuler sesuai minat, lalu berkoordinasi dengan pembina masing-masing.\n\nBuka halaman Ekstrakurikuler untuk melihat pilihan kegiatan dan dokumentasinya.",
            ],
            [
                'slug'      => 'beasiswa-pip',
                'judul'     => 'Layanan Beasiswa dan PIP',
                'deskripsi' => 'Informasi beasiswa, Program Indonesia Pintar, syarat, dan jadwal pengajuan.',
                'ikon'      => 'beasiswa-pip',
                'tautan'    => null,
                'tautan_label' => null,
                'urutan'    => 4,
                'isi'       => "Ruang Layanan Beasiswa dan PIP menyediakan informasi program bantuan pendidikan, termasuk Program Indonesia Pintar (PIP), syarat pengajuan, dan jadwal pendaftaran.\n\nPastikan dokumen seperti Kartu Indonesia Pintar (KIP), Kartu Keluarga, dan rapor disiapkan sebelum pengajuan. Data penerima akan diverifikasi oleh sekolah sebelum diteruskan ke dinas terkait.\n\nUntuk pertanyaan lebih lanjut, hubungi bagian kesiswaan atau wali kelas.",
            ],
            [
                'slug'      => 'kemitraan',
                'judul'     => 'Kemitraan',
                'deskripsi' => 'Kerja sama sekolah dengan orang tua, instansi, dan masyarakat.',
                'ikon'      => 'kemitraan',
                'tautan'    => null,
                'tautan_label' => null,
                'urutan'    => 5,
                'isi'       => "Ruang Kemitraan memuat informasi kerja sama madrasah dengan orang tua, instansi pemerintah, dunia usaha, dan masyarakat.\n\nKemitraan mendukung kegiatan pembelajaran, pengembangan karakter, praktik baik, serta program kesiswaan yang bermanfaat bagi siswa.\n\nSekolah membuka kesempatan bagi pihak yang ingin berkontribusi melalui koordinasi dengan bagian kesiswaan atau humas.",
            ],
            [
                'slug'      => 'giat-lain',
                'judul'     => 'Giat Lain',
                'deskripsi' => 'Lomba, bakti sosial, program khusus, dan kegiatan tambahan siswa.',
                'ikon'      => 'giat-lain',
                'tautan'    => 'pengumuman',
                'tautan_label' => 'Lihat Pengumuman Terbaru',
                'urutan'    => 6,
                'isi'       => "Ruang Giat Lain menampung kegiatan tambahan siswa seperti lomba, bakti sosial, program khusus, dan kegiatan kolaborasi antarkelas.\n\nJadwal dan ketentuan setiap kegiatan diumumkan secara berkala. Siswa dapat mengikuti informasi terbaru melalui halaman Pengumuman atau menghubungi pembina kegiatan.\n\nRuang ini akan diperbarui sesuai program yang sedang berjalan di madrasah.",
            ],
        ];

        foreach ($ruang as $data) {
            KesiswaanItem::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
