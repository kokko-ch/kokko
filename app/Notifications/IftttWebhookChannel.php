<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class IftttWebhookChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return Illuminate\Http\Client\Response|null
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $url = $notifiable->routeNotificationFor('ifttt', $notification)) {
            return;
        }

        return Http::post($url, $this->buildJsonPayload(
            $notification->toIfttt($notifiable)
        ));
    }

    /**
     * Build up a JSON payload for the IFTTT webhook.
     *
     * @param  IftttMessage  $message
     * @return array
     */
    protected function buildJsonPayload(IftttMessage $message)
    {
        return [
            'value1' => $message->content,
            'value2' => $message->title,
        ];
    }
}
