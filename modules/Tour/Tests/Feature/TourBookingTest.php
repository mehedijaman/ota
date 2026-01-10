<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customer\Models\Customer;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourBooking;
use Modules\Tour\Models\TourExtra;
use Modules\Tour\Models\TourPricing;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->customer = Customer::factory()->create();
    $this->user = User::factory()->create([
        'profile_type' => 'customer',
        'profile_id' => $this->customer->id,
    ]);

    $this->tour = Tour::factory()->create([
        'name' => 'Sundarbans Adventure',
        'slug' => 'sundarbans-adventure',
        'is_active' => true,
    ]);

    $this->pricing = TourPricing::factory()->create([
        'tour_id' => $this->tour->id,
        'name' => 'Standard Package',
        'adult_price' => 5000,
        'child_price' => 3000,
        'infant_price' => 1000,
    ]);

    $this->extra = TourExtra::factory()->create([
        'tour_id' => $this->tour->id,
        'name' => 'Airport Transfer',
        'price' => 1000,
    ]);
});

describe('Tour Booking Flow', function () {
    it('requires authentication to access booking page', function () {
        $response = $this->get(route('site.tour.booking', $this->tour->slug));

        $response->assertRedirect(route('auth.loginForm'));
    });

    it('displays booking page for authenticated users', function () {
        $response = $this->actingAs($this->user)
            ->get(route('site.tour.booking', $this->tour->slug));

        $response->assertOk()
            ->assertSee($this->tour->name)
            ->assertSee('Select Pricing Plan')
            ->assertSee('Select Travel Date');
    });

    it('can submit a booking with valid data', function () {
        $bookingData = [
            'pricing_id' => $this->pricing->id,
            'travel_date' => now()->addDays(7)->format('Y-m-d'),
            'adults_count' => 2,
            'children_count' => 1,
            'infants_count' => 0,
            'contact_type' => 'myself',
            'contact_name' => 'John Doe',
            'contact_email' => 'john@example.com',
            'contact_phone' => '01712345678',
            'contact_address' => '123 Main St',
            'whatsapp' => '01712345678',
            'travelers' => [
                [
                    'type' => 'adult',
                    'is_lead' => 1,
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'john@example.com',
                    'phone' => '01712345678',
                ],
                [
                    'type' => 'adult',
                    'is_lead' => 0,
                    'first_name' => 'Jane',
                    'last_name' => 'Doe',
                    'email' => 'jane@example.com',
                    'phone' => '01712345679',
                ],
                [
                    'type' => 'child',
                    'is_lead' => 0,
                    'first_name' => 'Jimmy',
                    'last_name' => 'Doe',
                    'email' => '',
                    'phone' => '',
                ],
            ],
            'extras' => [],
            'payment_method' => 'later',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('site.tour.submitBooking', $this->tour->slug), $bookingData);

        $response->assertRedirect();

        $this->assertDatabaseHas('tour_bookings', [
            'tour_id' => $this->tour->id,
            'customer_id' => $this->customer->id,
            'adult_count' => 2,
            'child_count' => 1,
            'payment_method' => 'later',
            'payment_status' => 'unpaid',
        ]);

        $booking = TourBooking::where('customer_id', $this->customer->id)->first();
        expect($booking->travelers)->toHaveCount(3);
    });

    it('calculates total amount correctly', function () {
        $bookingData = [
            'pricing_id' => $this->pricing->id,
            'travel_date' => now()->addDays(7)->format('Y-m-d'),
            'adults_count' => 2,
            'children_count' => 1,
            'infants_count' => 1,
            'contact_type' => 'myself',
            'contact_name' => 'John Doe',
            'contact_email' => 'john@example.com',
            'contact_phone' => '01712345678',
            'travelers' => [
                ['type' => 'adult', 'is_lead' => 1, 'first_name' => 'John', 'last_name' => 'Doe'],
                ['type' => 'adult', 'is_lead' => 0, 'first_name' => 'Jane', 'last_name' => 'Doe'],
                ['type' => 'child', 'is_lead' => 0, 'first_name' => 'Jimmy', 'last_name' => 'Doe'],
                ['type' => 'infant', 'is_lead' => 0, 'first_name' => 'Baby', 'last_name' => 'Doe'],
            ],
            'extras' => [],
            'payment_method' => 'later',
        ];

        $this->actingAs($this->user)
            ->post(route('site.tour.submitBooking', $this->tour->slug), $bookingData);

        $booking = TourBooking::where('customer_id', $this->customer->id)->first();

        // 2 adults * 5000 + 1 child * 3000 + 1 infant * 1000 = 14000
        expect((float) $booking->total_amount)->toBe(14000.0);
    });

    it('includes extras in total calculation', function () {
        $bookingData = [
            'pricing_id' => $this->pricing->id,
            'travel_date' => now()->addDays(7)->format('Y-m-d'),
            'adults_count' => 1,
            'children_count' => 0,
            'infants_count' => 0,
            'contact_type' => 'myself',
            'contact_name' => 'John Doe',
            'contact_email' => 'john@example.com',
            'contact_phone' => '01712345678',
            'travelers' => [
                ['type' => 'adult', 'is_lead' => 1, 'first_name' => 'John', 'last_name' => 'Doe'],
            ],
            'extras' => [
                ['extra_id' => $this->extra->id, 'quantity' => 2],
            ],
            'payment_method' => 'later',
        ];

        $this->actingAs($this->user)
            ->post(route('site.tour.submitBooking', $this->tour->slug), $bookingData);

        $booking = TourBooking::where('customer_id', $this->customer->id)->first();

        // 1 adult * 5000 + 2 extras * 1000 = 7000
        expect((float) $booking->total_amount)->toBe(7000.0)
            ->and($booking->extras)->toHaveCount(1)
            ->and($booking->extras->first()->quantity)->toBe(2);
    });

    it('redirects to payment page for instant payment', function () {
        $bookingData = [
            'pricing_id' => $this->pricing->id,
            'travel_date' => now()->addDays(7)->format('Y-m-d'),
            'adults_count' => 1,
            'children_count' => 0,
            'infants_count' => 0,
            'contact_type' => 'myself',
            'contact_name' => 'John Doe',
            'contact_email' => 'john@example.com',
            'contact_phone' => '01712345678',
            'travelers' => [
                ['type' => 'adult', 'is_lead' => 1, 'first_name' => 'John', 'last_name' => 'Doe'],
            ],
            'extras' => [],
            'payment_method' => 'instant',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('site.tour.submitBooking', $this->tour->slug), $bookingData);

        $booking = TourBooking::where('customer_id', $this->customer->id)->first();

        $response->assertRedirect(route('site.tour.payment', $booking->booking_number));
    });

    it('validates required fields', function () {
        $response = $this->actingAs($this->user)
            ->post(route('site.tour.submitBooking', $this->tour->slug), []);

        $response->assertSessionHasErrors([
            'pricing_id',
            'travel_date',
            'adults_count',
            'contact_name',
            'contact_email',
            'contact_phone',
        ]);
    });

    it('validates travel date is in the future', function () {
        $bookingData = [
            'pricing_id' => $this->pricing->id,
            'travel_date' => now()->subDays(1)->format('Y-m-d'),
            'adults_count' => 1,
            'contact_name' => 'John Doe',
            'contact_email' => 'john@example.com',
            'contact_phone' => '01712345678',
            'travelers' => [
                ['type' => 'adult', 'is_lead' => 1, 'first_name' => 'John', 'last_name' => 'Doe'],
            ],
            'payment_method' => 'later',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('site.tour.submitBooking', $this->tour->slug), $bookingData);

        $response->assertSessionHasErrors('travel_date');
    });

    it('requires at least one adult', function () {
        $bookingData = [
            'pricing_id' => $this->pricing->id,
            'travel_date' => now()->addDays(7)->format('Y-m-d'),
            'adults_count' => 0,
            'children_count' => 2,
            'infants_count' => 0,
            'contact_name' => 'John Doe',
            'contact_email' => 'john@example.com',
            'contact_phone' => '01712345678',
            'travelers' => [],
            'payment_method' => 'later',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('site.tour.submitBooking', $this->tour->slug), $bookingData);

        $response->assertSessionHasErrors('adults_count');
    });
});

