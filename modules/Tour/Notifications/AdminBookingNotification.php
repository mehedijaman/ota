<?php

namespace Modules\Tour\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Tour\Models\TourBooking;

class AdminBookingNotification extends Notification
{
    use Queueable;

    public function __construct(
        public TourBooking $booking
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Tour Booking - ' . $this->booking->booking_number)
            ->greeting('New Booking Received!')
            ->line('A new tour booking has been submitted and requires your attention.')
            ->line('**Booking Details:**')
            ->line('Booking Number: **' . $this->booking->booking_number . '**')
            ->line('Tour: **' . $this->booking->tour->name . '**')
            ->line('Travel Date: **' . $this->booking->travel_date->format('d M, Y') . '**')
            ->line('Customer: **' . $this->booking->contact_name . '**')
            ->line('Email: **' . $this->booking->contact_email . '**')
            ->line('Phone: **' . $this->booking->contact_phone . '**')
            ->line('Total Travelers: **' . $this->booking->total_travelers . '**')
            ->line('Adults: ' . $this->booking->adults_count . ' | Children: ' . $this->booking->children_count . ' | Infants: ' . $this->booking->infants_count)
            ->line('Total Amount: **৳ ' . number_format($this->booking->total_amount, 2) . '**')
            ->line('Payment Method: **' . ucfirst($this->booking->payment_method) . '**')
            ->action('View Booking', url('/app/tour/bookings/' . $this->booking->id))
            ->line('Please review and approve this booking.');
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'tour_name' => $this->booking->tour->name,
            'customer_name' => $this->booking->contact_name,
            'total_amount' => $this->booking->total_amount,
        ];
    }
}
