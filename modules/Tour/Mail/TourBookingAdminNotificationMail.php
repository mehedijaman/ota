<?php

namespace Modules\Tour\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Tour\Models\TourBooking;

class TourBookingAdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TourBooking $booking
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Tour Booking - '.$this->booking->booking_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'tour::emails.booking-admin-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
