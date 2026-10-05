<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    private const FCM_ENDPOINT = 'https://fcm.googleapis.com/v1/projects/{project_id}/messages:send';

    /**
     * Send a push notification to a single user by their FCM token.
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): bool
    {
        if (! $user->fcm_token) {
            return false;
        }

        return $this->sendToToken($user->fcm_token, $title, $body, $data);
    }

    /**
     * Send to multiple users (skips users without a token).
     */
    public function sendToUsers(iterable $users, string $title, string $body, array $data = []): void
    {
        foreach ($users as $user) {
            $this->sendToUser($user, $title, $body, $data);
        }
    }

    /**
     * Send directly to an FCM registration token.
     */
    public function sendToToken(string $token, string $title, string $body, array $data = []): bool
    {
        $projectId = config('firebase.project_id');
        $serverKey  = config('firebase.server_key');

        if (! $projectId || ! $serverKey) {
            Log::warning('FcmService: FIREBASE_PROJECT_ID or FIREBASE_SERVER_KEY not set.');
            return false;
        }

        $payload = [
            'message' => [
                'token'        => $token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => array_map('strval', $data),
                'android' => [
                    'priority' => 'high',
                ],
                'apns' => [
                    'headers' => ['apns-priority' => '10'],
                ],
            ],
        ];

        $url = str_replace('{project_id}', $projectId, self::FCM_ENDPOINT);

        $response = Http::withToken($this->getAccessToken())
            ->post($url, $payload);

        if (! $response->successful()) {
            Log::error('FcmService: failed to send notification', [
                'status'  => $response->status(),
                'body'    => $response->body(),
                'token'   => substr($token, 0, 20).'...',
            ]);
            return false;
        }

        return true;
    }

    /**
     * Get a short-lived OAuth2 access token using the service account JSON.
     * Requires the kreait/laravel-firebase package and a service account file.
     */
    private function getAccessToken(): string
    {
        $credentialsPath = config('firebase.credentials.file');

        if (! $credentialsPath || ! file_exists($credentialsPath)) {
            // Fall back to legacy server key (for older projects still on V1 legacy API)
            return config('firebase.server_key', '');
        }

        $credentials = \Google\Auth\ApplicationDefaultCredentials::getCredentials(
            'https://www.googleapis.com/auth/firebase.messaging'
        );

        return $credentials->fetchAuthToken()['access_token'] ?? '';
    }
}
