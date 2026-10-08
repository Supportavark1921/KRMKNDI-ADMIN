<?php

namespace App\Http\Controllers;

use App\Models\DonationCategory;
use App\Models\Guru;
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
            'status' => ['required', 'in:active,inactive'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:donation_categories,id'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('gurus', 'public');
        }

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
            'guru'       => $guru,
            'categories' => DonationCategory::active()->orderBy('name')->get(),
            'assigned'   => $guru->donationCategories->pluck('id')->toArray(),
            'gurujiUsers' => User::whereIn('role', ['guruji'])->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    public function update(Request $request, Guru $guru): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status'      => ['required', 'in:active,inactive'],
            'user_id'     => ['nullable', 'exists:users,id'],
            'categories'  => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:donation_categories,id'],
            'image'       => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            if ($guru->image) {
                Storage::disk('public')->delete($guru->image);
            }
            $data['image'] = $request->file('image')->store('gurus', 'public');
        }

        // Allow explicitly clearing the user link when "None" is selected.
        $data['user_id'] = $data['user_id'] ?? null;

        $guru->update($data);
        $guru->donationCategories()->sync($data['categories'] ?? []);

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
