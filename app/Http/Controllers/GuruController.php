<?php

namespace App\Http\Controllers;

use App\Models\DonationCategory;
use App\Models\Guru;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GuruController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('manage-appointments');

        $query = Guru::withCount(['donations as total_transactions' => fn ($q) => $q->where('payment_status', 'success')]);

        if ($s = $request->query('search')) {
            $query->where('name', 'like', "%{$s}%");
        }

        return view('gurus.index', ['gurus' => $query->latest()->paginate(15)->withQueryString()]);
    }

    public function create(): View
    {
        Gate::authorize('manage-appointments');

        return view('gurus.create', ['categories' => DonationCategory::active()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'translations.hi.name' => ['nullable', 'string', 'max:150'],
            'translations.hi.description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:active,inactive'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:donation_categories,id'],
            'image' => ['nullable', 'image', 'max:5120'],
            'background_image' => ['nullable', 'image', 'max:5120'],
            'gallery_new.*' => ['nullable', 'image', 'max:5120'],
        ]);

        $data['translations'] = array_filter([
            'hi' => array_filter([
                'name' => $request->input('translations.hi.name'),
                'description' => $request->input('translations.hi.description'),
            ]),
        ]) ?: null;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('gurus', 'public');
        }

        if ($request->hasFile('background_image')) {
            $data['background_image'] = $request->file('background_image')->store('gurus/bg', 'public');
        }

        $gallery = [];
        foreach ($request->file('gallery_new', []) as $file) {
            $gallery[] = $file->store('gurus/gallery', 'public');
        }
        $data['gallery'] = $gallery ?: null;

        $guru = Guru::create($data);

        if (! empty($data['categories'])) {
            $guru->donationCategories()->sync($data['categories']);
        }

        return redirect()->route('gurus.index')->with('success', 'Guruji added successfully.');
    }

    public function show(Guru $guru): View
    {
        Gate::authorize('manage-appointments');

        $guru->load(['donationCategories', 'services']);
        $categoryStats = $guru->donations()
            ->where('payment_status', 'success')
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn ($group) => [
                'name' => $group->first()->category->name ?? 'Unknown',
                'total' => $group->sum('donation_amount'),
                'count' => $group->count(),
            ])
            ->sortByDesc('total')
            ->values();

        return view('gurus.show', compact('guru', 'categoryStats'));
    }

    public function edit(Guru $guru): View
    {
        Gate::authorize('manage-appointments');

        $guru->load('donationCategories');

        return view('gurus.edit', [
            'guru' => $guru,
            'categories' => DonationCategory::active()->orderBy('name')->get(),
            'assigned' => $guru->donationCategories->pluck('id')->toArray(),
            'gurujiUsers' => User::whereIn('role', ['guruji'])->orderBy('name')->get(['id', 'name', 'email']),
            'allServices' => Service::orderBy('id')->get(),
            'assignedServices' => Service::where('guru_id', $guru->id)->pluck('id')->toArray(),
        ]);
    }

    public function update(Request $request, Guru $guru): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'translations.hi.name' => ['nullable', 'string', 'max:150'],
            'translations.hi.description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:active,inactive'],
            'user_id' => ['nullable', 'exists:users,id'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:donation_categories,id'],
            'image' => ['nullable', 'image', 'max:5120'],
            'background_image' => ['nullable', 'image', 'max:5120'],
            'gallery_new.*' => ['nullable', 'image', 'max:5120'],
            'removed_gallery' => ['nullable', 'string'],
        ]);

        $data['translations'] = array_filter([
            'hi' => array_filter([
                'name' => $request->input('translations.hi.name'),
                'description' => $request->input('translations.hi.description'),
            ]),
        ]) ?: null;

        if ($request->hasFile('image')) {
            if ($guru->image) {
                Storage::disk('public')->delete($guru->image);
            }
            $data['image'] = $request->file('image')->store('gurus', 'public');
        }

        if ($request->hasFile('background_image')) {
            if ($guru->background_image) {
                Storage::disk('public')->delete($guru->background_image);
            }
            $data['background_image'] = $request->file('background_image')->store('gurus/bg', 'public');
        }

        // Gallery: start from existing, remove flagged, append new uploads
        $existing = $guru->gallery ?? [];
        $removed = array_filter(explode(',', $data['removed_gallery'] ?? ''));
        foreach ($removed as $path) {
            Storage::disk('public')->delete($path);
            $existing = array_values(array_filter($existing, fn ($p) => $p !== $path));
        }
        foreach ($request->file('gallery_new', []) as $file) {
            $existing[] = $file->store('gurus/gallery', 'public');
        }
        $data['gallery'] = $existing ?: null;

        // Allow explicitly clearing the user link when "None" is selected.
        $data['user_id'] = $data['user_id'] ?? null;

        $guru->update($data);
        $guru->donationCategories()->sync($data['categories'] ?? []);

        // Re-assign services: null out previously linked ones, then set the checked ones
        Service::where('guru_id', $guru->id)->update(['guru_id' => null]);
        $serviceIds = array_filter(array_map('intval', $request->input('service_ids', [])));
        if ($serviceIds) {
            Service::whereIn('id', $serviceIds)->update(['guru_id' => $guru->id]);
        }

        return redirect()->route('gurus.index')->with('success', 'Guruji updated.');
    }

    public function destroy(Guru $guru): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        if ($guru->donations()->exists()) {
            return back()->with('error', 'Cannot delete a Guruji with donation history. Deactivate instead.');
        }

        if ($guru->image) {
            Storage::disk('public')->delete($guru->image);
        }

        $guru->delete();

        return redirect()->route('gurus.index')->with('success', 'Guruji deleted.');
    }
}
