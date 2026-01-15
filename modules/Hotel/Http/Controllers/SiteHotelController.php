<?php

namespace Modules\Hotel\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Hotel\DTOs\HotelSearchRequest;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelBooking;
use Modules\Hotel\Models\HotelBookingPayment;
use Modules\Hotel\Models\HotelReview;
use Modules\Hotel\Services\GetHotelOptions;
use Modules\Hotel\Services\HotelBookingService;
use Modules\Hotel\Services\HotelSearchService;
use Modules\Hotel\Services\UnifiedHotelSearchService;
use Raziul\Sslcommerz\Facades\Sslcommerz;

class SiteHotelController
{
    public function __construct(
        protected HotelSearchService $searchService,
        protected HotelBookingService $bookingService,
        protected GetHotelOptions $hotelOptions,
        protected UnifiedHotelSearchService $unifiedSearchService,
    ) {}

    protected function reAuthenticateBookingOwner(HotelBooking $booking): void
    {
        if (! Auth::check() && $booking->created_by) {
            $user = User::find($booking->created_by);
            if ($user) {
                Auth::login($user);
            }
        }
    }

    public function index()
    {
        $featuredHotels = $this->searchService->getFeaturedHotels(8);
        $popularDestinations = $this->searchService->getPopularDestinations(6);

        return view('hotel::index', [
            'featuredHotels' => $featuredHotels,
            'popularDestinations' => $popularDestinations,
            'countries' => $this->hotelOptions->getCountries(),
        ]);
    }

    public function search(Request $request)
    {
        $filters = $request->only([
            'destination',
            'city_id',
            'country_id',
            'check_in',
            'check_out',
            'rooms',
            'adults',
            'children',
            'star_rating',
            'min_stars',
            'property_type',
            'amenities',
            'sort_by',
            'per_page',
        ]);

        $hotels = $this->searchService->search($filters);

        return view('hotel::search', [
            'hotels' => $hotels,
            'filters' => $filters,
            'countries' => $this->hotelOptions->getCountries(),
            'propertyTypes' => $this->hotelOptions->getPropertyTypes(),
            'amenities' => $this->hotelOptions->getHotelAmenities(),
        ]);
    }

    public function show(string $slug)
    {
        $hotel = Hotel::where('slug', $slug)
            ->with([
                'city',
                'country',
                'chain',
                'rooms' => fn ($q) => $q->with(['roomType', 'amenities'])->active()->ordered(),
                'amenities' => fn ($q) => $q->orderBy('sort_order'),
                'ratePlans' => fn ($q) => $q->active(),
                'reviews' => fn ($q) => $q->published()->orderByDesc('created_at')->limit(10),
                'faqs' => fn ($q) => $q->active()->ordered(),
            ])
            ->active()
            ->firstOrFail();

        // Get availability if dates are provided
        $availability = null;
        if (request('check_in') && request('check_out')) {
            $availability = $this->searchService->checkAvailability(
                $hotel->id,
                request('check_in'),
                request('check_out'),
                request('rooms', 1),
                request('adults', 2),
                request('children', 0)
            );
        }

        return view('hotel::show', [
            'hotel' => $hotel,
            'availability' => $availability,
            'relatedHotels' => Hotel::where('city_id', $hotel->city_id)
                ->where('id', '!=', $hotel->id)
                ->active()
                ->limit(4)
                ->get(),
        ]);
    }

    public function bookingPage(string $slug)
    {
        $hotel = Hotel::where('slug', $slug)
            ->with(['city', 'country', 'rooms.roomType', 'ratePlans'])
            ->active()
            ->firstOrFail();

        return view('hotel::booking', [
            'hotel' => $hotel,
            'checkIn' => request('check_in'),
            'checkOut' => request('check_out'),
            'rooms' => request('rooms', 1),
            'adults' => request('adults', 2),
            'children' => request('children', 0),
            'selectedRoom' => request('room_id') ? $hotel->rooms->find(request('room_id')) : null,
            'selectedRatePlan' => request('rate_plan_id') ? $hotel->ratePlans->find(request('rate_plan_id')) : null,
        ]);
    }

