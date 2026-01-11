<?php

namespace Modules\Tour\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\Tour\Mail\TourBookingAdminNotificationMail;
use Modules\Tour\Mail\TourBookingConfirmationMail;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourBooking;
use Modules\Tour\Models\TourBookingExtra;
use Modules\Tour\Models\TourBookingTraveler;

class TourBookingService
{
    public function getAll()
    {
        return TourBooking::query()
            ->with(['tour', 'customer'])
            ->orderBy('id', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn ($booking) => [
                'id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'tour' => $booking->tour?->name,
                'travel_date' => $booking->travel_date?->format('d M Y'),
                'contact_name' => $booking->contact_name,
                'contact_email' => $booking->contact_email,
                'total_travelers' => $booking->total_travelers,
                'total_amount' => $booking->total_amount,
                'amount_paid' => $booking->amount_paid,
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'created_at' => $booking->created_at->format('d M Y H:i'),
            ]);
    }

    public function getById(int $id): TourBooking
    {
        return TourBooking::with([
            'tour',
            'pricing',
            'customer',
            'travelers',
            'payments',
            'extras.extra',
            'cancellation',
        ])->findOrFail($id);
    }

    public function create(array $data): TourBooking
    {
        return DB::transaction(function () use ($data) {
            $data['booking_number'] = TourBooking::generateBookingNumber();

            // Calculate pricing
            $pricing = $this->calculatePricing($data);
            $data = array_merge($data, $pricing);

            $booking = TourBooking::create($data);

            // Save travelers
            if (! empty($data['travelers'])) {
                $this->saveTravelers($booking, $data['travelers']);
            }

            // Save extras
            if (! empty($data['extras'])) {
                $this->saveExtras($booking, $data['extras']);
            }

            // Load relationships for emails
            $booking->load(['tour']);

            // Send confirmation email to customer
            if ($booking->contact_email) {
                Mail::to($booking->contact_email)->queue(new TourBookingConfirmationMail($booking));
            }

            // Send notification email to admin
            $adminEmail = config('mail.from.address');
            if ($adminEmail) {
                Mail::to($adminEmail)->queue(new TourBookingAdminNotificationMail($booking));
            }

            return $booking;
        });
    }

    public function update(TourBooking $booking, array $data): TourBooking
    {
        return DB::transaction(function () use ($booking, $data) {
            $booking->update($data);

            if (isset($data['travelers'])) {
                $booking->travelers()->delete();
                $this->saveTravelers($booking, $data['travelers']);
            }

            return $booking;
        });
    }

    public function updateStatus(TourBooking $booking, string $status): TourBooking
    {
        return DB::transaction(function () use ($booking, $status) {
            $booking->status = $status;

            if ($status === 'confirmed' && ! $booking->confirmed_at) {
                $booking->confirmed_at = now();
            }

            if ($status === 'cancelled' && ! $booking->cancelled_at) {
                $booking->cancelled_at = now();
            }

            $booking->save();

            return $booking;
        });
    }

    public function calculatePricing(array $data): array
    {
        $tour = Tour::find($data['tour_id']);
        if (! $tour) {
            return [];
        }

        $adultsCount = $data['adult_count'] ?? 1;
        $childrenCount = $data['child_count'] ?? 0;
        $infantsCount = $data['infant_count'] ?? 0;

        $adultPrice = $tour->discount_price ?? $tour->base_price;
        $childPrice = $tour->child_price ?? ($adultPrice * 0.6);
        $infantPrice = $tour->infant_price ?? 0;

        $subtotal = ($adultsCount * $adultPrice) + ($childrenCount * $childPrice) + ($infantsCount * $infantPrice);
        $extrasTotal = 0;
        $discountAmount = $data['discount_amount'] ?? 0;
        $taxAmount = 0; // Can be calculated based on settings

        $totalAmount = $subtotal + $extrasTotal - $discountAmount + $taxAmount;
        $depositAmount = $tour->deposit_amount ?? ($totalAmount * 0.3);
        $amountDue = $totalAmount - ($data['amount_paid'] ?? 0);

        return [
            'subtotal' => round($subtotal, 2),
            'extras_total' => round($extrasTotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'tax_amount' => round($taxAmount, 2),
            'total_amount' => round($totalAmount, 2),
            'deposit_amount' => round($depositAmount, 2),
            'amount_due' => round($amountDue, 2),
        ];
    }

    protected function saveTravelers(TourBooking $booking, array $travelers): void
    {
        foreach ($travelers as $i => $traveler) {
            if (empty($traveler['first_name'])) {
                continue;
            }

            TourBookingTraveler::create([
                'booking_id' => $booking->id,
                'type' => $traveler['type'] ?? 'adult',
                'is_lead' => $i === 0,
                'title' => $traveler['title'] ?? null,
                'first_name' => $traveler['first_name'],
                'last_name' => $traveler['last_name'] ?? '',
                'email' => $traveler['email'] ?? null,
                'phone' => $traveler['phone'] ?? null,
                'date_of_birth' => $traveler['date_of_birth'] ?? null,
                'gender' => $traveler['gender'] ?? null,
                'nationality' => $traveler['nationality'] ?? 'Bangladeshi',
                'passport_number' => $traveler['passport_number'] ?? null,
                'passport_expiry' => $traveler['passport_expiry'] ?? null,
                'nid_number' => $traveler['nid_number'] ?? null,
                'dietary_requirements' => $traveler['dietary_requirements'] ?? null,
                'medical_conditions' => $traveler['medical_conditions'] ?? null,
                'special_requests' => $traveler['special_requests'] ?? null,
            ]);
        }
    }

    protected function saveExtras(TourBooking $booking, array $extras): void
    {
        foreach ($extras as $extra) {
            if (empty($extra['extra_id']) || empty($extra['quantity'])) {
                continue;
            }

            TourBookingExtra::create([
                'booking_id' => $booking->id,
                'extra_id' => $extra['extra_id'],
                'quantity' => $extra['quantity'],
                'unit_price' => $extra['unit_price'] ?? 0,
                'total_price' => ($extra['unit_price'] ?? 0) * $extra['quantity'],
            ]);
        }
    }
}
