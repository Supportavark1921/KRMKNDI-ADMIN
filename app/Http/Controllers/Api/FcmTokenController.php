<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    /**
     * Register or update an FCM device token.
     *
     * POST /api/v1/fcm-token
     * Body: { "token": "...", "platform": "android" }
     *
     * Works for anonymous and authenticated users.
     * Upserts by token value; links user_id when a bearer token is present.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token'    => ['required', 'string', 'max:500'],
            'platform' => ['sometimes', 'string', 'in:android,ios'],
        ]);

        $userId = $request->user('sanctum')?->id;

        DeviceToken::updateOrCreate(
            ['token' => $data['token']],
            [
                'user_id'  => $userId,
                'platform' => $data['platform'] ?? 'android',
            ],
        );

        return response()->json(['success' => true]);
    }
}
