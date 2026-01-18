<?php

namespace Modules\Flight\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Flight\Models\FlightBooking;

class FlightTicketIssued extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public FlightBooking $booking
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'E-Ticket - '.$this->booking->booking_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'flight::emails.ticket-issued',
            with: [
                'booking' => $this->booking->load([
                    'segments.airline',
                    'segments.departureAirport',
                    'segments.arrivalAirport',
                    'passengers',
                ]),
            ],
        );
    }

    public function attachments(): array
    {
        // In a real app, you'd attach PDF ticket here
        return [];
    }
}
