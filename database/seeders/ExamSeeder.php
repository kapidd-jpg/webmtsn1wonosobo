<?php

namespace Database\Seeders;

use App\Models\Exam;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        Exam::insert([
            ['tanggal' => '10 Oktober', 'jam' => '07:00 - 08:30', 'mapel' => 'Matematika', 'kelas' => 'IX A', 'jenis' => 'UTS', 'keterangan' => 'Bab 1-4', 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '11 Oktober', 'jam' => '07:00 - 08:30', 'mapel' => 'Bahasa Indonesia', 'kelas' => 'IX A', 'jenis' => 'UTS', 'keterangan' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}