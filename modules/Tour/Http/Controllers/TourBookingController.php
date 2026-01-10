<?php

namespace Modules\Tour\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Tour\Http\Requests\TourBookingValidate;
use Modules\Tour\Models\TourBooking;
use Modules\Tour\Services\GetTourOptions;
use Modules\Tour\Services\TourBookingService;

class TourBookingController extends BackendController
{
    public function __construct(
        protected TourBookingService $bookingService
    ) {}

    public function index(): Response
    {
        return inertia('TourBooking/BookingIndex', [
            'bookings' => $this->bookingService->getAll(),
        ]);
    }

    public function show(int $id): Response
    {
        return inertia('TourBooking/BookingShow', [
            'booking' => $this->bookingService->getById($id),
        ]);
    }

    public function create(GetTourOptions $tourOptions): Response
    {
        return inertia('TourBooking/BookingForm', [
            'tours' => $tourOptions->getWithPricing(),
            'customers' => \Modules\Customer\Models\Customer::select('id', 'name', 'email')
                ->orderBy('name')
                ->get(),
            'agents' => \Modules\Agent\Models\Agent::select('id', 'name', 'email')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(TourBookingValidate $request): RedirectResponse
    {
        $booking = $this->bookingService->create($request->validated());

        return redirect()->route('tourBooking.show', $booking->id)
            ->with('success', 'Booking created successfully. Booking #'.$booking->booking_number);
    }

    public function edit(int $id, GetTourOptions $tourOptions): Response
    {
        return inertia('TourBooking/BookingForm', [
            'booking' => $this->bookingService->getById($id),
            'tours' => $tourOptions->getWithPricing(),
            'customers' => \Modules\Customer\Models\Customer::select('id', 'name', 'email')
                ->orderBy('name')
                ->get(),
            'agents' => \Modules\Agent\Models\Agent::select('id', 'name', 'email')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(TourBookingValidate $request, int $id): RedirectResponse
    {
        $booking = TourBooking::findOrFail($id);

        $this->bookingService->update($booking, $request->validated());

        return redirect()->route('tourBooking.show', $id)
            ->with('success', 'Booking updated successfully.');
    }

    public function updateStatus(int $id, string $status): RedirectResponse
    {
        $booking = TourBooking::findOrFail($id);

        $this->bookingService->updateStatus($booking, $status);

        return redirect()->back()
            ->with('success', 'Booking status updated to '.$status.'.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $booking = TourBooking::findOrFail($id);

        if ($booking->payments()->where('status', 'completed')->exists()) {
            return redirect()->route('tourBooking.index')
                ->with('error', 'Cannot delete booking with completed payments.');
        }

        $booking->delete();

        return redirect()->route('tourBooking.index')
            ->with('success', 'Booking deleted successfully.');
    }
}
