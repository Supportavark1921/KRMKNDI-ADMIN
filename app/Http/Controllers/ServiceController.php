<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('manage-appointments');

        $query = Service::latest();

        if ($search = $request->query('search')) {
            $query->search($search);
        }

        if ($lang = $request->query('language')) {
            $query->whereRaw("JSON_TYPE(JSON_EXTRACT(translations, '$.".$lang."')) IS NOT NULL");
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return view('services.index', [
            'services' => $query->paginate(15)->withQueryString(),
            'languages' => Service::SUPPORTED_LANGUAGES,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('manage-appointments');

        return view('services.create', [
            'languages' => Service::SUPPORTED_LANGUAGES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $this->validated($request);
        $data['images'] = $this->handleImages($request);

        Service::create($data);

        return redirect()->route('services.index')
            ->with('success', 'Service created successfully.');
    }

    public function show(Service $service): View
    {
        Gate::authorize('manage-appointments');

        return view('services.show', [
            'service' => $service,
            'languages' => Service::SUPPORTED_LANGUAGES,
        ]);
    }

    public function edit(Service $service): View
    {
        Gate::authorize('manage-appointments');

        return view('services.edit', [
            'service' => $service,
            'languages' => Service::SUPPORTED_LANGUAGES,
        ]);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $this->validated($request, $service);
        $data['images'] = $this->handleImages($request, $service);

        $service->update($data);

        return redirect()->route('services.index')
            ->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        // Clean up stored images
        if ($primary = $service->primaryImage()) {
            Storage::disk('public')->delete($primary);
        }
        foreach ($service->gallery() as $path) {
            Storage::disk('public')->delete($path);
        }

        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Service deleted.');
    }

    // ── Private ──────────────────────────────────────────────────────────────

    private function validated(Request $request, ?Service $service = null): array
    {
        $langs = array_keys(Service::SUPPORTED_LANGUAGES);

        $rules = [
            'status' => ['required', 'in:active,inactive'],
            'pricing.amount' => ['required', 'integer', 'min:0', 'max:9999999'],
            'pricing.currency' => ['required', 'string', 'max:10'],
            'pricing.discount_amount' => ['nullable', 'integer', 'min:0', 'max:9999999'],
            'pooja_samagri'           => ['nullable', 'array'],
            'pooja_samagri.*.name'    => ['required', 'string', 'max:200'],
            'pooja_samagri.*.price'   => ['nullable', 'numeric', 'min:0'],
        ];

        // Require EN name; others are optional
        foreach ($langs as $lang) {
            $required = $lang === Service::DEFAULT_LANGUAGE ? 'required_with:translations.'.$lang.'.title,translations.'.$lang.'.description' : 'nullable';
            $rules["translations.{$lang}.name"] = [$required, 'nullable', 'string', 'max:120'];
            $rules["translations.{$lang}.title"] = ['nullable', 'string', 'max:200'];
            $rules["translations.{$lang}.description"] = ['nullable', 'string', 'max:2000'];
        }

        // English name always required
        $rules['translations.en.name'] = ['required', 'string', 'max:120'];

        $data = $request->validate($rules);

        // Strip empty language blocks
        $translations = [];
        foreach ($data['translations'] ?? [] as $lang => $block) {
            $clean = array_filter($block, fn ($v) => $v !== null && $v !== '');
            if (! empty($clean)) {
                $translations[$lang] = $block;
            }
        }
        $data['translations'] = $translations;

        // Clean pricing nulls
        $pricing = $data['pricing'] ?? [];
        if (($pricing['discount_amount'] ?? null) === '') {
            $pricing['discount_amount'] = null;
        }
        $data['pricing'] = $pricing;

        // Clean samagri: drop rows with blank name, cast price to float or null, re-index
        $samagri = [];
        foreach ($data['pooja_samagri'] ?? [] as $item) {
            $name = trim($item['name'] ?? '');
            if ($name === '') continue;
            $samagri[] = [
                'name'  => $name,
                'price' => isset($item['price']) && $item['price'] !== '' ? (float) $item['price'] : null,
            ];
        }
        $data['pooja_samagri'] = $samagri;

        return $data;
    }

    private function handleImages(Request $request, ?Service $service = null): array
    {
        $existing = $service?->images ?? ['primary' => null, 'gallery' => []];
        $primary = $existing['primary'];
        $gallery = $existing['gallery'] ?? [];

        // Primary image
        if ($request->hasFile('primary_image')) {
            if ($primary) {
                Storage::disk('public')->delete($primary);
            }
            $primary = $request->file('primary_image')->store('services/primary', 'public');
        }

        // Gallery additions
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $gallery[] = $file->store('services/gallery', 'public');
            }
        }

        // Gallery removals (sent as JSON array of paths to remove)
        if ($remove = $request->input('remove_gallery')) {
            $toRemove = is_array($remove) ? $remove : json_decode($remove, true) ?? [];
            foreach ($toRemove as $path) {
                Storage::disk('public')->delete($path);
                $gallery = array_values(array_diff($gallery, [$path]));
            }
        }

        // Gallery reorder (sent as comma-separated ordered paths)
        if ($order = $request->input('gallery_order')) {
            $ordered = array_filter(explode(',', $order));
            $gallery = array_values(array_intersect($ordered, $gallery));
        }

        return ['primary' => $primary, 'gallery' => array_values($gallery)];
    }
}
