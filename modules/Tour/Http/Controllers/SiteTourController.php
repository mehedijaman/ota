<?php

namespace Modules\Tour\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Support\Http\Controllers\SiteController;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourCategory;
use Modules\Tour\Models\TourDestination;
use Modules\Tour\Services\TourBookingService;
use Raziul\Sslcommerz\Sslcommerz;
use Barryvdh\DomPDF\Facade\Pdf;

class SiteTourController extends SiteController
{
    public function __construct(
        protected TourBookingService $bookingService
    ) {}

    public function index(): View
    {
        $featuredTours = Tour::query()
            ->active()
            ->featured()
            ->with(['category', 'destination'])
            ->take(8)
            ->get();

        $categories = TourCategory::query()
            ->active()
            ->withCount('tours')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $destinations = TourDestination::query()
            ->active()
            ->featured()
            ->take(6)
            ->get();

        return view('tour::index', [
            'featuredTours' => $featuredTours,
            'categories' => $categories,
            'destinations' => $destinations,
        ]);
    }

    public function search(): View
    {
        $query = Tour::query()
            ->active()
            ->with(['category', 'destination']);

        // Filter by category
        if (request('category')) {
            $query->where('category_id', request('category'));
        }

        // Filter by destination
        if (request('destination')) {
            $query->where('destination_id', request('destination'));
        }

        // Filter by duration
        if (request('duration')) {
            $duration = request('duration');
            if ($duration === '1-3') {
                $query->whereBetween('duration_days', [1, 3]);
            } elseif ($duration === '4-7') {
                $query->whereBetween('duration_days', [4, 7]);
            } elseif ($duration === '7+') {
                $query->where('duration_days', '>', 7);
            }
        }

        // Search by name
        if (request('q')) {
            $query->where('name', 'like', '%'.request('q').'%');
        }

        // Sort
        $sort = request('sort', 'latest');
        if ($sort === 'price_low') {
            $query->orderByRaw('COALESCE(discount_price, base_price) ASC');
        } elseif ($sort === 'price_high') {
            $query->orderByRaw('COALESCE(discount_price, base_price) DESC');
        } elseif ($sort === 'popular') {
            $query->orderBy('view_count', 'desc');
        } else {
            $query->latest();
        }

        $tours = $query->paginate(12)->withQueryString();

        $categories = TourCategory::active()->orderBy('sort_order')->get();
        $destinations = TourDestination::active()->orderBy('name')->get();

        return view('tour::search-tour', [
            'tours' => $tours,
            'categories' => $categories,
            'destinations' => $destinations,
        ]);
    }

    public function show(string $slug): View
    {
        $tour = Tour::query()
            ->where('slug', $slug)
            ->active()
            ->with([
                'category',
                'destination',
                'itineraries' => fn ($q) => $q->orderBy('day_number'),
                'inclusions',
                'faqs',
                'highlights',
                'reviews' => fn ($q) => $q->approved()->latest()->take(5),
            ])
            ->firstOrFail();

        // Related tours
        $relatedTours = Tour::query()
            ->active()
            ->where('id', '!=', $tour->id)
            ->where(function ($q) use ($tour) {
                $q->where('category_id', $tour->category_id)
                    ->orWhere('destination_id', $tour->destination_id);
            })
            ->take(4)
            ->get();

        return view('tour::show-tour', [
            'tour' => $tour,
            'relatedTours' => $relatedTours,
        ]);
    }

    public function bookingPage(string $slug): View
    {
        $tour = Tour::query()
            ->where('slug', $slug)
            ->active()
            ->with([
                'pricings' => fn ($q) => $q->active()->orderBy('price_per_person'),
                'extras' => fn ($q) => $q->active()->orderBy('sort_order'),
            ])
            ->firstOrFail();

        // Load customer data if user has customer profile
        $customer = null;
        $user = auth()->user();
        if ($user && $user->profile_type === 'customer' && $user->profile_id) {
            $customer = \Modules\Customer\Models\Customer::find($user->profile_id);
        }

        return view('tour::booking', [
            'tour' => $tour,
            'user' => $user,
            'customer' => $customer,
        ]);
    }

