<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PushNotificationController extends Controller
{
    public function __construct(private readonly FcmService $fcm) {}

    public function create(): View
    {
        $users = User::orderBy('name')->get(['id', 'name', 'phone']);

        return view('push-notifications.create', compact('users'));
    }

    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'target'     => ['required', 'in:all,users'],
            'user_ids'   => ['required_if:target,users', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'title'      => ['required', 'string', 'max:100'],
            'body'       => ['required', 'string', 'max:200'],
            'screen'     => ['nullable', 'string', 'in:History,Home'],
            'tab'        => ['nullable', 'string', 'in:bookings,donations'],
            'color'      => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'image'      => ['nullable', 'url', 'max:500'],
        ]);

        $payload = array_filter([
            'screen' => $data['screen'] ?? null,
            'tab'    => $data['tab']    ?? null,
        ]);

        $options = array_filter([
            'color' => $data['color'] ?? null,
            'image' => $data['image'] ?? null,
        ]);

        $result = $data['target'] === 'all'
            ? $this->fcm->sendToAll($data['title'], $data['body'], $payload, $options)
            : $this->fcm->sendToUsers($data['user_ids'], $data['title'], $data['body'], $payload, $options);

        $msg = "Sent to {$result['sent']} device(s).";
        if ($result['failed']) {
            $msg .= " {$result['failed']} failed (stale tokens removed).";
        }

        return back()->with('success', $msg);
    }
}
