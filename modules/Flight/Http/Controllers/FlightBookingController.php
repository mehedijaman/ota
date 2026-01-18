<?php

namespace Modules\Flight\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Flight\Http\Requests\FlightBookingValidate;
use Modules\Flight\Http\Requests\FlightPaymentValidate;
use Modules\Flight\Models\FlightBooking;
use Modules\Flight\Services\FlightBookingService;
use Modules\Flight\Services\FlightCancellationService;
use Modules\Flight\Services\FlightTicketingService;

class FlightBookingController extends Controller
{
    public function __construct(
        protected FlightBookingService $bookingService,
        protected FlightTicketingService $ticketingService,
        protected FlightCancellationService $cancellationService,
    ) {}

    public function index(): Response
    {
        return Inertia::render('FlightBooking/FlightBookingIndex', [
            'bookings' => $this->bookingService->getAll(),
            'stats' => $this->bookingService->getStats(),
            'filters' => request()->only(['search', 'status', 'payment_status', 'ticketing_status', 'date_from', 'date_to']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('FlightBooking/FlightBookingForm');
    }

    public function store(FlightBookingValidate $request): RedirectResponse
    {
        $booking = $this->bookingService->create($request->validated());

        return redirect()
            ->route('flightBooking.show', $booking->id)
            ->with('success', 'Booking created successfully. Booking #: '.$booking->booking_number);
    }

    public function show(int $id): Response
    {
        return Inertia::render('FlightBooking/FlightBookingShow', [
            'booking' => $this->bookingService->getById($id),
        ]);
    }

    public function edit(int $id): Response
    {
        return Inertia::render('FlightBooking/FlightBookingForm', [
            'booking' => $this->bookingService->getById($id),
        ]);
    }

    public function update(FlightBookingValidate $request, int $id): RedirectResponse
    {
        $booking = FlightBooking::findOrFail($id);
        $this->bookingService->update($booking, $request->validated());

        return redirect()
            ->route('flightBooking.show', $id)
            ->with('success', 'Booking updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        FlightBooking::findOrFail($id)->delete();

        return redirect()
            ->route('flightBooking.index')
            ->with('success', 'Booking deleted successfully.');
    }

    // Status Management
    public function updateStatus(int $id, string $status): RedirectResponse
    {
        $booking = FlightBooking::findOrFail($id);
        $this->bookingService->updateStatus($booking, $status);

        return back()->with('success', 'Booking status updated to '.ucfirst($status));
    }

    public function confirm(int $id): RedirectResponse
    {
        $booking = FlightBooking::findOrFail($id);
        $this->bookingService->updateStatus($booking, 'confirmed');

        return back()->with('success', 'Booking confirmed successfully.');
    }

    // Ticketing
    public function issueTicket(int $id): RedirectResponse
    {
        $booking = FlightBooking::findOrFail($id);

        try {
            $this->ticketingService->issueTicket($booking);

            return back()->with('success', 'Tickets issued successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function voidTicket(int $id, Request $request): RedirectResponse
    {
        $booking = FlightBooking::findOrFail($id);

        try {
            $this->ticketingService->voidTicket($booking, $request->input('reason'));

            return back()->with('success', 'Tickets voided successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Payments
    public function addPayment(FlightPaymentValidate $request, int $id): RedirectResponse
    {
        $booking = FlightBooking::findOrFail($id);
        $this->bookingService->addPayment($booking, $request->validated());

        return back()->with('success', 'Payment added successfully.');
    }

    public function verifyPayment(int $id, int $paymentId): RedirectResponse
    {
        $booking = FlightBooking::findOrFail($id);
        $this->bookingService->verifyPayment($booking, $paymentId);

        return back()->with('success', 'Payment verified successfully.');
    }

    // Cancellation
    public function requestCancellation(int $id, Request $request): RedirectResponse
    {
        $booking = FlightBooking::findOrFail($id);

        try {
            $this->cancellationService->requestCancellation($booking, $request->all());

            return back()->with('success', 'Cancellation request submitted.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function approveCancellation(int $id): RedirectResponse
    {
        $booking = FlightBooking::findOrFail($id);
        $cancellation = $booking->cancellation;

        if (! $cancellation) {
            return back()->with('error', 'No cancellation request found.');
        }

        $this->cancellationService->approveCancellation($cancellation);

        return back()->with('success', 'Cancellation approved.');
    }

    public function processRefund(int $id, Request $request): RedirectResponse
    {
        $booking = FlightBooking::findOrFail($id);
        $cancellation = $booking->cancellation;

        if (! $cancellation) {
            return back()->with('error', 'No cancellation found.');
        }

        try {
            $this->cancellationService->processRefund($cancellation, $request->all());

            return back()->with('success', 'Refund processed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Recycle Bin
    public function recycleBin(): Response
    {
        return Inertia::render('FlightBooking/FlightBookingRecycleBin', [
            'bookings' => $this->bookingService->getRecycleBin(),
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        $this->bookingService->restore($id);

        return back()->with('success', 'Booking restored successfully.');
    }

    public function destroyForce(int $id): RedirectResponse
    {
        $this->bookingService->forceDelete($id);

        return redirect()->route('flightBooking.recycleBin')->with('success', 'Booking permanently deleted.');
    }

    // Bulk Actions
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $count = $this->bookingService->bulkDelete($request->input('ids', []));

        return back()->with('success', "{$count} bookings deleted.");
    }

    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $count = $this->bookingService->bulkUpdateStatus(
            $request->input('ids', []),
            $request->input('status')
        );

        return back()->with('success', "{$count} bookings updated.");
    }
}
