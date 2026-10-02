<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * GET /api/services?language=en&status=active
     */
    public function index(Request $request): JsonResponse
    {
        $lang = $request->query('language', Service::DEFAULT_LANGUAGE);
        $status = $request->query('status', 'active');

        $services = Service::when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->get()
            ->map(fn ($s) => $this->format($s, $lang));

        return response()->json(['data' => $services]);
    }

    /**
     * GET /api/services/{id}?language=en
     */
    public function show(Request $request, Service $service): JsonResponse
    {
        $lang = $request->query('language', Service::DEFAULT_LANGUAGE);

        return response()->json(['data' => $this->format($service, $lang)]);
    }

    // ── Private ──────────────────────────────────────────────────────────────

    private function format(Service $service, string $lang): array
    {
        $fallback = Service::DEFAULT_LANGUAGE;
        $t = $service->translations ?? [];
        $langData = $t[$lang] ?? $t[$fallback] ?? [];

        return [
            'id' => $service->id,
            'name' => $langData['name'] ?? null,
            'title' => $langData['title'] ?? null,
            'description' => $langData['description'] ?? null,
            'language' => array_key_exists($lang, $t) ? $lang : $fallback,
            'fallback_used' => ! array_key_exists($lang, $t),
            'available_languages' => array_keys($t),
            'primary_image' => $service->primaryImage()
                ? Storage::disk('public')->url($service->primaryImage())
                : null,
            'gallery' => array_map(
                fn ($p) => Storage::disk('public')->url($p),
                $service->gallery()
            ),
            'pricing' => [
                'amount' => $service->amount(),
                'currency' => $service->currency(),
                'discount_amount' => $service->discountAmount(),
                'formatted' => $service->amount() !== null
                    ? '₹'.number_format($service->amount())
                    : null,
            ],
            'status' => $service->status,
            'created_at' => $service->created_at?->toISOString(),
            'updated_at' => $service->updated_at?->toISOString(),
        ];
    }
}
