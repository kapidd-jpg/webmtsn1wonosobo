<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        return Exam::orderBy('tanggal')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal'     => 'required|string|max:50',
            'jam'         => 'required|string|max:50',
            'mapel'       => 'required|string|max:100',
            'kelas'       => 'required|string|max:50',
            'jenis'       => 'required|in:Ulangan Harian,UTS,UAS',
            'keterangan'  => 'nullable|string',
        ]);

        return Exam::create($data);
    }

    public function update(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'tanggal'     => 'required|string|max:50',
            'jam'         => 'required|string|max:50',
            'mapel'       => 'required|string|max:100',
            'kelas'       => 'required|string|max:50',
            'jenis'       => 'required|in:Ulangan Harian,UTS,UAS',
            'keterangan'  => 'nullable|string',
        ]);

        $exam->update($data);

        return $exam;
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();

        return response()->noContent();
    }
}