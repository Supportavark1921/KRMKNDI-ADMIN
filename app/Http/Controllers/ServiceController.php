<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-appointments');
        Service::ensureDefaults();

        return view('services.index', ['services' => Service::orderBy('is_active', 'desc')->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-appointments');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:services,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'duration_minutes' => ['required', 'integer', 'in:30,45,60,90,120,180'],
            'price' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ]);
        $data['is_active'] = true;
        Service::create($data);

        return back()->with('success', 'Service added successfully.');
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        Gate::authorize('manage-appointments');
        $data = $request->validate([
            'description' => ['nullable', 'string', 'max:500'],
            'duration_minutes' => ['required', 'integer', 'in:30,45,60,90,120,180'],
            'price' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $service->update($data);

        return back()->with('success', 'Service updated.');
    }
}
