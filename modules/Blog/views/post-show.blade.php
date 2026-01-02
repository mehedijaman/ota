@extends('site-layout')

@section('meta-title', $post->meta_tag_title)

@section('meta-description', $post->meta_tag_description)

@section('bodyEndScripts')
    @vite('resources-site/js/blog-app.js')
@endsection

@section('content')
    <div class="bg-slate-50 min-h-screen py-16 sm:py-24">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <article class="rounded-2xl bg-white shadow-sm overflow-hidden">
                <!-- Featured Image -->
                @if ($post->image_url)
                    <div class="relative">
                        <img src="{{ $post->image_url }}" alt="{{ $post->title }}"
                            class="aspect-video w-full object-cover">
                    </div>
                @endif

                <!-- Content Area -->
                <div class="p-6 sm:p-10 lg:p-12">
                    <!-- Meta -->
                    <div class="flex flex-wrap items-center gap-4 text-sm">
                        <time datetime="{{ $post->published_at }}" class="font-semibold text-blue-600">
                            {{ $post->published_at->format('F d, Y') }}
                        </time>
                        @if ($post->author)
                            <span class="text-slate-300">•</span>
                            <div class="flex items-center gap-2">
                                @if ($post->author->image_url)
                                    <img src="{{ $post->author->image_url }}" alt="{{ $post->author->name }}"
                                        class="h-6 w-6 rounded-full object-cover">
                                @else
                                    <div class="h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-xs font-bold text-blue-600">{{ substr($post->author->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <span class="font-medium text-slate-700">{{ $post->author->name }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="mt-6 text-2xl font-bold leading-tight text-slate-900 sm:text-3xl lg:text-4xl">
                        {{ $post->title }}
                    </h1>

                    <!-- Tags -->
                    @if ($post->tags->count())
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($post->tags as $tag)
                                <a href="/blog/tag/{{ $tag->slug }}"
                                    class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600 hover:bg-blue-100 transition-colors">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <!-- Divider -->
                    <hr class="my-8 border-slate-200">

                    <!-- Article Body -->
                    <div class="prose prose-slate max-w-none prose-headings:font-bold prose-headings:text-slate-900 prose-a:text-blue-600 hover:prose-a:text-blue-800 prose-img:rounded-xl">
                        {!! $post->content !!}
                    </div>

                    <!-- Back to Blog -->
                    <div class="mt-12 pt-8 border-t border-slate-200">
                        <a href="{{ route('site.blog.index') }}"
                            class="inline-flex items-center gap-2 rounded-full border border-blue-600 px-5 py-2.5 text-sm font-semibold text-blue-600 transition-all hover:bg-blue-600 hover:text-white">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                            </svg>
                            Back to Blog
                        </a>
                    </div>
                </div>
            </article>
        </div>
    </div>
@endsection
