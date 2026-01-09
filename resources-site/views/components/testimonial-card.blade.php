@props([
    'testimonial',
])

<div
    class="group relative flex flex-col rounded-2xl border border-slate-100 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-blue-100 hover:shadow-xl"
>
    <!-- Quote Icon -->
    <div class="absolute -top-4 left-6">
        <div
            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/25"
        >
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"
                />
            </svg>
        </div>
    </div>

    <!-- Star Rating -->
    <div class="mb-4 mt-4 flex gap-1">
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

    <!-- Title -->
    @if ($testimonial->title)
        <h3 class="mb-3 text-lg font-semibold text-slate-800">
            "{{ $testimonial->title }}"
        </h3>
    @endif

    <!-- Description -->
    <p class="mb-6 flex-1 leading-relaxed text-slate-600">
        {{ Str::limit($testimonial->description, 200) }}
    </p>

    <!-- Author Info -->
    <div class="mt-auto flex items-center gap-4 border-t border-slate-100 pt-4">
        <!-- Avatar -->
        <div class="relative shrink-0">
            @if ($testimonial->image_url)
                <img
                    src="{{ $testimonial->image_url }}"
                    alt="{{ $testimonial->name }}"
                    class="h-12 w-12 rounded-full object-cover ring-2 ring-slate-100"
                />
            @else
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-lg font-bold text-white ring-2 ring-slate-100"
                >
                    {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                </div>
            @endif
            <!-- Verified badge -->
            <div
                class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 ring-2 ring-white"
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
            <h4 class="truncate font-semibold text-slate-800">
                {{ $testimonial->name }}
            </h4>
            <p class="truncate text-sm text-slate-500">
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
