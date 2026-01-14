<?php

namespace Modules\Hotel\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Hotel\Models\HotelChain;

class HotelChainFactory extends Factory
{
    protected $model = HotelChain::class;

    protected array $chains = [
        ['name' => 'Pan Pacific Hotels', 'loyalty' => 'Pan Pacific Discover'],
        ['name' => 'Radisson Hotel Group', 'loyalty' => 'Radisson Rewards'],
        ['name' => 'Best Western', 'loyalty' => 'Best Western Rewards'],
        ['name' => 'InterContinental Hotels Group', 'loyalty' => 'IHG One Rewards'],
        ['name' => 'Marriott International', 'loyalty' => 'Marriott Bonvoy'],
        ['name' => 'Hyatt Hotels', 'loyalty' => 'World of Hyatt'],
        ['name' => 'Accor Hotels', 'loyalty' => 'ALL - Accor Live Limitless'],
        ['name' => 'Wyndham Hotels', 'loyalty' => 'Wyndham Rewards'],
        ['name' => 'The Peninsula Hotels', 'loyalty' => 'Peninsula Club'],
        ['name' => 'Shangri-La Hotels', 'loyalty' => 'Shangri-La Circle'],
    ];

    public function definition(): array
    {
        $chain = $this->faker->unique()->randomElement($this->chains);

        return [
            'name' => $chain['name'],
            'slug' => Str::slug($chain['name']),
            'code' => strtoupper(Str::substr(Str::slug($chain['name'], ''), 0, 4)),
            'description' => "Experience world-class hospitality with {$chain['name']}.",
            'website' => 'https://www.'.Str::slug($chain['name']).'.com',
            'loyalty_program' => $chain['loyalty'],
            'headquarters_country' => $this->faker->randomElement(['US', 'UK', 'FR', 'SG', 'AE']),
            'founded_year' => $this->faker->year('-50 years'),
            'is_active' => true,
            'hotels_count' => 0,
            'meta_title' => "{$chain['name']} - Hotels Worldwide",
            'meta_description' => "Book {$chain['name']} hotels worldwide. Best rates guaranteed.",
        ];
    }
}
