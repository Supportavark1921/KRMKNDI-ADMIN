<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('promotions.view');

        $query = Promotion::withTrashed();

        if ($s = $request->query('search')) {
            $query->where('title', 'like', "%{$s}%");
        }
        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }
        if ($placement = $request->query('placement')) {
            $query->where('placement', $placement);
        }
        if ($status = $request->query('status')) {
            if ($status === 'trashed') {
                $query->onlyTrashed();
            } else {
                $query->where('status', $status)->withoutTrashed();
            }
        }
        if ($request->query('live')) {
            $query->live()->withoutTrashed();
        }

        return view('admin.promotions.index', [
            'promotions' => $query->orderBy('sort_order')->orderByDesc('updated_at')->paginate(20)->withQueryString(),
            'types' => Promotion::TYPES,
            'placements' => Promotion::PLACEMENTS,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('promotions.create');

        return view('admin.promotions.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('promotions.create');

        $data = $this->validatePromotion($request);
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('promotions', 'public');
        }

        Promotion::create($data);

        return redirect()->route('admin.promotions.index')->with('success', 'Promotion created.');
    }

    public function edit(Promotion $promotion): View
    {
        Gate::authorize('promotions.update');

        return view('admin.promotions.edit', array_merge($this->formData(), ['promotion' => $promotion]));
    }

    public function update(Request $request, Promotion $promotion): RedirectResponse
    {
        Gate::authorize('promotions.update');

        $data = $this->validatePromotion($request, $promotion->id);
        $data['updated_by'] = auth()->id();

        if ($request->hasFile('image')) {
            if ($promotion->image) {
                Storage::disk('public')->delete($promotion->image);
            }
            $data['image'] = $request->file('image')->store('promotions', 'public');
        }

        $promotion->update($data);

        return redirect()->route('admin.promotions.index')->with('success', 'Promotion updated.');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        Gate::authorize('promotions.delete');
        $promotion->delete(); // soft delete only

        return redirect()->route('admin.promotions.index')->with('success', 'Promotion archived.');
    }

    public function restore(int $id): RedirectResponse
    {
        Gate::authorize('promotions.restore');
        Promotion::withTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Promotion restored.');
    }

    public function activate(Promotion $promotion): RedirectResponse
    {
        Gate::authorize('promotions.update');
        $promotion->update(['status' => 'active', 'updated_by' => auth()->id()]);

        return back()->with('success', 'Promotion activated.');
    }

    public function deactivate(Promotion $promotion): RedirectResponse
    {
        Gate::authorize('promotions.update');
        $promotion->update(['status' => 'inactive', 'updated_by' => auth()->id()]);

        return back()->with('success', 'Promotion deactivated.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function formData(): array
    {
        return [
            'types'      => Promotion::TYPES,
            'placements' => Promotion::PLACEMENTS,
            'ctaTypes'   => Promotion::CTA_TYPES,
            'audiences'  => Promotion::AUDIENCES,
            'statuses'   => Promotion::STATUSES,
            'ctaProducts'   => \App\Models\Product::where('status', 'active')->orderBy('name')->get(['id', 'name', 'product_code']),
            'ctaCategories' => \App\Models\ProductCategory::where('status', 'active')->orderBy('name')->get(['id', 'name', 'parent_id']),
            'ctaServices'   => \App\Models\Service::where('status', 'active')->orderBy('id')->get(['id', 'translations']),
            'ctaMatajis'    => \App\Models\Mataji::where('status', 'active')->orderBy('name')->get(['id', 'name']),
        ];
    }

    private function validatePromotion(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'type' => ['required', 'in:'.implode(',', Promotion::TYPES)],
            'placement' => ['required', 'in:'.implode(',', Promotion::PLACEMENTS)],
            'status' => ['required', 'in:'.implode(',', Promotion::STATUSES)],
            'cta_type' => ['required', 'in:'.implode(',', Promotion::CTA_TYPES)],
            'cta_value' => ['nullable', 'string', 'max:500'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'audience' => ['required', 'in:'.implode(',', Promotion::AUDIENCES)],
            // Translations: hi, en optional
            'translations.hi.title' => ['nullable', 'string', 'max:200'],
            'translations.hi.description' => ['nullable', 'string', 'max:2000'],
        ]);

        // Build translations from sub-keys
        $data['translations'] = array_filter([
            'hi' => array_filter([
                'title' => $request->input('translations.hi.title'),
                'description' => $request->input('translations.hi.description'),
            ]),
        ]);

        unset($data['image']); // handled separately
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
