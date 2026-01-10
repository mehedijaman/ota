<?php

namespace Modules\Tour\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourCancellationPolicy;
use Modules\Tour\Models\TourExtra;
use Modules\Tour\Models\TourFaq;
use Modules\Tour\Models\TourHighlight;
use Modules\Tour\Models\TourInclusion;
use Modules\Tour\Models\TourItinerary;
use Modules\Tour\Models\TourPricing;

class TourSeeder extends Seeder
{
    protected array $inclusions = [
        ['title' => 'Round-trip AC transport from Dhaka', 'icon' => 'ri-bus-line'],
        ['title' => 'Hotel/Resort accommodation', 'icon' => 'ri-hotel-line'],
        ['title' => 'Daily breakfast', 'icon' => 'ri-restaurant-line'],
        ['title' => 'All meals included', 'icon' => 'ri-restaurant-2-line'],
        ['title' => 'Professional tour guide', 'icon' => 'ri-user-voice-line'],
        ['title' => 'Sightseeing as per itinerary', 'icon' => 'ri-camera-line'],
        ['title' => 'Boat rides & transfers', 'icon' => 'ri-ship-line'],
        ['title' => 'Entry tickets to attractions', 'icon' => 'ri-ticket-line'],
        ['title' => 'Welcome drink on arrival', 'icon' => 'ri-goblet-line'],
        ['title' => 'First aid kit', 'icon' => 'ri-first-aid-kit-line'],
    ];

    protected array $exclusions = [
        ['title' => 'Personal expenses', 'icon' => 'ri-wallet-line'],
        ['title' => 'Travel insurance', 'icon' => 'ri-shield-check-line'],
        ['title' => 'Tips & gratuities', 'icon' => 'ri-hand-coin-line'],
        ['title' => 'Room service charges', 'icon' => 'ri-service-line'],
        ['title' => 'Extra activities not mentioned', 'icon' => 'ri-add-circle-line'],
        ['title' => 'Camera/Video fees at monuments', 'icon' => 'ri-camera-off-line'],
        ['title' => 'Laundry services', 'icon' => 'ri-t-shirt-line'],
        ['title' => 'Alcoholic beverages', 'icon' => 'ri-goblet-line'],
    ];

    protected array $faqs = [
        ['q' => 'What should I pack for this tour?', 'a' => 'Comfortable clothing, walking shoes, sunscreen, sunglasses, hat, camera, personal medications, and light rain gear depending on season.'],
        ['q' => 'Is this tour suitable for children?', 'a' => 'Yes, most of our tours are family-friendly. Please check the difficulty level and age restrictions for specific tours.'],
        ['q' => 'What is the cancellation policy?', 'a' => 'Free cancellation up to the specified days before departure. After that, cancellation fees may apply.'],
        ['q' => 'Are meals included?', 'a' => 'Meal inclusions vary by tour. Please check the inclusions section for specific details.'],
        ['q' => 'What happens if weather is bad?', 'a' => 'We monitor weather conditions and may adjust itinerary or reschedule certain activities for your safety.'],
        ['q' => 'Can I customize this tour?', 'a' => 'Yes, we offer private customized tours. Contact us for personalized packages.'],
    ];

    protected array $highlights = [
        ['title' => 'Professional experienced guides', 'icon' => 'ri-user-star-line'],
        ['title' => 'Small group sizes for personalized experience', 'icon' => 'ri-group-line'],
        ['title' => 'Handpicked quality accommodations', 'icon' => 'ri-hotel-bed-line'],
        ['title' => 'Authentic local cuisine experiences', 'icon' => 'ri-restaurant-line'],
        ['title' => 'Flexible itinerary options', 'icon' => 'ri-calendar-check-line'],
        ['title' => 'Photo opportunities at iconic spots', 'icon' => 'ri-camera-lens-line'],
        ['title' => 'Cultural immersion activities', 'icon' => 'ri-ancient-gate-line'],
        ['title' => '24/7 customer support', 'icon' => 'ri-customer-service-2-line'],
    ];

    protected array $extras = [
        ['name' => 'Airport Transfer', 'description' => 'Private car pickup from airport', 'price' => 1500, 'price_type' => 'per_booking'],
        ['name' => 'Extra Night Stay', 'description' => 'Additional night at the hotel', 'price' => 3000, 'price_type' => 'per_person'],
        ['name' => 'Private Guide', 'description' => 'Dedicated personal guide', 'price' => 2000, 'price_type' => 'per_day'],
        ['name' => 'Camera Equipment Rental', 'description' => 'DSLR camera for photography', 'price' => 1000, 'price_type' => 'per_day'],
        ['name' => 'Special Dinner', 'description' => 'Traditional local cuisine dinner', 'price' => 800, 'price_type' => 'per_person'],
    ];

    protected array $cancellationPolicies = [
        ['name' => 'Full Refund', 'days_before' => 14, 'refund' => 100, 'fee' => 0],
        ['name' => 'Partial Refund', 'days_before' => 7, 'refund' => 75, 'fee' => 500],
        ['name' => 'Limited Refund', 'days_before' => 3, 'refund' => 50, 'fee' => 1000],
        ['name' => 'No Refund', 'days_before' => 0, 'refund' => 0, 'fee' => 0],
    ];

    protected array $pricingPackages = [
        ['name' => 'Standard', 'multiplier' => 1.0],
        ['name' => 'Premium', 'multiplier' => 1.5],
        ['name' => 'Budget', 'multiplier' => 0.7],
    ];

