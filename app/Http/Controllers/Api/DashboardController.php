<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CounselingSubmission;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Ringkasan angka untuk dashboard guru kesiswaan dan guru BK.
     */
    public function stats()
    {
        $totalPengajuan = CounselingSubmission::count();

        $kasusSelesai = CounselingSubmission::where(function ($query) {
            $query->where('sudah_ditangani', true)
                ->orWhere('status', 'selesai');
        })->count();

        $totalSiswa = User::where('role', 'siswa')->count();

        $siswaAktifEkskul = User::where('role', 'siswa')
            ->whereHas('extracurriculars')
            ->count();

        return response()->json([
            'pengajuan_masuk'      => $totalPengajuan,
            'belum_ditangani'      => max(0, $totalPengajuan - $kasusSelesai),
            'kasus_selesai'        => $kasusSelesai,
            'siswa_aktif_ekskul'   => $siswaAktifEkskul,
            'total_siswa'          => $totalSiswa,
        ]);
    }
}
