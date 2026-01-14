<?php

namespace Modules\Hotel\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Hotel\Models\HotelBooking;

class HotelBookingConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public HotelBooking $booking
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Confirmed - '.$this->booking->booking_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'hotel::emails.booking-confirmed',
            with: [
                'booking' => $this->booking->load(['hotel', 'rooms.room', 'rooms.ratePlan']),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
