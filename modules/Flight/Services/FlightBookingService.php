<?php

namespace Modules\Flight\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\Flight\DTOs\FlightBookingRequest;
use Modules\Flight\Mail\FlightBookingConfirmed;
use Modules\Flight\Models\FlightBooking;
use Modules\Flight\Models\FlightBookingPassenger;
use Modules\Flight\Models\FlightBookingPayment;
use Modules\Flight\Models\FlightBookingSegment;

class FlightBookingService
{
    public function getAll(): LengthAwarePaginator
    {
        return FlightBooking::query()
            ->with(['supplier', 'customer', 'agent', 'leadPassenger'])
            ->when(request('search'), fn ($q, $search) => $q->search($search))
            ->when(request('status'), fn ($q, $status) => $q->where('status', $status))
            ->when(request('payment_status'), fn ($q, $status) => $q->where('payment_status', $status))
            ->when(request('ticketing_status'), fn ($q, $status) => $q->where('ticketing_status', $status))
            ->when(request('date_from'), fn ($q, $date) => $q->where('departure_date', '>=', $date))
            ->when(request('date_to'), fn ($q, $date) => $q->where('departure_date', '<=', $date))
            ->orderBy(request('sort_by', 'created_at'), request('sort_order', 'desc'))
            ->paginate(request('per_page', 15))
            ->withQueryString();
    }

    public function getStats(): array
    {
        return [
            'total' => FlightBooking::count(),
            'pending' => FlightBooking::pending()->count(),
            'confirmed' => FlightBooking::confirmed()->count(),
            'ticketed' => FlightBooking::ticketed()->count(),
            'cancelled' => FlightBooking::cancelled()->count(),
            'total_revenue' => FlightBooking::whereIn('status', ['confirmed', 'completed'])->sum('total_amount'),
            'unpaid_amount' => FlightBooking::whereIn('status', ['pending', 'confirmed'])->sum('amount_due'),
        ];
    }

    public function getById(int $id): FlightBooking
    {
        return FlightBooking::with([
            'supplier',
            'customer',
            'agent',
            'segments.airline',
            'segments.departureAirport',
            'segments.arrivalAirport',
            'passengers',
            'seats',
            'ancillaries',
            'payments' => fn ($q) => $q->orderByDesc('created_at'),
            'cancellation',
            'exchanges',
            'statusHistories' => fn ($q) => $q->with('changedBy')->limit(10),
        ])->findOrFail($id);
    }

    public function create(array $data): FlightBooking
    {
        return DB::transaction(function () use ($data) {
            $data['booking_number'] = $this->generateBookingNumber();
            $data['created_by'] = auth()->id();
            $data['total_passengers'] = ($data['adult_count'] ?? 0) + ($data['child_count'] ?? 0) + ($data['infant_count'] ?? 0);

            $booking = FlightBooking::create(Arr::except($data, ['segments', 'passengers']));

            // Create segments
            if (! empty($data['segments'])) {
                $this->createSegments($booking, $data['segments']);
            }

            // Create passengers
            if (! empty($data['passengers'])) {
                $this->createPassengers($booking, $data['passengers']);
            }

            // Calculate totals
            $booking->refresh();
            $this->recalculateTotals($booking);

            // Record initial status
            $booking->recordStatusChange('pending', 'Booking created');

            return $booking;
        });
    }

    public function createFromDto(FlightBookingRequest $dto): FlightBooking
    {
        return $this->create($dto->toArray());
    }

    protected function createSegments(FlightBooking $booking, array $segments): void
    {
        foreach ($segments as $segmentData) {
            $segmentData['booking_id'] = $booking->id;
            FlightBookingSegment::create($segmentData);
        }
    }

    protected function createPassengers(FlightBooking $booking, array $passengers): void
    {
        $passengerNumber = 1;
        foreach ($passengers as $passengerData) {
            $passengerData['booking_id'] = $booking->id;
            $passengerData['passenger_number'] = $passengerNumber++;
            FlightBookingPassenger::create($passengerData);
        }
    }

