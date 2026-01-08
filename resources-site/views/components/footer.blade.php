<!-- FOOTER -->
<footer class="border-t border-slate-800 bg-slate-900 py-16 text-slate-300">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div
            class="mb-12 grid grid-cols-1 gap-12 md:grid-cols-2 lg:grid-cols-5"
        >
            <!-- Brand -->
            <div class="lg:col-span-2">
                <a
                    href="{{ route('site.index') }}"
                    class="group mb-6 flex items-center gap-3"
                >
                    @if ($siteSettings->show_logo && $siteSettings->logo_url)
                        <img
                            src="{{ $siteSettings->logo_url }}"
                            alt="{{ $siteSettings->site_name ?? 'Logo' }}"
                            class="h-10 w-auto object-contain brightness-0 invert transition-transform duration-300 group-hover:scale-105"
                        />
                    @else
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
                    @endif

                    @if ($siteSettings->show_name)
                        <span
                            class="text-2xl font-bold tracking-tight text-white"
                        >
                            {{ $siteSettings->site_name ?? 'TriptonicBD' }}
                        </span>
                    @endif
                </a>

                <p class="mb-6 max-w-sm leading-relaxed text-slate-400">
                    {{ $siteSettings->description }}
                </p>

                {{-- Social Media Links --}}
                <div class="flex gap-4">
                    @if ($siteSettings->twitter)
                        <a
                            href="{{ $siteSettings->twitter }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 transition-colors hover:bg-blue-600 hover:text-white"
                            aria-label="Twitter"
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
                    @endif

                    @if ($siteSettings->facebook)
                        <a
                            href="{{ $siteSettings->facebook }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 transition-colors hover:bg-blue-600 hover:text-white"
                            aria-label="Facebook"
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
                    @endif

                    @if ($siteSettings->instagram)
                        <a
                            href="{{ $siteSettings->instagram }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 transition-colors hover:bg-blue-600 hover:text-white"
                            aria-label="Instagram"
                        >
                            <svg
                                class="h-5 w-5"
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
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 transition-colors hover:bg-red-600 hover:text-white"
                            aria-label="YouTube"
                        >
                            <svg
                                class="h-5 w-5"
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
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 transition-colors hover:bg-blue-700 hover:text-white"
                            aria-label="LinkedIn"
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
                    @endif

                    @if ($siteSettings->pinterest)
                        <a
                            href="{{ $siteSettings->pinterest }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 transition-colors hover:bg-red-600 hover:text-white"
                            aria-label="Pinterest"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M12 0c-6.627 0-12 5.372-12 12 0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738.098.119.112.224.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146 1.124.347 2.317.535 3.554.535 6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"
                                />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Links -->
            <div>
                <h4 class="mb-6 font-bold text-white">Company</h4>
                <ul class="space-y-4">
                    <li>
                        <a
                            href="/about"
                            class="transition-colors hover:text-blue-400"
                        >
                            About Us
                        </a>
                    </li>
                    <li>
                        <a
                            href="#"
                            class="transition-colors hover:text-blue-400"
                        >
                            Careers
                        </a>
                    </li>
                    <li>
                        <a
                            href="#"
                            class="transition-colors hover:text-blue-400"
                        >
                            Blog
                        </a>
                    </li>
                    <li>
                        <a
                            href="#"
                            class="transition-colors hover:text-blue-400"
                        >
                            Partners
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="mb-6 font-bold text-white">Support</h4>
                <ul class="space-y-4">
                    <li>
                        <a
                            href="{{ route('site.faq.index') }}"
                            class="transition-colors hover:text-blue-400"
                        >
                            Help Center
                        </a>
                    </li>
                    <li>
                        <a
                            href="/terms-and-conditions"
                            class="transition-colors hover:text-blue-400"
                        >
                            Terms of Service
                        </a>
                    </li>
                    <li>
                        <a
                            href="/privacy-policy"
                            class="transition-colors hover:text-blue-400"
                        >
                            Privacy Policy
                        </a>
                    </li>
                    <li>
                        <a
                            href="#"
                            class="transition-colors hover:text-blue-400"
                        >
                            Contact Us
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="mb-6 font-bold text-white">Top Destinations</h4>
                <ul class="space-y-4">
                    <li>
                        <a
                            href="#"
                            class="transition-colors hover:text-blue-400"
                        >
                            Maldives
                        </a>
                    </li>
                    <li>
                        <a
                            href="#"
                            class="transition-colors hover:text-blue-400"
                        >
                            Thailand
                        </a>
                    </li>
                    <li>
                        <a
                            href="#"
                            class="transition-colors hover:text-blue-400"
                        >
                            Dubai
                        </a>
                    </li>
                    <li>
                        <a
                            href="#"
                            class="transition-colors hover:text-blue-400"
                        >
                            Cox's Bazar
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div
            class="flex flex-col items-center justify-between gap-4 border-t border-slate-800 pt-8 md:flex-row"
        >
            <p class="text-sm text-slate-500">
                © {{ date('Y') }}
                {{ $siteSettings->site_name ?? 'TriptonicBD' }}. All rights
                reserved.
            </p>
            <div class="flex gap-6 text-sm">
                <a
                    href="#"
                    class="text-slate-500 transition-colors hover:text-white"
                >
                    Sitemap
                </a>
                <a
                    href="#"
                    class="text-slate-500 transition-colors hover:text-white"
                >
                    Cookies
                </a>
            </div>
        </div>
    </div>
</footer>
