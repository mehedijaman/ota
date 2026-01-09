@extends('site-layout')

@section('meta-title', 'Testimonials')

@section('meta-description', 'Read what our happy travelers say about their experiences with us')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- Page Header -->
    <div
        class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 py-20"
    >
        <div class="absolute inset-0 opacity-20">
            <div
                class="absolute left-1/4 top-0 h-64 w-64 rounded-full bg-blue-500 blur-3xl"
            ></div>
            <div
                class="absolute bottom-0 right-1/4 h-64 w-64 rounded-full bg-indigo-500 blur-3xl"
            ></div>
        </div>
        <div
            class="relative mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8"
        >
            <h1
                class="text-4xl font-bold tracking-tight text-white sm:text-5xl"
            >
                Customer Testimonials
            </h1>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-300">
                Read what our happy travelers say about their experiences
            </p>
        </div>
    </div>

    <div class="relative mx-auto -mt-10 mb-16 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-white p-8 shadow-xl">
            <!-- Header -->
            <div class="mb-12 text-center">
                <span
                    class="mb-4 inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-sm font-medium text-blue-600"
                >
                    <svg
                        class="h-4 w-4"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                    </svg>
                    Customer Reviews
                </span>
                <h2 class="text-3xl font-bold text-slate-800 sm:text-4xl">
                    What Our Travelers Say
                </h2>
                <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-500">
                    Real experiences from real travelers who explored the world
                    with us
                </p>
            </div>

            @if ($testimonials->count() > 0)
                <!-- Testimonials Grid -->
                <div
                    class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3"
                >
                    @foreach ($testimonials as $testimonial)
                        <x-testimonial-card :testimonial="$testimonial" />
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12">
                    {{ $testimonials->links('pagination.tailwind') }}
                </div>
            @else
                <!-- Empty State -->
                <div class="py-16 text-center">
                    <svg
                        class="mx-auto h-16 w-16 text-slate-300"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                        />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-slate-600">
                        No testimonials yet
                    </h3>
                    <p class="mt-2 text-slate-500">
                        Check back soon for customer reviews!
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection
