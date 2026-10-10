<?php

namespace App\Http\Controllers;

use App\Models\GuruService;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GuruServiceController extends Controller
{
    public function store(Request $request, Service $service): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $this->validated($request);

        GuruService::create([
            'guru_id'       => $data['guru_id'],
            'service_id'    => $service->id,
            'pricing'       => $this->buildPricing($data),
            'pooja_samagri' => $this->buildSamagri($data),
            'status'        => $data['gs_status'],
        ]);

        activity()->causedBy(auth()->user())->performedOn($service)
            ->withProperties(['guru_id' => $data['guru_id']])
            ->log('guru_service_added');

        return back()->with('success', 'Guruji pricing added.');
    }

    public function update(Request $request, Service $service, GuruService $guruService): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $data = $this->validated($request, forUpdate: true);

        $guruService->update([
            'pricing'       => $this->buildPricing($data),
            'pooja_samagri' => $this->buildSamagri($data),
            'status'        => $data['gs_status'],
        ]);

        activity()->causedBy(auth()->user())->performedOn($service)
            ->withProperties(['guru_service_id' => $guruService->id])
            ->log('guru_service_updated');

        return back()->with('success', 'Guruji pricing updated.');
    }

    public function destroy(Service $service, GuruService $guruService): RedirectResponse
    {
        Gate::authorize('manage-appointments');

        $guruService->delete();

        activity()->causedBy(auth()->user())->performedOn($service)
            ->withProperties(['guru_service_id' => $guruService->id])
            ->log('guru_service_removed');

        return back()->with('success', 'Guruji pricing removed.');
    }

    private function validated(Request $request, bool $forUpdate = false): array
    {
        $rules = [
            'pricing_amount'          => ['required', 'integer', 'min:0'],
            'pricing_currency'        => ['required', 'string', 'max:10'],
            'pricing_discount_amount' => ['nullable', 'integer', 'min:0'],
            'pooja_samagri'           => ['nullable', 'array'],
            'pooja_samagri.*.name'    => ['required', 'string', 'max:200'],
            'pooja_samagri.*.price'   => ['nullable', 'numeric', 'min:0'],
            'gs_status'               => ['required', 'in:active,inactive'],
        ];

        if (! $forUpdate) {
            $rules['guru_id'] = ['required', 'exists:gurus,id'];
        }

        return $request->validate($rules);
    }

    private function buildPricing(array $data): array
    {
        return [
            'amount'          => (int) $data['pricing_amount'],
            'currency'        => $data['pricing_currency'],
            'discount_amount' => isset($data['pricing_discount_amount']) && $data['pricing_discount_amount'] !== ''
                ? (int) $data['pricing_discount_amount']
                : null,
        ];
    }

    private function buildSamagri(array $data): array
    {
        $samagri = [];
        foreach ($data['pooja_samagri'] ?? [] as $item) {
            $name = trim($item['name'] ?? '');
            if ($name === '') continue;
            $samagri[] = [
                'name'  => $name,
                'price' => isset($item['price']) && $item['price'] !== '' ? (float) $item['price'] : null,
            ];
        }
        return $samagri;
    }
}
