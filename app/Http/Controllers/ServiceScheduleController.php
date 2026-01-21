<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceSchedule;
use Illuminate\Http\Request;

class ServiceScheduleController extends Controller
{
    public function index(Service $service)
    {
        $this->authorize('update', $service);
        
        $schedules = $service->schedules()->get()->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'title' => $schedule->title,
                'start' => $schedule->start->toIso8601String(),
                'end' => $schedule->end->toIso8601String(),
            ];
        });

        return response()->json($schedules);
    }

    public function store(Request $request, Service $service)
    {
        $this->authorize('update', $service);

        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'title' => 'nullable|string|max:255',
        ]);

        $schedule = $service->schedules()->create([
            'start' => $request->start,
            'end' => $request->end,
            'title' => $request->title ?? 'Booked',
        ]);

        return response()->json([
            'id' => $schedule->id,
            'title' => $schedule->title,
            'start' => $schedule->start->toIso8601String(),
            'end' => $schedule->end->toIso8601String(),
        ]);
    }

    public function update(Request $request, Service $service, ServiceSchedule $schedule)
    {
        $this->authorize('update', $service);

        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'title' => 'nullable|string|max:255',
        ]);

        $schedule->update([
            'start' => $request->start,
            'end' => $request->end,
            'title' => $request->title ?? 'Booked',
        ]);

        return response()->json([
            'id' => $schedule->id,
            'title' => $schedule->title,
            'start' => $schedule->start->toIso8601String(),
            'end' => $schedule->end->toIso8601String(),
        ]);
    }

    public function destroy(Service $service, ServiceSchedule $schedule)
    {
        $this->authorize('update', $service);
        $schedule->delete();

        return response()->json(['message' => 'Schedule deleted successfully']);
    }
}