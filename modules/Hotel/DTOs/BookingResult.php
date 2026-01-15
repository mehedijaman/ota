<?php

namespace Modules\Hotel\DTOs;

/**
 * Data Transfer Object for booking results from suppliers.
 */
readonly class BookingResult
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_FAILED = 'failed';

    public const STATUS_ON_REQUEST = 'on_request';

    public function __construct(
        public string $supplierCode,
        public string $status,
        public ?string $supplierBookingId = null,
        public ?string $supplierConfirmationNumber = null,
        public ?string $message = null,
        public ?float $totalAmount = null,
        public ?string $currency = 'BDT',
        public ?string $cancellationDeadline = null,
        public ?string $cancellationPolicy = null,
        public array $roomDetails = [],
        public array $errors = [],
        public array $rawData = [],
    ) {}

    public function isSuccessful(): bool
    {
        return in_array($this->status, [self::STATUS_CONFIRMED, self::STATUS_PENDING, self::STATUS_ON_REQUEST]);
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function toArray(): array
    {
        return [
            'supplier_code' => $this->supplierCode,
            'status' => $this->status,
            'supplier_booking_id' => $this->supplierBookingId,
            'supplier_confirmation_number' => $this->supplierConfirmationNumber,
            'message' => $this->message,
            'total_amount' => $this->totalAmount,
            'currency' => $this->currency,
            'cancellation_deadline' => $this->cancellationDeadline,
            'cancellation_policy' => $this->cancellationPolicy,
            'room_details' => $this->roomDetails,
            'errors' => $this->errors,
        ];
    }
}
