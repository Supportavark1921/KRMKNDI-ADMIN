<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/auth/otp/send
     *
     * Generates an OTP for the given phone number.
     * Phase 2: logs the code instead of sending WhatsApp/SMS.
     * Phase 4: replace the log call with OtpSender::send().
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phoneNumber' => ['required', 'string', 'min:8', 'max:20'],
        ]);

        $phone = $request->input('phoneNumber');
        $otp   = OtpCode::generateFor($phone);

        // Phase 2: static code 123456 — log it for testing.
        // Phase 4: replace with real WhatsApp/SMS driver.
        Log::info('[OTP] code for '.$phone.': '.$otp->code);

        return response()->json([
            'success'     => true,
            'challengeId' => (string) $otp->id,
            'expiresAt'   => $otp->expires_at->toIso8601String(),
        ]);
    }

    /**
     * POST /api/auth/otp/verify
     *
     * Verifies the OTP, finds or creates the end-user, and returns a token.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phoneNumber' => ['required', 'string'],
            'otp'         => ['required', 'string', 'size:6'],
            'challengeId' => ['required', 'string'],
        ]);

        $phone = $request->input('phoneNumber');
        $code  = $request->input('otp');

        // Phase 2: accept the static demo code 123456 without DB check.
        $isStatic = ($code === '123456');
        if (! $isStatic && ! OtpCode::verifyFor($phone, $code)) {
            throw ValidationException::withMessages([
                'otp' => ['The code is incorrect or has expired.'],
            ]);
        }

        $user = User::firstOrCreate(
            ['phone' => $phone],
            [
                'name'     => 'User',
                'email'    => $phone.'@placeholder.krmkndi',
                'password' => \Illuminate\Support\Str::random(32),
                'role'     => 'user',
                'status'   => 'active',
            ]
        );

        $user->phone_verified_at = now();
        $user->save();

        // Revoke any previous mobile tokens for this user.
        $user->tokens()->where('name', 'mobile')->delete();

        $newToken  = $user->createToken('mobile');
        $expiresAt = $newToken->accessToken->expires_at;

        return response()->json([
            'success'   => true,
            'token'     => $newToken->plainTextToken,
            'expiresAt' => $expiresAt?->toIso8601String(),
            'user'      => [
                'id'    => $user->id,
                'name'  => $user->name,
                'phone' => $user->phone,
            ],
        ]);
    }

    /**
     * POST /api/auth/refresh
     * Issues a new token and revokes the current one — no re-login required.
     * Requires: Bearer token (auth:sanctum)
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();

        // Revoke the token used for this request.
        $user->currentAccessToken()->delete();

        $newToken  = $user->createToken('mobile');
        $expiresAt = $newToken->accessToken->expires_at;

        return response()->json([
            'success'   => true,
            'token'     => $newToken->plainTextToken,
            'expiresAt' => $expiresAt?->toIso8601String(),
        ]);
    }

    /**
     * POST /api/auth/logout
     * Requires: Bearer token (auth:sanctum)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['success' => true]);
    }

    /**
     * GET /api/auth/me
     * Requires: Bearer token (auth:sanctum)
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user'    => [
                'id'                => $user->id,
                'name'              => $user->name,
                'phone'             => $user->phone,
                'phone_verified_at' => $user->phone_verified_at?->toIso8601String(),
            ],
        ]);
    }
}
