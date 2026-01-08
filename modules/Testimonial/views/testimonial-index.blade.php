@extends('site-layout')

@section('meta-title', 'Testimonials')

@section('meta-description', '')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <x-page-header></x-page-header>
    <div
        class="relative mx-auto -mt-14 mb-16 max-w-screen-xl rounded bg-white p-8 px-4"
    >
        <div class="flex flex-col items-center">
            <x-section-header
                title="Client Testimonials"
                text="What clients say about their experience with us"
            ></x-section-header>

            <div
                class="relative mx-auto grid max-w-lg grid-cols-1 gap-6 md:max-w-none md:grid-cols-3 lg:gap-10"
            >
                @foreach ($testimonials as $testimonial)
                    <x-testimonial-card
                        :testimonial="$testimonial"
                    ></x-testimonial-card>
                @endforeach
            </div>
        </div>

        <div class="mt-12">
            {{ $testimonials->links('pagination.tailwind') }}
        </div>
    </div>
@endsection
