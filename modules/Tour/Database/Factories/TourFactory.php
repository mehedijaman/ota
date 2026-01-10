<?php

namespace Modules\Tour\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourCategory;
use Modules\Tour\Models\TourDestination;

class TourFactory extends Factory
{
    protected $model = Tour::class;

    protected array $tourTemplates = [
        // Beach & Island Tours
        ['name' => "Cox's Bazar Beach Getaway", 'days' => 3],
        ['name' => 'Saint Martin Island Escape', 'days' => 4],
        ['name' => 'Kuakata Sunrise Experience', 'days' => 2],
        ['name' => 'Inani Beach Paradise', 'days' => 3],
        ['name' => 'Technaf Coastal Adventure', 'days' => 5],

        // Hill & Mountain Tours
        ['name' => 'Bandarban Hill Trek', 'days' => 4],
        ['name' => 'Sajek Valley Adventure', 'days' => 3],
        ['name' => 'Rangamati Lake Cruise', 'days' => 3],
        ['name' => 'Khagrachari Tribal Experience', 'days' => 4],
        ['name' => 'Nilgiri Peak Expedition', 'days' => 2],
        ['name' => 'Boga Lake Trek', 'days' => 5],
        ['name' => 'Keokradong Summit Adventure', 'days' => 6],

        // Nature & Wildlife
        ['name' => 'Sundarbans Mangrove Safari', 'days' => 3],
        ['name' => 'Sundarbans Tiger Expedition', 'days' => 5],
        ['name' => 'Lawachara Rainforest Walk', 'days' => 2],
        ['name' => 'Ratargul Swamp Forest Tour', 'days' => 1],
        ['name' => 'Madhupur National Park Safari', 'days' => 2],

        // Tea Garden & Nature
        ['name' => 'Sylhet Tea Garden Tour', 'days' => 3],
        ['name' => 'Sreemangal Tea Estate Experience', 'days' => 2],
        ['name' => 'Jaflong Stone Collection Tour', 'days' => 2],
        ['name' => 'Bisnakandi Waterfall Adventure', 'days' => 2],
        ['name' => 'Lalakhal Boat Cruise', 'days' => 1],

        // Cultural & Heritage
        ['name' => 'Old Dhaka Heritage Walk', 'days' => 1],
        ['name' => 'Sonargaon Historical Tour', 'days' => 1],
        ['name' => 'Paharpur Buddhist Monastery', 'days' => 2],
        ['name' => 'Mahasthangarh Archaeological Tour', 'days' => 2],
        ['name' => 'Bagerhat Mosque City Tour', 'days' => 2],
        ['name' => 'Kantajew Temple Exploration', 'days' => 2],

        // Adventure Tours
        ['name' => 'Padma River Camping', 'days' => 2],
        ['name' => 'Meghna River Sunset Cruise', 'days' => 1],
        ['name' => 'Kaptai Lake Kayaking', 'days' => 2],
        ['name' => 'Char Kukri Mukri Island Camping', 'days' => 3],
        ['name' => 'Nijhum Dwip Wildlife Safari', 'days' => 4],

        // Religious & Spiritual
        ['name' => 'Dhaka Mosque & Temple Tour', 'days' => 1],
        ['name' => 'Chittagong Hill Buddhist Circuit', 'days' => 3],
        ['name' => 'Sylhet Shrine Pilgrimage', 'days' => 2],

        // Food & Culinary
        ['name' => 'Old Dhaka Food Trail', 'days' => 1],
        ['name' => 'Chittagong Seafood Experience', 'days' => 2],
        ['name' => 'Sylhet Pitha Festival Tour', 'days' => 2],

        // Photography Tours
        ['name' => 'Cox\'s Bazar Sunrise Photography', 'days' => 3],
        ['name' => 'Sundarbans Wildlife Photography', 'days' => 5],
        ['name' => 'Sajek Cloud Photography Tour', 'days' => 3],

        // Seasonal Tours
        ['name' => 'Tangail Jackfruit Season Tour', 'days' => 1],
        ['name' => 'Rajshahi Mango Festival', 'days' => 2],
        ['name' => 'Mongla Hilsa Fish Season Tour', 'days' => 2],

        // Luxury Tours
        ['name' => 'Premium Cox\'s Bazar 5-Star Experience', 'days' => 4],
        ['name' => 'Luxury Sundarbans Yacht Safari', 'days' => 4],
        ['name' => 'VIP Sylhet Wellness Retreat', 'days' => 5],

        // Family Tours
        ['name' => 'Family Fun Cox\'s Bazar Package', 'days' => 4],
        ['name' => 'Kids Adventure Bandarban', 'days' => 3],
    ];

    public function definition(): array
    {
        $template = $this->faker->unique()->randomElement($this->tourTemplates);
        $name = $template['name'];
        $days = $template['days'];

        return [
            'category_id' => TourCategory::inRandomOrder()->first()?->id,
            'destination_id' => TourDestination::inRandomOrder()->first()?->id,
            'name' => $name,
            'slug' => Str::slug($name),
            'code' => strtoupper(Str::random(3)).'-'.str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'excerpt' => $this->faker->sentence(15),
            'description' => $this->generateDescription($name),
            'duration_days' => $days,
            'duration_nights' => max(0, $days - 1),
            'is_featured' => $this->faker->boolean(20),
            'is_bestseller' => $this->faker->boolean(15),
            'is_active' => true,
            'start_location' => 'Dhaka',
            'end_location' => 'Dhaka',
            'languages' => ['Bengali', 'English'],
            'age_restrictions' => ['min_age' => 0, 'max_age' => null],
            'meta_title' => $name.' | Tour Package',
            'meta_description' => $this->faker->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-1 month'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    protected function generateDescription(string $name): string
    {
        $intro = "Experience the magic of {$name} with our carefully curated tour package.";
        $para1 = $this->faker->paragraph(4);
        $para2 = $this->faker->paragraph(3);
        $para3 = 'This tour includes professional guides, comfortable transportation, and handpicked accommodations to ensure a memorable journey.';

        return "<p>{$intro}</p><p>{$para1}</p><p>{$para2}</p><p>{$para3}</p>";
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function bestseller(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_bestseller' => true,
        ]);
    }
}
