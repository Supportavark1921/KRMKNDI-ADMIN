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
    public function index(): JsonResponse
    {
        $gurus = Guru::active()->orderBy('name')->get()
            ->map(fn ($g) => $this->formatGuru($g));

        return response()->json(['data' => $gurus]);
    }

    /** GET /api/gurus/{guru} */
    public function show(Guru $guru): JsonResponse
    {
        return response()->json(['data' => $this->formatGuru($guru, true)]);
    }

    /** GET /api/gurus/{guru}/donation-categories */
    public function categories(Guru $guru): JsonResponse
    {
        $categories = $guru->activeCategories()->get()
            ->map(fn ($c) => [
                'id'          => $c->id,
                'name'        => $c->name,
                'description' => $c->description,
                'image'       => $c->image ? Storage::disk('public')->url($c->image) : null,
                'status'      => $c->pivot->status,
            ]);

        $fee = DonationFeeConfig::active();

        return response()->json([
            'data' => [
                'guru'       => $this->formatGuru($guru),
                'categories' => $categories,
                'fee_config' => [
                    'handling_charge' => $fee->handling_charge,
                    'gst_rate'        => $fee->gst_rate,
                    'gst_label'       => "GST @ {$fee->gst_rate}%",
                ],
            ],
        ]);
    }

    private function formatGuru(Guru $guru, bool $withStats = false): array
    {
        $result = [
            'id'          => $guru->id,
            'name'        => $guru->name,
            'description' => $guru->description,
            'image'       => $guru->image ? Storage::disk('public')->url($guru->image) : null,
            'status'      => $guru->status,
        ];

        if ($withStats) {
            $result['stats'] = [
                'total_donations'   => $guru->totalDonations(),
                'total_donors'      => $guru->totalDonors(),
                'total_transactions'=> $guru->totalTransactions(),
            ];
        }

        return $result;
    }
}
