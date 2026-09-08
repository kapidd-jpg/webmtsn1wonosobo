<?php

namespace Database\Seeders;

use App\Models\KesiswaanItem;
use Illuminate\Database\Seeder;

class KesiswaanSeeder extends Seeder
{
    public function run(): void
    {
        KesiswaanItem::insert([
            ['judul' => 'Organisasi Siswa', 'deskripsi' => 'Wadah bagi siswa untuk belajar berorganisasi dan memimpin.', 'urutan' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Kegiatan Sekolah', 'deskripsi' => 'Berbagai kegiatan yang mendukung kreativitas dan pengalaman siswa.', 'urutan' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Prestasi', 'deskripsi' => 'Informasi dan apresiasi terhadap prestasi siswa.', 'urutan' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Tata Tertib', 'deskripsi' => 'Informasi mengenai aturan dan kedisiplinan siswa.', 'urutan' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}