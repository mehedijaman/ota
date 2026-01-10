<?php

namespace Modules\Tour\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Tour\Models\TourBooking;

class BookingConfirmationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public TourBooking $booking
    ) {}

    public function via($notifiable): array
    {
        $channels = ['mail'];
        
        // Add SMS channel if phone number exists
        if ($notifiable->phone) {
            $channels[] = 'sms';
        }
        
        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Confirmation - ' . $this->booking->booking_number)
            ->greeting('Hello ' . $this->booking->contact_name . '!')
            ->line('Thank you for booking with us!')
            ->line('**Booking Details:**')
            ->line('Booking Number: **' . $this->booking->booking_number . '**')
            ->line('Tour: **' . $this->booking->tour->name . '**')
            ->line('Travel Date: **' . $this->booking->travel_date->format('d M, Y') . '**')
            ->line('Total Travelers: **' . $this->booking->total_travelers . '**')
            ->line('Total Amount: **৳ ' . number_format($this->booking->total_amount, 2) . '**')
            ->line('Payment Status: **' . ucfirst($this->booking->payment_status) . '**')
            ->line('Booking Status: **' . ucfirst($this->booking->status) . '**')
            ->line('Your booking is currently pending admin approval. We will notify you once it is confirmed.')
            ->action('View Booking', url('/customer/bookings/' . $this->booking->id))
            ->line('If you have any questions, please contact our support team.');
    }

    public function toSms($notifiable): string
    {
        return "Booking Confirmed! {$this->booking->booking_number} for {$this->booking->tour->name} on {$this->booking->travel_date->format('d M')}. Total: ৳{$this->booking->total_amount}. Status: Pending approval.";
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'tour_name' => $this->booking->tour->name,
            'travel_date' => $this->booking->travel_date->format('Y-m-d'),
            'total_amount' => $this->booking->total_amount,
        ];
    }
}
