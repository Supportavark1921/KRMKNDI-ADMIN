<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookingFeeConfig;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    /**
     * GET /api/booking/charges
     *
     * Returns the platform service fee and GST breakdown.
     * Used by the app to display charges on the booking summary screen.
     */
    public function charges(): JsonResponse
    {
        $config = BookingFeeConfig::active();

        return response()->json([
            'success' => true,
            'data' => [
                'platform_fee' => $config->platform_fee,
                'gst_rate'     => $config->gst_rate,
                'gst_label'    => 'GST @ '.$config->gst_rate.'%',
            ],
        ]);
    }
}
