<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Modules\Visa\Mail\VisaApplicationConfirmation;
use Modules\Visa\Mail\VisaPaymentConfirmation;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaApplication;
use Modules\Visa\Models\VisaCountry;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('VisaApplicationConfirmation Mailable', function () {
    it('can be instantiated', function () {
        $country = VisaCountry::factory()->create();
        $visa = Visa::factory()->create(['country_id' => $country->id]);
        $application = VisaApplication::factory()->create([
            'visa_id' => $visa->id,
            'country_id' => $country->id,
        ]);

        $mailable = new VisaApplicationConfirmation($application);

        expect($mailable)->toBeInstanceOf(VisaApplicationConfirmation::class)
            ->and($mailable->application)->toBeInstanceOf(VisaApplication::class);
    });

    it('has correct envelope subject', function () {
        $country = VisaCountry::factory()->create();
        $visa = Visa::factory()->create(['country_id' => $country->id]);
        $application = VisaApplication::factory()->create([
            'visa_id' => $visa->id,
            'country_id' => $country->id,
            'application_number' => 'VA-SUBJ1234',
        ]);

        $mailable = new VisaApplicationConfirmation($application);
        $envelope = $mailable->envelope();

        expect($envelope->subject)->toBe('Visa Application Confirmed - VA-SUBJ1234');
    });

    it('has content with correct view', function () {
        $country = VisaCountry::factory()->create();
        $visa = Visa::factory()->create(['country_id' => $country->id]);
        $application = VisaApplication::factory()->create([
            'visa_id' => $visa->id,
            'country_id' => $country->id,
        ]);

        $mailable = new VisaApplicationConfirmation($application);
        $content = $mailable->content();

        expect($content->view)->toBe('emails.visa.application-confirmation');
    });
});

describe('VisaPaymentConfirmation Mailable', function () {
    it('can be instantiated', function () {
        $country = VisaCountry::factory()->create();
        $visa = Visa::factory()->create(['country_id' => $country->id]);
        $application = VisaApplication::factory()->create([
            'visa_id' => $visa->id,
            'country_id' => $country->id,
        ]);

        $mailable = new VisaPaymentConfirmation($application, 'TXN123456', 'bkash');

        expect($mailable)->toBeInstanceOf(VisaPaymentConfirmation::class)
            ->and($mailable->application)->toBeInstanceOf(VisaApplication::class)
            ->and($mailable->transactionId)->toBe('TXN123456')
            ->and($mailable->paymentMethod)->toBe('bkash');
    });

    it('has correct envelope subject', function () {
        $country = VisaCountry::factory()->create();
        $visa = Visa::factory()->create(['country_id' => $country->id]);
        $application = VisaApplication::factory()->create([
            'visa_id' => $visa->id,
            'country_id' => $country->id,
            'application_number' => 'VA-PAY5678',
        ]);

        $mailable = new VisaPaymentConfirmation($application, 'TXN123456', 'sslcommerz');
        $envelope = $mailable->envelope();

        expect($envelope->subject)->toBe('Payment Confirmed - VA-PAY5678');
    });

    it('has content with correct view', function () {
        $country = VisaCountry::factory()->create();
        $visa = Visa::factory()->create(['country_id' => $country->id]);
        $application = VisaApplication::factory()->create([
            'visa_id' => $visa->id,
            'country_id' => $country->id,
        ]);

        $mailable = new VisaPaymentConfirmation($application, 'TXN123456', 'manual');
        $content = $mailable->content();

        expect($content->view)->toBe('emails.visa.payment-confirmation');
    });
});

describe('Mail Sending', function () {
    it('queues application confirmation mail', function () {
        Mail::fake();

        $country = VisaCountry::factory()->create();
        $visa = Visa::factory()->create(['country_id' => $country->id]);
        $application = VisaApplication::factory()->create([
            'visa_id' => $visa->id,
            'country_id' => $country->id,
            'contact_email' => 'test@example.com',
        ]);

        Mail::to($application->contact_email)
            ->send(new VisaApplicationConfirmation($application));

        Mail::assertQueued(VisaApplicationConfirmation::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });
    });

    it('queues payment confirmation mail', function () {
        Mail::fake();

        $country = VisaCountry::factory()->create();
        $visa = Visa::factory()->create(['country_id' => $country->id]);
        $application = VisaApplication::factory()->create([
            'visa_id' => $visa->id,
            'country_id' => $country->id,
            'contact_email' => 'payer@example.com',
        ]);

        Mail::to($application->contact_email)
            ->send(new VisaPaymentConfirmation($application, 'TXN-ABC123', 'bkash'));

        Mail::assertQueued(VisaPaymentConfirmation::class, function ($mail) {
            return $mail->hasTo('payer@example.com');
        });
    });

    it('does not send email when mail is faked and no send called', function () {
        Mail::fake();

        Mail::assertNothingSent();
    });
});
