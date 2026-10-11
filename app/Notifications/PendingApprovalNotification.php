<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PendingApprovalNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $entity,  // 'article' | 'promotion'
        private readonly string $title,
        private readonly int    $id,
        private readonly string $submitterName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $label = ucfirst($this->entity);
        $route = $this->entity === 'article'
            ? route('admin.articles.index', ['status' => 'pending'])
            : route('admin.promotions.index', ['status' => 'pending']);

        return [
            'title'   => "{$label} awaiting approval",
            'message' => "\"{$this->title}\" submitted by {$this->submitterName} needs your review.",
            'url'     => $route,
            'kind'    => 'request',
        ];
    }
}
