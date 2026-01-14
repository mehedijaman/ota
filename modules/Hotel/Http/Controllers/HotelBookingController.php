<?php

namespace Modules\Hotel\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Hotel\Http\Requests\AddHotelPaymentRequest;
use Modules\Hotel\Http\Requests\HotelBookingValidate;
use Modules\Hotel\Models\HotelBooking;
use Modules\Hotel\Services\GetHotelOptions;
use Modules\Hotel\Services\HotelBookingService;
use Modules\Support\Http\Controllers\BackendController;

class HotelBookingController extends BackendController
{
    public function __construct(
        protected HotelBookingService $bookingService,
        protected GetHotelOptions $hotelOptions
    ) {}

    public function index(): Response
    {
        return inertia('HotelBooking/BookingIndex', [
            'bookings' => $this->bookingService->getAll(),
            'stats' => $this->bookingService->getStats(),
            'hotels' => $this->hotelOptions->get(),
        ]);
    }

    public function show(int $id): Response
    {
        return inertia('HotelBooking/BookingShow', [
            'booking' => $this->bookingService->getById($id),
        ]);
    }

    public function create(): Response
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

        return inertia('HotelBooking/BookingForm', [
            'hotels' => $this->hotelOptions->get(),
            'customers' => $customers,
            'agents' => \Modules\Agent\Models\Agent::select('id', 'name', 'email', 'phone', 'whatsapp', 'address')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(HotelBookingValidate $request): RedirectResponse
    {
        $booking = $this->bookingService->create($request->validated());

        return redirect()->route('hotelBooking.show', $booking->id)
            ->with('success', 'Booking created successfully. Booking #'.$booking->booking_number);
    }

    public function edit(int $id): Response
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

        return inertia('HotelBooking/BookingForm', [
            'booking' => $this->bookingService->getById($id),
            'hotels' => $this->hotelOptions->get(),
            'customers' => $customers,
            'agents' => \Modules\Agent\Models\Agent::select('id', 'name', 'email', 'phone', 'whatsapp', 'address')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(HotelBookingValidate $request, int $id): RedirectResponse
    {
        $booking = HotelBooking::findOrFail($id);

        $this->bookingService->update($booking, $request->validated());

        return redirect()->route('hotelBooking.show', $id)
            ->with('success', 'Booking updated successfully.');
    }

    public function updateStatus(int $id, string $status): RedirectResponse
    {
        $booking = HotelBooking::findOrFail($id);

        $this->bookingService->updateStatus($booking, $status);

        return redirect()->back()
            ->with('success', 'Booking status updated to '.$status.'.');
    }

    public function addPayment(AddHotelPaymentRequest $request, int $id): RedirectResponse
    {
        $booking = HotelBooking::findOrFail($id);

        $this->bookingService->addPayment($booking, $request->validated());

        return redirect()->back()
            ->with('success', 'Payment of ৳'.number_format($request->amount, 2).' recorded successfully.');
    }

    public function verifyPayment(int $id, int $paymentId): RedirectResponse
    {
        $booking = HotelBooking::findOrFail($id);

        $this->bookingService->verifyPayment($booking, $paymentId);

        return redirect()->back()
            ->with('success', 'Payment verified successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $booking = HotelBooking::findOrFail($id);

        if ($booking->payments()->where('status', 'completed')->exists()) {
            return redirect()->route('hotelBooking.index')
                ->with('error', 'Cannot delete booking with completed payments.');
        }

        $booking->delete();

        return redirect()->route('hotelBooking.index')
            ->with('success', 'Booking deleted successfully.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:hotel_bookings,id']);

        $count = $this->bookingService->bulkDelete($request->ids);

        return redirect()->back()->with('success', "$count bookings deleted successfully.");
    }

    public function bulkUpdateStatus(\Illuminate\Http\Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:hotel_bookings,id',
            'status' => 'required|string|in:confirmed,cancelled,checked_in,checked_out',
        ]);

        $count = $this->bookingService->bulkUpdateStatus($request->ids, $request->status);

        return redirect()->back()->with('success', "$count bookings updated to {$request->status}.");
    }

    public function recycleBin(): Response
    {
        return inertia('HotelBooking/BookingRecycleBin', [
            'bookings' => $this->bookingService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $this->bookingService->restore($id);

        return redirect()->route('hotelBooking.recycleBin')
            ->with('success', 'Booking restored successfully.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $this->bookingService->forceDelete($id);

        return redirect()->route('hotelBooking.recycleBin')
            ->with('success', 'Booking permanently deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $this->bookingService->emptyRecycleBin();

        return redirect()->route('hotelBooking.recycleBin')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        $this->bookingService->restoreAll();

        return redirect()->route('hotelBooking.recycleBin')
            ->with('success', 'All bookings restored.');
    }
}
