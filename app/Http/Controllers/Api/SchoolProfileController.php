<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use Illuminate\Http\Request;

class SchoolProfileController extends Controller
{
    // Selalu pakai baris pertama; kalau belum ada, buat otomatis
    private function getProfile(): SchoolProfile
    {
        return SchoolProfile::firstOrCreate(['id' => 1]);
    }

    public function show()
    {
        return $this->getProfile();
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'nama_sekolah'     => 'nullable|string|max:150',
            'kepala_sekolah'   => 'nullable|string|max:150',
            'alamat'           => 'nullable|string|max:255',
            'telepon'          => 'nullable|string|max:50',
            'email'            => 'nullable|email|max:150',
            'tahun_berdiri'    => 'nullable|string|max:10',
            'akreditasi'       => 'nullable|string|max:10',
            'visi'             => 'nullable|string',
            'misi'             => 'nullable|string',
            'sejarah_singkat'  => 'nullable|string',
        ]);

        $profile = $this->getProfile();
        $profile->update($data);

        return $profile;
    }
}