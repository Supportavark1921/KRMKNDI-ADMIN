<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    /**
     * Store or update the FCM device token for the authenticated user.
     *
     * POST /api/v1/fcm-token
     * Body: { "token": "..." }
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'max:500'],
        ]);

        $request->user()->update(['fcm_token' => $data['token']]);

        return response()->json(['success' => true]);
    }

    /**
     * Remove the FCM token (user logged out from device).
     *
     * DELETE /api/v1/fcm-token
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->update(['fcm_token' => null]);

        return response()->json(['success' => true]);
    }
}
