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

        $roots = ProductCategory::with(['children.products', 'products'])
            ->withCount('products')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return view('admin.store.categories.index', compact('roots'));
    }

    public function create(): View
    {
        Gate::authorize('manage-store');

        $parents = ProductCategory::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.store.categories.create', compact('parents'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-store');

        $data = $request->validate([
            'parent_id'   => ['nullable', 'integer', 'exists:product_categories,id'],
            'name'        => ['required', 'string', 'max:150'],
            'slug'        => ['nullable', 'string', 'max:150', 'alpha_dash', 'unique:product_categories,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'status'      => ['required', 'in:active,inactive'],
            'image'       => ['nullable', 'image', 'max:5120'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['parent_id'] = $data['parent_id'] ?: null;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        ProductCategory::create($data);

        return redirect()->route('admin.store.categories.index')->with('success', 'Category created.');
    }

    public function edit(ProductCategory $category): View
    {
        Gate::authorize('manage-store');

        // Exclude self and own children from parent options
        $parents = ProductCategory::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.store.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, ProductCategory $category): RedirectResponse
    {
        Gate::authorize('manage-store');

        $data = $request->validate([
            'parent_id'   => ['nullable', 'integer', 'exists:product_categories,id'],
            'name'        => ['required', 'string', 'max:150'],
            'slug'        => ['nullable', 'string', 'max:150', 'alpha_dash', "unique:product_categories,slug,{$category->id}"],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'status'      => ['required', 'in:active,inactive'],
            'image'       => ['nullable', 'image', 'max:5120'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['parent_id'] = $data['parent_id'] ?: null;

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
            return back()->with('error', 'Cannot delete: category has products. Reassign them first.');
        }

        if ($category->children()->exists()) {
            return back()->with('error', 'Cannot delete: category has subcategories. Delete them first.');
        }

        $category->delete();

        return redirect()->route('admin.store.categories.index')->with('success', 'Category deleted.');
    }
}
