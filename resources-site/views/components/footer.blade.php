<!-- FOOTER -->
<footer class="bg-gray-900 text-gray-300 py-12">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-6 text-sm">
        <div>
            <h4 class="font-semibold mb-3">Company</h4>
            <p><a href="{{ route('site.about') }}">About</a></p>
            {{-- <p><a href="{{ route('site.contact') }}">Contact</a></p> --}}
        </div>
        <div>
            <h4 class="font-semibold mb-3">Services</h4>
            <p><a href="{{ route('site.flight.index') }}">Flights</a></p>
            <p><a href="{{ route('site.hotel.index') }}">Hotels</a></p>
        </div>
        <div>
            <h4 class="font-semibold mb-3">Support</h4>
            {{-- <p><a href="{{ route('site.contact') }}">Contact</a></p> --}}
            <p><a href="{{ route('site.faq.index') }}">FAQ</a></p>
        </div>
        <div>
            <h4 class="font-semibold mb-3">Legal</h4>
            <p><a href="{{ route('site.privacy-policy') }}">Privacy Policy</a></p>
            <p><a href="{{ route('site.terms-and-conditions') }}">Terms and Conditions</a></p>
        </div>
    </div>
</footer>