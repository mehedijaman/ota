@extends('site-layout')

@section('meta-title', 'Help Center & FAQ | TriptonicBD')

@section('meta-description', 'Find answers to your questions about bookings, payments, cancellations, and more.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Category tab switching
            const categoryButtons = document.querySelectorAll(
                '[data-category-tab]'
            )
            const categoryPanels = document.querySelectorAll(
                '[data-category-panel]'
            )

            categoryButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    const targetCategory = this.dataset.categoryTab

                    // Update button states
                    categoryButtons.forEach((btn) => {
                        btn.classList.remove(
                            'bg-blue-600',
                            'text-white',
                            'shadow-lg'
                        )
                        btn.classList.add(
                            'bg-white',
                            'text-slate-600',
                            'border',
                            'border-slate-200'
                        )
                    })
                    this.classList.remove(
                        'bg-white',
                        'text-slate-600',
                        'border',
                        'border-slate-200'
                    )
                    this.classList.add('bg-blue-600', 'text-white', 'shadow-lg')

                    // Show/hide panels
                    categoryPanels.forEach((panel) => {
                        if (
                            panel.dataset.categoryPanel === targetCategory ||
                            targetCategory === 'all'
                        ) {
                            panel.classList.remove('hidden')
                        } else {
                            panel.classList.add('hidden')
                        }
                    })
                })
            })

            // Accordion functionality
            const accordionItems = document.querySelectorAll(
                '[data-accordion-item]'
            )

            accordionItems.forEach((item) => {
                const header = item.querySelector('[data-accordion-header]')
                const content = item.querySelector('[data-accordion-content]')
                const icon = item.querySelector('[data-accordion-icon]')

                header.addEventListener('click', function () {
                    const isOpen = content.classList.contains('max-h-96')

                    // Close all other items in the same category
                    const parentPanel = item.closest('[data-category-panel]')
                    if (parentPanel) {
                        parentPanel
                            .querySelectorAll('[data-accordion-content]')
                            .forEach((c) => {
                                c.classList.remove(
                                    'max-h-96',
                                    'opacity-100',
                                    'pb-6'
                                )
                                c.classList.add('max-h-0', 'opacity-0')
                            })
                        parentPanel
                            .querySelectorAll('[data-accordion-icon]')
                            .forEach((i) => {
                                i.classList.remove('rotate-180')
                            })
                    }

                    // Toggle current item
                    if (!isOpen) {
                        content.classList.remove('max-h-0', 'opacity-0')
                        content.classList.add('max-h-96', 'opacity-100', 'pb-6')
                        icon.classList.add('rotate-180')
                    }
                })
            })

            // Search functionality
            const searchInput = document.querySelector('[data-faq-search]')
            const allFaqItems = document.querySelectorAll(
                '[data-accordion-item]'
            )
            const noResultsEl = document.getElementById('no-results')

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const searchTerm = this.value.toLowerCase().trim()
                    let hasResults = false

                    if (searchTerm === '') {
                        // Show all categories and items
                        categoryPanels.forEach((panel) =>
                            panel.classList.remove('hidden')
                        )
                        allFaqItems.forEach((item) =>
                            item.classList.remove('hidden')
                        )
                        if (noResultsEl) noResultsEl.classList.add('hidden')
                        return
                    }

                    // Show all panels for search
                    categoryPanels.forEach((panel) =>
                        panel.classList.remove('hidden')
                    )

                    // Filter FAQ items
                    allFaqItems.forEach((item) => {
                        const question = item
                            .querySelector('[data-accordion-header]')
                            .textContent.toLowerCase()
                        const answer = item
                            .querySelector('[data-accordion-content]')
                            .textContent.toLowerCase()

                        if (
                            question.includes(searchTerm) ||
                            answer.includes(searchTerm)
                        ) {
                            item.classList.remove('hidden')
                            hasResults = true
                        } else {
                            item.classList.add('hidden')
                        }
                    })

                    // Show/hide no results message
                    if (noResultsEl) {
                        noResultsEl.classList.toggle('hidden', hasResults)
                    }
                })
            }
        })
    </script>
@endsection

