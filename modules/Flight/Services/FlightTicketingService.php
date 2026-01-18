<?php

namespace Modules\Flight\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Flight\Mail\FlightTicketIssued;
use Modules\Flight\Models\FlightBooking;
use Modules\Flight\Models\FlightBookingPassenger;

class FlightTicketingService
{
    /**
     * Issue tickets for a booking.
     */
    public function issueTicket(FlightBooking $booking): FlightBooking
    {
        return DB::transaction(function () use ($booking) {
            // Validate booking is ready for ticketing
            $this->validateForTicketing($booking);

            // Call Adapter to issue ticket
            $adapter = \Modules\Flight\Adapters\FlightSupplierAdapterFactory::make($booking->supplier);
            if ($adapter) {
                try {
                    $result = $adapter->issueTicket($booking->supplier_booking_id);
                    if (! $result->success) {
                         throw new \Exception('Supplier failed to issue ticket: ' . $result->message);
                    }
                    // Update connection details if needed from result
                } catch (\Exception $e) {
                    throw new \Exception('Adapter error: ' . $e->getMessage());
                }
            }

            // Generate ticket numbers for each passenger (if not provided by adapter, or sync local)
            // For now, keep local generation as fallback or primary if adapter doesn't return tickets
            foreach ($booking->passengers as $passenger) {
                $this->issuePassengerTicket($passenger, $booking);
            }

            // Update booking status
            $booking->update([
                'ticketing_status' => 'ticketed',
                'ticketed_at' => now(),
                'ticketed_by' => auth()->id(),
            ]);

            $booking->recordStatusChange('ticketed', 'Tickets issued');

            // Send ticket email
            if ($booking->contact_email) {
                Mail::to($booking->contact_email)->queue(new FlightTicketIssued($booking));
            }

            Log::info('Tickets issued', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
            ]);

            return $booking->fresh();
        });
    }

    /**
     * Issue ticket for a passenger.
     */
    protected function issuePassengerTicket(FlightBookingPassenger $passenger, FlightBooking $booking): void
    {
        $ticketNumber = $this->generateTicketNumber($booking);

        $passenger->update([
            'ticket_number' => $ticketNumber,
            'ticket_status' => 'issued',
            'ticket_issued_at' => now(),
        ]);
    }

    /**
     * Void a ticket.
     */
    public function voidTicket(FlightBooking $booking, ?string $reason = null): FlightBooking
    {
        return DB::transaction(function () use ($booking, $reason) {
            // Validate booking can be voided
            if (! $booking->isTicketed()) {
                throw new \Exception('Booking is not ticketed.');
            }

            // Void all passenger tickets
            foreach ($booking->passengers as $passenger) {
                $passenger->update([
                    'ticket_status' => 'voided',
                    'ticket_voided_at' => now(),
                    'void_reason' => $reason,
                ]);
            }

            // Update booking status
            $booking->update([
                'ticketing_status' => 'voided',
                'void_at' => now(),
            ]);

            $booking->recordStatusChange('voided', $reason ?? 'Tickets voided');

            Log::info('Tickets voided', [
                'booking_id' => $booking->id,
                'reason' => $reason,
            ]);

            return $booking->fresh();
        });
    }

    /**
     * Reissue tickets (for exchanges/changes).
     */
    public function reissueTicket(FlightBooking $booking, array $changes = []): FlightBooking
    {
        return DB::transaction(function () use ($booking) {
            // First void existing tickets
            foreach ($booking->passengers as $passenger) {
                if ($passenger->ticket_status === 'issued') {
                    $passenger->update([
                        'original_ticket_number' => $passenger->ticket_number,
                        'ticket_status' => 'reissued',
                        'ticket_number' => $this->generateTicketNumber($booking),
                        'ticket_reissued_at' => now(),
                    ]);
                }
            }

            // Update booking
            $booking->update([
                'ticketing_status' => 'reissued',
                'reissued_at' => now(),
            ]);

            $booking->recordStatusChange('reissued', 'Tickets reissued');

            return $booking->fresh();
        });
    }

    /**
     * Validate booking is ready for ticketing.
     */
    protected function validateForTicketing(FlightBooking $booking): void
    {
        if ($booking->isTicketed()) {
            throw new \Exception('Booking is already ticketed.');
        }

        if ($booking->status !== 'confirmed') {
            throw new \Exception('Booking must be confirmed before ticketing.');
        }

        if ($booking->payment_status !== 'paid') {
            throw new \Exception('Booking must be fully paid before ticketing.');
        }

        if ($booking->passengers->isEmpty()) {
            throw new \Exception('Booking has no passengers.');
        }
    }

    /**
     * Generate a ticket number.
     */
    protected function generateTicketNumber(FlightBooking $booking): string
    {
        $airlineCode = $booking->segments->first()?->airline?->numeric_code ?? '000';
        $sequence = str_pad(random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);

        return $airlineCode.$sequence;
    }

    /**
     * Get ticket time limit for a booking.
     */
    public function getTicketTimeLimit(FlightBooking $booking): ?\Carbon\Carbon
    {
        if ($booking->ticket_time_limit) {
            return $booking->ticket_time_limit;
        }

        // Default: 24 hours from booking or 24 hours before departure, whichever is earlier
        $fromBooking = $booking->created_at->addHours(24);
        $beforeDeparture = $booking->departure_date?->subHours(24);

        if ($beforeDeparture && $beforeDeparture < $fromBooking) {
            return $beforeDeparture;
        }

        return $fromBooking;
    }

    /**
     * Check if ticketing deadline has passed.
     */
    public function hasTicketingDeadlinePassed(FlightBooking $booking): bool
    {
        $limit = $this->getTicketTimeLimit($booking);

        return $limit && now()->isAfter($limit);
    }
}
