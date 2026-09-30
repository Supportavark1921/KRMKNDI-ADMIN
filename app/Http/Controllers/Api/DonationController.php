<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationFeeConfig;
use App\Models\Guru;
use App\Models\DonationCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    /** GET /api/donation/fee-config */
    public function feeConfig(): JsonResponse
    {
        $fee = DonationFeeConfig::active();

        return response()->json([
            'data' => [
                'handling_charge'    => $fee->handling_charge,
                'gst_rate'           => $fee->gst_rate,
                'gst_label'          => "GST @ {$fee->gst_rate}%",
                'example_on_500'     => $fee->calculate(500),
            ],
        ]);
    }

    /**
     * POST /api/donations
     *
     * Request:
     *   guru_id, category_id, donation_amount, user_id?,
     *   payment_status, payment_id?, transaction_id?, payment_method?
     *
     * Backend calculates: handling_charge, gst_rate, gst_amount, total_amount
     * Idempotent on transaction_id.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'guru_id'         => ['required', 'integer', 'exists:gurus,id'],
            'category_id'     => ['required', 'integer', 'exists:donation_categories,id'],
            'donation_amount' => ['required', 'numeric', 'min:1', 'max:9999999'],
            'user_id'         => ['nullable', 'integer', 'exists:users,id'],
            'payment_status'  => ['required', 'in:pending,success,failed,cancelled'],
            'payment_id'      => ['nullable', 'string', 'max:200'],
            'transaction_id'  => ['nullable', 'string', 'max:200'],
            'payment_method'  => ['nullable', 'string', 'max:50'],
        ]);

        // Idempotency: return existing record if same transaction_id
        if (!empty($data['transaction_id'])) {
            $existing = Donation::where('transaction_id', $data['transaction_id'])->first();
            if ($existing) {
                return response()->json([
                    'data'    => $this->formatDonation($existing->load(['guru', 'category'])),
                    'created' => false,
                ], 200);
            }
        }

        // Server-side fee calculation — never trust client total
        $fee     = DonationFeeConfig::active();
        $amounts = $fee->calculate((float) $data['donation_amount']);

        $donation = Donation::create([
            'user_id'         => $data['user_id'] ?? null,
            'guru_id'         => $data['guru_id'],
            'category_id'     => $data['category_id'],
            'donation_amount' => $amounts['donation_amount'],
            'handling_charge' => $amounts['handling_charge'],
            'gst_rate'        => $amounts['gst_rate'],
            'gst_amount'      => $amounts['gst_amount'],
            'total_amount'    => $amounts['total_amount'],
            'currency'        => 'INR',
            'payment_status'  => $data['payment_status'],
            'payment_id'      => $data['payment_id'] ?? null,
            'transaction_id'  => $data['transaction_id'] ?? null,
            'payment_method'  => $data['payment_method'] ?? null,
        ]);

        return response()->json([
            'data'    => $this->formatDonation($donation->load(['guru', 'category'])),
            'created' => true,
        ], 201);
    }

    private function formatDonation(Donation $d): array
    {
        return [
            'id'              => $d->id,
            'donation_id'     => $d->donation_id,
            'guru'            => ['id' => $d->guru_id, 'name' => $d->guru->name ?? null],
            'category'        => ['id' => $d->category_id, 'name' => $d->category->name ?? null],
            'breakdown' => [
                'donation_amount' => $d->donation_amount,
                'handling_charge' => $d->handling_charge,
                'gst_rate'        => $d->gst_rate,
                'gst_amount'      => $d->gst_amount,
                'total_amount'    => $d->total_amount,
                'currency'        => $d->currency,
            ],
            'payment_status'  => $d->payment_status,
            'payment_id'      => $d->payment_id,
            'transaction_id'  => $d->transaction_id,
            'payment_method'  => $d->payment_method,
            'created_at'      => $d->created_at?->toISOString(),
        ];
    }
}
