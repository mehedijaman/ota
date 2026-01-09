@props([
    'posts',
])

@if ($posts->count() > 0)
    <!-- LATEST BLOG POSTS -->
    <section class="bg-slate-50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div
                class="mb-12 flex flex-col items-center justify-between gap-6 text-center md:flex-row md:text-left"
            >
                <div>
                    <span
                        class="mb-4 inline-flex items-center gap-2 rounded-full bg-blue-100 px-4 py-2 text-sm font-medium text-blue-600"
                    >
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"
                            />
                        </svg>
                        From Our Blog
                    </span>
                    <h2
                        class="text-3xl font-bold tracking-tight text-slate-800 sm:text-4xl"
                    >
                        Latest Travel Stories
                    </h2>
                    <p class="mt-3 max-w-2xl text-lg text-slate-500">
                        Tips, guides, and inspiration for your next adventure
                    </p>
                </div>
                <a
                    href="{{ route('site.blog.index') }}"
                    class="group inline-flex items-center gap-2 rounded-full bg-blue-600 px-6 py-3 font-medium text-white shadow-lg shadow-blue-600/25 transition-all hover:bg-blue-700 hover:shadow-blue-600/40"
                >
                    View All Posts
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

            <!-- Posts Grid -->
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <article
                        class="group flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                    >
                        <!-- Image -->
                        <a
                            href="{{ route('site.blog.show', $post->slug) }}"
                            class="relative block aspect-[16/10] overflow-hidden"
                        >
                            @if ($post->image_url)
                                <img
                                    src="{{ $post->image_url }}"
                                    alt="{{ $post->title }}"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                            @else
                                <div
                                    class="flex h-full w-full items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600"
                                >
                                    <svg
                                        class="h-16 w-16 text-white/50"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"
                                        />
                                    </svg>
                                </div>
                            @endif
                            <!-- Date Badge -->
                            <div
                                class="absolute left-4 top-4 rounded-lg bg-white/90 px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm backdrop-blur-sm"
                            >
                                {{ $post->published_at?->format('M d, Y') ?? 'Draft' }}
                            </div>
                        </a>

                        <!-- Content -->
                        <div class="flex flex-1 flex-col p-6">
                            <!-- Tags -->
                            @if ($post->tags && $post->tags->count() > 0)
                                <div class="mb-3 flex flex-wrap gap-2">
                                    @foreach ($post->tags->take(2) as $tag)
                                        <span
                                            class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-600"
                                        >
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Title -->
                            <h3 class="mb-3 text-xl font-bold text-slate-800">
                                <a
                                    href="{{ route('site.blog.show', $post->slug) }}"
                                    class="transition-colors hover:text-blue-600"
                                >
                                    {{ Str::limit($post->title, 60) }}
                                </a>
                            </h3>

                            <!-- Summary -->
                            <p
                                class="mb-4 flex-1 text-sm leading-relaxed text-slate-500"
                            >
                                {{ Str::limit($post->summary, 120) }}
                            </p>

                            <!-- Footer -->
                            <div
                                class="mt-auto flex items-center justify-between border-t border-slate-100 pt-4"
                            >
                                <!-- Author -->
                                @if ($post->author)
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-xs font-bold text-white"
                                        >
                                            {{ strtoupper(substr($post->author->name, 0, 1)) }}
                                        </div>
                                        <span
                                            class="text-sm font-medium text-slate-600"
                                        >
                                            {{ $post->author->name }}
                                        </span>
                                    </div>
                                @else
                                    <div class="text-sm text-slate-400">
                                        TriptonicBD
                                    </div>
                                @endif

                                <!-- Read More -->
                                <a
                                    href="{{ route('site.blog.show', $post->slug) }}"
                                    class="group/link inline-flex items-center gap-1 text-sm font-medium text-blue-600 transition-colors hover:text-blue-700"
                                >
                                    Read More
                                    <svg
                                        class="h-4 w-4 transition-transform group-hover/link:translate-x-0.5"
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
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
