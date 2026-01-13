<?php

namespace Modules\Visa\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Support\Http\Controllers\SiteController;
use Modules\Visa\Mail\VisaApplicationConfirmation;
use Modules\Visa\Mail\VisaPaymentConfirmation;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaApplication;
use Modules\Visa\Models\VisaCountry;
use Modules\Visa\Services\SiteVisaService;
use Raziul\Sslcommerz\Facades\Sslcommerz;

class SiteVisaController extends SiteController
{
    /**
     * Re-authenticate the user who created the application.
     */
    protected function reAuthenticateApplicationOwner(VisaApplication $application): void
    {
        if (! Auth::check() && $application->created_by) {
            $user = User::find($application->created_by);
            if ($user) {
                Auth::login($user);
            }
        }
    }

    public function index(): View
    {
        $popularCountries = SiteVisaService::getPopularCountries();
        $featuredVisas = SiteVisaService::getFeaturedVisas();
        $categories = SiteVisaService::getActiveCategories();
        $countries = SiteVisaService::getActiveCountries();

        return view('visa::index', [
            'popularCountries' => $popularCountries,
            'featuredVisas' => $featuredVisas,
            'categories' => $categories,
            'countries' => $countries,
        ]);
    }

    public function search(): View
    {
        $visas = SiteVisaService::searchVisas();
        $countries = SiteVisaService::getActiveCountries();
        $categories = SiteVisaService::getActiveCategories();

        return view('visa::search', [
            'visas' => $visas,
            'countries' => $countries,
            'categories' => $categories,
        ]);
    }

    public function country(string $slug): View
    {
        $country = VisaCountry::where('slug', $slug)
            ->active()
            ->firstOrFail();

        $visas = SiteVisaService::getVisasByCountry($country);
        $categories = SiteVisaService::getActiveCategories();

        return view('visa::country', [
            'country' => $country,
            'visas' => $visas,
            'categories' => $categories,
        ]);
    }

    public function show(string $slug): View
    {
        $visa = SiteVisaService::getVisaBySlug($slug);

        if (! $visa) {
            abort(404);
        }

        $relatedVisas = SiteVisaService::getRelatedVisas($visa);

        return view('visa::visa-show', [
            'visa' => $visa,
            'relatedVisas' => $relatedVisas,
        ]);
    }

    public function requirements(string $country): View
    {
        $countryModel = VisaCountry::where('slug', $country)
            ->orWhere('code', strtoupper($country))
            ->active()
            ->firstOrFail();

        $visas = SiteVisaService::getVisasByCountry($countryModel);

        return view('visa::requirements', [
            'country' => $countryModel,
            'visas' => $visas,
        ]);
    }

    public function bookingPage(string $slug): View
    {
        $visa = Visa::query()
            ->where('slug', $slug)
            ->active()
            ->with([
                'country',
                'category',
                'requirements' => fn ($q) => $q->active()->orderBy('sort_order'),
                'fees' => fn ($q) => $q->active()->orderBy('sort_order'),
                'processingTimes' => fn ($q) => $q->active()->orderBy('sort_order'),
            ])
            ->firstOrFail();

        $customer = null;
        $user = auth()->user();
        if ($user && $user->profile_type === 'customer' && $user->profile_id) {
            $customer = \Modules\Customer\Models\Customer::find($user->profile_id);
        }

        return view('visa::booking', [
            'visa' => $visa,
            'user' => $user,
            'customer' => $customer,
        ]);
    }

