<?php

namespace Modules\Tour\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Tour\Models\TourBooking;

class TourBookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TourBooking $booking
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Confirmation - '.$this->booking->booking_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'tour::emails.booking-confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
