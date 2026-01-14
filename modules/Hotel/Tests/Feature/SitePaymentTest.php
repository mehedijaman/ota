<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customer\Models\Customer;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelBooking;
use Raziul\Sslcommerz\Facades\Sslcommerz;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->customer = Customer::factory()->create();
    $this->user = User::factory()->create([
        'profile_type' => 'customer',
        'profile_id' => $this->customer->id,
    ]);

    $this->hotel = Hotel::factory()->create([
        'slug' => 'test-hotel',
        'is_active' => true,
    ]);

    $this->booking = HotelBooking::factory()->create([
        'hotel_id' => $this->hotel->id,
        'customer_id' => $this->customer->id,
        'created_by' => $this->user->id,
        'payment_status' => 'unpaid',
        'total_amount' => 5000,
        'contact_name' => 'John Doe',
        'contact_email' => 'john@example.com',
        'booking_number' => 'HB-123456',
        'contact_phone' => '01700000000',
    ]);
});

describe('Site Hotel Payment Flow', function () {

    it('displays payment page for unpaid booking', function () {
        $this->actingAs($this->user)
            ->get(route('site.hotel.payment', $this->booking))
            ->assertSuccessful()
            ->assertSee('Select Payment Method');
    });

    it('redirects if booking is already paid', function () {
        $this->booking->update(['payment_status' => 'paid']);

        $this->actingAs($this->user)
            ->get(route('site.hotel.payment', $this->booking))
            ->assertRedirect(route('site.hotel.bookingConfirmation', [$this->hotel->slug, $this->booking]));
    });

    it('processes manual payment', function () {
        $response = $this->actingAs($this->user)
            ->post(route('site.hotel.processPayment', $this->booking), [
                'payment_gateway' => 'manual',
                'transaction_number' => 'TRX123',
                'notes' => 'Paid manually',
            ]);

        $response->assertRedirect(route('site.hotel.bookingConfirmation', [$this->hotel->slug, $this->booking]));

        $this->booking->refresh();
        expect($this->booking->status)->toBe('confirmed');
        expect($this->booking->payments()->count())->toBe(1);
        expect($this->booking->payments()->first()->gateway_transaction_id)->toBe('TRX123');
    });

    it('initiates sslcommerz payment', function () {
        // Mock SSLCommerz Response
        $mockResponse = Mockery::mock(\Raziul\Sslcommerz\Data\PaymentResponse::class);
        $mockResponse->shouldReceive('success')->andReturn(true);
        $mockResponse->shouldReceive('gatewayPageURL')->andReturn('https://sandbox.sslcommerz.com/pay');
        $mockResponse->shouldReceive('failedReason')->andReturn(null);
        $mockResponse->shouldReceive('toArray')->andReturn([]);

        // Mock SSLCommerz Facade
        Sslcommerz::shouldReceive('setOrder')->andReturnSelf();
        Sslcommerz::shouldReceive('setCustomer')->andReturnSelf();
        Sslcommerz::shouldReceive('setShippingInfo')->andReturnSelf();
        Sslcommerz::shouldReceive('setProductProfile')->andReturnSelf();
        Sslcommerz::shouldReceive('setCallbackUrls')->andReturnSelf();
        Sslcommerz::shouldReceive('makePayment')->andReturn($mockResponse);

        $response = $this->actingAs($this->user)
            ->post(route('site.hotel.processPayment', $this->booking), [
                'payment_gateway' => 'sslcommerz',
            ]);

        $response->assertRedirect('https://sandbox.sslcommerz.com/pay');
    });

    it('initiates bkash payment', function () {
        $this->mock(\Ihasan\Bkash\Bkash::class)
            ->shouldReceive('createPayment')
            ->once()
            ->andReturn(['bkashURL' => 'https://sandbox.bkash.com/pay']);

        $response = $this->actingAs($this->user)
            ->post(route('site.hotel.processPayment', $this->booking), [
                'payment_gateway' => 'bkash',
            ]);

        $response->assertRedirect('https://sandbox.bkash.com/pay');
    });

    it('handles bkash callback success', function () {
        $this->mock(\Ihasan\Bkash\Bkash::class)
            ->shouldReceive('executePayment')
            ->once()
            ->with('PAY123')
            ->andReturn([
                'trxID' => 'TRX_BKASH',
                'transactionStatus' => 'Completed',
                'statusMessage' => 'Success',
            ]);

        $response = $this->actingAs($this->user)
            ->get(route('site.hotel.payment.bkash.callback', [
                'booking' => $this->booking,
                'paymentID' => 'PAY123',
                'status' => 'success',
            ]));

        $response->assertRedirect(route('site.hotel.bookingConfirmation', [$this->hotel->slug, $this->booking]));

        $this->booking->refresh();
        expect($this->booking->payment_status)->toBe('paid');
        expect($this->booking->status)->toBe('confirmed');
        expect($this->booking->payments()->first()->gateway_transaction_id)->toBe('TRX_BKASH');
    });

    it('handles bkash callback failure', function () {
        $response = $this->actingAs($this->user)
            ->get(route('site.hotel.payment.bkash.callback', [
                'booking' => $this->booking,
                'paymentID' => 'PAY123',
                'status' => 'failure',
            ]));

        $response->assertRedirect(route('site.hotel.payment', $this->booking));

        $this->booking->refresh();
        expect($this->booking->payment_status)->toBe('unpaid');
    });
});
