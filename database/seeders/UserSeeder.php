<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['nis_nip' => '20240001'],
            [
                'name' => 'Ahmad Setiawan',
                'role' => 'siswa',
                'kelas' => 'IX A',
                'wali_kelas' => 'Ibu Siti Rahmawati, S.Pd.',
                'email' => 'ahmad.setiawan@siswa.local',
                'password' => Hash::make('siswa123'),
            ]
        );

        User::updateOrCreate(
            ['nis_nip' => '198501012010011001'],
            [
                'name' => 'Bpk. Arif Rahman',
                'role' => 'guru',
                'kelas' => null,
                'wali_kelas' => null,
                'email' => 'arif.rahman@guru.local',
                'password' => Hash::make('guru123'),
            ]
        );

                User::updateOrCreate(
            ['nis_nip' => '198701012015012002'],
            [
                'name' => 'Ibu Dewi Kusuma, S.Pd.',
                'role' => 'guru_bk',
                'email' => 'dewi.kusuma@guru.local',
                'password' => Hash::make('gurubk123'),
            ]
        );

        User::updateOrCreate(
            ['nis_nip' => 'admin001'],
            [
                'name' => 'Administrator',
                'role' => 'admin',
                'email' => 'admin@portal.local',
                'password' => Hash::make('admin123'),
            ]
        );
    }
}