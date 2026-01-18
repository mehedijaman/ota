<?php

namespace Modules\Flight\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Flight\DTOs\FlightSearchRequest;
use Modules\Flight\Models\FlightAirport;
use Modules\Flight\Models\FlightBooking;
use Modules\Flight\Models\FlightPopularRoute;
use Modules\Flight\Services\FlightBookingService;
use Modules\Flight\Services\FlightSearchService;

class SiteFlightController extends Controller
{
    public function __construct(
        protected FlightSearchService $searchService,
        protected FlightBookingService $bookingService,
    ) {}

    /**
     * Flight search page.
     */
    public function index()
    {
        $popularRoutes = FlightPopularRoute::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('rank')
            ->limit(8)
            ->get();

        $popularAirports = FlightAirport::query()
            ->where('is_active', true)
            ->where('is_popular', true)
            ->orderByDesc('search_priority')
            ->limit(12)
            ->get();

        return view('flight::search', [
            'popularRoutes' => $popularRoutes,
            'popularAirports' => $popularAirports,
        ]);
    }

    /**
     * Flight search results page.
     */
    public function results(Request $request)
    {
        $searchRequest = FlightSearchRequest::fromArray($request->all());
        $results = $this->searchService->search($searchRequest);

        $filters = [
            'airlines' => $results->pluck('airlineName', 'airlineCode')->unique(),
            'stops' => $results->pluck('stops')->unique()->sort(),
            'price_range' => [
                'min' => $results->min('totalPrice'),
                'max' => $results->max('totalPrice'),
            ],
            'duration_range' => [
                'min' => $results->min('durationMinutes'),
                'max' => $results->max('durationMinutes'),
            ],
        ];

        return view('flight::results', [
            'results' => $results,
            'search' => $searchRequest,
            'filters' => $filters,
        ]);
    }

    /**
     * Booking page.
     */
    public function booking(Request $request)
    {
        $offerId = $request->input('offer_id');

        return view('flight::booking', [
            'offerId' => $offerId,
            'search' => $request->all(),
        ]);
    }

    /**
     * Store booking from public site.
     */
    public function storeBooking(Request $request)
    {
        $request->validate([
            'contact_first_name' => 'required|string|max:100',
            'contact_last_name' => 'required|string|max:100',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string',
            'passengers' => 'required|array|min:1',
            'departure_date' => 'required|date',
        ]);

        $data = $request->all();
        $data['booking_source'] = 'website';
        $data['customer_id'] = auth()->id();
        $data['trip_type'] = $data['trip_type'] ?? 'one_way';
        $data['cabin_class'] = $data['cabin_class'] ?? 'economy';
        $data['adult_count'] = $data['adult_count'] ?? 1;
        $data['child_count'] = $data['child_count'] ?? 0;
        $data['infant_count'] = $data['infant_count'] ?? 0;

        $booking = $this->bookingService->create($data);

        return redirect()
            ->route('flight.payment', ['booking' => $booking->uuid])
            ->with('success', 'Booking created! Please proceed to payment.');
    }

    /**
     * Payment page.
     */
    public function payment(string $uuid)
    {
        $booking = FlightBooking::where('uuid', $uuid)->firstOrFail();

        return view('flight::payment', [
            'booking' => $booking->load(['segments', 'passengers']),
        ]);
    }

    /**
     * Process payment.
     */
    public function processPayment(Request $request, string $uuid)
    {
        $booking = FlightBooking::where('uuid', $uuid)->firstOrFail();

        // Basic validation for manual payment simulation
        $request->validate([
            'payment_method' => 'required|string|in:card,bkash,bank',
        ]);

        // Map frontend methods to backend expected values
        $methodMap = [
            'card' => 'credit_card',
            'bkash' => 'bkash',
            'bank' => 'bank_transfer',
        ];

        // Simulate successful payment data
        $paymentData = [
            'type' => 'payment',
            'method' => $methodMap[$request->payment_method] ?? 'cash',
            'amount' => $booking->total_amount, // Pay full amount
            'currency_code' => 'BDT',
            'status' => 'completed',
            'reference_number' => 'TXN-'.strtoupper(bin2hex(random_bytes(4))),
            'notes' => 'Payment via website',
        ];

        // Add payment
        $this->bookingService->addPayment($booking, $paymentData);

        // Auto-confirm booking if fully paid (mock logic)
        // In real world, this happens via webhook or subsequent check.
        // Here we assume immediate success.
        if ($booking->refresh()->payment_status === 'paid' || $booking->payment_status === 'partial') {
             $this->bookingService->updateStatus($booking, 'confirmed', 'Auto-confirmed after payment');
        }

        return redirect()
            ->route('flight.confirmation', ['booking' => $booking->uuid])
            ->with('success', 'Payment successful! Your booking is confirmed.');
    }

    /**
     * Booking confirmation page.
     */
    public function confirmation(string $uuid)
    {
        $booking = FlightBooking::where('uuid', $uuid)
            ->with(['segments.airline', 'segments.departureAirport', 'segments.arrivalAirport', 'passengers'])
            ->firstOrFail();

        return view('flight::confirmation', [
            'booking' => $booking,
        ]);
    }

    /**
     * My bookings page.
     */
    public function myBookings()
    {
        $customerId = auth()->id();

        $bookings = FlightBooking::query()
            ->where('customer_id', $customerId)
            ->with(['segments.airline', 'segments.departureAirport', 'segments.arrivalAirport'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('flight::my-bookings', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * View booking details.
     */
    public function viewBooking(string $uuid)
    {
        $booking = FlightBooking::where('uuid', $uuid)
            ->with(['segments.airline', 'segments.departureAirport', 'segments.arrivalAirport', 'passengers', 'payments'])
            ->firstOrFail();

        if (auth()->check() && $booking->customer_id !== auth()->id()) {
            abort(403);
        }

        return view('flight::booking-details', [
            'booking' => $booking,
        ]);
    }
}
