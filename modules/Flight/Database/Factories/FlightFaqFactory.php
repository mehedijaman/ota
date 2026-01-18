<?php

namespace Modules\Flight\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Flight\Models\FlightAirline;
use Modules\Flight\Models\FlightFaq;

class FlightFaqFactory extends Factory
{
    protected $model = FlightFaq::class;

    protected array $faqs = [
        ['q' => 'How do I check in for my flight?', 'a' => 'You can check in online through our website or mobile app starting 24 hours before departure.', 'cat' => 'checkin'],
        ['q' => 'What is the baggage allowance?', 'a' => 'Baggage allowance varies by fare class. Economy usually includes 23kg checked baggage and 7kg cabin baggage.', 'cat' => 'baggage'],
        ['q' => 'Can I change my booking?', 'a' => 'Yes, you can change your booking subject to fare rules and applicable fees.', 'cat' => 'exchange'],
        ['q' => 'How do I request a refund?', 'a' => 'Refund requests can be submitted through our website. Processing time is typically 7-14 business days.', 'cat' => 'refund'],
        ['q' => 'What is the cancellation policy?', 'a' => 'Cancellation policies vary by fare type. Refundable tickets can be cancelled with minimal fees.', 'cat' => 'cancellation'],
    ];

    public function definition(): array
    {
        $faq = $this->faker->randomElement($this->faqs);

        return [
            'uuid' => $this->faker->uuid(),
            'airline_id' => $this->faker->optional(0.3)->randomElement(FlightAirline::pluck('id')->toArray() ?: [null]),
            'question' => $faq['q'],
            'answer' => $faq['a'],
            'category' => $faq['cat'],
            'sort_order' => $this->faker->numberBetween(1, 100),
            'is_active' => true,
            'is_featured' => $this->faker->boolean(20),
            'views_count' => $this->faker->numberBetween(0, 500),
        ];
    }
}
