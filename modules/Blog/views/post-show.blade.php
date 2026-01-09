@extends('site-layout')

@section('meta-title', $post->meta_tag_title)

@section('meta-description', $post->meta_tag_description)

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
            <!-- Breadcrumb -->
            <nav class="mb-6 flex justify-center">
                <ol class="flex items-center gap-2 text-sm text-slate-400">
                    <li>
                        <a
                            href="{{ route('site.blog.index') }}"
                            class="transition-colors hover:text-white"
                        >
                            Blog
                        </a>
                    </li>
                    <li>
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
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </li>
                    <li class="text-blue-400">Article</li>
                </ol>
            </nav>
            <h1
                class="mx-auto max-w-4xl text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl"
            >
                {{ $post->title }}
            </h1>
            <!-- Meta -->
            <div
                class="mt-6 flex flex-wrap items-center justify-center gap-4 text-sm text-slate-300"
            >
                <time
                    datetime="{{ $post->published_at }}"
                    class="flex items-center gap-2"
                >
                    <svg
                        class="h-4 w-4 text-blue-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                    </svg>
                    {{ $post->published_at->format('F d, Y') }}
                </time>
                @if ($post->author)
                    <span class="text-slate-500">•</span>
                    <div class="flex items-center gap-2">
                        @if ($post->author->image_url)
                            <img
                                src="{{ $post->author->image_url }}"
                                alt="{{ $post->author->name }}"
                                class="h-6 w-6 rounded-full object-cover ring-2 ring-white/20"
                            />
                        @else
                            <div
                                class="flex h-6 w-6 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-xs font-bold text-white ring-2 ring-white/20"
                            >
                                {{ strtoupper(substr($post->author->name, 0, 1)) }}
                            </div>
                        @endif
                        <span>{{ $post->author->name }}</span>
                    </div>
                @endif
            </div>
            <!-- Tags -->
            @if ($post->tags->count())
                <div class="mt-6 flex flex-wrap justify-center gap-2">
                    @foreach ($post->tags as $tag)
                        <a
                            href="{{ route('site.blog.tag', $tag->slug) }}"
                            class="rounded-full bg-white/10 px-4 py-1.5 text-xs font-medium text-white backdrop-blur-sm transition-colors hover:bg-white/20"
                        >
                            {{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="relative mx-auto -mt-10 mb-16 max-w-4xl px-4 sm:px-6 lg:px-8">
        <article class="overflow-hidden rounded-2xl bg-white shadow-xl">
            <!-- Featured Image -->
            @if ($post->image_url)
                <div class="relative">
                    <img
                        src="{{ $post->image_url }}"
                        alt="{{ $post->title }}"
                        class="aspect-video w-full object-cover"
                    />
                </div>
            @endif

            <!-- Content Area -->
            <div class="p-6 sm:p-10 lg:p-12">
                <!-- Article Body -->
                <div
                    class="prose prose-lg prose-slate prose-headings:font-bold prose-headings:text-slate-900 prose-a:text-blue-600 hover:prose-a:text-blue-800 prose-img:rounded-xl prose-pre:bg-slate-900 max-w-none"
                >
                    {!! $post->content !!}
                </div>

                <!-- Share & Back -->
                <div
                    class="mt-12 flex flex-col items-center justify-between gap-6 border-t border-slate-200 pt-8 sm:flex-row"
                >
                    <!-- Share -->
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-slate-500">
                            Share this article:
                        </span>
                        <div class="flex gap-2">
                            <a
                                href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition-colors hover:bg-blue-500 hover:text-white"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"
                                    />
                                </svg>
                            </a>
                            <a
                                href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition-colors hover:bg-blue-600 hover:text-white"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
                                    />
                                </svg>
                            </a>
                            <a
                                href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($post->title) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition-colors hover:bg-blue-700 hover:text-white"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"
                                    />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Back to Blog -->
                    <a
                        href="{{ route('site.blog.index') }}"
                        class="group inline-flex items-center gap-2 rounded-full bg-blue-600 px-6 py-3 font-medium text-white shadow-lg shadow-blue-600/25 transition-all hover:bg-blue-700 hover:shadow-blue-600/40"
                    >
                        <svg
                            class="h-4 w-4 transition-transform group-hover:-translate-x-1"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M7 16l-4-4m0 0l4-4m-4 4h18"
                            />
                        </svg>
                        Back to Blog
                    </a>
                </div>
            </div>
        </article>
    </div>
@endsection
