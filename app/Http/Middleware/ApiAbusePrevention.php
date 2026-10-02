<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiAbusePrevention
{
    /**
     * Per-endpoint rate limit definitions.
     *
     * Format: prefix => [maxAttempts, decaySeconds]
     * Matched by checking if the request path starts with the prefix.
     * The first match wins; order matters.
     */
    private const LIMITS = [
        'api/v1/auth'      => [10,  300],  // 10 per 5 min  (OTP / auth flows)
        'api/v1/donations' => [5,   60],   // 5  per min    (payment mutations)
        'api/v1/panchang'  => [20,  60],   // 20 per min    (astro lookups)
    ];

    private const DEFAULT_LIMIT = [120, 60]; // 120 per min for everything else

    public function handle(Request $request, Closure $next): Response
    {
        // Laravel admin panel — skip rate limiting for session-authenticated requests.
        if ($request->hasSession() && $request->user('web')) {
            return $next($request);
        }

        [$max, $decay] = $this->limitsFor($request->path());

        // Layer 1: per-IP
        $ipKey = 'abuse:ip:'.$request->ip().':'.$this->pathBucket($request->path());
        if (RateLimiter::tooManyAttempts($ipKey, $max)) {
            return $this->throttled(RateLimiter::availableIn($ipKey));
        }
        RateLimiter::hit($ipKey, $decay);

        // Layer 2: per-authenticated-user (when a Bearer token is present)
        $token = $request->bearerToken();
        if ($token) {
            $userKey = 'abuse:user:'.hash('sha256', $token).':'.$this->pathBucket($request->path());
            $userMax = (int) ($max * 0.5); // authenticated callers get half the IP budget
            if (RateLimiter::tooManyAttempts($userKey, $userMax)) {
                return $this->throttled(RateLimiter::availableIn($userKey));
            }
            RateLimiter::hit($userKey, $decay);
        }

        $response = $next($request);

        // Expose rate-limit state to the client
        $remaining = RateLimiter::remaining($ipKey, $max);
        return $response
            ->header('X-RateLimit-Limit', $max)
            ->header('X-RateLimit-Remaining', max(0, $remaining));
    }

    private function limitsFor(string $path): array
    {
        foreach (self::LIMITS as $prefix => $config) {
            if (str_starts_with($path, $prefix)) {
                return $config;
            }
        }
        return self::DEFAULT_LIMIT;
    }

    /** Collapse path to a bucket key (strip IDs / UUIDs). */
    private function pathBucket(string $path): string
    {
        return preg_replace('/[0-9a-f\-]{8,}/i', '*', $path) ?? $path;
    }

    private function throttled(int $retryAfter): Response
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => 'RATE_LIMITED',
                'message' => 'Too many requests. Please wait before trying again.',
            ],
        ], 429)->header('Retry-After', $retryAfter);
    }
}
