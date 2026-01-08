<?php

namespace Modules\Message\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Message\Models\Message;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(4);

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'subject' => $this->faker->sentence(5),
            'message' => $this->faker->paragraph(3),
            'read_at' => $this->faker->optional()->dateTime(),

            'created_at' => $this->faker->dateTimeBetween('-1 year', '-6 month'),
            'updated_at' => $this->faker->dateTimeBetween('-5 month', 'now'),
        ];
    }
}
