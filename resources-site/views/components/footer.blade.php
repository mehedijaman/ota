<!-- FOOTER -->
<footer class="bg-gradient-to-b from-slate-900 to-slate-950">
    {{-- Contact & Addresses Bar --}}
    <div class="border-b border-slate-800/50 bg-slate-800/30">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Contact Us Column --}}
                <div class="rounded-xl bg-slate-800/50 p-5 backdrop-blur-sm">
                    <h4
                        class="mb-4 flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-white"
                    >
                        <span
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600/20"
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
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                />
                            </svg>
                        </span>
                        Contact Us
                    </h4>
                    <ul class="space-y-2.5 text-sm">
                        @if (! empty($siteSettings->emails))
                            @php
                                $firstEmail = collect($siteSettings->emails)->first();
                            @endphp

                            @if (! empty($firstEmail['email']))
                                <li class="flex items-center gap-2">
                                    <span class="text-slate-500">Email:</span>
                                    <a
                                        href="mailto:{{ $firstEmail['email'] }}"
                                        class="text-blue-400 transition-colors hover:text-blue-300"
                                    >
                                        {{ $firstEmail['email'] }}
                                    </a>
                                </li>
                            @endif
                        @endif

                        @if (! empty($siteSettings->phones))
                            @php
                                $firstPhone = collect($siteSettings->phones)->first();
                            @endphp

                            @if (! empty($firstPhone['number']))
                                <li class="flex items-center gap-2">
                                    <span class="text-slate-500">Hotline:</span>
                                    <a
                                        href="tel:{{ preg_replace('/[^0-9+]/', '', $firstPhone['number']) }}"
                                        class="font-medium text-emerald-400 transition-colors hover:text-emerald-300"
                                    >
                                        {{ $firstPhone['number'] }}
                                    </a>
                                </li>
                            @endif
                        @endif

                        @if ($siteSettings->whatsapp)
                            <li class="flex items-center gap-2">
                                <span class="text-slate-500">WhatsApp:</span>
                                <a
                                    href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSettings->whatsapp) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-green-400 transition-colors hover:text-green-300"
                                >
                                    Message us
                                </a>
                            </li>
                        @endif

                        @if ($siteSettings->telegram)
                            <li class="flex items-center gap-2">
                                <span class="text-slate-500">Telegram:</span>
                                <a
                                    href="{{ str_starts_with($siteSettings->telegram, '@') ? 'https://t.me/' . ltrim($siteSettings->telegram, '@') : 'https://t.me/' . preg_replace('/[^0-9]/', '', $siteSettings->telegram) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-sky-400 transition-colors hover:text-sky-300"
                                >
                                    Message us
                                </a>
                            </li>
                        @endif
                    </ul>
                    {{-- Social Icons --}}
                    <div
                        class="mt-4 flex items-center gap-2 border-t border-slate-700/50 pt-4"
                    >
                        @if ($siteSettings->facebook)
                            <a
                                href="{{ $siteSettings->facebook }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-700/50 text-slate-400 transition-all hover:bg-blue-600 hover:text-white"
                                aria-label="Facebook"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
                                    />
                                </svg>
                            </a>
                        @endif

                        @if ($siteSettings->whatsapp)
                            <a
                                href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSettings->whatsapp) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-700/50 text-slate-400 transition-all hover:bg-green-600 hover:text-white"
                                aria-label="WhatsApp"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"
                                    />
                                </svg>
                            </a>
                        @endif

                        @if ($siteSettings->twitter)
                            <a
                                href="{{ $siteSettings->twitter }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-700/50 text-slate-400 transition-all hover:bg-slate-600 hover:text-white"
                                aria-label="Twitter"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"
                                    />
                                </svg>
                            </a>
                        @endif

                        @if ($siteSettings->instagram)
                            <a
                                href="{{ $siteSettings->instagram }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-700/50 text-slate-400 transition-all hover:bg-gradient-to-br hover:from-purple-600 hover:to-pink-500 hover:text-white"
                                aria-label="Instagram"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M12 2.163c3.204 0 3.584.012 4.85.072 3.252.158 5.85 2.961 6.008 6.215.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-2.755 6.066-5.922 6.215-1.265.058-1.645.069-4.85.069-3.204 0-3.584-.012-4.85-.069-3.26-.159-5.852-2.965-6.008-6.215-.059-1.265-.069-1.645-.069-4.849 0-3.204.012-3.584.069-4.849.149-3.226 2.755-6.07 5.922-6.215 1.265-.057 1.645-.069 4.85-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"
                                    />
                                </svg>
                            </a>
                        @endif

                        @if ($siteSettings->youtube)
                            <a
                                href="{{ $siteSettings->youtube }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-700/50 text-slate-400 transition-all hover:bg-red-600 hover:text-white"
                                aria-label="YouTube"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"
                                    />
                                </svg>
                            </a>
                        @endif

                        @if ($siteSettings->linkedin)
                            <a
                                href="{{ $siteSettings->linkedin }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-700/50 text-slate-400 transition-all hover:bg-blue-700 hover:text-white"
                                aria-label="LinkedIn"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"
                                    />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Address Columns --}}
                @if (! empty($siteSettings->addresses))
                    @foreach ($siteSettings->addresses as $addr)
                        @if (! empty($addr['address']))
                            <div
                                class="rounded-xl bg-slate-800/50 p-5 backdrop-blur-sm"
                            >
                                <h4
                                    class="mb-4 flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-white"
                                >
                                    <span
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-600/20"
                                    >
                                        <svg
                                            class="h-4 w-4 text-orange-400"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"
                                            />
                                        </svg>
                                    </span>
                                    {{ $addr['label'] ?? 'Office' }}
                                </h4>
                                <p
                                    class="text-sm leading-relaxed text-slate-400"
                                >
                                    {!! nl2br(e($addr['address'])) !!}
                                </p>
                                @if (! empty($addr['google_map']))
                                    <a
                                        href="{{ $addr['google_map'] }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="mt-4 inline-flex items-center gap-2 rounded-lg bg-orange-500/10 px-3 py-2 text-sm font-medium text-orange-400 transition-all hover:bg-orange-500 hover:text-white"
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
                                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"
                                            />
                                        </svg>
                                        View Map
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- Main Footer Content --}}
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-12">
                {{-- Brand Section --}}
                <div class="lg:col-span-4">
                    <a
                        href="{{ route('site.index') }}"
                        class="group mb-5 inline-flex items-center gap-3"
                    >
                        @if ($siteSettings->show_logo && $siteSettings->logo_url)
                            <img
                                src="{{ $siteSettings->logo_url }}"
                                alt="{{ $siteSettings->site_name ?? 'Logo' }}"
                                class="h-12 w-auto object-contain brightness-0 invert transition-transform duration-300 group-hover:scale-105"
                            />
                        @else
                            <div
                                class="relative flex h-12 w-12 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/30 transition-transform duration-300 group-hover:scale-105"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6 -rotate-45 transform"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z"
                                    />
                                </svg>
                            </div>
                        @endif
                        @if ($siteSettings->show_name)
                            <span
                                class="text-2xl font-bold tracking-tight text-white"
                            >
                                {{ $siteSettings->site_name ?? 'TriptonicBD' }}
                            </span>
                        @endif
                    </a>
                    <p
                        class="mb-6 max-w-sm text-sm leading-relaxed text-slate-400"
                    >
                        {{ $siteSettings->description }}
                    </p>
                    {{-- Trust Badges --}}
                    <div class="flex flex-wrap items-center gap-3">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-3 py-1.5 text-xs font-medium text-emerald-400"
                        >
                            <svg
                                class="h-3.5 w-3.5"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            Trusted Agency
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-blue-500/10 px-3 py-1.5 text-xs font-medium text-blue-400"
                        >
                            <svg
                                class="h-3.5 w-3.5"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            Secure Booking
                        </span>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div class="lg:col-span-2">
                    <h4
                        class="mb-5 text-sm font-semibold uppercase tracking-wider text-white"
                    >
                        Company
                    </h4>
                    <ul class="space-y-3">
                        <li>
                            <a
                                href="/about"
                                class="group flex items-center gap-2 text-sm text-slate-400 transition-colors hover:text-white"
                            >
                                <span
                                    class="h-1 w-1 rounded-full bg-slate-600 transition-colors group-hover:bg-blue-500"
                                ></span>
                                About Us
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="group flex items-center gap-2 text-sm text-slate-400 transition-colors hover:text-white"
                            >
                                <span
                                    class="h-1 w-1 rounded-full bg-slate-600 transition-colors group-hover:bg-blue-500"
                                ></span>
                                Careers
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="group flex items-center gap-2 text-sm text-slate-400 transition-colors hover:text-white"
                            >
                                <span
                                    class="h-1 w-1 rounded-full bg-slate-600 transition-colors group-hover:bg-blue-500"
                                ></span>
                                Blog
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="group flex items-center gap-2 text-sm text-slate-400 transition-colors hover:text-white"
                            >
                                <span
                                    class="h-1 w-1 rounded-full bg-slate-600 transition-colors group-hover:bg-blue-500"
                                ></span>
                                Partners
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="lg:col-span-2">
                    <h4
                        class="mb-5 text-sm font-semibold uppercase tracking-wider text-white"
                    >
                        Support
                    </h4>
                    <ul class="space-y-3">
                        <li>
                            <a
                                href="{{ route('site.faq.index') }}"
                                class="group flex items-center gap-2 text-sm text-slate-400 transition-colors hover:text-white"
                            >
                                <span
                                    class="h-1 w-1 rounded-full bg-slate-600 transition-colors group-hover:bg-blue-500"
                                ></span>
                                Help Center
                            </a>
                        </li>
                        <li>
                            <a
                                href="/terms-and-conditions"
                                class="group flex items-center gap-2 text-sm text-slate-400 transition-colors hover:text-white"
                            >
                                <span
                                    class="h-1 w-1 rounded-full bg-slate-600 transition-colors group-hover:bg-blue-500"
                                ></span>
                                Terms of Service
                            </a>
                        </li>
                        <li>
                            <a
                                href="/privacy-policy"
                                class="group flex items-center gap-2 text-sm text-slate-400 transition-colors hover:text-white"
                            >
                                <span
                                    class="h-1 w-1 rounded-full bg-slate-600 transition-colors group-hover:bg-blue-500"
                                ></span>
                                Privacy Policy
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="group flex items-center gap-2 text-sm text-slate-400 transition-colors hover:text-white"
                            >
                                <span
                                    class="h-1 w-1 rounded-full bg-slate-600 transition-colors group-hover:bg-blue-500"
                                ></span>
                                Contact Us
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="lg:col-span-2">
                    <h4
                        class="mb-5 text-sm font-semibold uppercase tracking-wider text-white"
                    >
                        Destinations
                    </h4>
                    <ul class="space-y-3">
                        <li>
                            <a
                                href="#"
                                class="group flex items-center gap-2 text-sm text-slate-400 transition-colors hover:text-white"
                            >
                                <span
                                    class="h-1 w-1 rounded-full bg-slate-600 transition-colors group-hover:bg-blue-500"
                                ></span>
                                Maldives
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="group flex items-center gap-2 text-sm text-slate-400 transition-colors hover:text-white"
                            >
                                <span
                                    class="h-1 w-1 rounded-full bg-slate-600 transition-colors group-hover:bg-blue-500"
                                ></span>
                                Thailand
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="group flex items-center gap-2 text-sm text-slate-400 transition-colors hover:text-white"
                            >
                                <span
                                    class="h-1 w-1 rounded-full bg-slate-600 transition-colors group-hover:bg-blue-500"
                                ></span>
                                Dubai
                            </a>
                        </li>
                        <li>
                            <a
                                href="#"
                                class="group flex items-center gap-2 text-sm text-slate-400 transition-colors hover:text-white"
                            >
                                <span
                                    class="h-1 w-1 rounded-full bg-slate-600 transition-colors group-hover:bg-blue-500"
                                ></span>
                                Cox's Bazar
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Newsletter --}}
                <div class="lg:col-span-2">
                    <h4
                        class="mb-5 text-sm font-semibold uppercase tracking-wider text-white"
                    >
                        Stay Updated
                    </h4>
                    <p class="mb-4 text-sm text-slate-400">
                        Get the latest deals and travel tips.
                    </p>
                    <form class="space-y-3">
                        <input
                            type="email"
                            placeholder="Enter your email"
                            class="w-full rounded-lg border border-slate-700 bg-slate-800/50 px-4 py-2.5 text-sm text-white placeholder-slate-500 transition-colors focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        />
                        <button
                            type="submit"
                            class="w-full rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition-all hover:from-blue-500 hover:to-indigo-500 hover:shadow-lg hover:shadow-blue-500/25"
                        >
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Bar --}}
    <div class="border-t border-slate-800">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div
                class="flex flex-col items-center justify-between gap-4 md:flex-row"
            >
                <p class="text-sm text-slate-500">
                    © {{ date('Y') }}
                    {{ $siteSettings->site_name ?? 'TriptonicBD' }}. All rights
                    reserved.
                </p>
                <div
                    class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm"
                >
                    <a
                        href="#"
                        class="text-slate-500 transition-colors hover:text-slate-300"
                    >
                        Sitemap
                    </a>
                    <a
                        href="#"
                        class="text-slate-500 transition-colors hover:text-slate-300"
                    >
                        Cookies
                    </a>
                    <a
                        href="#"
                        class="text-slate-500 transition-colors hover:text-slate-300"
                    >
                        Accessibility
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
