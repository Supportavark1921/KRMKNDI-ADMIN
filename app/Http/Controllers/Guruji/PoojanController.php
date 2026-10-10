<?php

namespace App\Http\Controllers\Guruji;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruService;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PoojanController extends Controller
{
    private function myGuru(): Guru
    {
        $guru = Guru::where('user_id', auth()->id())->first();
        abort_unless($guru, 403, 'No Guruji profile linked to your account.');
        return $guru;
    }

    /** True if this service is assigned to the given Guru (direct or via guru_services). */
    private function isAssigned(Service $service, Guru $guru): bool
    {
        return (int) $service->guru_id === $guru->id
            || GuruService::where('guru_id', $guru->id)->where('service_id', $service->id)->exists();
    }

    public function index(): View
    {
        Gate::authorize('my-poojan.view');

        $guru = $this->myGuru();

        // Services directly assigned by admin (services.guru_id) OR via guru_services row
        $services = Service::with(['guruServices' => fn ($q) => $q->where('guru_id', $guru->id)])
            ->where(function ($q) use ($guru) {
                $q->where('guru_id', $guru->id)
                  ->orWhereHas('guruServices', fn ($q2) => $q2->where('guru_id', $guru->id));
            })
            ->latest()->get();

        return view('guruji.poojan.index', compact('services', 'guru'));
    }

    public function edit(Service $service): View
    {
        Gate::authorize('my-poojan.update');

        $guru = $this->myGuru();
        abort_unless($this->isAssigned($service, $guru), 403, 'This service is not assigned to you.');

        // Use existing row or an unsaved stub — no pricing yet is fine
        $gs = GuruService::firstOrNew(['guru_id' => $guru->id, 'service_id' => $service->id]);

        return view('guruji.poojan.edit', [
            'service'   => $service,
            'gs'        => $gs,
            'languages' => Service::SUPPORTED_LANGUAGES,
        ]);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        Gate::authorize('my-poojan.update');

        $guru = $this->myGuru();
        abort_unless($this->isAssigned($service, $guru), 403, 'This service is not assigned to you.');

        $langs = array_keys(Service::SUPPORTED_LANGUAGES);
        $rules = [
            'pricing_amount'          => ['required', 'integer', 'min:0'],
            'pricing_currency'        => ['required', 'string', 'max:10'],
            'pricing_discount_amount' => ['nullable', 'integer', 'min:0'],
            'pooja_samagri'           => ['nullable', 'array'],
            'pooja_samagri.*.name'    => ['required', 'string', 'max:200'],
            'pooja_samagri.*.price'   => ['nullable', 'numeric', 'min:0'],
        ];
        foreach ($langs as $lang) {
            $rules["translations.{$lang}.name"]        = ['nullable', 'string', 'max:120'];
            $rules["translations.{$lang}.title"]       = ['nullable', 'string', 'max:200'];
            $rules["translations.{$lang}.description"] = ['nullable', 'string', 'max:2000'];
        }
        $rules['translations.en.name'] = ['required', 'string', 'max:120'];

        $data = $request->validate($rules);

        // ── Translations ─────────────────────────────────────────────────────
        $translations = [];
        foreach ($data['translations'] ?? [] as $lang => $block) {
            $clean = array_filter($block, fn ($v) => $v !== null && $v !== '');
            if (! empty($clean)) {
                $translations[$lang] = $block;
            }
        }

        // ── Images ───────────────────────────────────────────────────────────
        $images  = $service->images ?? ['primary' => null, 'gallery' => []];
        $primary = $images['primary'];
        $gallery = $images['gallery'] ?? [];

        if ($request->hasFile('primary_image')) {
            if ($primary) {
                Storage::disk('public')->delete($primary);
            }
            $primary = $request->file('primary_image')->store('services/primary', 'public');
        }

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $gallery[] = $file->store('services/gallery', 'public');
            }
        }

        if ($remove = $request->input('remove_gallery')) {
            $toRemove = is_array($remove) ? $remove : json_decode($remove, true) ?? [];
            foreach ($toRemove as $path) {
                Storage::disk('public')->delete($path);
                $gallery = array_values(array_diff($gallery, [$path]));
            }
        }

        $service->update([
            'translations' => $translations,
            'images'       => ['primary' => $primary, 'gallery' => array_values($gallery)],
        ]);

        // ── GuruService row ──────────────────────────────────────────────────
        $samagri = [];
        foreach ($data['pooja_samagri'] ?? [] as $item) {
            $name = trim($item['name'] ?? '');
            if ($name === '') continue;
            $samagri[] = [
                'name'  => $name,
                'price' => isset($item['price']) && $item['price'] !== '' ? (float) $item['price'] : null,
            ];
        }

        $pricing = [
            'amount'          => (int) $data['pricing_amount'],
            'currency'        => $data['pricing_currency'],
            'discount_amount' => ($data['pricing_discount_amount'] ?? null) !== null && $data['pricing_discount_amount'] !== ''
                ? (int) $data['pricing_discount_amount'] : null,
        ];

        GuruService::updateOrCreate(
            ['guru_id' => $guru->id, 'service_id' => $service->id],
            ['pricing' => $pricing, 'pooja_samagri' => $samagri, 'status' => 'active']
        );

        activity()->causedBy(auth()->user())->performedOn($service)
            ->withProperties(['guru_id' => $guru->id])
            ->log('poojan_service_updated');

        return redirect()->route('my.poojan.index')
            ->with('success', 'Service updated.');
    }
}