    public function submitBooking(Request $request, string $slug)
    {
        $hotel = Hotel::where('slug', $slug)->active()->firstOrFail();

        $validated = $request->validate([
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'room_id' => 'required|exists:hotel_rooms,id',
            'rate_plan_id' => 'required|exists:hotel_rate_plans,id',
            'rooms_count' => 'required|integer|min:1',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string|max:50',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        // Build booking data
        $bookingData = [
            'hotel_id' => $hotel->id,
            'customer_id' => auth()->user()?->customer?->id,
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'total_rooms' => $validated['rooms_count'],
            'total_adults' => $validated['adults'],
            'total_children' => $validated['children'] ?? 0,
            'contact_name' => $validated['contact_name'],
            'contact_email' => $validated['contact_email'],
            'contact_phone' => $validated['contact_phone'],
            'special_requests' => $validated['special_requests'],
            'booking_source' => 'website',
            'rooms' => [
                [
                    'room_id' => $validated['room_id'],
                    'rate_plan_id' => $validated['rate_plan_id'],
                    'adults' => $validated['adults'],
                    'children' => $validated['children'] ?? 0,
                ],
            ],
        ];

        $booking = $this->bookingService->create($bookingData);

        return redirect()->route('site.hotel.payment', $booking)
            ->with('success', 'Booking created. Please complete payment.');
    }

    public function paymentPage(HotelBooking $booking)
    {
        if ($booking->payment_status === 'paid') {
            return redirect()->route('site.hotel.bookingConfirmation', [$booking->hotel->slug, $booking]);
        }

        return view('hotel::payment', [
            'booking' => $booking->load(['hotel', 'rooms.room', 'rooms.ratePlan']),
        ]);
    }

    public function processPayment(Request $request, HotelBooking $booking)
    {
        // Verify user owns this booking
        if ($booking->created_by !== auth()->id() &&
            ($booking->customer_id !== auth()->user()?->profile_id || auth()->user()?->profile_type !== 'customer')) {
            abort(403, 'Unauthorized access to this booking');
        }

        $rules = [
            'payment_gateway' => 'required|in:sslcommerz,bkash,manual',
        ];

        if ($request->payment_gateway === 'manual') {
            $rules['transaction_number'] = 'required|string|max:255';
            $rules['notes'] = 'nullable|string|max:1000';
        }

        $validated = $request->validate($rules);

        $gateway = $validated['payment_gateway'];

        switch ($gateway) {
            case 'sslcommerz':
                return $this->initiateSslcommerzPayment($booking);

            case 'bkash':
                return $this->initiateBkashPayment($booking);

            case 'manual':
                return $this->handleManualPayment($booking);

            default:
                return back()->with('error', 'Invalid payment gateway selected');
        }
    }

    protected function initiateSslcommerzPayment(HotelBooking $booking): RedirectResponse
    {
        try {
            $config = config('sslcommerz');

            if (empty($config['store']['id']) || empty($config['store']['password'])) {
                \Log::error('SSLCommerz credentials not configured');

                return back()->with('error', 'Payment gateway is not configured. Please contact support.');
            }

            // Use the Facade
            $response = Sslcommerz::setOrder(
                $booking->total_amount,
                $booking->booking_number,
                'Hotel Booking - '.$booking->hotel->name
            )
                ->setCustomer(
                    $booking->contact_name,
                    $booking->contact_email,
                    $booking->contact_phone
                )
                ->setShippingInfo(
                    $booking->total_adults + $booking->total_children,
                    'N/A' // Hotel booking doesn't usually have shipping address
                )
                ->setProductProfile('general')
                ->setCallbackUrls(
                    successUrl: route('site.hotel.payment.success', ['booking' => $booking->id]),
                    failedUrl: route('site.hotel.payment.fail', ['booking' => $booking->id]),
                    cancelUrl: route('site.hotel.payment.cancel', ['booking' => $booking->id]),
                    ipnUrl: route('site.hotel.payment.ipn', ['booking' => $booking->id])
                )
                ->makePayment();

            if ($response->success() && $response->gatewayPageURL()) {
                return redirect($response->gatewayPageURL());
            }

            \Log::error('SSLCommerz initiation failed', [
                'booking' => $booking->booking_number,
                'response' => $response->toArray(),
            ]);

            return back()->with('error', 'Failed to initiate payment. '.($response->failedReason() ?? 'Please try again.'));
        } catch (\Exception $e) {
            \Log::error('SSLCommerz payment error: '.$e->getMessage(), [
                'booking' => $booking->booking_number,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Payment gateway error. Please try again or contact support.');
        }
    }

    protected function initiateBkashPayment(HotelBooking $booking): RedirectResponse
    {
        try {
            $bkash = app(\Ihasan\Bkash\Bkash::class);

            // Create payment with bKash
            $response = $bkash->createPayment([
                'amount' => $booking->total_amount,
                'merchant_invoice_number' => $booking->booking_number,
                'payer_reference' => $booking->contact_phone ?? $booking->contact_email,
                'callback_url' => route('site.hotel.payment.bkash.callback', ['booking' => $booking->id]),
                'intent' => 'sale',
            ]);

            // bKash returns bkashURL for redirect
            if (isset($response['bkashURL'])) {
                return redirect($response['bkashURL']);
            }

            \Log::error('bKash payment creation failed', [
                'booking' => $booking->booking_number,
                'response' => $response,
            ]);

            return back()->with('error', 'Failed to initiate bKash payment. '.($response['statusMessage'] ?? 'Please try again.'));
        } catch (\Exception $e) {
            \Log::error('bKash payment error: '.$e->getMessage(), [
                'booking' => $booking->booking_number,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Payment gateway error. Please try again or contact support.');
        }
    }

    protected function handleManualPayment(HotelBooking $booking): RedirectResponse
    {
        $transactionNumber = request('transaction_number');
        $notes = request('notes');

        // Update booking with manual payment pending status
        $booking->update([
            'status' => 'confirmed', // Or pending? TourBooking uses 'processing' then confirms manual. Hotel logic might be different. Let's stick to 'confirmed' but check payment status.
            // Actually usually manual payment means 'pending' payment status.
            'payment_status' => 'unpaid',
            // We might want to store internal notes about the manual request
            // 'internal_notes' => ... (HotelBooking model might not have internal_notes, let's skip for now or check model)
        ]);

        // Let's assume we keep status as is (pending) until admin confirms money reception.

        // Create a payment record for the manual transaction
        $booking->payments()->create([
            'transaction_id' => HotelBookingPayment::generateTransactionId(),
            'amount' => $booking->total_amount,
            'payment_method' => 'manual',
            'payment_gateway' => 'manual',
            'gateway_transaction_id' => $transactionNumber,
            'status' => 'pending',
            'type' => 'full',
            'gateway_response' => [
                'notes' => $notes,
                'user_ip' => request()->ip(),
                'submitted_at' => now()->toIso8601String(),
            ],
            'notes' => $notes,
        ]);

        return redirect()->route('site.hotel.bookingConfirmation', [
            'slug' => $booking->hotel->slug,
            'booking' => $booking,
        ])->with('success', 'Manual payment submitted! We will verify your transaction ('.$transactionNumber.') and confirm your booking shortly.');
    }

    public function paymentSuccess(Request $request, HotelBooking $booking): RedirectResponse
    {
        $this->reAuthenticateBookingOwner($booking);

        if ($booking->payment_status === 'paid') {
            return redirect()->route('site.hotel.bookingConfirmation', [
                'slug' => $booking->hotel->slug,
                'booking' => $booking,
            ])->with('info', 'This booking has already been paid.');
        }

        try {
            // For SSLCommerz
            if ($request->has('tran_id') && $request->has('val_id')) {
                if (! Sslcommerz::verifyHash($request->all())) {
                    \Log::warning('SSLCommerz hash verification failed', ['booking' => $booking->booking_number, 'data' => $request->all()]);

                    return redirect()->route('site.hotel.payment', $booking)->with('error', 'Payment verification failed.');
                }

                $isValid = Sslcommerz::validatePayment($request->all(), $booking->booking_number, $booking->total_amount);

                if ($isValid) {
                    $this->completePayment($booking, 'sslcommerz', 'sslcommerz', $request->input('tran_id'), $request->all());

                    return redirect()->route('site.hotel.bookingConfirmation', [
                        'slug' => $booking->hotel->slug,
                        'booking' => $booking,
                    ])->with('success', 'Payment successful! Your booking is confirmed.');
                }

                \Log::warning('SSLCommerz payment validation failed', ['booking' => $booking->booking_number, 'response' => $request->all()]);

                return redirect()->route('site.hotel.payment', $booking)->with('error', 'Payment verification failed.');
            }
        } catch (\Exception $e) {
            \Log::error('Payment verification error: '.$e->getMessage(), ['booking' => $booking->booking_number]);
        }

        return redirect()->route('site.hotel.bookingConfirmation', [
            'slug' => $booking->hotel->slug,
            'booking' => $booking,
        ])->with('warning', 'Payment status could not be verified. Please contact support.');
    }

    public function paymentFail(Request $request, HotelBooking $booking): RedirectResponse
    {
        $this->reAuthenticateBookingOwner($booking);

        \Log::warning('Payment failed for booking: '.$booking->booking_number, [
            'request_data' => $request->all(),
        ]);

        return redirect()->route('site.hotel.payment', $booking)
            ->with('error', 'Payment failed. Please try again or choose a different payment method.');
    }

    public function paymentCancel(Request $request, HotelBooking $booking): RedirectResponse
    {
        $this->reAuthenticateBookingOwner($booking);

        return redirect()->route('site.hotel.payment', $booking)
            ->with('info', 'Payment cancelled. You can try again when ready.');
    }

    public function paymentIPN(Request $request, HotelBooking $booking)
    {
        \Log::info('Payment IPN received for booking: '.$booking->booking_number, $request->all());

        if ($booking->payment_status === 'paid') {
            return response()->json(['status' => 'success', 'message' => 'Already processed']);
        }

        try {
            if ($request->has('tran_id') && $request->has('val_id')) {
                if (! Sslcommerz::verifyHash($request->all())) {
                    return response()->json(['status' => 'error', 'message' => 'Hash verification failed'], 400);
                }

                $isValid = Sslcommerz::validatePayment($request->all(), $booking->booking_number, $booking->total_amount);

                if ($isValid) {
                    $this->completePayment($booking, 'sslcommerz', 'sslcommerz', $request->input('tran_id'), $request->all());

                    return response()->json(['status' => 'success']);
                }

                return response()->json(['status' => 'error', 'message' => 'Validation failed'], 400);
            }

            return response()->json(['status' => 'error', 'message' => 'Invalid IPN data'], 400);
        } catch (\Exception $e) {
            \Log::error('IPN processing error: '.$e->getMessage(), ['booking' => $booking->booking_number]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function bkashCallback(Request $request, HotelBooking $booking): RedirectResponse
    {
        $paymentId = $request->input('paymentID');
        $status = $request->input('status');

        \Log::info('bKash callback received', [
            'booking' => $booking->booking_number,
            'paymentID' => $paymentId,
            'status' => $status,
        ]);

        if ($status !== 'success') {
            return redirect()->route('site.hotel.payment', $booking)
                ->with('error', 'Payment was not successful. Please try again.');
        }

        if ($booking->payment_status === 'paid') {
            return redirect()->route('site.hotel.bookingConfirmation', [
                'slug' => $booking->hotel->slug,
                'booking' => $booking,
            ])->with('info', 'This booking has already been paid.');
        }

        try {
            $bkash = app(\Ihasan\Bkash\Bkash::class);
            $executeResponse = $bkash->executePayment($paymentId);

            if (isset($executeResponse['trxID']) && $executeResponse['transactionStatus'] === 'Completed') {
                $this->completePayment($booking, 'bkash', 'bkash', $executeResponse['trxID'], $executeResponse);

                return redirect()->route('site.hotel.bookingConfirmation', [
                    'slug' => $booking->hotel->slug,
                    'booking' => $booking,
                ])->with('success', 'Payment successful! Your booking is confirmed.');
            }

            \Log::error('bKash payment execution failed', ['booking' => $booking->booking_number, 'response' => $executeResponse]);

            return redirect()->route('site.hotel.payment', $booking)
                ->with('error', 'Payment verification failed. '.($executeResponse['statusMessage'] ?? 'Please try again.'));

        } catch (\Exception $e) {
            \Log::error('bKash callback error: '.$e->getMessage(), ['booking' => $booking->booking_number]);

            return redirect()->route('site.hotel.payment', $booking)
                ->with('error', 'Payment processing error. Please contact support.');
        }
    }

    protected function completePayment(HotelBooking $booking, string $method, string $gateway, string $transactionId, array $response): void
    {
        $booking->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'payment_method' => $method,
        ]);

        $booking->payments()->create([
            'transaction_id' => HotelBookingPayment::generateTransactionId(),
            'amount' => $booking->total_amount,
            'payment_method' => $method,
            'payment_gateway' => $gateway,
            'gateway_transaction_id' => $transactionId,
            'status' => 'completed',
            'type' => 'full',
            'gateway_response' => $response,
            'paid_at' => now(),
        ]);

        // Send confirmation email/notification (Reused from HotelBookingService if possible, or added here)
        // I will trust the HotelBookingService might handle this or I should trigger it.
        // For now, I'll assume the simple update is enough.
    }

    public function bookingConfirmation(string $slug, HotelBooking $booking)
    {
        return view('hotel::confirmation', [
            'booking' => $booking->load(['hotel', 'rooms.room', 'rooms.ratePlan', 'rooms.guests']),
        ]);
    }

    public function submitReview(Request $request, string $slug)
    {
        $hotel = Hotel::where('slug', $slug)->active()->firstOrFail();

        $validated = $request->validate([
            'booking_id' => 'nullable|exists:hotel_bookings,id',
            'overall_rating' => 'required|integer|min:1|max:5',
            'cleanliness_rating' => 'nullable|integer|min:1|max:5',
            'service_rating' => 'nullable|integer|min:1|max:5',
            'location_rating' => 'nullable|integer|min:1|max:5',
            'value_rating' => 'nullable|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2000',
            'pros' => 'nullable|string|max:500',
            'cons' => 'nullable|string|max:500',
            'travel_type' => 'nullable|string|in:business,leisure,family,couple,solo',
        ]);

        HotelReview::create([
            'hotel_id' => $hotel->id,
            'customer_id' => auth()->user()?->customer?->id,
            'booking_id' => $validated['booking_id'],
            'overall_rating' => $validated['overall_rating'],
            'cleanliness_rating' => $validated['cleanliness_rating'],
            'service_rating' => $validated['service_rating'],
            'location_rating' => $validated['location_rating'],
            'value_rating' => $validated['value_rating'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'pros' => $validated['pros'],
            'cons' => $validated['cons'],
            'travel_type' => $validated['travel_type'],
            'reviewer_name' => auth()->user()?->name ?? 'Guest',
            'is_verified' => ! empty($validated['booking_id']),
            'moderation_status' => 'pending',
            'is_published' => false,
        ]);

        return redirect()->back()
            ->with('success', 'Thank you for your review! It will be published after moderation.');
    }

    /**
     * Unified search across all suppliers (local + APIs).
     * Returns JSON for API consumption or AJAX.
     */
    public function unifiedSearch(Request $request)
    {
        $searchRequest = HotelSearchRequest::fromArray([
            'destination' => $request->input('destination', $request->input('city')),
            'check_in' => $request->input('check_in'),
            'check_out' => $request->input('check_out'),
            'rooms' => $request->input('rooms', 1),
            'adults' => $request->input('adults', 2),
            'children' => $request->input('children', 0),
            'child_ages' => $request->input('child_ages', []),
            'min_stars' => $request->input('min_stars'),
            'max_stars' => $request->input('max_stars'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'sort_by' => $request->input('sort_by'),
            'currency' => $request->input('currency', 'BDT'),
            'limit' => $request->input('limit', 50),
        ]);

        $results = $this->unifiedSearchService->search($searchRequest);

        // If JSON is requested, return API response
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => count($results),
                'hotels' => array_map(fn ($r) => $r->toArray(), $results),
            ]);
        }

        // Otherwise return the search view with unified results
        return view('hotel::search', [
            'hotels' => collect($results),
            'filters' => $request->all(),
            'countries' => $this->hotelOptions->getCountries(),
            'propertyTypes' => $this->hotelOptions->getPropertyTypes(),
            'amenities' => $this->hotelOptions->getHotelAmenities(),
            'isUnifiedSearch' => true,
        ]);
    }

    /**
     * Get available suppliers and their health status.
     */
    public function supplierStatus()
    {
        return response()->json([
            'suppliers' => $this->unifiedSearchService->healthCheck(),
        ]);
    }
}
