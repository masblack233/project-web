<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with(['user', 'schedules'])->latest()->paginate(12);
        return view('services.index', compact('services'));
    }

    public function show(Service $service)
    {
        $service->load(['user', 'schedules']);
        return view('services.show', compact('service'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'nullable|string|max:255',
            'schedule_title' => 'nullable|string|max:255',
            'schedule_start' => 'nullable|date',
            'schedule_end' => 'nullable|date|after:schedule_start',
        ]);

        $data = $request->only(['name', 'description', 'price']);
        $data['user_id'] = auth()->id();

        $service = Service::create($data);

        // Create initial schedule if provided
        if ($request->filled('schedule_start') && $request->filled('schedule_end')) {
            $service->schedules()->create([
                'start' => $request->schedule_start,
                'end' => $request->schedule_end,
                'title' => $request->schedule_title ?? 'Booked',
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Service created successfully!');
    }

    public function update(Request $request, Service $service)
    {
        $this->authorize('update', $service);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'nullable|string|max:255',
        ]);

        $service->update($request->only(['name', 'description', 'price']));

        return redirect()->route('dashboard')->with('success', 'Service updated successfully!');
    }

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);
        $service->delete();

        return redirect()->route('dashboard')->with('success', 'Service deleted successfully!');
    }
}