<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use App\Models\Mataji;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MatajisController extends Controller
{
    public function index(): View
    {
        Gate::authorize('matajis.view');

        return view('admin.store.matajis.index', [
            'matajis' => Mataji::latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('manage-store');

        return view('admin.store.matajis.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-store');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'temple_name' => ['nullable', 'string', 'max:200'],
            'address' => ['nullable', 'string', 'max:300'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pin_code' => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string', 'max:2000'],
            'offering_available' => ['boolean'],
            'status' => ['required', 'in:active,inactive'],
            'image' => ['nullable', 'image', 'max:5120'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:150'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('matajis', 'public');
        }

        $data['contact_info'] = array_filter([
            'phone' => $data['contact_phone'] ?? null,
            'email' => $data['contact_email'] ?? null,
        ]);

        unset($data['contact_phone'], $data['contact_email']);
        $data['offering_available'] = $request->boolean('offering_available');

        Mataji::create($data);

        return redirect()->route('admin.store.matajis.index')->with('success', 'Mataji added.');
    }

    public function edit(Mataji $mataji): View
    {
        Gate::authorize('manage-store');

        return view('admin.store.matajis.edit', compact('mataji'));
    }

    public function update(Request $request, Mataji $mataji): RedirectResponse
    {
        Gate::authorize('manage-store');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'temple_name' => ['nullable', 'string', 'max:200'],
            'address' => ['nullable', 'string', 'max:300'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pin_code' => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string', 'max:2000'],
            'offering_available' => ['boolean'],
            'status' => ['required', 'in:active,inactive'],
            'image' => ['nullable', 'image', 'max:5120'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:150'],
        ]);

        if ($request->hasFile('image')) {
            if ($mataji->image) {
                Storage::disk('public')->delete($mataji->image);
            }
            $data['image'] = $request->file('image')->store('matajis', 'public');
        }

        $data['contact_info'] = array_filter([
            'phone' => $data['contact_phone'] ?? null,
            'email' => $data['contact_email'] ?? null,
        ]);

        unset($data['contact_phone'], $data['contact_email']);
        $data['offering_available'] = $request->boolean('offering_available');

        $mataji->update($data);

        return redirect()->route('admin.store.matajis.index')->with('success', 'Mataji updated.');
    }

    public function destroy(Mataji $mataji): RedirectResponse
    {
        Gate::authorize('manage-store');

        if ($mataji->products()->exists()) {
            return back()->with('error', 'Cannot delete a Mataji with products. Deactivate instead.');
        }

        if ($mataji->image) {
            Storage::disk('public')->delete($mataji->image);
        }

        $mataji->delete();

        return redirect()->route('admin.store.matajis.index')->with('success', 'Mataji deleted.');
    }
}
