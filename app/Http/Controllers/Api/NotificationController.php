<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private readonly FcmService $fcm) {}

    /**
     * Send a push notification to all users or a specific set of users.
     *
     * POST /api/admin/notifications/send
     * Body: {
     *   "target": "all" | "users",
     *   "user_ids": [1, 2, 3],   // required when target = "users"
     *   "title": "...",
     *   "body": "...",
     *   "data": { "screen": "History", "tab": "donations" }  // optional
     * }
     */
    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'target'              => ['required', 'in:all,users'],
            'user_ids'            => ['required_if:target,users', 'array'],
            'user_ids.*'          => ['integer', 'exists:users,id'],
            'title'               => ['required', 'string', 'max:100'],
            'body'                => ['required', 'string', 'max:200'],
            'data'                => ['sometimes', 'array'],
            'data.*'              => ['string'],
        ]);

        $payload = $data['data'] ?? [];

        $result = $data['target'] === 'all'
            ? $this->fcm->sendToAll($data['title'], $data['body'], $payload)
            : $this->fcm->sendToUsers($data['user_ids'], $data['title'], $data['body'], $payload);

        return response()->json([
            'success' => true,
            'sent'    => $result['sent'],
            'failed'  => $result['failed'],
        ]);
    }
}
