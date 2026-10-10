<?php

namespace App\Http\Controllers\Guruji;

use App\Http\Controllers\Controller;
use App\Models\DonationCategory;
use App\Models\Guru;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SevaController extends Controller
{
    private function myGuru(): Guru
    {
        $guru = Guru::where('user_id', auth()->id())->first();
        abort_unless($guru, 403, 'No Guruji profile linked to your account.');
        return $guru;
    }

    public function index(): View
    {
        Gate::authorize('my-seva.view');

        $guru       = $this->myGuru();
        $categories = DonationCategory::whereHas('gurus', fn ($q) => $q->where('gurus.id', $guru->id))
            ->latest()->get();

        return view('guruji.seva.index', compact('categories', 'guru'));
    }

    public function edit(DonationCategory $donationCategory): View
    {
        Gate::authorize('my-seva.update');

        $guru = $this->myGuru();
        abort_unless(
            $donationCategory->gurus()->where('gurus.id', $guru->id)->exists(),
            403,
            'This category is not assigned to you.'
        );

        return view('guruji.seva.edit', compact('donationCategory'));
    }

    public function update(Request $request, DonationCategory $donationCategory): RedirectResponse
    {
        Gate::authorize('my-seva.update');

        $guru = $this->myGuru();
        abort_unless(
            $donationCategory->gurus()->where('gurus.id', $guru->id)->exists(),
            403,
            'This category is not assigned to you.'
        );

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
            ->withProperties(['old' => $old])
            ->log('seva_category_updated');

        return redirect()->route('my.seva.index')
            ->with('success', 'Category updated.');
    }
}