    public function update(FlightBooking $booking, array $data): FlightBooking
    {
        return DB::transaction(function () use ($booking, $data) {
            $oldStatus = $booking->status;

            $booking->update(Arr::except($data, ['segments', 'passengers']));

            if ($oldStatus !== $booking->status) {
                $booking->recordStatusChange($booking->status, 'Booking updated');
            }

            return $booking->fresh();
        });
    }

    public function updateStatus(FlightBooking $booking, string $status, ?string $notes = null): FlightBooking
    {
        $oldStatus = $booking->status;
        $updateData = ['status' => $status];

        switch ($status) {
            case 'confirmed':
                $updateData['confirmed_at'] = now();
                $updateData['confirmed_by'] = auth()->id();
                break;
            case 'cancelled':
                $updateData['cancelled_at'] = now();
                $updateData['cancelled_by'] = auth()->id();
                break;
            case 'void':
                $updateData['void_at'] = now();
                break;
        }

        $booking->update($updateData);
        $booking->recordStatusChange($status, $notes);

        // Send notifications
        if ($status === 'confirmed' && $booking->contact_email) {
            Mail::to($booking->contact_email)->queue(new FlightBookingConfirmed($booking));
        }

        return $booking->fresh();
    }

    public function addPayment(FlightBooking $booking, array $data): FlightBookingPayment
    {
        return DB::transaction(function () use ($booking, $data) {
            $data['booking_id'] = $booking->id;
            $data['reference_number'] = $data['reference_number'] ?? $this->generatePaymentReference();
            $data['net_amount'] = $data['net_amount'] ?? $data['amount']; // Default net_amount to amount
            $data['created_by'] = auth()->id();

            if (($data['status'] ?? 'pending') === 'completed') {
                $data['is_verified'] = true;
                $data['verified_at'] = now();
                $data['verified_by'] = auth()->id();
            }

            $payment = FlightBookingPayment::create($data);

            $booking->recalculateTotals();

            return $payment;
        });
    }

    public function verifyPayment(FlightBooking $booking, int $paymentId): void
    {
        $payment = $booking->payments()->findOrFail($paymentId);
        $payment->verify();
        $booking->recalculateTotals();
    }

    public function recalculateTotals(FlightBooking $booking): void
    {
        $booking->recalculateTotals();
    }

    protected function generateBookingNumber(): string
    {
        do {
            $number = 'FLT-'.strtoupper(bin2hex(random_bytes(4)));
        } while (FlightBooking::where('booking_number', $number)->exists());

        return $number;
    }

    protected function generatePaymentReference(): string
    {
        return 'PAY-'.strtoupper(bin2hex(random_bytes(4)));
    }

    // Recycle bin methods
    public function getRecycleBin(): LengthAwarePaginator
    {
        return FlightBooking::onlyTrashed()
            ->with(['supplier', 'customer'])
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();
    }

    public function restore(int $id): void
    {
        FlightBooking::onlyTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int $id): void
    {
        $booking = FlightBooking::onlyTrashed()->findOrFail($id);
        $booking->segments()->forceDelete();
        $booking->passengers()->forceDelete();
        $booking->payments()->forceDelete();
        $booking->forceDelete();
    }

    public function bulkDelete(array $ids): int
    {
        $count = 0;
        foreach (FlightBooking::whereIn('id', $ids)->get() as $booking) {
            if (! $booking->isTicketed()) {
                $booking->delete();
                $count++;
            }
        }

        return $count;
    }

    public function bulkUpdateStatus(array $ids, string $status): int
    {
        $count = 0;
        foreach (FlightBooking::whereIn('id', $ids)->get() as $booking) {
            if ($booking->status !== $status) {
                $this->updateStatus($booking, $status);
                $count++;
            }
        }

        return $count;
    }
}
