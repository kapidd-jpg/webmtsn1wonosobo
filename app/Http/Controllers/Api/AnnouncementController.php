<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        return Announcement::orderByDesc('id')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal'   => 'required|string|max:10',
            'bulan'     => 'required|string|max:3',
            'kategori'  => 'required|string|max:50',
            'judul'     => 'required|string|max:150',
            'deskripsi' => 'required|string',
        ]);

        return Announcement::create($data);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'tanggal'   => 'required|string|max:10',
            'bulan'     => 'required|string|max:3',
            'kategori'  => 'required|string|max:50',
            'judul'     => 'required|string|max:150',
            'deskripsi' => 'required|string',
        ]);

        $announcement->update($data);

        return $announcement;
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return response()->noContent();
    }
}