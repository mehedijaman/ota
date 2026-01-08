<?php

namespace Modules\Setting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Setting\Models\AISetting;

class AISettingFactory extends Factory
{
    protected $model = AISetting::class;

    public function definition(): array
    {
        return [
            'provider' => fake()->randomElement(['OpenAI', 'Google Gemini', 'Anthropic', 'Mistral']),
            'model' => fake()->randomElement(['gpt-4o', 'gpt-4-turbo', 'gemini-pro', 'claude-3-opus']),
            'api_key' => fake()->sha256(),
            'is_default' => false,
        ];
    }

    public function openai(): static
    {
        return $this->state(fn (array $attributes) => [
            'provider' => 'OpenAI',
            'model' => 'gpt-4o',
        ]);
    }

    public function gemini(): static
    {
        return $this->state(fn (array $attributes) => [
            'provider' => 'Google Gemini',
            'model' => 'gemini-pro',
        ]);
    }

    public function anthropic(): static
    {
        return $this->state(fn (array $attributes) => [
            'provider' => 'Anthropic',
            'model' => 'claude-3-opus',
        ]);
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
