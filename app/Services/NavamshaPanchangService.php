<?php

namespace App\Services;

use App\Models\NavamshaApiLog;
use App\Models\PanchangCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NavamshaPanchangService
{
    // Rounding precision for location normalisation (2 decimal places ≈ ~1 km grid)
    private const PRECISION = 2;

    private const BASE_URL    = 'https://api.navamsha.in';
    private const ENDPOINT    = '/api/v1/panchang/full';
    private const LOCK_TTL    = 30;   // seconds to hold the distributed lock
    private const LOCK_WAIT   = 10;   // seconds a waiting request polls for the lock

    // ── Public entry point ────────────────────────────────────────────────────

    /**
     * Return Panchang for the given location / date / feature.
     *
     * @param  float  $latitude
     * @param  float  $longitude
     * @param  float  $timezone   UTC offset, e.g. 5.5 for IST
     * @param  string $date       Y-m-d
     * @param  string $feature    panchang_full | choghadiya | sun_times …
     * @param  string $locationName  optional display label
     * @return array{success:bool, cached:bool, date:string, location:array, data:array|null, error:array|null}
     */
    public function getPanchang(
        float  $latitude,
        float  $longitude,
        float  $timezone,
        string $date,
        string $feature = 'panchang_full',
        string $locationName = ''
    ): array {
        [$normLat, $normLon] = $this->normalizeLocation($latitude, $longitude);
        $cacheKey = $this->generateCacheKey($date, $normLat, $normLon, $timezone, $feature);

        // ── First cache check (no lock) ───────────────────────────────────────
        $cached = $this->checkCache($cacheKey);
        if ($cached) {
            return $this->formatResponse($cached, true);
        }

        // ── Acquire distributed lock to prevent duplicate Navamsha calls ──────
        $lockKey = 'panchang_lock:' . $cacheKey;
        $lock    = Cache::lock($lockKey, self::LOCK_TTL);

        try {
            if (! $lock->block(self::LOCK_WAIT)) {
                // Could not get lock within wait window — return stale or error
                $cached = $this->checkCache($cacheKey);
                if ($cached) {
                    return $this->formatResponse($cached, true);
                }
                return $this->errorResponse('LOCK_TIMEOUT', 'Panchang data is temporarily unavailable. Please retry.');
            }

            // ── Second cache check (inside lock) ──────────────────────────────
            $cached = $this->checkCache($cacheKey);
            if ($cached) {
                return $this->formatResponse($cached, true);
            }

            // ── Call Navamsha ─────────────────────────────────────────────────
            $result = $this->callNavamsha($latitude, $longitude, $timezone, $date, $feature, $cacheKey);

            if (! $result['success']) {
                return $this->errorResponse('PANCHANG_UNAVAILABLE', $result['error'] ?? 'Panchang data is temporarily unavailable.');
            }

            // ── Persist & return ──────────────────────────────────────────────
            $record = $this->saveResponse(
                cacheKey:    $cacheKey,
                date:        $date,
                latitude:    $latitude,
                longitude:   $longitude,
                normLat:     $normLat,
                normLon:     $normLon,
                timezone:    $timezone,
                feature:     $feature,
                locationName:$locationName,
                apiResponse: $result['raw'],
                panchangData:$result['data'],
            );

            return $this->formatResponse($record, false);

        } finally {
            $lock->release();
        }
    }

    // ── Cache helpers ─────────────────────────────────────────────────────────

    public function generateCacheKey(
        string $date,
        float  $normLat,
        float  $normLon,
        float  $timezone,
        string $feature = 'panchang_full'
    ): string {
        return sprintf('%s:%.2f:%.2f:%.1f:%s', $date, $normLat, $normLon, $timezone, $feature);
    }

    public function normalizeLocation(float $lat, float $lon): array
    {
        $p = 10 ** self::PRECISION;
        return [
            round(round($lat * $p) / $p, self::PRECISION),
            round(round($lon * $p) / $p, self::PRECISION),
        ];
    }

    private function checkCache(string $cacheKey): ?PanchangCache
    {
        $record = PanchangCache::where('cache_key', $cacheKey)->first();

        if (! $record) {
            return null;
        }

        // Always return historical records; only filter future expiry for "today" data
        return $record;
    }

    // ── Navamsha API client ───────────────────────────────────────────────────

    private function callNavamsha(
        float  $latitude,
        float  $longitude,
        float  $timezone,
        string $date,
        string $feature,
        string $cacheKey
    ): array {
        $apiKey   = config('services.navamsha.key');
        $endpoint = self::BASE_URL . $this->endpointPath($feature);

        $parsed = Carbon::createFromFormat('Y-m-d', $date);

        $payload = [
            'year'      => (int) $parsed->format('Y'),
            'month'     => (int) $parsed->format('m'),
            'date'      => (int) $parsed->format('d'),
            'hours'     => 6,
            'minutes'   => 0,
            'seconds'   => 0,
            'latitude'  => $latitude,
            'longitude' => $longitude,
            'timezone'  => (string) $timezone,
        ];

        $start = microtime(true);
        $httpStatus = null;
        $success    = false;
        $errorMsg   = null;

        try {
            $response = Http::withHeaders(['X-API-Key' => $apiKey])
                ->timeout(15)
                ->withOptions(['verify' => config('services.navamsha.verify_ssl', true)])
                ->post($endpoint, $payload);

            $httpStatus = $response->status();
            $elapsed    = (int) ((microtime(true) - $start) * 1000);

            if ($response->successful()) {
                $body = $response->json();
                $success = true;

                $this->log($endpoint, $cacheKey, $feature, $date, $latitude, $longitude, $timezone, $httpStatus, $elapsed, true);

                return ['success' => true, 'raw' => $body, 'data' => $this->extractPanchangData($body)];
            }

            $errorMsg = 'HTTP ' . $httpStatus . ': ' . $response->body();

        } catch (\Throwable $e) {
            $elapsed  = (int) ((microtime(true) - $start) * 1000);
            $errorMsg = $e->getMessage();
            Log::error('Navamsha API error', ['error' => $errorMsg, 'cache_key' => $cacheKey]);
        }

        $this->log($endpoint, $cacheKey, $feature, $date, $latitude, $longitude, $timezone, $httpStatus, $elapsed ?? 0, false, $errorMsg);

        return ['success' => false, 'error' => $errorMsg];
    }

    private function endpointPath(string $feature): string
    {
        return match ($feature) {
            'choghadiya'    => '/api/v1/panchang/choghadiya',
            'hora'          => '/api/v1/panchang/hora',
            'rahu_kaal'     => '/api/v1/panchang/rahu-kaal',
            'sun_times'     => '/api/v1/sun-times',
            'abhijit'       => '/api/v1/panchang/abhijit',
            default         => self::ENDPOINT,
        };
    }

    private function extractPanchangData(array $raw): array
    {
        // Return the full response as structured data; the APK picks what it needs.
        // If Navamsha wraps in a 'data' key, unwrap it.
        return $raw['data'] ?? $raw;
    }

    // ── Persistence ───────────────────────────────────────────────────────────

    private function saveResponse(
        string $cacheKey,
        string $date,
        float  $latitude,
        float  $longitude,
        float  $normLat,
        float  $normLon,
        float  $timezone,
        string $feature,
        string $locationName,
        array  $apiResponse,
        array  $panchangData
    ): PanchangCache {
        // Expire at end of local calendar day
        $expiresAt = Carbon::createFromFormat('Y-m-d', $date)
            ->setTime(23, 59, 59)
            ->subHours((int) $timezone)
            ->subMinutes((int) (($timezone - (int) $timezone) * 60));

        return PanchangCache::updateOrCreate(
            ['cache_key' => $cacheKey],
            [
                'date'                 => $date,
                'latitude'             => $latitude,
                'longitude'            => $longitude,
                'normalized_latitude'  => $normLat,
                'normalized_longitude' => $normLon,
                'timezone'             => $timezone,
                'location_name'        => $locationName ?: null,
                'feature'              => $feature,
                'api_endpoint'         => self::BASE_URL . $this->endpointPath($feature),
                'api_response'         => $apiResponse,
                'panchang_data'        => $panchangData,
                'api_status'           => 'success',
                'fetched_at'           => now(),
                'expires_at'           => $expiresAt,
            ]
        );
    }

    // ── Logging ───────────────────────────────────────────────────────────────

    private function log(
        string  $endpoint,
        string  $cacheKey,
        string  $feature,
        string  $date,
        float   $latitude,
        float   $longitude,
        float   $timezone,
        ?int    $httpStatus,
        int     $responseTimeMs,
        bool    $success,
        ?string $errorMessage = null
    ): void {
        try {
            NavamshaApiLog::create([
                'endpoint'        => $endpoint,
                'cache_key'       => $cacheKey,
                'feature'         => $feature,
                'request_date'    => $date,
                'latitude'        => $latitude,
                'longitude'       => $longitude,
                'timezone'        => $timezone,
                'http_status'     => $httpStatus,
                'response_time_ms'=> $responseTimeMs,
                'success'         => $success,
                'error_message'   => $errorMessage,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to write Navamsha API log: ' . $e->getMessage());
        }
    }

    // ── Response formatting ───────────────────────────────────────────────────

    private function formatResponse(PanchangCache $record, bool $cached): array
    {
        return [
            'success'  => true,
            'cached'   => $cached,
            'date'     => $record->date->format('Y-m-d'),
            'location' => [
                'name'      => $record->location_name,
                'latitude'  => $record->latitude,
                'longitude' => $record->longitude,
                'timezone'  => $record->timezone,
            ],
            'data'     => $record->panchang_data,
        ];
    }

    private function errorResponse(string $code, string $message): array
    {
        return [
            'success' => false,
            'cached'  => false,
            'error'   => ['code' => $code, 'message' => $message],
        ];
    }

    // ── Admin stats ───────────────────────────────────────────────────────────

    public function stats(): array
    {
        $today = today()->toDateString();
        $month = today()->format('Y-m');

        $callsToday = NavamshaApiLog::where('request_date', $today)->count();
        $callsMonth = NavamshaApiLog::whereYear('request_date', today()->year)
            ->whereMonth('request_date', today()->month)->count();
        $totalCache = PanchangCache::count();
        $missesToday= NavamshaApiLog::where('request_date', $today)->count(); // each log = a miss
        $hitsToday  = max(0, $callsToday === 0 ? 0 : 0); // placeholder — track separately if needed
        $errorsToday= NavamshaApiLog::where('request_date', $today)->where('success', false)->count();

        return compact('callsToday', 'callsMonth', 'totalCache', 'missesToday', 'errorsToday');
    }
}
