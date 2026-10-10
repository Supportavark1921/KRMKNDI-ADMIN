<?php

namespace App\Http\Controllers\Guruji;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopCategoryController extends Controller
{
    private function myGuru(): Guru
    {
        $guru = Guru::where('user_id', auth()->id())->first();
        abort_unless($guru, 403, 'No Guruji profile linked to your account.');
        return $guru;
    }

    public function index(): View
    {
        Gate::authorize('my-store.view');
        $guru       = $this->myGuru();
        $categories = ProductCategory::withCount('products')
            ->where('guru_id', $guru->id)
            ->orderBy('name')->get();

        return view('guruji.store.categories.index', compact('categories', 'guru'));
    }

    public function create(): View
    {
        Gate::authorize('my-store.create');
        return view('guruji.store.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('my-store.create');
        $guru = $this->myGuru();

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status'      => ['required', 'in:active,inactive'],
            'image'       => ['nullable', 'image', 'max:5120'],
        ]);

        $data['guru_id'] = $guru->id;
        $data['slug']    = Str::slug($data['name']) . '-' . $guru->id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('store/categories', 'public');
        }

        $category = ProductCategory::create($data);

        activity()->causedBy(auth()->user())->performedOn($category)->log('created');

        return redirect()->route('my.store.categories.index')->with('success', 'Category created.');
    }

    public function edit(ProductCategory $category): View
    {
        Gate::authorize('my-store.update');
        $guru = $this->myGuru();
        abort_unless((int) $category->guru_id === $guru->id, 403, 'Not your category.');

        return view('guruji.store.categories.edit', compact('category'));
    }

    public function update(Request $request, ProductCategory $category): RedirectResponse
    {
        Gate::authorize('my-store.update');
        $guru = $this->myGuru();
        abort_unless((int) $category->guru_id === $guru->id, 403, 'Not your category.');

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status'      => ['required', 'in:active,inactive'],
            'image'       => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            if ($category->image) Storage::disk('public')->delete($category->image);
            $data['image'] = $request->file('image')->store('store/categories', 'public');
        }

        $category->update($data);
        activity()->causedBy(auth()->user())->performedOn($category)->log('updated');

        return redirect()->route('my.store.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(ProductCategory $category): RedirectResponse
    {
        Gate::authorize('my-store.delete');
        $guru = $this->myGuru();
        abort_unless((int) $category->guru_id === $guru->id, 403, 'Not your category.');

        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete a category that has products. Deactivate instead.');
        }

        if ($category->image) Storage::disk('public')->delete($category->image);
        $category->delete();

        return redirect()->route('my.store.categories.index')->with('success', 'Category deleted.');
    }
}
