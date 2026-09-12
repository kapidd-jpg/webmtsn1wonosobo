<?php

namespace Database\Seeders;

use App\Models\LearningMaterial;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        LearningMaterial::insert([
            ['mapel' => 'Matematika', 'judul' => 'Bilangan Bulat', 'kelas' => 'IX A', 'deskripsi' => 'Materi tentang operasi bilangan bulat.', 'created_at' => now(), 'updated_at' => now()],
            ['mapel' => 'IPA', 'judul' => 'Sistem Pernapasan', 'kelas' => 'IX A', 'deskripsi' => 'Materi tentang sistem pernapasan manusia.', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}