<?php

namespace Modules\Hotel\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\Hotel\Mail\HotelBookingCancelled;
use Modules\Hotel\Mail\HotelBookingConfirmed;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelBooking;
use Modules\Hotel\Models\HotelBookingPayment;
use Modules\Hotel\Models\HotelBookingRoom;

class HotelBookingService
{
    public function getAll(): LengthAwarePaginator
    {
        return HotelBooking::query()
            ->with(['hotel', 'customer', 'agent'])
            ->when(request('search'), fn ($q, $search) => $q->search($search))
            ->when(request('hotel_id'), fn ($q, $id) => $q->where('hotel_id', $id))
            ->when(request('status'), fn ($q, $status) => $q->where('status', $status))
            ->when(request('payment_status'), fn ($q, $status) => $q->where('payment_status', $status))
            ->when(request('date_from'), fn ($q, $date) => $q->where('check_in_date', '>=', $date))
            ->when(request('date_to'), fn ($q, $date) => $q->where('check_out_date', '<=', $date))
            ->orderBy(request('sort_by', 'created_at'), request('sort_order', 'desc'))
            ->paginate(request('per_page', 15))
            ->withQueryString();
    }

    public function getStats(): array
    {
        $today = now()->toDateString();

        return [
            'total' => HotelBooking::count(),
            'pending' => HotelBooking::pending()->count(),
            'confirmed' => HotelBooking::confirmed()->count(),
            'arriving_today' => HotelBooking::arrivingToday()->count(),
            'departing_today' => HotelBooking::departingToday()->count(),
            'total_revenue' => HotelBooking::whereIn('status', ['confirmed', 'checked_in', 'checked_out', 'completed'])->sum('total_amount'),
            'unpaid_amount' => HotelBooking::whereIn('status', ['confirmed', 'checked_in'])->sum('amount_due'),
        ];
    }

    public function getById(int $id): HotelBooking
    {
        return HotelBooking::with([
            'hotel',
            'customer',
            'agent',
            'rooms.room',
            'rooms.ratePlan',
            'rooms.guests',
            'payments' => fn ($q) => $q->orderByDesc('created_at'),
            'cancellation',
            'statusHistories' => fn ($q) => $q->with('changedBy')->limit(10),
            'review',
        ])->findOrFail($id);
    }

    public function create(array $data): HotelBooking
    {
        return DB::transaction(function () use ($data) {
            $data['booking_number'] = HotelBooking::generateBookingNumber();
            $data['created_by'] = auth()->id();

            // Calculate nights
            $checkIn = \Carbon\Carbon::parse($data['check_in_date']);
            $checkOut = \Carbon\Carbon::parse($data['check_out_date']);
            $data['nights'] = $checkIn->diffInDays($checkOut);

            $booking = HotelBooking::create(Arr::except($data, ['rooms', 'guests']));

            // Create booking rooms
            if (! empty($data['rooms'])) {
                foreach ($data['rooms'] as $roomData) {
                    $roomData['booking_id'] = $booking->id;
                    $roomData['check_in_date'] = $data['check_in_date'];
                    $roomData['check_out_date'] = $data['check_out_date'];

                    $bookingRoom = HotelBookingRoom::create(Arr::except($roomData, ['guests']));

                    // Create guests for this room
                    if (! empty($roomData['guests'])) {
                        foreach ($roomData['guests'] as $guestData) {
                            $guestData['booking_room_id'] = $bookingRoom->id;
                            $bookingRoom->guests()->create($guestData);
                        }
                    }
                }
            }

            // Refresh to get default values (status, etc)
            $booking->refresh();

            // Record initial status
            $booking->recordStatusChange(null, $booking->status, 'Booking created');

            // Update hotel booking count
            Hotel::where('id', $booking->hotel_id)->increment('bookings_count');

            return $booking;
        });
    }

    public function update(HotelBooking $booking, array $data): HotelBooking
    {
        return DB::transaction(function () use ($booking, $data) {
            $oldStatus = $booking->status;

            $booking->update(Arr::except($data, ['rooms', 'guests']));

            if ($oldStatus !== $booking->status) {
                $booking->recordStatusChange($oldStatus, $booking->status, 'Booking updated');
            }

            return $booking->fresh();
        });
    }

    public function updateStatus(HotelBooking $booking, string $status): HotelBooking
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
            case 'checked_in':
                $updateData['checked_in_at'] = now();
                break;
            case 'checked_out':
                $updateData['checked_out_at'] = now();
                break;
            case 'no_show':
                $updateData['no_show_at'] = now();
                break;
        }

        $booking->update($updateData);
        $booking->recordStatusChange($oldStatus, $status);

        // Send notifications
        if ($status === 'confirmed' && $booking->contact_email) {
            Mail::to($booking->contact_email)->queue(new HotelBookingConfirmed($booking));
        }

        if ($status === 'cancelled' && $booking->contact_email) {
            Mail::to($booking->contact_email)->queue(new HotelBookingCancelled($booking));
        }

        return $booking->fresh();
    }

    public function addPayment(HotelBooking $booking, array $data): HotelBookingPayment
    {
        return DB::transaction(function () use ($booking, $data) {
            $data['booking_id'] = $booking->id;
            $data['transaction_id'] = HotelBookingPayment::generateTransactionId();
            $data['processed_by'] = auth()->id();

            if ($data['status'] === 'completed') {
                $data['paid_at'] = now();
            }

            $payment = HotelBookingPayment::create($data);

            $booking->updatePaymentStatus();

            return $payment;
        });
    }

    public function verifyPayment(HotelBooking $booking, int $paymentId): void
    {
        $payment = $booking->payments()->findOrFail($paymentId);
        $payment->markAsCompleted();
    }

    public function getRecycleBin(): LengthAwarePaginator
    {
        return HotelBooking::onlyTrashed()
            ->with(['hotel', 'customer'])
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString();
    }

    public function restore(int $id): void
    {
        HotelBooking::onlyTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int $id): void
    {
        $booking = HotelBooking::onlyTrashed()->findOrFail($id);
        $booking->rooms()->forceDelete();
        $booking->payments()->forceDelete();
        $booking->forceDelete();
    }

    public function emptyRecycleBin(): void
    {
        HotelBooking::onlyTrashed()->each(fn ($booking) => $this->forceDelete($booking->id));
    }

    public function restoreAll(): void
    {
        HotelBooking::onlyTrashed()->restore();
    }

    public function bulkDelete(array $ids): int
    {
        $bookings = HotelBooking::whereIn('id', $ids)->get();
        $count = 0;

        foreach ($bookings as $booking) {
            if ($booking->payments()->where('status', 'completed')->exists()) {
                continue;
            }
            $booking->delete();
            $count++;
        }

        return $count;
    }

    public function bulkUpdateStatus(array $ids, string $status): int
    {
        $bookings = HotelBooking::whereIn('id', $ids)->get();
        $count = 0;

        foreach ($bookings as $booking) {
            if ($booking->status === $status) {
                continue;
            }

            $this->updateStatus($booking, $status);
            $count++;
        }

        return $count;
    }
}
