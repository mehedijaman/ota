@props([
    'testimonials',
])

@if ($testimonials->count() > 0)
    <!-- TESTIMONIALS -->
    <section
        class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 py-20"
    >
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div
                class="absolute left-0 top-0 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full bg-blue-500 blur-3xl"
            ></div>
            <div
                class="absolute bottom-0 right-0 h-96 w-96 translate-x-1/2 translate-y-1/2 rounded-full bg-indigo-500 blur-3xl"
            ></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div
                class="mb-16 flex flex-col items-center justify-between gap-6 text-center md:flex-row md:text-left"
            >
                <div>
                    <span
                        class="mb-4 inline-flex items-center gap-2 rounded-full bg-blue-500/10 px-4 py-2 text-sm font-medium text-blue-400"
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
                    <h2
                        class="text-4xl font-bold tracking-tight text-white sm:text-5xl"
                    >
                        What Our Travelers Say
                    </h2>
                    <p class="mt-4 max-w-2xl text-lg text-slate-400">
                        Real experiences from real travelers who explored the
                        world with us
                    </p>
                </div>
                <a
                    href="{{ route('site.testimonial.index') }}"
                    class="group inline-flex items-center gap-2 rounded-full bg-white/10 px-6 py-3 font-medium text-white backdrop-blur-sm transition-all hover:bg-white/20"
                >
                    See All Reviews
                    <svg
                        class="h-4 w-4 transition-transform group-hover:translate-x-1"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7"
                        />
                    </svg>
                </a>
            </div>

            <!-- Testimonials Grid -->
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($testimonials as $index => $testimonial)
                    <div
                        class="group relative flex flex-col rounded-3xl bg-white/5 p-8 backdrop-blur-sm transition-all duration-500 hover:-translate-y-2 hover:bg-white/10"
                        style="animation-delay: {{ $index * 100 }}ms"
                    >
                        <!-- Decorative gradient border -->
                        <div
                            class="pointer-events-none absolute inset-0 rounded-3xl border border-white/10 transition-colors group-hover:border-white/20"
                        ></div>

                        <!-- Quote Icon -->
                        <div class="mb-6 flex items-center justify-between">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/25"
                            >
                                <svg
                                    class="h-6 w-6 text-white"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"
                                    />
                                </svg>
                            </div>

                            <!-- Star Rating -->
                            <div class="flex gap-1">
                                @for ($i = 0; $i < 5; $i++)
                                    <svg
                                        class="h-5 w-5 text-amber-400"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                        />
                                    </svg>
                                @endfor
                            </div>
                        </div>

                        <!-- Title -->
                        @if ($testimonial->title)
                            <h3 class="mb-3 text-xl font-semibold text-white">
                                "{{ $testimonial->title }}"
                            </h3>
                        @endif

                        <!-- Description -->
                        <p
                            class="mb-8 flex-1 text-base leading-relaxed text-slate-300"
                        >
                            {{ Str::limit($testimonial->description, 180) }}
                        </p>

                        <!-- Author Info -->
                        <div
                            class="mt-auto flex items-center gap-4 border-t border-white/10 pt-6"
                        >
                            <!-- Avatar -->
                            <div class="relative shrink-0">
                                @if ($testimonial->image_url)
                                    <img
                                        src="{{ $testimonial->image_url }}"
                                        alt="{{ $testimonial->name }}"
                                        class="h-14 w-14 rounded-full object-cover ring-2 ring-white/20"
                                    />
                                @else
                                    <div
                                        class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-xl font-bold text-white ring-2 ring-white/20"
                                    >
                                        {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                                    </div>
                                @endif
                                <!-- Verified badge -->
                                <div
                                    class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 ring-2 ring-slate-900"
                                >
                                    <svg
                                        class="h-3 w-3 text-white"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                            </div>

                            <!-- Name & Details -->
                            <div class="min-w-0 flex-1">
                                <h4
                                    class="truncate text-lg font-semibold text-white"
                                >
                                    {{ $testimonial->name }}
                                </h4>
                                <p class="truncate text-sm text-slate-400">
                                    @if ($testimonial->designation && $testimonial->company)
                                        {{ $testimonial->designation }} at
                                        {{ $testimonial->company }}
                                    @elseif ($testimonial->designation)
                                        {{ $testimonial->designation }}
                                    @elseif ($testimonial->company)
                                        {{ $testimonial->company }}
                                    @else
                                            Verified Traveler
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Trust Indicators -->
            <div
                class="mt-16 flex flex-wrap items-center justify-center gap-8 border-t border-white/10 pt-12 text-center"
            >
                <div class="flex flex-col items-center">
                    <span class="text-3xl font-bold text-white">10,000+</span>
                    <span class="text-sm text-slate-400">Happy Travelers</span>
                </div>
                <div class="h-12 w-px bg-white/10"></div>
                <div class="flex flex-col items-center">
                    <span class="text-3xl font-bold text-white">4.9/5</span>
                    <span class="text-sm text-slate-400">Average Rating</span>
                </div>
                <div class="h-12 w-px bg-white/10"></div>
                <div class="flex flex-col items-center">
                    <span class="text-3xl font-bold text-white">50+</span>
                    <span class="text-sm text-slate-400">Destinations</span>
                </div>
                <div class="h-12 w-px bg-white/10"></div>
                <div class="flex flex-col items-center">
                    <span class="text-3xl font-bold text-white">98%</span>
                    <span class="text-sm text-slate-400">Would Recommend</span>
                </div>
            </div>
        </div>
    </section>
@endif
