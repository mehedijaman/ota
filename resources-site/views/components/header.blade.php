<!-- NAVBAR -->
<header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <a href="{{ route('site.index') }}" class="shrink-0 flex items-center gap-3 group">
                <div class="relative w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30 overflow-hidden group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-linear-to-tr from-blue-600 to-indigo-600 group-hover:from-blue-500 group-hover:to-indigo-500 transition-colors"></div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="relative z-10 h-5 w-5 transform -rotate-45" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                    </svg>
                </div>
                <span class="font-bold text-2xl text-slate-800 tracking-tight">Travelly</span>
            </a>

            <!-- Desktop Nav -->
            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ route('site.index') }}" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">Home</a>
                <a href="{{ route('site.flight.index') }}" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">Flight</a>
                <a href="{{ route('site.hotel.index') }}" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">Hotel</a>
                <a href="{{ route('site.tour.index') }}" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">Tour</a>
                <a href="{{ route('site.visa.index') }}" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">Visa</a>
                
                <div class="relative group">
                    <button class="flex items-center gap-1 text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors outline-none">
                        More
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Dropdown -->
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right z-50">
                        <div class="py-2">
                            <a href="{{ route('site.bill.index') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-blue-600">Bill</a>
                            <a href="{{ route('site.medical.index') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-blue-600">Medical</a>
                            <a href="https://12go.asia/en" target="_blank" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-blue-600">Transport</a>
                            <a href="{{ route('site.faq.index') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-blue-600">FAQ</a>
                            <a href="{{ route('site.about') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-blue-600">About</a>
                            <a href="{{ route('site.why-us') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-blue-600">Why Us</a>
                            <a href="{{ route('site.blog.index') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-blue-600">Blogs</a>
                        </div>
                    </div>
                </div>

                <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-full text-sm font-semibold shadow-lg shadow-blue-200 transition-all hover:scale-105 active:scale-95">
                    Login
                </button>
            </nav>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none">
                <svg id="menu-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg id="close-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-gray-100">
        <div class="px-4 pt-2 pb-6 space-y-1">
            <a href="{{ route('site.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600">Home</a>
            <a href="{{ route('site.flight.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600">Flight</a>
            <a href="{{ route('site.hotel.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600">Hotel</a>
            <a href="{{ route('site.tour.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600">Tour</a>
            <a href="{{ route('site.visa.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600">Visa</a>
            <a href="{{ route('site.bill.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600">Bill</a>
            <a href="{{ route('site.medical.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600">Medical</a>
            <a href="https://12go.asia/en" target="_blank" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600">Transport</a>
            <a href="{{ route('site.faq.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600">FAQ</a>
            <a href="{{ route('site.about') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600">About</a>
            <a href="{{ route('site.why-us') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600">Why Us</a>
            <a href="{{ route('site.blog.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-blue-600">Blogs</a>
            <div class="pt-4 mt-4 border-t border-gray-100">
                <button class="w-full bg-blue-600 text-white px-5 py-3 rounded-lg text-base font-semibold shadow-lg shadow-blue-200">
                    Login
                </button>
            </div>
        </div>
    </div>
</header>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        });
    });
</script>