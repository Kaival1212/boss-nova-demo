<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentBooked extends Notification implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public Client $client;

    public Booking $booking;

    public function __construct(Client $client)
    {
        $this->client = $client;

        $this->booking = $client->bookings()->latest()->first();
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
            ->subject('Appointment Confirmed - '.date('Y-m-d', strtotime($this->booking->date)))
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Great news! Your appointment has been successfully confirmed.')
            ->line('**Appointment Details:**')
            ->line('📅 **Date:** '.date('Y-m-d', strtotime($this->booking->date)))
            ->line('🕐 **Time:** '.date('g:i A', strtotime($this->booking->date)))
            ->line('**What to bring:**')
            ->line('• Valid ID and insurance card')
            ->line('• List of current medications')
            ->line('• Any relevant medical records')
            ->action('View Appointment Details', url('/appointments/'.$this->booking->id))
            ->line('Need to reschedule? You can manage your appointment anytime through your account.')
            ->line('If you have any questions or concerns, please don\'t hesitate to reach out to us at '.config('mail.from.address').' or call us at (555) 123-4567.')
            ->salutation('Best regards,
The Bossa Nova Health Team');
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
