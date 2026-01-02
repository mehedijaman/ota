@php
$airlines = [
    ['name' => 'Biman Bangladesh', 'domain' => 'biman-airlines.com'],
    ['name' => 'US-Bangla Airlines', 'domain' => 'usbair.com'],
    ['name' => 'Novoair', 'domain' => 'flynovoair.com'],
    ['name' => 'Air Astra', 'domain' => 'airastra.com'],
    ['name' => 'Indigo', 'domain' => 'goindigo.in'],
    ['name' => 'Air India', 'domain' => 'airindia.com'],
    ['name' => 'Vistara', 'domain' => 'airvistara.com'],
    ['name' => 'SpiceJet', 'domain' => 'spicejet.com'],
    ['name' => 'SriLankan Airlines', 'domain' => 'srilankan.com'],
    ['name' => 'Nepal Airlines', 'domain' => 'nepalairlines.com.np'],
    ['name' => 'Druk Air', 'domain' => 'drukair.com.bt'],
    ['name' => 'Maldivian', 'domain' => 'maldivian.aero'],
];
@endphp

<!-- TOP AIRLINES -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-10">
        <h2 class="text-3xl font-bold text-slate-800">Top Airlines</h2>
        <p class="text-slate-500 mt-2">Fly with the best airlines in South Asia</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
        @foreach($airlines as $airline)
        <div class="group bg-white p-6 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col items-center justify-center gap-4 cursor-pointer border border-slate-100 hover:border-blue-100 h-40">
            <div class="h-16 w-16 p-2 rounded-full bg-slate-50 group-hover:bg-white flex items-center justify-center transition-all duration-300">
                <img src="https://www.google.com/s2/favicons?sz=128&domain={{ $airline['domain'] }}" alt="{{ $airline['name'] }}" class="w-full h-full object-contain transition-all duration-300 transform group-hover:scale-110">
            </div>
            <span class="text-sm font-bold text-slate-500 group-hover:text-blue-600 transition-colors text-center leading-tight">{{ $airline['name'] }}</span>
        </div>
        @endforeach
    </div>
</section>