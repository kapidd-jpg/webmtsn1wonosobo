<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CounselingSubmission;
use Illuminate\Http\Request;

class CounselingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'    => 'required|string|max:100',
            'kelas'   => 'required|string|max:50',
            'masalah' => 'required|string',
        ]);

        $data['user_id'] = auth()->id();
        $data['status'] = 'baru';

        CounselingSubmission::create($data);

        return response()->json(['message' => 'Pengajuan konsultasi berhasil dikirim.'], 201);
    }

    public function index()
    {
        return CounselingSubmission::orderByDesc('id')->get();
    }

    public function updateStatus(Request $request, CounselingSubmission $counselingSubmission)
    {
        $data = $request->validate([
            'status' => 'required|in:baru,diproses,selesai',
        ]);

        $counselingSubmission->update($data);

        return $counselingSubmission;
    }
}