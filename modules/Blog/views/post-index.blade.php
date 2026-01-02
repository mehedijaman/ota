@extends('site-layout')

@section('meta-title', 'Blog')

@section('meta-description', 'Here you will find the latest news and updates about us.')

@section('bodyEndScripts')
    @vite('resources-site/js/blog-app.js')
@endsection

@section('content')
    <blog-toolbar :archive-options="{{ json_encode($archiveOptions) }}" :tags="{{ json_encode($tags) }}"></blog-toolbar>

    <div class="bg-slate-50 min-h-screen py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="mb-12 text-center">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    Blog
                </h1>
                <p class="mt-3 text-lg text-slate-600">
                    @if (isset($fromArchive))
                        Posts from archive: <span class="font-semibold text-blue-600">{{ $fromArchive }}</span>
                    @elseif (isset($fromTag))
                        Posts tagged with: <span class="font-semibold text-blue-600">{{ $fromTag }}</span>
                    @elseif (isset($fromSearch))
                        Search results for: <span class="font-semibold text-blue-600">{{ $fromSearch }}</span>
                    @else
                        Here you will find the latest news and updates about us.
                    @endif
                </p>
            </div>

            @if ($posts->count())
                <!-- Posts Grid -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        <article class="group flex flex-col overflow-hidden rounded-xl bg-white shadow-sm hover:shadow-lg transition-shadow duration-300">
                            <!-- Image -->
                            <div class="relative overflow-hidden">
                                @if ($post->image_url)
                                    <img src="{{ $post->image_url }}" alt="{{ $post->title }}"
                                        class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                @else
                                    <div class="aspect-video w-full bg-linear-to-br from-blue-100 to-blue-50 flex items-center justify-center">
                                        <svg class="h-12 w-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex flex-1 flex-col p-6">
                                <!-- Date -->
                                <time datetime="{{ $post->published_at }}" class="text-sm font-semibold text-blue-600">
                                    {{ $post->published_at->format('F d, Y') }}
                                </time>

                                <!-- Title -->
                                <h3 class="mt-3 text-lg font-bold leading-snug text-slate-900 line-clamp-2">
                                    {{ $post->title }}
                                </h3>

                                <!-- Summary -->
                                <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-500 line-clamp-3">
                                    {!! strip_tags($post->summary) !!}
                                </p>

                                <!-- Read More Button -->
                                <div class="mt-6">
                                    <a href="/blog/{{ $post->slug }}"
                                        class="inline-flex items-center gap-2 rounded-full border border-blue-600 px-5 py-2.5 text-sm font-semibold text-blue-600 transition-all hover:bg-blue-600 hover:text-white">
                                        Read More
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-xl bg-white p-12 text-center shadow-sm">
                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">No posts found</h3>
                    <p class="mt-2 text-slate-500">Try adjusting your search or filter criteria.</p>
                </div>
            @endif

            <!-- Pagination -->
            <div class="mt-12 flex justify-center">
                {{ $posts->links('pagination.tailwind') }}
            </div>
        </div>
    </div>
@endsection
