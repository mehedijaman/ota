<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customer\Models\Customer;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourBooking;
use Modules\Tour\Models\TourPricing;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('redirects to SSLCommerz when selecting SSLCommerz payment', function () {
    $customer = Customer::factory()->create();
    $user = User::factory()->create([
        'profile_type' => 'customer',
        'profile_id' => $customer->id,
    ]);

    $tour = Tour::factory()->create([
        'name' => 'Test Tour',
        'slug' => 'test-tour',
        'is_active' => true,
    ]);

    TourPricing::factory()->create([
        'tour_id' => $tour->id,
        'name' => 'Standard Package',
        'adult_price' => 5000,
        'child_price' => 3000,
        'infant_price' => 1000,
    ]);

    $booking = TourBooking::create([
        'booking_number' => 'TB'.date('Ymd').strtoupper(substr(uniqid(), -6)),
        'tour_id' => $tour->id,
        'contact_name' => 'Test User',
        'contact_email' => 'test@example.com',
        'contact_phone' => '01700000000',
        'contact_address' => 'Test Address',
        'travel_date' => now()->addDays(30),
        'adults' => 2,
        'children' => 0,
        'infants' => 0,
        'total_travelers' => 2,
        'adult_price' => 5000,
        'child_price' => 0,
        'infant_price' => 0,
        'subtotal' => 10000,
        'discount_amount' => 0,
        'total_amount' => 10000,
        'payment_status' => 'unpaid',
        'booking_status' => 'pending',
        'created_by' => $user->id,
    ]);

    // Authenticate the user first
    $this->actingAs($user);

    $page = visit(route('site.tour.payment', $booking->booking_number));

    $page->assertSee('Complete Your Payment')
        ->assertSee('SSLCommerz')
        ->click('label:has-text("SSLCommerz")')
        ->click('button:has-text("Proceed to Payment")');

    // Allow time for redirect to SSLCommerz
    sleep(3);

    // Should redirect to SSLCommerz sandbox
    expect($page->url())->toContain('sslcommerz.com');
});
