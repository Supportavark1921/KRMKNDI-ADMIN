<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoriesController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-store');

        return view('admin.store.categories.index', [
            'categories' => ProductCategory::withCount('products')->orderBy('sort_order')->paginate(25),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('manage-store');

        return view('admin.store.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-store');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'alpha_dash', 'unique:product_categories,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        ProductCategory::create($data);

        return redirect()->route('admin.store.categories.index')->with('success', 'Category created.');
    }

    public function edit(ProductCategory $category): View
    {
        Gate::authorize('manage-store');

        return view('admin.store.categories.edit', compact('category'));
    }

    public function update(Request $request, ProductCategory $category): RedirectResponse
    {
        Gate::authorize('manage-store');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'alpha_dash', "unique:product_categories,slug,{$category->id}"],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.store.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(ProductCategory $category): RedirectResponse
    {
        Gate::authorize('manage-store');

        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete a category with products. Reassign products first.');
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.store.categories.index')->with('success', 'Category deleted.');
    }
}
