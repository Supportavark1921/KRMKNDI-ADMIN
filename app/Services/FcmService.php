<?php

namespace App\Services;

use App\Models\DeviceToken;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    private const FCM_ENDPOINT = 'https://fcm.googleapis.com/v1/projects/{project_id}/messages:send';

    /**
     * Send a notification to all devices belonging to the given user ids.
     *
     * @param  int[]  $userIds
     * @param  array<string,string>  $data
     * @return array{sent: int, failed: int}
     */
    public function sendToUsers(array $userIds, string $title, string $body, array $data = [], array $options = []): array
    {
        $tokens = DeviceToken::whereIn('user_id', $userIds)->pluck('token')->all();

        return $this->sendToTokens($tokens, $title, $body, $data, $options);
    }

    /**
     * Send a notification to every registered device.
     *
     * @param  array<string,string>  $data
     * @param  array{color?:string,image?:string}  $options
     * @return array{sent: int, failed: int}
     */
    public function sendToAll(string $title, string $body, array $data = [], array $options = []): array
    {
        $tokens = DeviceToken::pluck('token')->all();

        return $this->sendToTokens($tokens, $title, $body, $data, $options);
    }

    /**
     * @param  string[]  $tokens
     * @param  array<string,string>  $data
     * @param  array{color?:string,image?:string}  $options
     * @return array{sent: int, failed: int}
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = [], array $options = []): array
    {
        if (empty($tokens)) {
            return ['sent' => 0, 'failed' => 0, 'error' => 'No registered device tokens found.'];
        }

        // Validate config before looping — avoids pointless per-token failures.
        if (! config('firebase.project_id')) {
            return ['sent' => 0, 'failed' => count($tokens), 'error' => 'FIREBASE_PROJECT_ID is not set in .env'];
        }

        ['token' => $accessToken, 'error' => $authError] = $this->accessToken();
        if (! $accessToken) {
            return ['sent' => 0, 'failed' => count($tokens), 'error' => $authError];
        }

        $sent = 0;
        $failed = 0;
        $staleTokens = [];

        foreach ($tokens as $token) {
            $result = $this->sendOne($token, $title, $body, $data, $options, $accessToken);

            if ($result === 'ok') {
                $sent++;
            } elseif ($result === 'stale') {
                $staleTokens[] = $token;
                $failed++;
            } else {
                $failed++;
            }
        }

        // Remove tokens FCM says are no longer valid.
        if ($staleTokens) {
            DeviceToken::whereIn('token', $staleTokens)->delete();
        }

        return ['sent' => $sent, 'failed' => $failed, 'error' => null];
    }

    /** @return 'ok'|'stale'|'error' */
    private function sendOne(string $token, string $title, string $body, array $data, array $options = [], string $accessToken = ''): string
    {
        $projectId = config('firebase.project_id');

        $notification = ['title' => $title, 'body' => $body];
        if (! empty($options['image'])) {
            $notification['image'] = $options['image'];
        }

        $androidNotification = ['priority' => 'high'];
        if (! empty($options['color'])) {
            $androidNotification['notification'] = ['color' => $options['color']];
        }
        if (! empty($options['image'])) {
            $androidNotification['notification']['image'] = $options['image'];
        }

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => $notification,
                'data' => (object) array_map('strval', $data),
                'android' => $androidNotification,
            ],
        ];

        $url = str_replace('{project_id}', $projectId, self::FCM_ENDPOINT);
        $response = Http::withToken($accessToken)->post($url, $payload);

        if ($response->successful()) {
            return 'ok';
        }

        $errorCode = $response->json('error.details.0.errorCode')
            ?? $response->json('error.status')
            ?? '';

        Log::error('[FCM] response', ['status' => $response->status(), 'body' => $response->body(), 'errorCode' => $errorCode]);

        if ($response->status() === 404 || $errorCode === 'UNREGISTERED') {
            Log::info('[FCM] stale token removed', ['token' => substr($token, 0, 20)]);

            return 'stale';
        }

        Log::error('[FCM] send failed', ['status' => $response->status(), 'body' => $response->body()]);

        return 'error';
    }

    /** @return array{token: string, error: string} */
    private function accessToken(): array
    {
        $credentialsPath = config('firebase.credentials.file');

        if (! $credentialsPath) {
            Log::warning('[FCM] FIREBASE_CREDENTIALS not set in .env');

            return ['token' => '', 'error' => 'FIREBASE_CREDENTIALS is not set in .env'];
        }

        // Resolve relative paths. Try storage_path() first for "storage/app/…"
        // paths, then base_path() — avoids issues on shared hosting where
        // base_path() may resolve to public/ instead of the project root.
        if (! str_starts_with($credentialsPath, '/') && ! preg_match('/^[A-Za-z]:[\\/]/', $credentialsPath)) {
            if (str_starts_with($credentialsPath, 'storage/')) {
                $credentialsPath = storage_path(substr($credentialsPath, strlen('storage/')));
            } else {
                $credentialsPath = base_path($credentialsPath);
            }
        }

        if (! file_exists($credentialsPath)) {
            $msg = 'Firebase service account file not found at: '.$credentialsPath;
            Log::warning('[FCM] '.$msg);

            return ['token' => '', 'error' => $msg];
        }

        $json = json_decode(file_get_contents($credentialsPath), true);

        if (! $json || ($json['type'] ?? '') !== 'service_account') {
            $msg = 'Firebase credentials file is not a valid service account JSON: '.$credentialsPath;
            Log::error('[FCM] '.$msg);

            return ['token' => '', 'error' => $msg];
        }

        $credentials = new ServiceAccountCredentials(
            'https://www.googleapis.com/auth/firebase.messaging',
            $json,
        );

        try {
            $token = $credentials->fetchAuthToken()['access_token'] ?? '';
            if (! $token) {
                Log::error('[FCM] fetchAuthToken returned empty access token');

                return ['token' => '', 'error' => 'Google returned an empty access token — check the service account key is still valid'];
            }

            return ['token' => $token, 'error' => ''];
        } catch (\Exception $e) {
            Log::error('[FCM] failed to fetch access token: '.$e->getMessage());

            return ['token' => '', 'error' => 'Google auth error: '.$e->getMessage()];
        }
    }
}