    public function run(): void
    {
        $this->call([
            TourCategorySeeder::class,
            TourDestinationSeeder::class,
        ]);

        $this->command->info('Creating 50 Tour Packages...');

        // Create 50 tours using factory
        $tours = Tour::factory()->count(50)->create();

        $this->command->getOutput()->progressStart($tours->count());

        foreach ($tours as $tour) {
            $this->createPricings($tour);
            $this->createItineraries($tour);
            $this->createInclusions($tour);
            $this->createExclusions($tour);
            $this->createHighlights($tour);
            $this->createFaqs($tour);
            $this->createExtras($tour);
            $this->createCancellationPolicies($tour);

            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info('Tours created successfully!');
    }

    protected function createPricings(Tour $tour): void
    {
        $basePrice = fake()->numberBetween(3, 20) * 1000;

        // Create 1-3 pricing packages
        $selectedPackages = fake()->randomElements($this->pricingPackages, fake()->numberBetween(1, 3));

        foreach ($selectedPackages as $package) {
            $adultPrice = round($basePrice * $package['multiplier'], -2);

            TourPricing::create([
                'tour_id' => $tour->id,
                'name' => $package['name'],
                'description' => $package['name'].' package with '.(fake()->randomElement(['basic', 'standard', 'enhanced'])).' amenities',
                'adult_price' => $adultPrice,
                'child_price' => round($adultPrice * 0.6, -2),
                'infant_price' => 0,
                'is_active' => true,
            ]);
        }
    }

    protected function createItineraries(Tour $tour): void
    {
        $activities = ['Sightseeing', 'Photography', 'Hiking', 'Swimming', 'Boat Ride', 'Local Market Visit', 'Temple Visit', 'Beach Walk', 'Bird Watching', 'Sunset View'];

        for ($day = 1; $day <= $tour->duration_days; $day++) {
            $title = match ($day) {
                1 => 'Day 1: Arrival & Welcome',
                $tour->duration_days => "Day {$day}: Departure Day",
                default => "Day {$day}: Exploration & Adventure",
            };

            TourItinerary::create([
                'tour_id' => $tour->id,
                'title' => $title,
                'description' => fake()->paragraph(2),
                'accommodation' => $day < $tour->duration_days ? fake()->randomElement(['Hotel', 'Resort', 'Cottage', 'Guest House']) : null,
                'meals_included' => $day === 1 ? ['lunch', 'dinner'] : ($day === $tour->duration_days ? ['breakfast'] : ['breakfast', 'lunch', 'dinner']),
                'distance_km' => fake()->randomFloat(1, 5, 50),
                'activities' => fake()->randomElements($activities, fake()->numberBetween(2, 4)),
                'highlights' => fake()->sentences(fake()->numberBetween(1, 3)),
            ]);
        }
    }

    protected function createInclusions(Tour $tour): void
    {
        $selected = fake()->randomElements($this->inclusions, fake()->numberBetween(5, 8));
        foreach ($selected as $i => $inclusion) {
            TourInclusion::create([
                'tour_id' => $tour->id,
                'type' => 'inclusion',
                'title' => $inclusion['title'],
                'icon' => $inclusion['icon'],
                'sort_order' => $i,
            ]);
        }
    }

    protected function createExclusions(Tour $tour): void
    {
        $selected = fake()->randomElements($this->exclusions, fake()->numberBetween(3, 5));
        foreach ($selected as $i => $exclusion) {
            TourInclusion::create([
                'tour_id' => $tour->id,
                'type' => 'exclusion',
                'title' => $exclusion['title'],
                'icon' => $exclusion['icon'],
                'sort_order' => $i,
            ]);
        }
    }

    protected function createHighlights(Tour $tour): void
    {
        $selected = fake()->randomElements($this->highlights, fake()->numberBetween(4, 6));
        foreach ($selected as $i => $highlight) {
            TourHighlight::create([
                'tour_id' => $tour->id,
                'title' => $highlight['title'],
                'description' => fake()->sentence(),
                'icon' => $highlight['icon'],
                'sort_order' => $i,
            ]);
        }
    }

    protected function createFaqs(Tour $tour): void
    {
        $selected = fake()->randomElements($this->faqs, fake()->numberBetween(3, 5));
        foreach ($selected as $i => $faq) {
            TourFaq::create([
                'tour_id' => $tour->id,
                'question' => $faq['q'],
                'answer' => $faq['a'],
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }
    }

    protected function createExtras(Tour $tour): void
    {
        $selected = fake()->randomElements($this->extras, fake()->numberBetween(2, 4));
        foreach ($selected as $extra) {
            TourExtra::create([
                'tour_id' => $tour->id,
                'name' => $extra['name'],
                'description' => $extra['description'],
                'price' => $extra['price'],
                'price_type' => $extra['price_type'],
                'max_quantity' => fake()->numberBetween(1, 10),
                'is_required' => fake()->boolean(10),
                'is_active' => true,
            ]);
        }
    }

    protected function createCancellationPolicies(Tour $tour): void
    {
        foreach ($this->cancellationPolicies as $i => $policy) {
            TourCancellationPolicy::create([
                'tour_id' => $tour->id,
                'name' => $policy['name'],
                'days_before_departure' => $policy['days_before'],
                'refund_percentage' => $policy['refund'],
                'cancellation_fee' => $policy['fee'],
                'description' => "Cancellation {$policy['days_before']}+ days before departure",
                'is_default' => $i === 0,
                'is_active' => true,
            ]);
        }
    }
}
