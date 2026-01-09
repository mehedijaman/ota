<?php

namespace Modules\Tour\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourDepartureDate;
use Modules\Tour\Models\TourFaq;
use Modules\Tour\Models\TourHighlight;
use Modules\Tour\Models\TourInclusion;
use Modules\Tour\Models\TourItinerary;

class TourSeeder extends Seeder
{
    protected array $inclusions = [
        'Round-trip AC transport from Dhaka',
        'Hotel/Resort accommodation',
        'Daily breakfast',
        'All meals included',
        'Professional tour guide',
        'Sightseeing as per itinerary',
        'Boat rides & transfers',
        'Entry tickets to attractions',
        'Welcome drink on arrival',
        'First aid kit',
    ];

    protected array $exclusions = [
        'Personal expenses',
        'Travel insurance',
        'Tips & gratuities',
        'Room service charges',
        'Extra activities not mentioned',
        'Camera/Video fees at monuments',
        'Laundry services',
        'Alcoholic beverages',
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
        'Professional experienced guides',
        'Small group sizes for personalized experience',
        'Handpicked quality accommodations',
        'Authentic local cuisine experiences',
        'Flexible itinerary options',
        'Photo opportunities at iconic spots',
        'Cultural immersion activities',
        '24/7 customer support',
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
            $this->createItineraries($tour);
            $this->createInclusions($tour);
            $this->createExclusions($tour);
            $this->createHighlights($tour);
            $this->createFaqs($tour);
            $this->createDepartureDates($tour);

            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info('Tours created successfully!');
    }

    protected function createItineraries(Tour $tour): void
    {
        for ($day = 1; $day <= $tour->duration_days; $day++) {
            $title = match ($day) {
                1 => 'Arrival & Welcome',
                $tour->duration_days => 'Departure Day',
                default => 'Day '.$day.' - Exploration',
            };

            TourItinerary::create([
                'tour_id' => $tour->id,
                'day_number' => $day,
                'title' => $title,
                'description' => fake()->paragraph(2),
                'accommodation' => $day < $tour->duration_days ? fake()->randomElement(['Hotel', 'Resort', 'Cottage', 'Guest House']) : null,
                'meals_included' => $day === 1 ? ['lunch', 'dinner'] : ($day === $tour->duration_days ? ['breakfast'] : ['breakfast', 'lunch', 'dinner']),
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
                'title' => $inclusion,
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
                'title' => $exclusion,
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
                'title' => $highlight,
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
            ]);
        }
    }

    protected function createDepartureDates(Tour $tour): void
    {
        // Create 5-10 upcoming departure dates
        $count = fake()->numberBetween(5, 10);

        for ($i = 0; $i < $count; $i++) {
            $departureDate = now()->addDays(fake()->numberBetween(7 + ($i * 15), 14 + ($i * 15)));
            $slots = fake()->numberBetween(10, 30);

            TourDepartureDate::create([
                'tour_id' => $tour->id,
                'departure_date' => $departureDate,
                'return_date' => $departureDate->copy()->addDays($tour->duration_days - 1),
                'available_slots' => $slots,
                'booked_slots' => fake()->numberBetween(0, (int) ($slots * 0.5)),
                'status' => 'available',
            ]);
        }
    }
}
