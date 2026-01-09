@extends('site-layout')

@section('meta-title', 'Blog')

@section('meta-description', 'Here you will find the latest news and updates about us.')

@section('bodyEndScripts')
    @vite('resources-site/js/blog-app.js')
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
                @if (isset($fromArchive))
                    Blog Archive
                @elseif (isset($fromTag))
                    Tagged Posts
                @elseif (isset($fromSearch))
                    Search Results
                @else
                        Our Blog
                @endif
            </h1>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-300">
                @if (isset($fromArchive))
                    Posts from
                    <span class="font-semibold text-blue-400">
                        {{ $fromArchive }}
                    </span>
                @elseif (isset($fromTag))
                    Posts tagged with
                    <span class="font-semibold text-blue-400">
                        {{ $fromTag }}
                    </span>
                @elseif (isset($fromSearch))
                    Results for
                    <span class="font-semibold text-blue-400">
                        "{{ $fromSearch }}"
                    </span>
                @else
                        Tips, guides, and inspiration for your next adventure
                @endif
            </p>
        </div>
    </div>

    <blog-toolbar
        :archive-options="{{ json_encode($archiveOptions) }}"
        :tags="{{ json_encode($tags) }}"
    ></blog-toolbar>

    <div class="relative mx-auto -mt-10 mb-16 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-white p-8 shadow-xl">
            <!-- Header -->
            <div class="mb-12 text-center">
                <span
                    class="mb-4 inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-sm font-medium text-blue-600"
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
                    Latest Articles
                </span>
                <h2 class="text-3xl font-bold text-slate-800 sm:text-4xl">
                    Travel Stories & Tips
                </h2>
                <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-500">
                    Discover insights, guides, and inspiration from our travel
                    experts
                </p>
            </div>

            @if ($posts->count())
                <!-- Posts Grid -->
                <div
                    class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3"
                >
                    @foreach ($posts as $post)
                        <article
                            class="group flex flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-blue-100 hover:shadow-xl"
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
                                    {{ $post->published_at->format('M d, Y') }}
                                </div>
                            </a>

                            <!-- Content -->
                            <div class="flex flex-1 flex-col p-6">
                                <!-- Tags -->
                                @if ($post->tags && $post->tags->count() > 0)
                                    <div class="mb-3 flex flex-wrap gap-2">
                                        @foreach ($post->tags->take(2) as $tag)
                                            <a
                                                href="{{ route('site.blog.tag', $tag->slug) }}"
                                                class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-600 transition-colors hover:bg-blue-100"
                                            >
                                                {{ $tag->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Title -->
                                <h3
                                    class="mb-3 text-xl font-bold text-slate-800"
                                >
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
                                    {{ Str::limit(strip_tags($post->summary), 120) }}
                                </p>

                                <!-- Footer -->
                                <div
                                    class="mt-auto flex items-center justify-between border-t border-slate-100 pt-4"
                                >
                                    <!-- Author -->
                                    @if ($post->author)
                                        <div class="flex items-center gap-2">
                                            @if ($post->author->image_url)
                                                <img
                                                    src="{{ $post->author->image_url }}"
                                                    alt="{{ $post->author->name }}"
                                                    class="h-8 w-8 rounded-full object-cover ring-2 ring-slate-100"
                                                />
                                            @else
                                                <div
                                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-xs font-bold text-white ring-2 ring-slate-100"
                                                >
                                                    {{ strtoupper(substr($post->author->name, 0, 1)) }}
                                                </div>
                                            @endif
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

                <!-- Pagination -->
                <div class="mt-12">
                    {{ $posts->links('pagination.tailwind') }}
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
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"
                        />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-slate-600">
                        No posts found
                    </h3>
                    <p class="mt-2 text-slate-500">
                        Try adjusting your search or filter criteria.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection
