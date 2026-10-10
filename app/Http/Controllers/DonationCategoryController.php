<?php

namespace App\Http\Controllers;

use App\Models\DonationCategory;
use App\Models\Guru;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DonationCategoryController extends Controller
{
    public function index(): View
    {
        Gate::authorize('donation-categories.view');

        $myGuruId = Guru::where('user_id', auth()->id())->value('id');

        $query = DonationCategory::with('gurus')->withCount(['gurus', 'donations'])->latest();
        // Guruji only sees categories assigned to them
        if ($myGuruId && ! auth()->user()->can('donation-categories.update')) {
            $query->whereHas('gurus', fn ($q) => $q->where('gurus.id', $myGuruId));
        }

        return view('donation-categories.index', [
            'categories' => $query->paginate(20),
            'myGuruId'   => $myGuruId,
        ]);
    }

    public function editMyCategory(DonationCategory $donationCategory): View
    {
        Gate::authorize('donation-categories.view');

        $myGuruId = Guru::where('user_id', auth()->id())->value('id');
        if (! $myGuruId || ! $donationCategory->gurus()->where('gurus.id', $myGuruId)->exists()) {
            abort(403, 'You are not assigned to this category.');
        }

        return view('donation-categories.my-edit', compact('donationCategory'));
    }

    public function updateMyCategory(Request $request, DonationCategory $donationCategory): RedirectResponse
    {
        Gate::authorize('donation-categories.view');

        $myGuruId = Guru::where('user_id', auth()->id())->value('id');
        if (! $myGuruId || ! $donationCategory->gurus()->where('gurus.id', $myGuruId)->exists()) {
            abort(403, 'You are not assigned to this category.');
        }

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:120', 'unique:donation_categories,name,'.$donationCategory->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'image'       => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            if ($donationCategory->image) {
                Storage::disk('public')->delete($donationCategory->image);
            }
            $data['image'] = $request->file('image')->store('donation-categories', 'public');
        }

        $old = $donationCategory->only(['name', 'description', 'image']);
        $donationCategory->update($data);

        activity()->causedBy(auth()->user())->performedOn($donationCategory)
            ->withProperties(['old' => $old, 'new' => $data])
            ->log('donation_category_content_updated');

        return redirect()->route('donation-categories.index')
            ->with('success', 'Category updated.');
    }

    public function create(): View
    {
        Gate::authorize('donation-categories.create');

        return view('donation-categories.create', ['gurus' => Guru::active()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('donation-categories.create');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:donation_categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
            'gurus' => ['nullable', 'array'],
            'gurus.*' => ['integer', 'exists:gurus,id'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('donation-categories', 'public');
        }

        $category = DonationCategory::create($data);

        if (! empty($data['gurus'])) {
            $category->gurus()->sync($data['gurus']);
        }

        return redirect()->route('donation-categories.index')->with('success', 'Category created.');
    }

    public function edit(DonationCategory $donationCategory): View
    {
        Gate::authorize('donation-categories.update');

        return view('donation-categories.edit', [
            'category' => $donationCategory->load('gurus'),
            'gurus' => Guru::active()->orderBy('name')->get(),
            'assigned' => $donationCategory->gurus->pluck('id')->toArray(),
        ]);
    }

    public function update(Request $request, DonationCategory $donationCategory): RedirectResponse
    {
        Gate::authorize('donation-categories.update');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:donation_categories,name,'.$donationCategory->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
            'gurus' => ['nullable', 'array'],
            'gurus.*' => ['integer', 'exists:gurus,id'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            if ($donationCategory->image) {
                Storage::disk('public')->delete($donationCategory->image);
            }
            $data['image'] = $request->file('image')->store('donation-categories', 'public');
        }

        $donationCategory->update($data);
        $donationCategory->gurus()->sync($data['gurus'] ?? []);

        return redirect()->route('donation-categories.index')->with('success', 'Category updated.');
    }

    public function destroy(DonationCategory $donationCategory): RedirectResponse
    {
        Gate::authorize('donation-categories.delete');

        if ($donationCategory->donations()->exists()) {
            return back()->with('error', 'Cannot delete a category with donation history. Deactivate instead.');
        }

        if ($donationCategory->image) {
            Storage::disk('public')->delete($donationCategory->image);
        }

        $donationCategory->delete();

        return redirect()->route('donation-categories.index')->with('success', 'Category deleted.');
    }
}
