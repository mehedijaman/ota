<!-- NAVBAR -->
<header
    class="sticky top-0 z-50 border-b border-gray-100 bg-white/80 backdrop-blur-md"
>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-20 items-center justify-between">
            <!-- Logo -->
            <a
                href="{{ route('site.index') }}"
                class="group flex shrink-0 items-center gap-3"
            >
                <div
                    class="relative flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl text-white shadow-lg shadow-blue-500/30 transition-transform duration-300 group-hover:scale-105"
                >
                    <div
                        class="bg-linear-to-tr absolute inset-0 from-blue-600 to-indigo-600 transition-colors group-hover:from-blue-500 group-hover:to-indigo-500"
                    ></div>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="relative z-10 h-5 w-5 -rotate-45 transform"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path
                            d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z"
                        />
                    </svg>
                </div>
                <span class="text-2xl font-bold tracking-tight text-slate-800">
                    {{ $siteSettings->site_name ?? 'TriptonicBD' }}
                </span>
            </a>

            <!-- Desktop Nav -->
            <nav class="hidden items-center gap-8 md:flex">
                @foreach ($primaryMenu ?? [] as $item)
                    @if ($item->children->isNotEmpty())
                        {{-- Menu item with dropdown --}}
                        <div class="group relative">
                            <button
                                class="flex items-center gap-1 text-sm font-medium text-slate-600 outline-none transition-colors hover:text-blue-600"
                            >
                                {{ $item->title }}
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 transition-transform group-hover:rotate-180"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    />
                                </svg>
                            </button>
                            {{-- Dropdown --}}
                            <div
                                class="invisible absolute left-0 z-50 mt-2 w-48 origin-top-left transform rounded-xl border border-gray-100 bg-white opacity-0 shadow-xl transition-all duration-200 group-hover:visible group-hover:opacity-100"
                            >
                                <div class="py-2">
                                    @foreach ($item->children as $child)
                                        <a
                                            href="{{ $child->url }}"
                                            @if($child->target === '_blank') target="_blank" rel="noopener noreferrer" @endif
                                            class="block px-4 py-2 text-sm text-slate-600 transition-colors hover:bg-slate-50 hover:text-blue-600"
                                        >
                                            {{ $child->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Simple menu item --}}
                        <a
                            href="{{ $item->url }}"
                            @if($item->target === '_blank') target="_blank" rel="noopener noreferrer" @endif
                            class="text-sm font-medium text-slate-600 transition-colors hover:text-blue-600"
                        >
                            {{ $item->title }}
                        </a>
                    @endif
                @endforeach

                <button
                    class="rounded-full bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-200 transition-all hover:scale-105 hover:bg-blue-700 active:scale-95"
                >
                    Login
                </button>
            </nav>

            <!-- Mobile Menu Button -->
            <button
                id="mobile-menu-btn"
                class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 focus:outline-none md:hidden"
            >
                <svg
                    id="menu-icon"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 transition-transform duration-300"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
                <svg
                    id="close-icon"
                    xmlns="http://www.w3.org/2000/svg"
                    class="hidden h-6 w-6 transition-transform duration-300"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div
        id="mobile-menu"
        class="hidden border-b border-gray-100 bg-white md:hidden"
    >
        <div class="space-y-1 px-4 pb-6 pt-2">
            @foreach ($primaryMenu ?? [] as $index => $item)
                @if ($item->children->isNotEmpty())
                    {{-- Mobile dropdown --}}
                    <div class="mobile-dropdown space-y-1">
                        <button
                            type="button"
                            class="mobile-dropdown-toggle flex w-full items-center justify-between rounded-lg px-3 py-2 text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600"
                            data-dropdown="mobile-dropdown-{{ $index }}"
                        >
                            <span>{{ $item->title }}</span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="mobile-dropdown-icon h-4 w-4 transition-transform duration-200"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>
                        <div
                            id="mobile-dropdown-{{ $index }}"
                            class="mobile-dropdown-content hidden space-y-1 pl-4"
                        >
                            @foreach ($item->children as $child)
                                <a
                                    href="{{ $child->url }}"
                                    @if($child->target === '_blank') target="_blank" rel="noopener noreferrer" @endif
                                    class="block rounded-lg px-3 py-2 text-sm text-slate-500 hover:bg-slate-50 hover:text-blue-600"
                                >
                                    {{ $child->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- Simple mobile menu item --}}
                    <a
                        href="{{ $item->url }}"
                        @if($item->target === '_blank') target="_blank" rel="noopener noreferrer" @endif
                        class="block rounded-lg px-3 py-2 text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600"
                    >
                        {{ $item->title }}
                    </a>
                @endif
            @endforeach

            <div class="mt-4 border-t border-gray-100 pt-4">
                <button
                    class="w-full rounded-lg bg-blue-600 px-5 py-3 text-base font-semibold text-white shadow-lg shadow-blue-200"
                >
                    Login
                </button>
            </div>
        </div>
    </div>
</header>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('mobile-menu-btn')
        const menu = document.getElementById('mobile-menu')
        const menuIcon = document.getElementById('menu-icon')
        const closeIcon = document.getElementById('close-icon')

        // Toggle mobile menu
        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden')
            menuIcon.classList.toggle('hidden')
            closeIcon.classList.toggle('hidden')
        })

        // Handle mobile dropdown toggles
        document
            .querySelectorAll('.mobile-dropdown-toggle')
            .forEach((toggle) => {
                toggle.addEventListener('click', () => {
                    const dropdownId = toggle.getAttribute('data-dropdown')
                    const dropdown = document.getElementById(dropdownId)
                    const icon = toggle.querySelector('.mobile-dropdown-icon')

                    // Toggle visibility
                    dropdown.classList.toggle('hidden')

                    // Rotate icon
                    icon.classList.toggle('rotate-180')
                })
            })
    })
</script>
