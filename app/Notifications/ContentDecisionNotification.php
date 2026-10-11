<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContentDecisionNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $entity,   // 'article' | 'promotion'
        private readonly string $title,
        private readonly int    $id,
        private readonly string $decision, // 'approved' | 'rejected'
        private readonly string $reviewerName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $label   = ucfirst($this->entity);
        $approved = $this->decision === 'approved';
        $route   = $this->entity === 'article'
            ? route('admin.articles.index')
            : route('admin.promotions.index');

        return [
            'title'   => $approved
                ? "{$label} approved"
                : "{$label} rejected",
            'message' => $approved
                ? "Your {$this->entity} \"{$this->title}\" was approved by {$this->reviewerName}."
                : "Your {$this->entity} \"{$this->title}\" was not approved by {$this->reviewerName}.",
            'url'     => $route,
            'kind'    => $approved ? 'success' : 'warning',
        ];
    }
}
