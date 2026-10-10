<?php

namespace App\Http\Controllers\Guruji;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Mataji;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ShopProductController extends Controller
{
    private function myGuru(): Guru
    {
        $guru = Guru::where('user_id', auth()->id())->first();
        abort_unless($guru, 403, 'No Guruji profile linked to your account.');
        return $guru;
    }

    private function myCategories(Guru $guru)
    {
        return ProductCategory::where('status', 'active')->orderBy('name')->get();
    }

    public function index(Request $request): View
    {
        Gate::authorize('my-store.view');
        $guru = $this->myGuru();

        $query = Product::with(['images', 'category'])
            ->where('guru_id', $guru->id)
            ->latest();

        if ($s = $request->query('search')) {
            $query->where('name', 'like', "%{$s}%");
        }
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return view('guruji.store.products.index', [
            'products' => $query->paginate(20)->withQueryString(),
            'guru'     => $guru,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('my-store.create');
        $guru = $this->myGuru();

        return view('guruji.store.products.create', [
            'categories' => $this->myCategories($guru),
            'matajis'    => Mataji::where('status', 'active')->orderBy('name')->get(),
            'guru'       => $guru,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('my-store.create');
        $guru = $this->myGuru();

        $data = $this->validated($request);

        DB::transaction(function () use ($request, $data, $guru) {
            $data['product']['guru_id'] = $guru->id;
            $product = Product::create($data['product']);

            foreach ($data['attributes'] as $attr) {
                $product->attributes()->create($attr);
            }

            foreach ($request->file('images', []) as $i => $file) {
                $product->images()->create([
                    'path'       => $file->store('products', 'public'),
                    'alt_text'   => $product->name,
                    'sort_order' => $i,
                ]);
            }

            activity()->causedBy(auth()->user())->performedOn($product)->log('created');
        });

        return redirect()->route('my.store.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        Gate::authorize('my-store.update');
        $guru = $this->myGuru();
        abort_unless((int) $product->guru_id === $guru->id, 403, 'Not your product.');

        return view('guruji.store.products.edit', [
            'product'    => $product->load('images', 'attributes'),
            'categories' => $this->myCategories($guru),
            'matajis'    => Mataji::where('status', 'active')->orderBy('name')->get(),
            'guru'       => $guru,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        Gate::authorize('my-store.update');
        $guru = $this->myGuru();
        abort_unless((int) $product->guru_id === $guru->id, 403, 'Not your product.');

        $data = $this->validated($request, $product->id);

        DB::transaction(function () use ($request, $product, $data) {
            $product->update($data['product']);

            $product->attributes()->delete();
            foreach ($data['attributes'] as $attr) {
                $product->attributes()->create($attr);
            }

            $existing = $product->images()->max('sort_order') ?? -1;
            foreach ($request->file('images', []) as $i => $file) {
                $product->images()->create([
                    'path'       => $file->store('products', 'public'),
                    'alt_text'   => $product->name,
                    'sort_order' => $existing + $i + 1,
                ]);
            }

            foreach ($request->input('delete_images', []) as $imgId) {
                $img = $product->images()->find($imgId);
                if ($img) {
                    Storage::disk('public')->delete($img->path);
                    $img->delete();
                }
            }

            activity()->causedBy(auth()->user())->performedOn($product)->log('updated');
        });

        return redirect()->route('my.store.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        Gate::authorize('my-store.delete');
        $guru = $this->myGuru();
        abort_unless((int) $product->guru_id === $guru->id, 403, 'Not your product.');

        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $product->delete();

        return redirect()->route('my.store.products.index')->with('success', 'Product deleted.');
    }

    private function validated(Request $request, ?int $productId = null): array
    {
        $skuRule = ['nullable', 'string', 'max:100'];
        $skuRule[] = $productId ? "unique:products,sku,{$productId}" : 'unique:products,sku';

        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:200'],
            'category_id'       => ['required', 'integer', 'exists:product_categories,id'],
            'mataji_id'         => ['nullable', 'integer', 'exists:matajis,id'],
            'product_type'      => ['required', 'in:NORMAL,MATAJI_OFFERING,MATAJI_OFFERED_RESALE'],
            'sku'               => $skuRule,
            'short_description' => ['nullable', 'string', 'max:300'],
            'description'       => ['nullable', 'string'],
            'price'             => ['required', 'numeric', 'min:0'],
            'compare_at_price'  => ['nullable', 'numeric', 'min:0'],
            'status'            => ['required', 'in:draft,active,inactive,sold_out'],
            'images.*'          => ['nullable', 'image', 'max:5120'],
            'attr_key.*'        => ['nullable', 'string', 'max:100'],
            'attr_value.*'      => ['nullable', 'string', 'max:300'],
        ]);

        $productData = $validated;
        unset($productData['images'], $productData['attr_key'], $productData['attr_value']);

        $keys   = $request->input('attr_key', []);
        $values = $request->input('attr_value', []);
        $attrs  = [];
        foreach ($keys as $i => $key) {
            if ($key !== '' && isset($values[$i]) && $values[$i] !== '') {
                $attrs[] = ['key' => $key, 'value' => $values[$i]];
            }
        }

        return ['product' => $productData, 'attributes' => $attrs];
    }
}
