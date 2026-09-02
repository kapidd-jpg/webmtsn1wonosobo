<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    private array $dayOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    public function index()
    {
        $schedules = Schedule::all();

        return $schedules->sortBy(function ($item) {
            return array_search($item->hari, $this->dayOrder) . $item->jam;
        })->values();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hari'  => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam'   => 'required|string|max:50',
            'mapel' => 'required|string|max:100',
            'kelas' => 'required|string|max:50',
            'guru'  => 'required|string|max:100',
        ]);

        return Schedule::create($data);
    }

    public function update(Request $request, Schedule $schedule)
    {
        $data = $request->validate([
            'hari'  => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam'   => 'required|string|max:50',
            'mapel' => 'required|string|max:100',
            'kelas' => 'required|string|max:50',
            'guru'  => 'required|string|max:100',
        ]);

        $schedule->update($data);

        return $schedule;
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return response()->noContent();
    }
}