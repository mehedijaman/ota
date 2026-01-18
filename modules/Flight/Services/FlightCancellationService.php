<?php

namespace Modules\Flight\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Flight\Mail\FlightBookingCancelled;
use Modules\Flight\Models\FlightBooking;
use Modules\Flight\Models\FlightBookingCancellation;
use Modules\Flight\Models\FlightBookingPayment;

class FlightCancellationService
{
    /**
     * Request cancellation for a booking.
     */
    public function requestCancellation(FlightBooking $booking, array $data): FlightBookingCancellation
    {
        return DB::transaction(function () use ($booking, $data) {
            // Validate booking can be cancelled
            $this->validateForCancellation($booking);

            // Calculate refund amounts
            $financials = $this->calculateCancellationFinancials($booking, $data);

            // Create cancellation record
            $cancellation = FlightBookingCancellation::create([
                'booking_id' => $booking->id,
                'type' => $data['type'] ?? 'full',
                'reason_category' => $data['reason_category'] ?? 'customer_request',
                'reason_details' => $data['reason_details'] ?? null,
                'booking_amount' => $financials['booking_amount'],
                'airline_penalty' => $financials['airline_penalty'],
                'service_charge' => $financials['service_charge'],
                'tax_refundable' => $financials['tax_refundable'],
                'total_deduction' => $financials['total_deduction'],
                'refundable_amount' => $financials['refundable_amount'],
                'currency_code' => $booking->currency_code,
                'status' => 'requested',
                'refund_status' => 'pending_approval',
                'requested_at' => now(),
                'requested_by' => auth()->id(),
                'admin_notes' => $data['admin_notes'] ?? null,
            ]);

            // Update booking status
            $booking->update(['status' => 'cancellation_requested']);
            $booking->recordStatusChange('cancellation_requested', 'Cancellation requested');

            Log::info('Cancellation requested', [
                'booking_id' => $booking->id,
                'cancellation_id' => $cancellation->id,
            ]);

            return $cancellation;
        });
    }

