<?php

namespace Modules\Visa\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Visa\Models\Visa;
use Modules\Visa\Models\VisaFaq;

class VisaFaqFactory extends Factory
{
    protected $model = VisaFaq::class;

    protected array $faqs = [
        ['q' => 'What is the processing time for this visa?', 'a' => 'The standard processing time is 10-15 working days. Express processing options are available for faster delivery.'],
        ['q' => 'What documents are required for the visa application?', 'a' => 'You will need a valid passport, passport photos, bank statements, and supporting documents based on your visa type.'],
        ['q' => 'Can I track my visa application status?', 'a' => 'Yes, you can track your application status through our online portal or contact our support team.'],
        ['q' => 'Is travel insurance mandatory?', 'a' => 'Travel insurance requirements vary by country. Please check the specific visa requirements for your destination.'],
        ['q' => 'How long is the visa valid for?', 'a' => 'Visa validity depends on the type of visa. Tourist visas are typically valid for 30-90 days.'],
        ['q' => 'Can I extend my visa while abroad?', 'a' => 'Extension policies vary by country. Some visas can be extended from within the destination country.'],
        ['q' => 'What happens if my visa application is rejected?', 'a' => 'If rejected, you will receive a refund of our service fee (embassy fees are non-refundable). We can also help you reapply.'],
        ['q' => 'Do I need to attend an interview?', 'a' => 'Interview requirements vary by country and visa type. We will inform you if an interview is required.'],
        ['q' => 'Can I apply for a visa on behalf of my family members?', 'a' => 'Yes, you can submit applications for family members. Each applicant needs their own set of documents.'],
        ['q' => 'What payment methods do you accept?', 'a' => 'We accept bKash, Nagad, credit/debit cards, and bank transfers.'],
    ];

    public function definition(): array
    {
        $faq = fake()->randomElement($this->faqs);

        return [
            'visa_id' => Visa::factory(),
            'question' => $faq['q'],
            'answer' => $faq['a'],
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
