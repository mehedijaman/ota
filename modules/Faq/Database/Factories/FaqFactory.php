<?php

namespace Modules\Faq\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Faq\Models\Faq;
use Modules\Faq\Models\FaqCategory;

class FaqFactory extends Factory
{
    protected $model = Faq::class;

    public function definition(): array
    {
        return [
            'faq_category_id' => FaqCategory::factory(),
            'question' => fake()->sentence(8).'?',
            'answer' => fake()->paragraphs(3, true),
            'status' => 'published',
            'sort_order' => fake()->numberBetween(0, 100),
            'is_featured' => false,
            'target_audience' => 'all',
            'meta_title' => null,
            'meta_description' => null,
        ];
    }

    public function forCategory(FaqCategory $category): static
    {
        return $this->state(fn (array $attributes) => [
            'faq_category_id' => $category->id,
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function forB2C(): static
    {
        return $this->state(fn (array $attributes) => [
            'target_audience' => 'b2c',
        ]);
    }

    public function forB2B(): static
    {
        return $this->state(fn (array $attributes) => [
            'target_audience' => 'b2b',
        ]);
    }

    public function withSeo(): static
    {
        return $this->state(fn (array $attributes) => [
            'meta_title' => fake()->sentence(6),
            'meta_description' => fake()->sentence(15),
        ]);
    }
}