    public function submitBooking(Request $request, string $slug): RedirectResponse
    {
        $tour = Tour::where('slug', $slug)->active()->with(['pricings', 'extras'])->firstOrFail();

        $validated = $request->validate([
            'pricing_id' => 'required|exists:tour_pricings,id',
            'travel_date' => 'required|date|after:today',
            'adults_count' => 'required|integer|min:1|max:50',
            'children_count' => 'nullable|integer|min:0|max:50',
            'infants_count' => 'nullable|integer|min:0|max:50',
            'contact_type' => 'required|in:myself,other',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_address' => 'nullable|string|max:500',
            'whatsapp' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'travelers' => 'required|array|min:1',
            'travelers.*.type' => 'required|in:adult,child,infant',
            'travelers.*.is_lead' => 'boolean',
            'travelers.*.first_name' => 'required|string|max:255',
            'travelers.*.last_name' => 'required|string|max:255',
            'travelers.*.email' => 'nullable|email',
            'travelers.*.phone' => 'nullable|string|max:20',
            'travelers.*.date_of_birth' => 'nullable|date',
            'travelers.*.gender' => 'nullable|in:male,female,other',
            'travelers.*.passport_number' => 'nullable|string|max:50',
            'travelers.*.nid_number' => 'nullable|string|max:50',
            'extras' => 'nullable|array',
            'extras.*.extra_id' => 'required|exists:tour_extras,id',
            'extras.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:instant,later',
            'special_requests' => 'nullable|string|max:2000',
            'dietary_requirements' => 'nullable|string|max:1000',
            'medical_conditions' => 'nullable|string|max:1000',
        ]);

        // Validate date against tour validity
        if ($tour->valid_from && $validated['travel_date'] < $tour->valid_from->format('Y-m-d')) {
            return back()->withErrors(['travel_date' => 'Selected date is before tour availability.'])->withInput();
        }
        if ($tour->valid_to && $validated['travel_date'] > $tour->valid_to->format('Y-m-d')) {
            return back()->withErrors(['travel_date' => 'Selected date is after tour availability.'])->withInput();
        }

        // Get pricing
        $pricing = $tour->pricings()->findOrFail($validated['pricing_id']);

        // Calculate amounts
        $adultsCount = $validated['adults_count'];
        $childrenCount = $validated['children_count'] ?? 0;
        $infantsCount = $validated['infants_count'] ?? 0;

        $subtotal = ($adultsCount * $pricing->adult_price)
            + ($childrenCount * ($pricing->child_price ?? 0))
            + ($infantsCount * ($pricing->infant_price ?? 0));

        $extrasTotal = 0;
        if (!empty($validated['extras'])) {
            foreach ($validated['extras'] as $extraData) {
                $extra = $tour->extras()->findOrFail($extraData['extra_id']);
                $extrasTotal += $extra->price * $extraData['quantity'];
            }
        }

        $totalAmount = $subtotal + $extrasTotal;

        // Prepare booking data
        $bookingData = [
            'booking_number' => \Modules\Tour\Models\TourBooking::generateBookingNumber(),
            'tour_id' => $tour->id,
            'pricing_id' => $validated['pricing_id'],
            'customer_id' => auth()->user()->profile_type === 'customer' ? auth()->user()->profile_id : null,
            'created_by' => auth()->id(),
            'travel_date' => $validated['travel_date'],
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'unpaid',
            'adult_count' => $adultsCount,
            'child_count' => $childrenCount,
            'infant_count' => $infantsCount,
            'contact_name' => $validated['contact_name'],
            'contact_email' => $validated['contact_email'],
            'contact_phone' => $validated['contact_phone'],
            'contact_address' => $validated['contact_address'] ?? null,
            'whatsapp' => $validated['whatsapp'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'emergency_phone' => $validated['emergency_phone'] ?? null,
            'special_requests' => $validated['special_requests'] ?? null,
            'dietary_requirements' => $validated['dietary_requirements'] ?? null,
            'medical_conditions' => $validated['medical_conditions'] ?? null,
            'subtotal' => $subtotal,
            'extras_total' => $extrasTotal,
            'total_amount' => $totalAmount,
            'amount_due' => $totalAmount,
            'booking_source' => 'website',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        // Create booking
        $booking = \Modules\Tour\Models\TourBooking::create($bookingData);

        // Create travelers
        foreach ($validated['travelers'] as $travelerData) {
            $booking->travelers()->create($travelerData);
        }

        // Create extras
        if (!empty($validated['extras'])) {
            foreach ($validated['extras'] as $extraData) {
                $extra = $tour->extras()->findOrFail($extraData['extra_id']);
                $booking->extras()->create([
                    'extra_id' => $extraData['extra_id'],
                    'quantity' => $extraData['quantity'],
                    'unit_price' => $extra->price,
                    'total_price' => $extra->price * $extraData['quantity'],
                ]);
            }
        }

        // Send notifications
        try {
            // Get customer/user for notification
            $customer = null;
            if (auth()->user()->profile_type === 'customer' && auth()->user()->profile_id) {
                $customer = \Modules\Customer\Models\Customer::find(auth()->user()->profile_id);
            }
            
            // Send confirmation to customer
            if ($customer) {
                $customer->notify(new \Modules\Tour\Notifications\BookingConfirmationNotification($booking));
            } else {
                // Fallback to email only
                \Mail::to($booking->contact_email)->send(
                    new \Modules\Tour\Mail\TourBookingConfirmationMail($booking)
                );
            }

            // Send notification to admin
            if ($adminEmail = config('mail.admin_email', 'admin@example.com')) {
                \Notification::route('mail', $adminEmail)
                    ->notify(new \Modules\Tour\Notifications\AdminBookingNotification($booking));
            }
        } catch (\Exception $e) {
            \Log::error('Booking notification failed: ' . $e->getMessage());
        }

        // Store booking ID in session
        session()->flash('booking_id', $booking->id);
        session()->flash('booking_number', $booking->booking_number);

        // Redirect based on payment method
        if ($validated['payment_method'] === 'instant') {
            // Redirect to payment gateway selection page
            return redirect()->route('site.tour.payment', [
                'booking' => $booking->booking_number,
            ])->with('info', 'Please select your payment method to complete the booking.');
        }

        return redirect()->route('site.tour.bookingConfirmation', [
            'slug' => $slug,
            'booking' => $booking->booking_number,
        ])->with('success', 'Your booking has been submitted successfully! Our team will review and confirm your booking shortly.');
    }

    public function bookingConfirmation(string $slug, string $booking): View
    {
        $tour = Tour::where('slug', $slug)->firstOrFail();

        $bookingRecord = \Modules\Tour\Models\TourBooking::where('booking_number', $booking)
            ->with(['tour', 'customer'])
            ->firstOrFail();

        // Verify user owns this booking
        if ($bookingRecord->created_by !== auth()->id() && 
            ($bookingRecord->customer_id !== auth()->user()->profile_id || auth()->user()->profile_type !== 'customer')) {
            abort(403, 'Unauthorized access to this booking');
        }

        return view('tour::booking-confirmation', [
            'tour' => $tour,
            'booking' => $bookingRecord,
        ]);
    }

    public function submitReview(Request $request, string $slug): RedirectResponse
    {
        $tour = Tour::where('slug', $slug)->active()->firstOrFail();

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:10|max:2000',
            'reviewer_name' => 'required|string|max:255',
            'reviewer_email' => 'required|email|max:255',
            'travel_date' => 'nullable|date|before_or_equal:today',
            'pros' => 'nullable|array',
            'pros.*' => 'string|max:255',
            'cons' => 'nullable|array',
            'cons.*' => 'string|max:255',
        ]);

        // Create review (pending approval)
        $tour->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'content' => $validated['comment'],
            'name' => $validated['reviewer_name'],
            'email' => $validated['reviewer_email'],
            'travel_date' => $validated['travel_date'] ?? null,
            'pros' => $validated['pros'] ?? null,
            'cons' => $validated['cons'] ?? null,
            'is_approved' => false,
            'is_verified' => auth()->check(),
        ]);

        return back()->with('success', 'Thank you for your review! It will be published after approval.');
    }

    public function paymentPage(string $booking): View|\Illuminate\Http\RedirectResponse
    {
        $bookingRecord = \Modules\Tour\Models\TourBooking::where('booking_number', $booking)
            ->with(['tour', 'pricing'])
            ->firstOrFail();

        // Verify user owns this booking
        if ($bookingRecord->created_by !== auth()->id() && 
            ($bookingRecord->customer_id !== auth()->user()->profile_id || auth()->user()->profile_type !== 'customer')) {
            abort(403, 'Unauthorized access to this booking');
        }

        // Check if already paid
        if ($bookingRecord->payment_status === 'paid') {
            return redirect()->route('site.tour.bookingConfirmation', [
                'slug' => $bookingRecord->tour->slug,
                'booking' => $booking,
            ])->with('info', 'This booking has already been paid.');
        }

        return view('tour::payment', [
            'booking' => $bookingRecord,
        ]);
    }

    public function processPayment(Request $request, string $booking)
    {
        $bookingRecord = \Modules\Tour\Models\TourBooking::where('booking_number', $booking)
            ->with(['tour'])
            ->firstOrFail();

        // Verify user owns this booking
        if ($bookingRecord->created_by !== auth()->id() && 
            ($bookingRecord->customer_id !== auth()->user()->profile_id || auth()->user()->profile_type !== 'customer')) {
            abort(403, 'Unauthorized access to this booking');
        }

        $rules = [
            'payment_gateway' => 'required|in:sslcommerz,bkash,manual',
        ];

        // Add validation for manual payment
        if ($request->payment_gateway === 'manual') {
            $rules['transaction_number'] = 'required|string|max:255';
            $rules['notes'] = 'nullable|string|max:1000';
        }

        $validated = $request->validate($rules);

        $gateway = $validated['payment_gateway'];

        // Handle different payment gateways
        switch ($gateway) {
            case 'sslcommerz':
                return $this->initiateSslcommerzPayment($bookingRecord);

            case 'bkash':
                return $this->initiateBkashPayment($bookingRecord);

            case 'manual':
                return $this->handleManualPayment($bookingRecord);

            default:
                return back()->with('error', 'Invalid payment gateway selected');
        }
    }

    protected function initiateSslcommerzPayment($booking)
    {
        try {
            $sslcommerz = app(\Raziul\Sslcommerz\SslcommerzClient::class);

            $sslcommerz->setAmount($booking->total_amount);
            $sslcommerz->setTransactionId($booking->booking_number);
            $sslcommerz->setProductName('Tour Booking - ' . $booking->tour->name);
            $sslcommerz->setProductCategory('Tour');
            $sslcommerz->setCustomerName($booking->contact_name);
            $sslcommerz->setCustomerEmail($booking->contact_email);
            $sslcommerz->setCustomerPhone($booking->contact_phone);
            $sslcommerz->setCustomerAddress($booking->contact_address ?? 'N/A');
            $sslcommerz->setCustomerCity('Dhaka');
            $sslcommerz->setCustomerCountry('Bangladesh');

            // Set callback URLs
            $sslcommerz->setSuccessUrl(route('site.tour.payment.success', ['booking' => $booking->booking_number]));
            $sslcommerz->setFailUrl(route('site.tour.payment.fail', ['booking' => $booking->booking_number]));
            $sslcommerz->setCancelUrl(route('site.tour.payment.cancel', ['booking' => $booking->booking_number]));

            $response = $sslcommerz->initiate();

            if ($response->isSuccessful()) {
                return redirect($response->getRedirectUrl());
            }

            return back()->with('error', 'Failed to initiate payment. Please try again.');
        } catch (\Exception $e) {
            \Log::error('SSLCommerz payment error: ' . $e->getMessage());
            return back()->with('error', 'Payment gateway error. Please try again or contact support.');
        }
    }

    protected function initiateBkashPayment($booking)
    {
        try {
            $bkash = app(\Ihasan\Bkash\Bkash::class);

            $response = $bkash->createPayment([
                'amount' => $booking->total_amount,
                'invoice' => $booking->booking_number,
                'intent' => 'sale',
            ]);

            if (isset($response['bkashURL'])) {
                return redirect($response['bkashURL']);
            }

            return back()->with('error', 'Failed to initiate bKash payment. Please try again.');
        } catch (\Exception $e) {
            \Log::error('bKash payment error: ' . $e->getMessage());
            return back()->with('error', 'Payment gateway error. Please try again or contact support.');
        }
    }

    protected function handleManualPayment($booking)
    {
        $transactionNumber = request('transaction_number');
        $notes = request('notes');

        // Update booking with manual payment instructions and transaction details
        $booking->update([
            'payment_method' => 'manual',
            'status' => 'pending', // Explicitly set status to pending
            'payment_status' => 'pending', // Payment is pending verification
            'notes' => ($booking->notes ?? '') . "\n\nManual Payment Request:" . 
                       "\nTransaction ID: " . $transactionNumber . 
                       "\nNotes: " . $notes . 
                       "\nRequested at: " . now()->format('Y-m-d H:i:s'),
        ]);

        // Create a payment record for the manual transaction
        $booking->payments()->create([
            'amount' => $booking->total_amount,
            'payment_method' => 'manual',
            'transaction_id' => $transactionNumber,
            'status' => 'pending',
            'payment_details' => [
                'notes' => $notes,
                'user_ip' => request()->ip(),
            ],
        ]);

        return redirect()->route('site.tour.bookingConfirmation', [
            'slug' => $booking->tour->slug,
            'booking' => $booking->booking_number,
        ])->with('success', 'Manual payment submitted! We will verify your transaction (' . $transactionNumber . ') and confirm your booking shortly.');
    }

    public function paymentSuccess(Request $request, string $booking)
    {
        $bookingRecord = \Modules\Tour\Models\TourBooking::where('booking_number', $booking)
            ->with(['tour'])
            ->firstOrFail();

        // Verify payment with gateway
        try {
            // For SSLCommerz
            if ($request->has('tran_id')) {
                $sslcommerz = new \Raziul\Sslcommerz\SslcommerzClient();
                $validation = $sslcommerz->validate($request->all());

                if ($validation->isSuccessful()) {
                    $bookingRecord->update([
                        'payment_status' => 'paid',
                        'payment_method' => 'sslcommerz',
                        'paid_amount' => $bookingRecord->total_amount,
                        'amount_due' => 0,
                        'payment_details' => $request->all(),
                    ]);

                    // Create payment record
                    $bookingRecord->payments()->create([
                        'amount' => $bookingRecord->total_amount,
                        'payment_method' => 'sslcommerz',
                        'transaction_id' => $request->input('tran_id'),
                        'status' => 'completed',
                        'payment_details' => $request->all(),
                        'paid_at' => now(),
                    ]);
                }
            }
            // For bKash
            elseif ($request->has('paymentID')) {
                $bookingRecord->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'bkash',
                    'paid_amount' => $bookingRecord->total_amount,
                    'amount_due' => 0,
                    'payment_details' => $request->all(),
                ]);

                $bookingRecord->payments()->create([
                    'amount' => $bookingRecord->total_amount,
                    'payment_method' => 'bkash',
                    'transaction_id' => $request->input('paymentID'),
                    'status' => 'completed',
                    'payment_details' => $request->all(),
                    'paid_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Payment verification error: ' . $e->getMessage());
        }

        return redirect()->route('site.tour.bookingConfirmation', [
            'slug' => $bookingRecord->tour->slug,
            'booking' => $booking,
        ])->with('success', 'Payment successful! Your booking is confirmed.');
    }

    public function paymentFail(Request $request, string $booking)
    {
        $bookingRecord = \Modules\Tour\Models\TourBooking::where('booking_number', $booking)
            ->with(['tour'])
            ->firstOrFail();

        // Log failed payment
        \Log::warning('Payment failed for booking: ' . $booking, $request->all());

        return redirect()->route('site.tour.payment', $booking)
            ->with('error', 'Payment failed. Please try again or choose a different payment method.');
    }

    public function paymentCancel(Request $request, string $booking)
    {
        $bookingRecord = \Modules\Tour\Models\TourBooking::where('booking_number', $booking)
            ->with(['tour'])
            ->firstOrFail();

        return redirect()->route('site.tour.payment', $booking)
            ->with('info', 'Payment cancelled. You can try again when ready.');
    }

    public function paymentIPN(Request $request, string $booking)
    {
        // Handle IPN (Instant Payment Notification) from payment gateways
        \Log::info('Payment IPN received for booking: ' . $booking, $request->all());

        $bookingRecord = \Modules\Tour\Models\TourBooking::where('booking_number', $booking)->first();

        if (!$bookingRecord) {
            return response()->json(['status' => 'error', 'message' => 'Booking not found'], 404);
        }

        try {
            // Verify and process IPN
            if ($request->has('tran_id')) {
                // SSLCommerz IPN
                $sslcommerz = new \Raziul\Sslcommerz\SslcommerzClient();
                $validation = $sslcommerz->validate($request->all());

                if ($validation->isSuccessful() && $bookingRecord->payment_status !== 'paid') {
                    $bookingRecord->update([
                        'payment_status' => 'paid',
                        'payment_method' => 'sslcommerz',
                        'paid_amount' => $bookingRecord->total_amount,
                        'amount_due' => 0,
                    ]);

                    $bookingRecord->payments()->create([
                        'amount' => $bookingRecord->total_amount,
                        'payment_method' => 'sslcommerz',
                        'transaction_id' => $request->input('tran_id'),
                        'status' => 'completed',
                        'payment_details' => $request->all(),
                        'paid_at' => now(),
                    ]);
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            \Log::error('IPN processing error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
