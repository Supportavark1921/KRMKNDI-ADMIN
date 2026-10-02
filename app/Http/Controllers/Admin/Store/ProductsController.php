<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use App\Models\Mataji;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductsController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('access-store-admin');

        $user = auth()->user();
        $query = Product::with(['category', 'primaryImage', 'inventory', 'vendor'])
            ->withTrashed();

        // Vendors only see their own products
        if ($user->isVendor()) {
            $query->where('vendor_id', $user->vendor?->id);
        }

        if ($s = $request->query('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('product_code', 'like', "%{$s}%")
                ->orWhere('sku', 'like', "%{$s}%"));
        }

        if ($cat = $request->query('category')) {
            $query->where('category_id', $cat);
        }

        if ($type = $request->query('type')) {
            $query->where('product_type', $type);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($vid = $request->query('vendor_id')) {
            $query->where('vendor_id', $vid);
        }

        return view('admin.store.products.index', [
            'products' => $query->latest()->paginate(20)->withQueryString(),
            'categories' => ProductCategory::active()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('access-store-admin');

        return view('admin.store.products.create', [
            'categories' => ProductCategory::active()->orderBy('sort_order')->get(),
            'matajis' => Mataji::active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('access-store-admin');

        $data = $this->validateProduct($request);

        DB::transaction(function () use ($request, $data) {
            $user = auth()->user();
            // Auto-assign vendor_id when vendor creates a product
            if ($user->isVendor()) {
                $data['product']['vendor_id'] = $user->vendor?->id;
            }

            $product = Product::create($data['product']);

            // Attributes
            foreach ($data['attributes'] as $attr) {
                $product->attributes()->create($attr);
            }

            // Images
            foreach ($request->file('images', []) as $i => $file) {
                $path = $file->store('products', 'public');
                $product->images()->create([
                    'path' => $path,
                    'alt_text' => $product->name,
                    'image_type' => 'product',
                    'is_primary' => $i === 0,
                    'sort_order' => $i,
                ]);
            }

            // Seed inventory if qty given
            if (($qty = (int) $request->input('initial_stock', 0)) > 0) {
                app(InventoryService::class)->addStock($product, $qty, 'Initial stock');
            }
        });

        return redirect()->route('admin.store.products.index')->with('success', 'Product created.');
    }

    private function authorizeProduct(Product $product): void
    {
        Gate::authorize('access-store-admin');
        $user = auth()->user();
        if ($user->isVendor()) {
            // Vendor must own the product; products with no vendor_id belong to admin
            if ($product->vendor_id === null || $product->vendor_id !== $user->vendor?->id) {
                abort(403);
            }
        }
    }

    public function show(Product $product): View
    {
        $this->authorizeProduct($product);
        $product->load(['category', 'mataji', 'images', 'attributes', 'inventory', 'inventoryTransactions' => fn ($q) => $q->latest()->limit(20)]);

        return view('admin.store.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $this->authorizeProduct($product);
        $product->load(['images', 'attributes', 'inventory']);

        return view('admin.store.products.edit', [
            'product' => $product,
            'categories' => ProductCategory::active()->orderBy('sort_order')->get(),
            'matajis' => Mataji::active()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorizeProduct($product);

        $data = $this->validateProduct($request, $product->id);

        DB::transaction(function () use ($request, $product, $data) {
            $product->update($data['product']);

            // Re-sync attributes: replace all
            $product->attributes()->delete();
            foreach ($data['attributes'] as $attr) {
                $product->attributes()->create($attr);
            }

            // New images appended
            $existing = $product->images()->max('sort_order') ?? -1;
            foreach ($request->file('images', []) as $i => $file) {
                $path = $file->store('products', 'public');
                $product->images()->create([
                    'path' => $path,
                    'alt_text' => $product->name,
                    'image_type' => 'product',
                    'is_primary' => false,
                    'sort_order' => $existing + 1 + $i,
                ]);
            }
        });

        return redirect()->route('admin.store.products.show', $product)->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorizeProduct($product);
        $product->delete(); // soft delete

        return redirect()->route('admin.store.products.index')->with('success', 'Product archived.');
    }

    public function restore(int $id): RedirectResponse
    {
        Gate::authorize('manage-store');
        Product::withTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Product restored.');
    }

    public function deleteImage(ProductImage $image): RedirectResponse
    {
        Gate::authorize('access-store-admin');
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Image removed.');
    }

    public function setPrimaryImage(ProductImage $image): RedirectResponse
    {
        Gate::authorize('access-store-admin');
        ProductImage::where('product_id', $image->product_id)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary image set.');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function validateProduct(Request $request, ?int $productId = null): array
    {
        $skuRule = ['nullable', 'string', 'max:100'];
        $skuRule[] = $productId
            ? "unique:products,sku,{$productId}"
            : 'unique:products,sku';

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'category_id' => ['required', 'integer', 'exists:product_categories,id'],
            'mataji_id' => ['nullable', 'integer', 'exists:matajis,id'],
            'sku' => $skuRule,
            'short_description' => ['nullable', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'brand_source' => ['nullable', 'string', 'max:150'],
            'product_type' => ['required', 'in:NORMAL,MATAJI_OFFERING,MATAJI_OFFERED_RESALE'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:draft,active,inactive,sold_out'],
            'offering_eligible' => ['boolean'],
            'resale_eligible' => ['boolean'],
            'images.*' => ['nullable', 'image', 'max:5120'],
            'attr_key.*' => ['nullable', 'string', 'max:100'],
            'attr_value.*' => ['nullable', 'string', 'max:300'],
        ]);

        $productData = $validated;
        unset($productData['images'], $productData['attr_key'], $productData['attr_value']);
        $productData['offering_eligible'] = $request->boolean('offering_eligible');
        $productData['resale_eligible'] = $request->boolean('resale_eligible');

        $keys = $request->input('attr_key', []);
        $values = $request->input('attr_value', []);
        $attrs = [];
        foreach ($keys as $i => $key) {
            if ($key !== '' && isset($values[$i]) && $values[$i] !== '') {
                $attrs[] = ['key' => $key, 'value' => $values[$i]];
            }
        }

        return ['product' => $productData, 'attributes' => $attrs];
    }
}
