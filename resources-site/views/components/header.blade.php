<!-- NAVBAR -->
<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
        <a href="{{ route('site.index') }}">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-600 rounded-full"></div>
                <span class="font-bold text-xl text-blue-600">Travelly</span>
            </div>
        </a>

        <nav class="hidden md:flex gap-6 text-sm font-medium">
            <a href="{{ route('site.index') }}" class="hover:text-blue-600">Home</a>
            <a href="{{ route('site.flight.index') }}" class="hover:text-blue-600">Flight</a>
            <a href="{{ route('site.hotel.index') }}" class="hover:text-blue-600">Hotel</a>
            <a href="{{ route('site.tour.index') }}" class="hover:text-blue-600">Tour</a>
            <a href="{{ route('site.visa.index') }}" class="hover:text-blue-600">Visa</a>
            <a href="{{ route('site.bill.index') }}" class="hover:text-blue-600">Bill</a>
            <a href="{{ route('site.medical.index') }}" class="hover:text-blue-600">Medical</a>
            <a href="https://12go.asia/en" target="_blank" class="hover:text-blue-600">Transport</a>
            <a href="{{ route('site.faq.index') }}" class="hover:text-blue-600">FAQ</a>
            <a href="{{ route('site.about') }}" class="hover:text-blue-600">About</a>
            <a href="{{ route('site.why-us') }}" class="hover:text-blue-600">Why Us</a>
        </nav>

        <button class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm">
            Login
        </button>
    </div>
</header>