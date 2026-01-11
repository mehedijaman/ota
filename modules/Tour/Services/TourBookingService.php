<?php

namespace Modules\Tour\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\Tour\Mail\TourBookingAdminNotificationMail;
use Modules\Tour\Mail\TourBookingConfirmationMail;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourBooking;
use Modules\Tour\Models\TourBookingExtra;
use Modules\Tour\Models\TourBookingPayment;
use Modules\Tour\Models\TourBookingTraveler;
use Modules\Tour\Models\TourExtra;

class TourBookingService
{
    public function getAll()
    {
        $query = TourBooking::query()
            ->with(['tour', 'customer'])
            ->orderBy('id', 'desc');

        // Apply filters
        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%");
            });
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('payment_status')) {
            $query->where('payment_status', request('payment_status'));
        }

        if (request('travel_from')) {
            $query->whereDate('travel_date', '>=', request('travel_from'));
        }

        if (request('travel_to')) {
            $query->whereDate('travel_date', '<=', request('travel_to'));
        }

        // Also support legacy search
        $query->search(request('searchContext'), request('searchTerm'));

        return $query
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

    public function getStats(): array
    {
        return [
            'total' => TourBooking::count(),
            'pending' => TourBooking::where('status', 'pending')->count(),
            'confirmed' => TourBooking::where('status', 'confirmed')->count(),
            'revenue' => TourBooking::whereIn('status', ['confirmed', 'completed'])->sum('amount_paid'),
        ];
    }

    public function getById(int $id): TourBooking
    {
        return TourBooking::with([
            'tour',
            'pricing',
            'customer',
            'agent',
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
            // Recalculate pricing
            $pricing = $this->calculatePricing($data);
            $data = array_merge($data, $pricing);

            $booking->update($data);

            if (isset($data['travelers'])) {
                $booking->travelers()->delete();
                $this->saveTravelers($booking, $data['travelers']);
            }

            if (isset($data['extras'])) {
                $booking->extras()->delete();
                $this->saveExtras($booking, $data['extras']);
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

    /**
     * Add a manual payment to a booking.
     *
     * @param  array<string, mixed>  $data
     */
    public function addPayment(TourBooking $booking, array $data): TourBooking
    {
        return DB::transaction(function () use ($booking, $data) {
            $payment = $booking->payments()->create([
                'transaction_id' => ! empty($data['transaction_id']) ? $data['transaction_id'] : TourBookingPayment::generateTransactionId(),
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'payment_gateway' => 'manual',
                'status' => 'completed',
                'notes' => $data['notes'] ?? null,
                'processed_by' => auth()->id(),
                'paid_at' => now(),
            ]);

            $newAmountPaid = $booking->amount_paid + $data['amount'];
            $newAmountDue = $booking->total_amount - $newAmountPaid;

            $paymentStatus = 'unpaid';
            if ($newAmountPaid >= $booking->total_amount) {
                $paymentStatus = 'paid';
            } elseif ($newAmountPaid > 0) {
                $paymentStatus = 'partial';
            }

            $booking->update([
                'amount_paid' => $newAmountPaid,
                'amount_due' => max(0, $newAmountDue),
                'payment_status' => $paymentStatus,
            ]);

            return $booking->fresh();
        });
    }

    /**
     * Verify a pending payment and update booking totals.
     */
    public function verifyPayment(TourBooking $booking, int $paymentId): TourBooking
    {
        return DB::transaction(function () use ($booking, $paymentId) {
            $payment = $booking->payments()->where('id', $paymentId)->firstOrFail();

            if ($payment->status !== 'pending') {
                throw new \Exception('Only pending payments can be verified.');
            }

            $payment->update([
                'status' => 'completed',
                'processed_by' => auth()->id(),
                'paid_at' => now(),
            ]);

            $newAmountPaid = $booking->amount_paid + $payment->amount;
            $newAmountDue = $booking->total_amount - $newAmountPaid;

            $paymentStatus = 'unpaid';
            if ($newAmountPaid >= $booking->total_amount) {
                $paymentStatus = 'paid';
            } elseif ($newAmountPaid > 0) {
                $paymentStatus = 'partial';
            }

            $booking->update([
                'amount_paid' => $newAmountPaid,
                'amount_due' => max(0, $newAmountDue),
                'payment_status' => $paymentStatus,
            ]);

            return $booking->fresh();
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

        // Use selected pricing plan if provided, otherwise fall back to tour's base price
        $pricing = null;
        if (! empty($data['pricing_id'])) {
            $pricing = $tour->pricings()->find($data['pricing_id']);
        }

        if ($pricing) {
            $adultPrice = $pricing->adult_price ?? 0;
            $childPrice = $pricing->child_price ?? 0;
            $infantPrice = $pricing->infant_price ?? 0;
        } else {
            $adultPrice = $tour->discount_price ?? $tour->base_price;
            $childPrice = $tour->child_price ?? ($adultPrice * 0.6);
            $infantPrice = $tour->infant_price ?? 0;
        }

        $subtotal = ($adultsCount * $adultPrice) + ($childrenCount * $childPrice) + ($infantsCount * $infantPrice);

        // Calculate extras total from extras array
        $extrasTotal = 0;
        if (! empty($data['extras']) && is_array($data['extras'])) {
            $extraIds = array_column($data['extras'], 'extra_id');
            $tourExtras = TourExtra::whereIn('id', $extraIds)->get()->keyBy('id');

            foreach ($data['extras'] as $extra) {
                $extraId = $extra['extra_id'] ?? null;
                $quantity = $extra['quantity'] ?? 1;

                if ($extraId && isset($tourExtras[$extraId])) {
                    $tourExtra = $tourExtras[$extraId];
                    $extraPrice = $tourExtra->price ?? 0;

                    // Handle different price types
                    if ($tourExtra->price_type === 'per_person') {
                        $extrasTotal += $extraPrice * $quantity * ($adultsCount + $childrenCount);
                    } elseif ($tourExtra->price_type === 'per_day') {
                        $extrasTotal += $extraPrice * $quantity;
                    } else {
                        // per_booking
                        $extrasTotal += $extraPrice * $quantity;
                    }
                }
            }
        }

        $discountAmount = $data['discount_amount'] ?? 0;
        $taxAmount = $data['tax_amount'] ?? 0;

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
        // Get all extra IDs and fetch their prices from the database
        $extraIds = array_column($extras, 'extra_id');
        $tourExtras = TourExtra::whereIn('id', $extraIds)->get()->keyBy('id');

        foreach ($extras as $extra) {
            if (empty($extra['extra_id']) || empty($extra['quantity'])) {
                continue;
            }

            $tourExtra = $tourExtras[$extra['extra_id']] ?? null;
            $unitPrice = $tourExtra?->price ?? 0;
            $quantity = $extra['quantity'];

            TourBookingExtra::create([
                'booking_id' => $booking->id,
                'extra_id' => $extra['extra_id'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $unitPrice * $quantity,
            ]);
        }
    }

    public function getRecycleBin()
    {
        return TourBooking::onlyTrashed()
            ->with(['tour'])
            ->orderBy('deleted_at', 'desc')
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
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'deleted_at' => $booking->deleted_at->toDateTimeString(),
            ]);
    }

    public function restore(int $id): bool
    {
        $booking = TourBooking::onlyTrashed()->findOrFail($id);

        return $booking->restore();
    }

    public function forceDelete(int $id): bool
    {
        $booking = TourBooking::onlyTrashed()->findOrFail($id);

        return DB::transaction(function () use ($booking) {
            $booking->travelers()->forceDelete();
            $booking->extras()->forceDelete();
            $booking->payments()->forceDelete();

            return $booking->forceDelete();
        });
    }

    public function emptyRecycleBin(): void
    {
        TourBooking::onlyTrashed()->each(function ($booking) {
            $booking->travelers()->forceDelete();
            $booking->extras()->forceDelete();
            $booking->payments()->forceDelete();
            $booking->forceDelete();
        });
    }

    public function restoreAll(): void
    {
        TourBooking::onlyTrashed()->restore();
    }
}
