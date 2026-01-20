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
            ->subject('New Appointment Booked')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your new appointment has been successfully booked!')
            ->line('Appointment Details:')
            ->line('Date: '.$this->booking->date)
            ->line('if you have any questions, feel free to contact us.')
            ->line('Thank you for using our application!')
            ->salutation('Best regards, Bossa Nova Health');

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
