<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClientProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['profile' => $request->user()->clientProfile()->firstOrCreate([])]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'birth_date' => ['nullable', 'date', 'before:today'],
            'birth_time' => ['nullable', 'date_format:H:i'],
            'birth_place' => ['nullable', 'string', 'max:160'],
            'address' => ['nullable', 'string', 'max:1000'],
            'family_details' => ['nullable', 'string', 'max:1000'],
        ]);
        $request->user()->clientProfile()->updateOrCreate([], $data);

        return back()->with('success', 'Your profile has been saved.');
    }

    public function clients(): View
    {
        Gate::authorize('manage-appointments');

        return view('clients.index', ['clients' => User::where('role', 'user')->with('clientProfile')->withCount('appointments')->orderBy('name')->get()]);
    }

    public function show(User $user): View
    {
        Gate::authorize('manage-appointments');
        abort_unless($user->role === 'user', 404);

        return view('clients.show', ['client' => $user->load(['clientProfile', 'appointments' => fn ($query) => $query->latest('appointment_date')])]);
    }

    public function notes(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('manage-appointments');
        abort_unless($user->role === 'user', 404);
        $data = $request->validate(['admin_notes' => ['nullable', 'string', 'max:2000']]);
        $user->clientProfile()->updateOrCreate([], $data);

        return back()->with('success', 'Private client notes saved.');
    }
}
