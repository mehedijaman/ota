<?php

namespace Modules\Flight\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Flight\Models\FlightBooking;

class FlightBookingCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public FlightBooking $booking
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Cancelled - '.$this->booking->booking_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'flight::emails.booking-cancelled',
            with: [
                'booking' => $this->booking->load([
                    'segments.airline',
                    'segments.departureAirport',
                    'segments.arrivalAirport',
                    'cancellation',
                ]),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
