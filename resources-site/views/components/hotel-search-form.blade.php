<div class="relative bg-slate-900 min-h-[350px] flex items-center pt-24 pb-16 overflow-hidden isolate">
    <!-- Background Image -->
    <div class="absolute inset-0 -z-20">
        <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80"
            alt="Hotel Background" class="w-full h-full object-cover">
    </div>

    <!-- Gradient Overlay -->
    <div class="absolute inset-0 -z-10 bg-linear-to-t from-slate-900/90 via-slate-900/50 to-slate-900/30"></div>

    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center text-white">
        <h1 class="text-4xl md:text-6xl font-extrabold mb-4 tracking-tight drop-shadow-xl">
            Find Your <span class="text-transparent bg-clip-text bg-linear-to-r from-blue-300 to-cyan-200">Perfect
                Stay</span>
        </h1>
        <p class="text-lg md:text-xl text-blue-50 max-w-2xl mx-auto font-light leading-relaxed drop-shadow-md mb-10">
            Discover amazing hotels at the best prices
        </p>

        <!-- Hotel Search Form -->
        <form action="{{ route('site.hotel.search') }}" method="GET"
            class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-6 md:p-8 max-w-5xl mx-auto text-left ring-1 ring-white/20">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-5">
                <div class="space-y-1.5 relative md:col-span-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Destination</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i
                                class="ri-map-pin-line text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input name="destination" value="{{ request('destination') }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium text-slate-700"
                            placeholder="City, hotel, or area">
                    </div>
                </div>
                <div class="space-y-1.5 relative">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Check-in</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i
                                class="ri-calendar-line text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input type="date" name="check_in" value="{{ request('check_in', date('Y-m-d')) }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium text-slate-700">
                    </div>
                </div>
                <div class="space-y-1.5 relative">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Check-out</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i
                                class="ri-calendar-check-line text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input type="date" name="check_out"
                            value="{{ request('check_out', date('Y-m-d', strtotime('+1 day'))) }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium text-slate-700">
                    </div>
                </div>
                <div class="space-y-1.5 relative">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Guests</label>
                    <select name="adults"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium text-slate-700">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ request('adults', 2) == $i ? 'selected' : '' }}>{{ $i }}
                                {{ $i == 1 ? 'Adult' : 'Adults' }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end pt-4 border-t border-slate-100/50">
                <button type="submit"
                    class="w-full md:w-auto bg-linear-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-3 px-8 rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                    <i class="ri-search-line"></i>
                    Search Hotels
                </button>
            </div>
        </form>
    </div>
</div>