@section('content')
    <!-- FAQ Hero & Search -->
    <div
        class="relative isolate flex min-h-[420px] items-center overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 pb-24 pt-32"
    >
        <!-- Decorative Elements -->
        <div class="absolute inset-0 -z-10">
            <div
                class="absolute left-10 top-20 h-72 w-72 rounded-full bg-blue-500/20 blur-3xl"
            ></div>
            <div
                class="absolute bottom-10 right-20 h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl"
            ></div>
        </div>

        <!-- Pattern Overlay -->
        <div
            class="absolute inset-0 -z-10 opacity-10"
            style="
                background-image: url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;);
            "
        ></div>

        <div
            class="container relative z-10 mx-auto max-w-4xl px-4 text-center text-white sm:px-6 lg:px-8"
        >
            <div
                class="mb-6 inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 backdrop-blur-sm"
            >
                <svg
                    class="h-5 w-5 text-blue-300"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                </svg>
                <span class="text-sm font-medium text-blue-100">
                    Help Center
                </span>
            </div>

            <h1
                class="mb-6 text-4xl font-bold tracking-tight md:text-5xl lg:text-6xl"
            >
                How can we
                <span
                    class="bg-gradient-to-r from-blue-400 to-cyan-300 bg-clip-text text-transparent"
                >
                    help you?
                </span>
            </h1>

            <p class="mx-auto mb-10 max-w-2xl text-lg text-slate-300">
                Search our knowledge base or browse by category to find the
                answers you need.
            </p>

            <!-- FAQ Search -->
            <div class="group relative mx-auto max-w-2xl">
                <div
                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-5"
                >
                    <svg
                        class="h-6 w-6 text-slate-400 transition-colors duration-200 group-focus-within:text-blue-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                        ></path>
                    </svg>
                </div>
                <input
                    data-faq-search
                    class="w-full rounded-2xl border-0 bg-white py-5 pl-14 pr-6 text-lg font-medium text-slate-800 placeholder-slate-400 shadow-2xl shadow-black/20 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-500/30"
                    placeholder="Search for answers (e.g. 'refund', 'cancellation')"
                />
            </div>
        </div>
    </div>

    <!-- FAQ Content -->
    <section class="bg-slate-50 py-16 lg:py-24">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if ($featuredFaqs->count() > 0)
                <!-- Featured FAQs -->
                <div class="mb-16">
                    <div class="mb-8 flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100"
                        >
                            <svg
                                class="h-5 w-5 text-amber-600"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                ></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800">
                            Frequently Asked
                        </h2>
                    </div>

                    <div class="grid gap-4">
                        @foreach ($featuredFaqs as $faq)
                            <div
                                data-accordion-item
                                class="overflow-hidden rounded-2xl border border-slate-200/60 bg-white shadow-sm transition-shadow duration-200 hover:shadow-md"
                            >
                                <div
                                    data-accordion-header
                                    class="group flex cursor-pointer items-center justify-between p-6"
                                >
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-amber-50"
                                        >
                                            <svg
                                                class="h-4 w-4 text-amber-500"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                                                ></path>
                                            </svg>
                                        </div>
                                        <h3
                                            class="text-lg font-semibold text-slate-800 transition-colors group-hover:text-blue-600"
                                        >
                                            {{ $faq->question }}
                                        </h3>
                                    </div>
                                    <svg
                                        data-accordion-icon
                                        class="ml-4 h-5 w-5 flex-shrink-0 transform text-slate-400 transition-transform duration-300"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        ></path>
                                    </svg>
                                </div>
                                <div
                                    data-accordion-content
                                    class="max-h-0 overflow-hidden px-6 opacity-0 transition-all duration-300 ease-in-out"
                                >
                                    <div
                                        class="prose prose-slate max-w-none pl-12 leading-relaxed text-slate-600"
                                    >
                                        {!! $faq->answer !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($categories->count() > 0)
                <!-- Category Tabs -->
                <div class="mb-10">
                    <div class="mb-8 flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100"
                        >
                            <svg
                                class="h-5 w-5 text-blue-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                ></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800">
                            Browse by Category
                        </h2>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button
                            data-category-tab="all"
                            class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-200 hover:bg-blue-700"
                        >
                            All Categories
                        </button>
                        @foreach ($categories as $category)
                            <button
                                data-category-tab="{{ $category->slug }}"
                                class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-600 transition-all duration-200 hover:border-slate-300 hover:bg-slate-50"
                            >
                                {{ $category->name }}
                                <span
                                    class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500"
                                >
                                    {{ $category->faqs->count() }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Category Panels -->
                @foreach ($categories as $category)
                    @if ($category->faqs->count() > 0)
                        <div
                            data-category-panel="{{ $category->slug }}"
                            class="mb-10"
                        >
                            <div class="mb-6 flex items-center gap-3">
                                <div
                                    class="h-8 w-1 rounded-full bg-gradient-to-b from-blue-500 to-blue-600"
                                ></div>
                                <h3 class="text-xl font-bold text-slate-800">
                                    {{ $category->name }}
                                </h3>
                                @if ($category->description)
                                    <span class="text-sm text-slate-500">
                                        — {{ $category->description }}
                                    </span>
                                @endif
                            </div>

                            <div class="grid gap-3">
                                @foreach ($category->faqs as $faq)
                                    <div
                                        data-accordion-item
                                        class="overflow-hidden rounded-xl border border-slate-200/60 bg-white shadow-sm transition-all duration-200 hover:border-blue-200/60 hover:shadow-md"
                                    >
                                        <div
                                            data-accordion-header
                                            class="group flex cursor-pointer items-center justify-between p-5"
                                        >
                                            <div
                                                class="flex items-center gap-4"
                                            >
                                                <div
                                                    class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-blue-50 transition-colors group-hover:bg-blue-100"
                                                >
                                                    <svg
                                                        class="h-4 w-4 text-blue-500"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                        ></path>
                                                    </svg>
                                                </div>
                                                <h4
                                                    class="font-medium text-slate-800 transition-colors group-hover:text-blue-600"
                                                >
                                                    {{ $faq->question }}
                                                </h4>
                                            </div>
                                            <svg
                                                data-accordion-icon
                                                class="ml-4 h-5 w-5 flex-shrink-0 transform text-slate-400 transition-transform duration-300"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 9l-7 7-7-7"
                                                ></path>
                                            </svg>
                                        </div>
                                        <div
                                            data-accordion-content
                                            class="max-h-0 overflow-hidden px-5 opacity-0 transition-all duration-300 ease-in-out"
                                        >
                                            <div
                                                class="prose prose-slate prose-sm max-w-none pl-12 leading-relaxed text-slate-600"
                                            >
                                                {!! $faq->answer !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                <!-- Empty State -->
                <div class="py-16 text-center">
                    <div
                        class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-slate-100"
                    >
                        <svg
                            class="h-10 w-10 text-slate-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            ></path>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-slate-800">
                        No FAQs Available
                    </h3>
                    <p class="text-slate-600">
                        Check back later for helpful answers to common
                        questions.
                    </p>
                </div>
            @endif

            <!-- No Search Results -->
            <div id="no-results" class="hidden py-12 text-center">
                <div
                    class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100"
                >
                    <svg
                        class="h-8 w-8 text-slate-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                        ></path>
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-bold text-slate-800">
                    No results found
                </h3>
                <p class="text-slate-600">
                    Try adjusting your search terms or browse by category.
                </p>
            </div>
        </div>
    </section>

    <!-- Contact Support CTA -->
    <section class="bg-white py-16">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div
                class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 to-indigo-700 p-10 text-center lg:p-14"
            >
                <!-- Decorative -->
                <div
                    class="absolute right-0 top-0 h-64 w-64 -translate-y-1/2 translate-x-1/2 rounded-full bg-white/10"
                ></div>
                <div
                    class="absolute bottom-0 left-0 h-48 w-48 -translate-x-1/2 translate-y-1/2 rounded-full bg-white/10"
                ></div>

                <div class="relative">
                    <div
                        class="mb-6 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm"
                    >
                        <svg
                            class="h-8 w-8 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"
                            ></path>
                        </svg>
                    </div>

                    <h2 class="mb-4 text-2xl font-bold text-white lg:text-3xl">
                        Still have questions?
                    </h2>
                    <p class="mx-auto mb-8 max-w-lg text-lg text-blue-100">
                        Can't find what you're looking for? Our friendly support
                        team is here to help you 24/7.
                    </p>

                    <div
                        class="flex flex-col items-center justify-center gap-4 sm:flex-row"
                    >
                        <a
                            href=""
                            class="inline-flex items-center gap-2 rounded-xl bg-white px-8 py-4 font-bold text-blue-600 shadow-lg shadow-blue-900/20 transition-colors hover:bg-blue-50"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                ></path>
                            </svg>
                            Contact Support
                        </a>
                        <a
                            href="tel:+8801234567890"
                            class="inline-flex items-center gap-2 font-medium text-white/90 transition-colors hover:text-white"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                ></path>
                            </svg>
                            Call Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
