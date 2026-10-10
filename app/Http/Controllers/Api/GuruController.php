<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonationFeeConfig;
use App\Models\Guru;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class GuruController extends Controller
{
    /** GET /api/gurus */
    public function index(\Illuminate\Http\Request $request): JsonResponse
    {
        $lang = $request->query('lang', 'en');
        $gurus = Guru::active()->orderBy('name')->get()
            ->map(fn ($g) => $this->formatGuru($g, false, $lang));

        return response()->json(['data' => $gurus]);
    }

    /** GET /api/gurus/{guru} */
    public function show(Guru $guru, \Illuminate\Http\Request $request): JsonResponse
    {
        $lang = $request->query('lang', 'en');
        return response()->json(['data' => $this->formatGuru($guru, true, $lang)]);
    }

    /** GET /api/gurus/{guru}/donation-categories */
    public function categories(Guru $guru, \Illuminate\Http\Request $request): JsonResponse
    {
        $lang = $request->query('lang', 'en');
        $categories = $guru->activeCategories()->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->translatedField('name', $lang),
                'description' => $c->translatedField('description', $lang) ?: null,
                'image' => $c->image ? Storage::disk('public')->url($c->image) : null,
                'status' => $c->pivot->status,
            ]);

        $fee = DonationFeeConfig::active();

        return response()->json([
            'data' => [
                'guru' => $this->formatGuru($guru, false, $lang),
                'categories' => $categories,
                'fee_config' => [
                    'handling_charge' => $fee->handling_charge,
                    'gst_rate' => $fee->gst_rate,
                    'gst_label' => "GST @ {$fee->gst_rate}%",
                ],
            ],
        ]);
    }

    private function formatGuru(Guru $guru, bool $withStats = false, string $lang = 'en'): array
    {
        $result = [
            'id' => $guru->id,
            'name' => $guru->translatedField('name', $lang),
            'description' => $guru->translatedField('description', $lang) ?: null,
            'image' => $guru->image ? Storage::disk('public')->url($guru->image) : null,
            'background_image' => $guru->background_image ? Storage::disk('public')->url($guru->background_image) : null,
            'gallery' => collect($guru->gallery ?? [])
                ->map(fn ($path) => Storage::disk('public')->url($path))
                ->values()
                ->all(),
            'status' => $guru->status,
        ];

        if ($withStats) {
            $result['stats'] = [
                'total_donations' => $guru->totalDonations(),
                'total_donors' => $guru->totalDonors(),
                'total_transactions' => $guru->totalTransactions(),
            ];
        }

        return $result;
    }
}
