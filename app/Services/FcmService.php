<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    private const FCM_ENDPOINT = 'https://fcm.googleapis.com/v1/projects/{project_id}/messages:send';

    /**
     * Send a notification to all devices belonging to the given user ids.
     *
     * @param  int[]                 $userIds
     * @param  array<string,string>  $data
     * @return array{sent: int, failed: int}
     */
    public function sendToUsers(array $userIds, string $title, string $body, array $data = []): array
    {
        $tokens = DeviceToken::whereIn('user_id', $userIds)->pluck('token')->all();

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send a notification to every registered device.
     *
     * @param  array<string,string>  $data
     * @return array{sent: int, failed: int}
     */
    public function sendToAll(string $title, string $body, array $data = []): array
    {
        $tokens = DeviceToken::pluck('token')->all();

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * @param  string[]              $tokens
     * @param  array<string,string>  $data
     * @return array{sent: int, failed: int}
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        $sent        = 0;
        $failed      = 0;
        $staleTokens = [];

        foreach ($tokens as $token) {
            $result = $this->sendOne($token, $title, $body, $data);

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

        return compact('sent', 'failed');
    }

    /** @return 'ok'|'stale'|'error' */
    private function sendOne(string $token, string $title, string $body, array $data): string
    {
        $projectId = config('firebase.project_id');

        if (! $projectId) {
            Log::warning('[FCM] FIREBASE_PROJECT_ID not set.');
            return 'error';
        }

        $accessToken = $this->accessToken();
        if (! $accessToken) {
            return 'error';
        }

        $payload = [
            'message' => [
                'token'        => $token,
                'notification' => ['title' => $title, 'body' => $body],
                'data'         => (object) array_map('strval', $data),
                'android'      => ['priority' => 'high'],
            ],
        ];

        $url      = str_replace('{project_id}', $projectId, self::FCM_ENDPOINT);
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

    private function accessToken(): string
    {
        $credentialsPath = config('firebase.credentials.file');

        if (! $credentialsPath || ! file_exists($credentialsPath)) {
            Log::warning('[FCM] service account file not found: '.$credentialsPath);
            return '';
        }

        // Load service account JSON directly — avoids needing GOOGLE_APPLICATION_CREDENTIALS env var.
        $json        = json_decode(file_get_contents($credentialsPath), true);
        $credentials = new \Google\Auth\Credentials\ServiceAccountCredentials(
            'https://www.googleapis.com/auth/firebase.messaging',
            $json,
        );

        return $credentials->fetchAuthToken()['access_token'] ?? '';
    }
}
