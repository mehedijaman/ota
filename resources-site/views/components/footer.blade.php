<!-- FOOTER -->
<footer class="border-t border-slate-800 bg-slate-900 py-16 text-slate-300">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-5">
            <!-- Brand -->
            <div class="sm:col-span-2 lg:col-span-2">
                <a href="{{ route('site.index') }}" class="group mb-6 flex items-center gap-3">
                    @if ($siteSettings->show_logo && $siteSettings->logo_url)
                        <img src="{{ $siteSettings->logo_url }}" alt="{{ $siteSettings->site_name ?? 'Logo' }}"
                            class="h-10 w-auto object-contain brightness-0 invert transition-transform duration-300 group-hover:scale-105" />
                    @else
                        <div
                            class="relative flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl text-white shadow-lg shadow-blue-500/30 transition-transform duration-300 group-hover:scale-105">
                            <div
                                class="bg-linear-to-tr absolute inset-0 from-blue-600 to-indigo-600 transition-colors group-hover:from-blue-500 group-hover:to-indigo-500">
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="relative z-10 h-5 w-5 -rotate-45 transform"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                            </svg>
                        </div>
                    @endif

                    @if ($siteSettings->show_name)
                        <span class="text-2xl font-bold tracking-tight text-white">
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
                        <a href="{{ $siteSettings->twitter }}" target="_blank" rel="noopener noreferrer"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 transition-colors hover:bg-blue-600 hover:text-white"
                            aria-label="Twitter">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                        </a>
                    @endif

                    @if ($siteSettings->facebook)
                        <a href="{{ $siteSettings->facebook }}" target="_blank" rel="noopener noreferrer"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 transition-colors hover:bg-blue-600 hover:text-white"
                            aria-label="Facebook">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                    @endif

                    @if ($siteSettings->instagram)
                        <a href="{{ $siteSettings->instagram }}" target="_blank" rel="noopener noreferrer"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 transition-colors hover:bg-blue-600 hover:text-white"
                            aria-label="Instagram">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.072 3.252.158 5.85 2.961 6.008 6.215.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-2.755 6.066-5.922 6.215-1.265.058-1.645.069-4.85.069-3.204 0-3.584-.012-4.85-.069-3.26-.159-5.852-2.965-6.008-6.215-.059-1.265-.069-1.645-.069-4.849 0-3.204.012-3.584.069-4.849.149-3.226 2.755-6.07 5.922-6.215 1.265-.057 1.645-.069 4.85-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                    @endif

                    @if ($siteSettings->youtube)
                        <a href="{{ $siteSettings->youtube }}" target="_blank" rel="noopener noreferrer"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 transition-colors hover:bg-red-600 hover:text-white"
                            aria-label="YouTube">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                            </svg>
                        </a>
                    @endif

                    @if ($siteSettings->linkedin)
                        <a href="{{ $siteSettings->linkedin }}" target="_blank" rel="noopener noreferrer"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 transition-colors hover:bg-blue-700 hover:text-white"
                            aria-label="LinkedIn">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                            </svg>
                        </a>
                    @endif

                    @if ($siteSettings->pinterest)
                        <a href="{{ $siteSettings->pinterest }}" target="_blank" rel="noopener noreferrer"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 transition-colors hover:bg-red-600 hover:text-white"
                            aria-label="Pinterest">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 0c-6.627 0-12 5.372-12 12 0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738.098.119.112.224.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146 1.124.347 2.317.535 3.554.535 6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Contact Info -->
            <div>
                <h4 class="mb-6 font-bold text-white">Contact Info</h4>
                <ul class="space-y-4 text-sm">
                    @if (!empty($siteSettings->emails))
                        @foreach ($siteSettings->emails as $email)
                            @if (!empty($email['email']))
                                <li class="flex items-start gap-3">
                                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-blue-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        @if (!empty($email['label']))
                                            <span class="block text-xs text-slate-500">
                                                {{ $email['label'] }}
                                            </span>
                                        @endif

                                        <a href="mailto:{{ $email['email'] }}"
                                            class="break-all transition-colors hover:text-blue-400">
                                            {{ $email['email'] }}
                                        </a>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    @endif

                    @if (!empty($siteSettings->phones))
                        @foreach ($siteSettings->phones as $phone)
                            @if (!empty($phone['number']))
                                <li class="flex items-center gap-3">
                                    <svg class="h-5 w-5 shrink-0 text-emerald-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <div>
                                        @if (!empty($phone['label']))
                                            <span class="block text-xs text-slate-500">
                                                {{ $phone['label'] }}
                                            </span>
                                        @endif

                                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone['number']) }}"
                                            class="font-medium text-emerald-400 transition-colors hover:text-emerald-300">
                                            {{ $phone['number'] }}
                                        </a>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    @endif

                    @if ($siteSettings->whatsapp)
                        <li class="flex items-center gap-3">
                            <svg class="h-5 w-5 shrink-0 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSettings->whatsapp) }}"
                                target="_blank" rel="noopener noreferrer" class="transition-colors hover:text-green-400">
                                WhatsApp
                            </a>
                        </li>
                    @endif

                    @if ($siteSettings->telegram)
                        <li class="flex items-center gap-3">
                            <svg class="h-5 w-5 shrink-0 text-sky-400" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z" />
                            </svg>
                            <a href="{{ str_starts_with($siteSettings->telegram, '@') ? 'https://t.me/' . ltrim($siteSettings->telegram, '@') : 'https://t.me/' . preg_replace('/[^0-9]/', '', $siteSettings->telegram) }}"
                                target="_blank" rel="noopener noreferrer" class="transition-colors hover:text-sky-400">
                                Telegram
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Addresses -->
            @if (!empty($siteSettings->addresses))
                <div class="sm:col-span-2 lg:col-span-2">
                    <h4 class="mb-6 font-bold text-white">Our Locations</h4>
                    <ul class="space-y-5 text-sm">
                        @foreach ($siteSettings->addresses as $address)
                            @if (!empty($address['address']))
                                <li class="flex items-start gap-3">
                                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-orange-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                    </svg>
                                    <div>
                                        @if (!empty($address['label']))
                                            <span class="mb-1 block font-medium text-white">
                                                {{ $address['label'] }}
                                            </span>
                                        @endif

                                        <span class="block text-slate-400">
                                            {{ $address['address'] }}
                                        </span>
                                        @if (!empty($address['google_map']))
                                            <a href="{{ $address['google_map'] }}" target="_blank" rel="noopener noreferrer"
                                                class="mt-1 inline-flex items-center gap-1.5 text-orange-400 transition-colors hover:text-orange-300">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                View on Map
                                            </a>
                                        @endif
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Bottom Bar -->
        <div class="flex flex-col items-center justify-between gap-4 border-t border-slate-800 pt-8 md:flex-row">
            <p class="text-sm text-slate-500">
                © {{ date('Y') }}
                {{ $siteSettings->site_name ?? 'TriptonicBD' }}. All rights
                reserved.
            </p>
            @if ($footerMenu && $footerMenu->count() > 0)
                <nav class="flex flex-wrap justify-center gap-6 text-sm">
                    @foreach ($footerMenu as $item)
                        <a href="{{ $item->url }}" 
                           class="text-slate-400 transition-colors hover:text-white"
                           @if($item->target) target="{{ $item->target }}" @endif>
                            {{ $item->title }}
                        </a>
                    @endforeach
                </nav>
            @endif
        </div>
    </div>
</footer>