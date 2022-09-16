<?php

namespace App\Notifications;

use App\Models\NotificationJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;

class RecurringNotification extends Notification
{
    use Queueable;

    /**
     * The notification job instance.
     *
     * @var \App\Models\NotificationJob
     */
    public $notificationJob;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\NotificationJob  $notificationJob
     * @return void
     */
    public function __construct(NotificationJob $notificationJob)
    {
        $this->notificationJob = $notificationJob;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['ifttt'];
    }

    /**
     * Get the IFTTT representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return IftttMessage
     */
    public function toIfttt($notifiable)
    {
        return (new IftttMessage)
                    ->title($this->notificationJob->title ?? config('app.name'))
                    ->content(Arr::random($this->notificationJob->content)['content']);
    }
}
