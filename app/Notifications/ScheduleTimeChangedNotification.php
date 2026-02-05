<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use TallStackUi\Traits\Interactions;

class ScheduleTimeChangedNotification extends Notification implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Interactions,Queueable;

    public $schedule;

    public $oldStart;

    public $newStart;

    public $changeReason;

    /**
     * Create a new notification instance.
     */
    public function __construct($schedule, $oldStart, $newStart, $changeReason)
    {
        $this->schedule = $schedule;
        $this->oldStart = $oldStart;
        $this->newStart = $newStart;
        $this->changeReason = $changeReason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Schedule Time Changed Notification')
            ->markdown('mail.schedule-time-changed-notification', [
                'oldStart' => $this->oldStart,
                'newStart' => $this->newStart,
                'changeReason' => $this->changeReason,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
