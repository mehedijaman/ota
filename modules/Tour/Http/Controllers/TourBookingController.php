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
        $customers = \Modules\Customer\Models\Customer::select('id', 'first_name', 'last_name', 'email', 'phone', 'whatsapp', 'address')
            ->orderBy('first_name')
            ->get()
            ->map(fn ($customer) => [
                'id' => $customer->id,
                'name' => $customer->full_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'whatsapp' => $customer->whatsapp,
                'address' => $customer->address,
            ]);

        return inertia('TourBooking/BookingForm', [
            'tours' => $tourOptions->getWithPricing(),
            'customers' => $customers,
            'agents' => \Modules\Agent\Models\Agent::select(
                'id',
                'name',
                'email',
                'phone',
                'whatsapp',
                'address'
            )
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
        $customers = \Modules\Customer\Models\Customer::select('id', 'first_name', 'last_name', 'email', 'phone', 'whatsapp', 'address')
            ->orderBy('first_name')
            ->get()
            ->map(fn ($customer) => [
                'id' => $customer->id,
                'name' => $customer->full_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'whatsapp' => $customer->whatsapp,
                'address' => $customer->address,
            ]);

        return inertia('TourBooking/BookingForm', [
            'booking' => $this->bookingService->getById($id),
            'tours' => $tourOptions->getWithPricing(),
            'customers' => $customers,
            'agents' => \Modules\Agent\Models\Agent::select(
                'id',
                'name',
                'email',
                'phone',
                'whatsapp',
                'address'
            )
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

    public function recycleBin(): Response
    {
        return inertia('TourBooking/BookingRecycleBin', [
            'bookings' => $this->bookingService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $this->bookingService->restore($id);

        return redirect()->route('tourBooking.recycleBin')
            ->with('success', 'Booking restored successfully.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $this->bookingService->forceDelete($id);

        return redirect()->route('tourBooking.recycleBin')
            ->with('success', 'Booking permanently deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $this->bookingService->emptyRecycleBin();

        return redirect()->route('tourBooking.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $this->bookingService->restoreAll();

        return redirect()->route('tourBooking.recycleBin')
            ->with('success', 'All bookings restored.');
    }
}
