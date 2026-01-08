<?php

namespace Modules\Testimonial\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Testimonial\Models\Testimonial;

use function Laravel\Prompts\info;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        info('Creating Testimonials...');
        Testimonial::factory()->count(30)->create();
        info('Testimonials created.');

        Schema::enableForeignKeyConstraints();
    }
}
