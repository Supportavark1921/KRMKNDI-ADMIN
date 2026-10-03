<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class VerifyAppSignature
{
    // Accept requests within ±5 minutes of server time.
    private const WINDOW_SECONDS = 300;

    // Keep nonces in cache for 2× the window to cover edge cases.
    private const NONCE_TTL_SECONDS = 600;

    // Routes that are fully public — no token or signature required.
    private const PUBLIC_PREFIXES = [
        'api/gurus',
        'api/v1/panchang',
        'api/v1/promotions',
        'api/services',
        'api/availability',
        'api/openapi.json',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Laravel admin panel — already authenticated via session; skip APK signature check.
        if ($request->hasSession() && $request->user('web')) {
            return $next($request);
        }

        // Fully public endpoints — no signature needed.
        foreach (self::PUBLIC_PREFIXES as $prefix) {
            if (str_starts_with($request->path(), $prefix)) {
                return $next($request);
            }
        }

        $appId = $request->header('X-App-Id');
        $timestamp = $request->header('X-Timestamp');
        $nonce = $request->header('X-Nonce');
        $signature = $request->header('X-Signature');

        // ── Presence check ────────────────────────────────────────────────────
        if (! $appId || ! $timestamp || ! $nonce || ! $signature) {
            return $this->reject('MISSING_SIGNATURE', 'Request signature headers are required.');
        }

        // ── Timestamp window ──────────────────────────────────────────────────
        $ts = (int) $timestamp;
        $tsSeconds = $ts > 9_999_999_999 ? (int) ($ts / 1000) : $ts; // support ms or s
        $drift = abs(time() - $tsSeconds);

        if ($drift > self::WINDOW_SECONDS) {
            return $this->reject('TIMESTAMP_EXPIRED', 'Request timestamp is outside the accepted window.');
        }

        // ── Replay protection — nonce dedup ───────────────────────────────────
        $nonceKey = 'api_nonce:'.hash('sha256', $appId.':'.$nonce);

        // Cache::add is atomic: returns false if the key already exists.
        if (! Cache::add($nonceKey, 1, self::NONCE_TTL_SECONDS)) {
            return $this->reject('NONCE_REPLAYED', 'This request has already been processed.');
        }

        // ── Signature verification ────────────────────────────────────────────
        $secret = $this->secretFor($appId);

        if (! $secret) {
            Cache::forget($nonceKey);
            return $this->reject('UNKNOWN_APP', 'Unknown application identifier.');
        }

        $method = strtoupper($request->method());
        $path = '/'.ltrim($request->path(), '/');
        $message = implode("\n", [$method, $path, $timestamp, $nonce]);
        $expected = hash_hmac('sha256', $message, $secret);

        if (! hash_equals($expected, strtolower($signature))) {
            Cache::forget($nonceKey);
            if (! $this->enforcing()) {
                \Illuminate\Support\Facades\Log::warning('VerifyAppSignature: invalid signature (non-enforcing)', [
                    'app_id' => $appId,
                    'path' => $request->path(),
                    'ip' => $request->ip(),
                ]);
                return $next($request);
            }
            return $this->reject('INVALID_SIGNATURE', 'Request signature is invalid.');
        }

        return $next($request);
    }

    private function secretFor(string $appId): ?string
    {
        // Support per-app-id secrets via APP_SIGNING_SECRET_{UPPER_APP_ID},
        // falling back to the single shared APP_SIGNING_SECRET.
        $envKey = 'APP_SIGNING_SECRET_'.strtoupper(preg_replace('/[^A-Z0-9]/i', '_', $appId));
        return env($envKey) ?: env('APP_SIGNING_SECRET');
    }

    private function enforcing(): bool
    {
        return filter_var(env('APP_SIGNATURE_ENFORCE', true), FILTER_VALIDATE_BOOLEAN);
    }

    private function reject(string $code, string $message): Response
    {
        return response()->json([
            'success' => false,
            'error' => ['code' => $code, 'message' => $message],
        ], 401);
    }
}