describe('Payment Flow', function () {
    it('displays payment page for unpaid booking', function () {
        $booking = TourBooking::factory()->create([
            'tour_id' => $this->tour->id,
            'pricing_id' => $this->pricing->id,
            'customer_id' => $this->customer->id,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('site.tour.payment', $booking->booking_number));

        $response->assertOk()
            ->assertSee('Select Payment Method')
            ->assertSee('SSLCommerz')
            ->assertSee('bKash')
            ->assertSee('Manual Payment');
    });

    it('redirects if booking is already paid', function () {
        $booking = TourBooking::factory()->create([
            'tour_id' => $this->tour->id,
            'pricing_id' => $this->pricing->id,
            'customer_id' => $this->customer->id,
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('site.tour.payment', $booking->booking_number));

        $response->assertRedirect(route('site.tour.bookingConfirmation', [
            'slug' => $this->tour->slug,
            'booking' => $booking->booking_number,
        ]));
    });

    it('prevents access to other users bookings', function () {
        $otherCustomer = Customer::factory()->create();
        $otherUser = User::factory()->create([
            'profile_type' => 'customer',
            'profile_id' => $otherCustomer->id,
        ]);

        $booking = TourBooking::factory()->create([
            'tour_id' => $this->tour->id,
            'pricing_id' => $this->pricing->id,
            'customer_id' => $otherCustomer->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('site.tour.payment', $booking->booking_number));

        $response->assertForbidden();
    });

    it('handles manual payment selection', function () {
        $booking = TourBooking::factory()->create([
            'tour_id' => $this->tour->id,
            'pricing_id' => $this->pricing->id,
            'customer_id' => $this->customer->id,
            'created_by' => $this->user->id,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('site.tour.processPayment', $booking->booking_number), [
                'payment_gateway' => 'manual',
                'transaction_number' => 'BANK-TXN-123456',
                'notes' => 'Paid via bank transfer',
            ]);

        $response->assertRedirect(route('site.tour.bookingConfirmation', [
            'slug' => $this->tour->slug,
            'booking' => $booking->booking_number,
        ]));

        $booking->refresh();
        expect($booking->status)->toBe('processing')
            ->and($booking->payments)->toHaveCount(1)
            ->and($booking->payments->first()->gateway_transaction_id)->toBe('BANK-TXN-123456')
            ->and($booking->payments->first()->status)->toBe('pending');
    });

    it('validates payment gateway selection', function () {
        $booking = TourBooking::factory()->create([
            'tour_id' => $this->tour->id,
            'pricing_id' => $this->pricing->id,
            'customer_id' => $this->customer->id,
            'created_by' => $this->user->id,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('site.tour.processPayment', $booking->booking_number), [
                'payment_gateway' => 'invalid_gateway',
            ]);

        $response->assertSessionHasErrors('payment_gateway');
    });

    it('requires transaction number for manual payment', function () {
        $booking = TourBooking::factory()->create([
            'tour_id' => $this->tour->id,
            'pricing_id' => $this->pricing->id,
            'customer_id' => $this->customer->id,
            'created_by' => $this->user->id,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('site.tour.processPayment', $booking->booking_number), [
                'payment_gateway' => 'manual',
            ]);

        $response->assertSessionHasErrors('transaction_number');
    });

    it('handles bkash callback with success status', function () {
        $booking = TourBooking::factory()->create([
            'tour_id' => $this->tour->id,
            'pricing_id' => $this->pricing->id,
            'customer_id' => $this->customer->id,
            'created_by' => $this->user->id,
            'payment_status' => 'unpaid',
            'total_amount' => 5000,
        ]);

        // Mock the bKash service
        $mockBkash = $this->mock(\Ihasan\Bkash\Bkash::class);
        $mockBkash->shouldReceive('executePayment')
            ->once()
            ->with('BKASH_PAYMENT_123')
            ->andReturn([
                'trxID' => 'TRX123456789',
                'transactionStatus' => 'Completed',
                'amount' => '5000',
                'currency' => 'BDT',
                'statusCode' => '0000',
                'statusMessage' => 'Successful',
            ]);

        $response = $this->get(route('site.tour.payment.bkash.callback', [
            'booking' => $booking->booking_number,
            'paymentID' => 'BKASH_PAYMENT_123',
            'status' => 'success',
        ]));

        $response->assertRedirect(route('site.tour.bookingConfirmation', [
            'slug' => $this->tour->slug,
            'booking' => $booking->booking_number,
        ]));

        $booking->refresh();
        expect($booking->payment_status)->toBe('paid')
            ->and($booking->status)->toBe('confirmed')
            ->and($booking->payments)->toHaveCount(1)
            ->and($booking->payments->first()->gateway_transaction_id)->toBe('TRX123456789');
    });

    it('handles bkash callback with failed status', function () {
        $booking = TourBooking::factory()->create([
            'tour_id' => $this->tour->id,
            'pricing_id' => $this->pricing->id,
            'customer_id' => $this->customer->id,
            'created_by' => $this->user->id,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->get(route('site.tour.payment.bkash.callback', [
            'booking' => $booking->booking_number,
            'paymentID' => 'BKASH_PAYMENT_123',
            'status' => 'cancel',
        ]));

        $response->assertRedirect(route('site.tour.payment', $booking->booking_number));

        $booking->refresh();
        expect($booking->payment_status)->toBe('unpaid');
    });
});

describe('Booking Confirmation', function () {
    it('displays booking confirmation page', function () {
        $booking = TourBooking::factory()->create([
            'tour_id' => $this->tour->id,
            'pricing_id' => $this->pricing->id,
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('site.tour.bookingConfirmation', [
                'slug' => $this->tour->slug,
                'booking' => $booking->booking_number,
            ]));

        $response->assertOk()
            ->assertSee($booking->booking_number)
            ->assertSee($this->tour->name);
    });

    it('prevents access to other users booking confirmations', function () {
        $otherCustomer = Customer::factory()->create();
        $otherUser = User::factory()->create([
            'profile_type' => 'customer',
            'profile_id' => $otherCustomer->id,
        ]);

        $booking = TourBooking::factory()->create([
            'tour_id' => $this->tour->id,
            'pricing_id' => $this->pricing->id,
            'customer_id' => $otherCustomer->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('site.tour.bookingConfirmation', [
                'slug' => $this->tour->slug,
                'booking' => $booking->booking_number,
            ]));

        $response->assertForbidden();
    });
});
