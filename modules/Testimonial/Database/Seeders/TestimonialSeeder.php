<?php

namespace Modules\Testimonial\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Testimonial\Models\Testimonial;

use function Laravel\Prompts\info;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        info('Creating Testimonials...');

        Testimonial::factory()->count(10)->active()->create();
        Testimonial::factory()->count(3)->featured()->active()->create();

        info('Testimonials created.');
    }
}