    public function submitBooking(Request $request, string $slug): RedirectResponse
    {
        $visa = Visa::where('slug', $slug)
            ->active()
            ->with(['country', 'fees', 'processingTimes'])
            ->firstOrFail();

        $validated = $request->validate([
            'processing_time_id' => 'nullable|exists:visa_processing_times,id',
            'intended_travel_date' => 'required|date|after:today',
            'intended_return_date' => 'nullable|date|after:intended_travel_date',
            'travel_purpose' => 'nullable|string|max:255',
            'adult_count' => 'required|integer|min:1|max:20',
            'child_count' => 'nullable|integer|min:0|max:20',
            'infant_count' => 'nullable|integer|min:0|max:10',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:50',
            'travelers' => 'required|array|min:1',
            'travelers.*.type' => 'required|in:adult,child,infant',
            'travelers.*.is_lead' => 'boolean',
            'travelers.*.title' => 'nullable|in:Mr,Mrs,Ms,Miss,Dr,Prof',
            'travelers.*.first_name' => 'required|string|max:100',
            'travelers.*.last_name' => 'required|string|max:100',
            'travelers.*.email' => 'nullable|email',
            'travelers.*.phone' => 'nullable|string|max:20',
            'travelers.*.date_of_birth' => 'required|date|before:today',
            'travelers.*.gender' => 'required|in:male,female,other',
            'travelers.*.nationality' => 'required|string|max:100',
            'travelers.*.passport_number' => 'required|string|max:50',
            'travelers.*.passport_issue_date' => 'required|date|before:today',
            'travelers.*.passport_expiry_date' => 'required|date|after:today',
            'travelers.*.passport_issue_place' => 'nullable|string|max:100',
            'payment_method' => 'required|in:instant,later',
            'special_requests' => 'nullable|string|max:2000',
        ]);

        // Calculate amounts
        $adultCount = $validated['adult_count'];
        $childCount = $validated['child_count'] ?? 0;
        $infantCount = $validated['infant_count'] ?? 0;
        $totalTravelers = $adultCount + $childCount + $infantCount;

        $subtotal = $visa->base_price * $totalTravelers;
        $embassyFees = $visa->embassy_fee * $totalTravelers;
        $serviceFees = $visa->service_fee;
        $vfsFees = $visa->vfs_fee ?? 0;

        // Express fee if selected
        $expressFees = 0;
        if (! empty($validated['processing_time_id'])) {
            $processingTime = $visa->processingTimes()->find($validated['processing_time_id']);
            if ($processingTime) {
                $expressFees = $processingTime->additional_fee * $totalTravelers;
            }
        }

        $totalAmount = $subtotal + $embassyFees + $serviceFees + $vfsFees + $expressFees;

        // Prepare application data
        $applicationData = [
            'application_number' => 'VA-'.strtoupper(Str::random(8)),
            'visa_id' => $visa->id,
            'country_id' => $visa->country_id,
            'customer_id' => auth()->user()->profile_type === 'customer' ? auth()->user()->profile_id : null,
            'agent_id' => null,
            'created_by' => auth()->id(),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'adult_count' => $adultCount,
            'child_count' => $childCount,
            'infant_count' => $infantCount,
            'intended_travel_date' => $validated['intended_travel_date'],
            'intended_return_date' => $validated['intended_return_date'] ?? null,
            'travel_purpose' => $validated['travel_purpose'] ?? 'Tourism',
            'contact_name' => $validated['contact_name'],
            'contact_email' => $validated['contact_email'],
            'contact_phone' => $validated['contact_phone'],
            'contact_address' => $validated['contact_address'] ?? null,
            'city' => $validated['city'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'country' => 'Bangladesh',
            'whatsapp' => $validated['whatsapp'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            'emergency_contact_relationship' => $validated['emergency_contact_relationship'] ?? null,
            'subtotal' => $subtotal,
            'embassy_fees' => $embassyFees,
            'service_fees' => $serviceFees,
            'express_fees' => $expressFees,
            'additional_fees' => $vfsFees,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $totalAmount,
            'amount_paid' => 0,
            'amount_due' => $totalAmount,
            'internal_notes' => $validated['special_requests'] ?? null,
            'application_source' => 'website',
        ];

        // Create application
        $application = VisaApplication::create($applicationData);

        // Create travelers
        foreach ($validated['travelers'] as $index => $travelerData) {
            $travelerData['is_lead'] = $index === 0;
            $application->travelers()->create($travelerData);
        }

        // Record status history
        $application->statusHistories()->create([
            'from_status' => null,
            'to_status' => 'pending',
            'notes' => 'Application submitted via website',
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);

        // Send confirmation email
        $application->load(['visa.country', 'travelers', 'createdBy', 'processingTime']);
        Mail::to($application->contact_email)->send(new VisaApplicationConfirmation($application));

        // Store application ID in session
        session()->flash('application_id', $application->id);
        session()->flash('application_number', $application->application_number);

        // Redirect based on payment method
        if ($validated['payment_method'] === 'instant') {
            return redirect()->route('site.visa.payment', [
                'application' => $application->application_number,
            ])->with('info', 'Please select your payment method to complete the application.');
        }

        return redirect()->route('site.visa.confirmation', [
            'slug' => $slug,
            'application' => $application->application_number,
        ])->with('success', 'Your visa application has been submitted successfully! Our team will review and contact you shortly.');
    }

    public function confirmation(string $slug, string $application): View
    {
        $visa = Visa::where('slug', $slug)->firstOrFail();

        $applicationRecord = VisaApplication::where('application_number', $application)
            ->with(['visa.country', 'travelers'])
            ->firstOrFail();

        // Verify user owns this application
        if ($applicationRecord->created_by !== auth()->id() &&
            ($applicationRecord->customer_id !== auth()->user()->profile_id || auth()->user()->profile_type !== 'customer')) {
            abort(403, 'Unauthorized access to this application');
        }

        return view('visa::confirmation', [
            'visa' => $visa,
            'application' => $applicationRecord,
        ]);
    }

    public function paymentPage(string $application): View|RedirectResponse
    {
        $applicationRecord = VisaApplication::where('application_number', $application)
            ->with(['visa.country'])
            ->firstOrFail();

        // Verify user owns this application
        if ($applicationRecord->created_by !== auth()->id() &&
            ($applicationRecord->customer_id !== auth()->user()->profile_id || auth()->user()->profile_type !== 'customer')) {
            abort(403, 'Unauthorized access to this application');
        }

        // Check if already paid
        if ($applicationRecord->payment_status === 'paid') {
            return redirect()->route('site.visa.confirmation', [
                'slug' => $applicationRecord->visa->slug,
                'application' => $application,
            ])->with('info', 'This application has already been paid.');
        }

        return view('visa::payment', [
            'application' => $applicationRecord,
        ]);
    }

    public function processPayment(Request $request, string $application): RedirectResponse
    {
        $applicationRecord = VisaApplication::where('application_number', $application)
            ->with(['visa'])
            ->firstOrFail();

        // Verify user owns this application
        if ($applicationRecord->created_by !== auth()->id() &&
            ($applicationRecord->customer_id !== auth()->user()->profile_id || auth()->user()->profile_type !== 'customer')) {
            abort(403, 'Unauthorized access to this application');
        }

        $rules = [
            'payment_gateway' => 'required|in:sslcommerz,bkash,manual',
        ];

        if ($request->payment_gateway === 'manual') {
            $rules['transaction_number'] = 'required|string|max:255';
            $rules['notes'] = 'nullable|string|max:1000';
        }

        $validated = $request->validate($rules);

        switch ($validated['payment_gateway']) {
            case 'sslcommerz':
                return $this->initiateSslcommerzPayment($applicationRecord);

            case 'bkash':
                return $this->initiateBkashPayment($applicationRecord);

            case 'manual':
                return $this->handleManualPayment($applicationRecord, $request);

            default:
                return back()->with('error', 'Invalid payment gateway selected');
        }
    }

    protected function initiateSslcommerzPayment(VisaApplication $application): RedirectResponse
    {
        try {
            $config = config('sslcommerz');

            if (empty($config['store']['id']) || empty($config['store']['password'])) {
                \Log::error('SSLCommerz credentials not configured');

                return back()->with('error', 'Payment gateway is not configured. Please contact support.');
            }

            $response = Sslcommerz::setOrder(
                $application->total_amount,
                $application->application_number,
                'Visa Application - '.$application->visa->name
            )
                ->setCustomer(
                    $application->contact_name,
                    $application->contact_email,
                    $application->contact_phone ?? '01700000000'
                )
                ->setShippingInfo(
                    $application->total_travelers,
                    $application->contact_address ?? 'N/A'
                )
                ->setProductProfile('general')
                ->setCallbackUrls(
                    successUrl: route('site.visa.payment.success', ['application' => $application->application_number]),
                    failedUrl: route('site.visa.payment.fail', ['application' => $application->application_number]),
                    cancelUrl: route('site.visa.payment.cancel', ['application' => $application->application_number]),
                    ipnUrl: route('site.visa.payment.ipn', ['application' => $application->application_number])
                )
                ->makePayment();

            if ($response->success() && $response->gatewayPageURL()) {
                return redirect($response->gatewayPageURL());
            }

            \Log::error('SSLCommerz initiation failed', [
                'application' => $application->application_number,
                'response' => $response->toArray(),
            ]);

            return back()->with('error', 'Failed to initiate payment. '.($response->failedReason() ?? 'Please try again.'));
        } catch (\Exception $e) {
            \Log::error('SSLCommerz payment error: '.$e->getMessage(), [
                'application' => $application->application_number,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Payment gateway error. Please try again or contact support.');
        }
    }

    protected function initiateBkashPayment(VisaApplication $application): RedirectResponse
    {
        try {
            $bkash = app(\Ihasan\Bkash\Bkash::class);

            $response = $bkash->createPayment([
                'amount' => $application->total_amount,
                'merchant_invoice_number' => $application->application_number,
                'payer_reference' => $application->contact_phone ?? $application->contact_email,
                'callback_url' => route('site.visa.payment.bkash.callback', ['application' => $application->application_number]),
                'intent' => 'sale',
            ]);

            if (isset($response['bkashURL'])) {
                return redirect($response['bkashURL']);
            }

            \Log::error('bKash payment creation failed', [
                'application' => $application->application_number,
                'response' => $response,
            ]);

            return back()->with('error', 'Failed to initiate bKash payment. '.($response['statusMessage'] ?? 'Please try again.'));
        } catch (\Exception $e) {
            \Log::error('bKash payment error: '.$e->getMessage(), [
                'application' => $application->application_number,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Payment gateway error. Please try again or contact support.');
        }
    }

    protected function handleManualPayment(VisaApplication $application, Request $request): RedirectResponse
    {
        $transactionNumber = $request->transaction_number;
        $notes = $request->notes;

        $application->update([
            'status' => 'documents_pending',
            'internal_notes' => ($application->internal_notes ?? '')."\n\nManual Payment Request:".
                "\nTransaction ID: ".$transactionNumber.
                "\nNotes: ".$notes.
                "\nRequested at: ".now()->format('Y-m-d H:i:s'),
        ]);

        $application->payments()->create([
            'transaction_id' => 'TXN-'.strtoupper(Str::random(12)),
            'amount' => $application->total_amount,
            'payment_method' => 'manual',
            'gateway_transaction_id' => $transactionNumber,
            'status' => 'pending',
            'notes' => $notes,
            'paid_at' => now(),
        ]);

        // Send confirmation email for manual payment submission
        $application->load(['visa.country', 'createdBy']);
        Mail::to($application->contact_email)->send(
            new VisaPaymentConfirmation($application, $transactionNumber, 'Bank Transfer / Manual')
        );

        return redirect()->route('site.visa.confirmation', [
            'slug' => $application->visa->slug,
            'application' => $application->application_number,
        ])->with('success', 'Manual payment submitted! We will verify your transaction ('.$transactionNumber.') and process your application shortly.');
    }

    public function paymentSuccess(Request $request, string $application): RedirectResponse
    {
        $applicationRecord = VisaApplication::where('application_number', $application)
            ->with('visa')
            ->firstOrFail();

        $this->reAuthenticateApplicationOwner($applicationRecord);

        try {
            $isValid = Sslcommerz::validate($request->all());

            if ($isValid) {
                $transactionId = $request->input('tran_id');
                $amount = $request->input('amount');

                $applicationRecord->update([
                    'payment_status' => 'paid',
                    'status' => 'documents_pending',
                    'amount_paid' => $amount,
                    'amount_due' => $applicationRecord->total_amount - $amount,
                ]);

                $applicationRecord->payments()->create([
                    'transaction_id' => 'TXN-'.strtoupper(Str::random(12)),
                    'amount' => $amount,
                    'payment_method' => 'online',
                    'gateway_transaction_id' => $transactionId,
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);

                $applicationRecord->statusHistories()->create([
                    'from_status' => 'pending',
                    'to_status' => 'documents_pending',
                    'notes' => 'Payment completed via SSLCommerz',
                    'changed_at' => now(),
                ]);

                // Send payment confirmation email
                $applicationRecord->load(['visa.country', 'createdBy']);
                Mail::to($applicationRecord->contact_email)->send(
                    new VisaPaymentConfirmation($applicationRecord, $transactionId, 'SSLCommerz')
                );

                return redirect()->route('site.visa.confirmation', [
                    'slug' => $applicationRecord->visa->slug,
                    'application' => $application,
                ])->with('success', 'Payment successful! Your visa application is now being processed.');
            }
        } catch (\Exception $e) {
            \Log::error('Payment validation failed: '.$e->getMessage());
        }

        return redirect()->route('site.visa.payment', ['application' => $application])
            ->with('error', 'Payment verification failed. Please try again or contact support.');
    }

    public function paymentFail(Request $request, string $application): RedirectResponse
    {
        $applicationRecord = VisaApplication::where('application_number', $application)
            ->with('visa')
            ->firstOrFail();

        $this->reAuthenticateApplicationOwner($applicationRecord);

        return redirect()->route('site.visa.payment', ['application' => $application])
            ->with('error', 'Payment failed. Please try again with a different payment method.');
    }

    public function paymentCancel(Request $request, string $application): RedirectResponse
    {
        $applicationRecord = VisaApplication::where('application_number', $application)
            ->with('visa')
            ->firstOrFail();

        $this->reAuthenticateApplicationOwner($applicationRecord);

        return redirect()->route('site.visa.payment', ['application' => $application])
            ->with('warning', 'Payment was cancelled. You can try again when ready.');
    }

    public function paymentIPN(Request $request, string $application)
    {
        \Log::info('Visa SSLCommerz IPN received', [
            'application' => $application,
            'data' => $request->all(),
        ]);

        $applicationRecord = VisaApplication::where('application_number', $application)->first();

        if (! $applicationRecord) {
            return response('Application not found', 404);
        }

        try {
            $isValid = Sslcommerz::validate($request->all());

            if ($isValid && $request->input('status') === 'VALID') {
                $applicationRecord->update([
                    'payment_status' => 'paid',
                    'status' => 'documents_pending',
                    'amount_paid' => $request->input('amount'),
                    'amount_due' => $applicationRecord->total_amount - $request->input('amount'),
                ]);

                $applicationRecord->payments()->updateOrCreate(
                    ['gateway_transaction_id' => $request->input('tran_id')],
                    [
                        'transaction_id' => 'TXN-'.strtoupper(Str::random(12)),
                        'amount' => $request->input('amount'),
                        'payment_method' => 'online',
                        'status' => 'completed',
                        'paid_at' => now(),
                    ]
                );
            }
        } catch (\Exception $e) {
            \Log::error('IPN processing failed: '.$e->getMessage());
        }

        return response('IPN Received', 200);
    }

    public function bkashCallback(Request $request, string $application): RedirectResponse
    {
        $applicationRecord = VisaApplication::where('application_number', $application)
            ->with('visa')
            ->firstOrFail();

        $this->reAuthenticateApplicationOwner($applicationRecord);

        try {
            $paymentID = $request->query('paymentID');
            $status = $request->query('status');

            if ($status === 'success' && $paymentID) {
                $bkash = app(\Ihasan\Bkash\Bkash::class);
                $response = $bkash->executePayment($paymentID);

                if (isset($response['transactionStatus']) && $response['transactionStatus'] === 'Completed') {
                    $applicationRecord->update([
                        'payment_status' => 'paid',
                        'status' => 'documents_pending',
                        'amount_paid' => $response['amount'],
                        'amount_due' => $applicationRecord->total_amount - $response['amount'],
                    ]);

                    $applicationRecord->payments()->create([
                        'transaction_id' => 'TXN-'.strtoupper(Str::random(12)),
                        'amount' => $response['amount'],
                        'payment_method' => 'bkash',
                        'gateway_transaction_id' => $response['trxID'] ?? $paymentID,
                        'status' => 'completed',
                        'paid_at' => now(),
                    ]);

                    // Send payment confirmation email
                    $applicationRecord->load(['visa.country', 'createdBy']);
                    Mail::to($applicationRecord->contact_email)->send(
                        new VisaPaymentConfirmation($applicationRecord, $response['trxID'] ?? $paymentID, 'bKash')
                    );

                    return redirect()->route('site.visa.confirmation', [
                        'slug' => $applicationRecord->visa->slug,
                        'application' => $application,
                    ])->with('success', 'Payment successful! Your visa application is now being processed.');
                }
            }
        } catch (\Exception $e) {
            \Log::error('bKash callback error: '.$e->getMessage());
        }

        return redirect()->route('site.visa.payment', ['application' => $application])
            ->with('error', 'Payment verification failed. Please try again.');
    }
}
