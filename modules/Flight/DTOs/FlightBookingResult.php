<?php

namespace Modules\Flight\DTOs;

/**
 * Data Transfer Object for flight booking results from suppliers.
 */
readonly class FlightBookingResult
{
    public function __construct(
        public bool $success,
        public ?string $supplierCode = null,
        public ?string $supplierBookingId = null,
        public ?string $pnr = null,
        public ?string $bookingStatus = null,
        public ?string $ticketingStatus = null,
        public ?string $ticketTimeLimit = null,
        public ?float $totalAmount = null,
        public ?string $currency = 'BDT',
        public ?string $message = null,
        public ?string $errorCode = null,
        public array $tickets = [],
        public array $segments = [],
        public array $passengers = [],
        public array $fareBreakdown = [],
        public array $rawResponse = [],
    ) {}

    public static function success(array $data): self
    {
        return new self(
            success: true,
            supplierCode: $data['supplier_code'] ?? null,
            supplierBookingId: $data['supplier_booking_id'] ?? $data['booking_id'] ?? null,
            pnr: $data['pnr'] ?? $data['confirmation_number'] ?? null,
            bookingStatus: $data['booking_status'] ?? 'confirmed',
            ticketingStatus: $data['ticketing_status'] ?? 'pending',
            ticketTimeLimit: $data['ticket_time_limit'] ?? null,
            totalAmount: isset($data['total_amount']) ? (float) $data['total_amount'] : null,
            currency: $data['currency'] ?? 'BDT',
            message: $data['message'] ?? 'Booking confirmed successfully',
            tickets: $data['tickets'] ?? [],
            segments: $data['segments'] ?? [],
            passengers: $data['passengers'] ?? [],
            fareBreakdown: $data['fare_breakdown'] ?? [],
            rawResponse: $data,
        );
    }

    public static function failure(string $message, ?string $errorCode = null, array $rawResponse = []): self
    {
        return new self(
            success: false,
            message: $message,
            errorCode: $errorCode,
            rawResponse: $rawResponse,
        );
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'supplier_code' => $this->supplierCode,
            'supplier_booking_id' => $this->supplierBookingId,
            'pnr' => $this->pnr,
            'booking_status' => $this->bookingStatus,
            'ticketing_status' => $this->ticketingStatus,
            'ticket_time_limit' => $this->ticketTimeLimit,
            'total_amount' => $this->totalAmount,
            'currency' => $this->currency,
            'message' => $this->message,
            'error_code' => $this->errorCode,
            'tickets' => $this->tickets,
            'segments' => $this->segments,
            'passengers' => $this->passengers,
            'fare_breakdown' => $this->fareBreakdown,
        ];
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function isFailure(): bool
    {
        return ! $this->success;
    }

    public function hasPnr(): bool
    {
        return $this->pnr !== null && $this->pnr !== '';
    }

    public function hasTickets(): bool
    {
        return ! empty($this->tickets);
    }

    public function isTicketed(): bool
    {
        return $this->ticketingStatus === 'ticketed' || $this->hasTickets();
    }

    public function getTicketNumbers(): array
    {
        return array_column($this->tickets, 'ticket_number');
    }

    public function getTotalAmountDisplay(): string
    {
        if ($this->totalAmount === null) {
            return 'N/A';
        }

        return number_format($this->totalAmount, 2).' '.$this->currency;
    }
}