    /**
     * Approve a cancellation request.
     */
    public function approveCancellation(FlightBookingCancellation $cancellation): FlightBookingCancellation
    {
        return DB::transaction(function () use ($cancellation) {
            $booking = $cancellation->booking;

            $cancellation->update([
                'status' => 'confirmed',
                'refund_status' => 'approved',
                'confirmed_at' => now(),
                'confirmed_by' => auth()->id(),
            ]);

            // Update booking status
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => auth()->id(),
            ]);

            $booking->recordStatusChange('cancelled', 'Cancellation approved');

            // Send notification
            if ($booking->contact_email) {
                Mail::to($booking->contact_email)->queue(new FlightBookingCancelled($booking));
            }

            return $cancellation->fresh();
        });
    }

    /**
     * Reject a cancellation request.
     */
    public function rejectCancellation(FlightBookingCancellation $cancellation, string $reason): FlightBookingCancellation
    {
        return DB::transaction(function () use ($cancellation, $reason) {
            $booking = $cancellation->booking;

            $cancellation->update([
                'status' => 'rejected',
                'refund_status' => 'rejected',
                'rejection_reason' => $reason,
                'rejected_at' => now(),
                'rejected_by' => auth()->id(),
            ]);

            // Restore booking status
            $booking->update(['status' => $booking->statusHistories()->where('new_status', '!=', 'cancellation_requested')->latest()->first()?->old_status ?? 'confirmed']);
            $booking->recordStatusChange($booking->status, 'Cancellation rejected: '.$reason);

            return $cancellation->fresh();
        });
    }

    /**
     * Process refund for approved cancellation.
     */
    public function processRefund(FlightBookingCancellation $cancellation, array $data = []): FlightBookingPayment
    {
        return DB::transaction(function () use ($cancellation, $data) {
            $booking = $cancellation->booking;

            // Validate cancellation is approved
            if ($cancellation->status !== 'confirmed' || $cancellation->refund_status !== 'approved') {
                throw new \Exception('Cancellation must be approved before processing refund.');
            }

            $refundAmount = $data['amount'] ?? $cancellation->refundable_amount;

            // Create refund payment record
            $payment = FlightBookingPayment::create([
                'booking_id' => $booking->id,
                'cancellation_id' => $cancellation->id,
                'type' => 'refund',
                'method' => $data['method'] ?? 'credit_balance',
                'amount' => $refundAmount,
                'currency_code' => $cancellation->currency_code,
                'status' => 'completed',
                'reference_number' => 'REF-'.strtoupper(bin2hex(random_bytes(4))),
                'notes' => $data['notes'] ?? 'Cancellation refund',
                'is_verified' => true,
                'verified_at' => now(),
                'verified_by' => auth()->id(),
                'created_by' => auth()->id(),
            ]);

            // Update cancellation
            $cancellation->update([
                'refunded_amount' => $cancellation->refunded_amount + $refundAmount,
                'refund_status' => 'refunded',
                'refunded_at' => now(),
                'refunded_by' => auth()->id(),
                'refund_reference' => $payment->reference_number,
            ]);

            // Update booking totals
            $booking->recalculateTotals();

            Log::info('Refund processed', [
                'booking_id' => $booking->id,
                'cancellation_id' => $cancellation->id,
                'refund_amount' => $refundAmount,
            ]);

            return $payment;
        });
    }

    /**
     * Calculate cancellation financials.
     */
    protected function calculateCancellationFinancials(FlightBooking $booking, array $data): array
    {
        $bookingAmount = $booking->total_amount;
        $paidAmount = $booking->amount_paid;

        // Get penalty rules (simplified)
        $daysBeforeDeparture = now()->diffInDays($booking->departure_date, false);

        $penaltyPercent = match (true) {
            $daysBeforeDeparture > 30 => 10,
            $daysBeforeDeparture > 14 => 25,
            $daysBeforeDeparture > 7 => 50,
            $daysBeforeDeparture > 3 => 75,
            default => 100,
        };

        $airlinePenalty = round($booking->base_fare_total * ($penaltyPercent / 100), 2);
        $serviceCharge = config('flight.cancellation_service_charge', 500);
        $taxRefundable = $booking->tax_total;

        $totalDeduction = $airlinePenalty + $serviceCharge;
        $refundableAmount = max(0, min($paidAmount, $bookingAmount - $totalDeduction));

        return [
            'booking_amount' => $bookingAmount,
            'airline_penalty' => $airlinePenalty,
            'service_charge' => $serviceCharge,
            'tax_refundable' => $taxRefundable,
            'total_deduction' => $totalDeduction,
            'refundable_amount' => $refundableAmount,
            'penalty_percent' => $penaltyPercent,
        ];
    }

    /**
     * Validate booking can be cancelled.
     */
    protected function validateForCancellation(FlightBooking $booking): void
    {
        if (in_array($booking->status, ['cancelled', 'completed', 'no_show'])) {
            throw new \Exception('Booking cannot be cancelled.');
        }

        if ($booking->cancellation && $booking->cancellation->status !== 'rejected') {
            throw new \Exception('Cancellation already exists for this booking.');
        }
    }

    /**
     * Get cancellation policy for a booking.
     */
    public function getCancellationPolicy(FlightBooking $booking): array
    {
        $daysBeforeDeparture = now()->diffInDays($booking->departure_date, false);

        $tiers = [
            ['days' => 30, 'penalty' => 10, 'label' => '30+ days before departure'],
            ['days' => 14, 'penalty' => 25, 'label' => '14-30 days before departure'],
            ['days' => 7, 'penalty' => 50, 'label' => '7-14 days before departure'],
            ['days' => 3, 'penalty' => 75, 'label' => '3-7 days before departure'],
            ['days' => 0, 'penalty' => 100, 'label' => 'Less than 3 days'],
        ];

        $currentTier = null;
        foreach ($tiers as $tier) {
            if ($daysBeforeDeparture >= $tier['days']) {
                $currentTier = $tier;
                break;
            }
        }

        return [
            'tiers' => $tiers,
            'current_tier' => $currentTier,
            'days_before_departure' => $daysBeforeDeparture,
            'is_refundable' => $daysBeforeDeparture > 0 && ($currentTier['penalty'] ?? 100) < 100,
        ];
    }
}
