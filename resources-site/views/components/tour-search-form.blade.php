<div class="relative bg-slate-900 min-h-[350px] flex items-center pt-24 pb-16 overflow-hidden isolate">
    <!-- Background Image -->
    <div class="absolute inset-0 -z-20">
        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80"
            alt="Tour Background" class="w-full h-full object-cover">
    </div>

    <!-- Gradient Overlay -->
    <div class="absolute inset-0 -z-10 bg-linear-to-t from-slate-900/90 via-slate-900/50 to-slate-900/30"></div>

    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center text-white">
        <h1 class="text-4xl md:text-6xl font-extrabold mb-4 tracking-tight drop-shadow-xl">
            Explore <span class="text-transparent bg-clip-text bg-linear-to-r from-orange-300 to-amber-200">The
                World</span>
        </h1>
        <p class="text-lg md:text-xl text-orange-50 max-w-2xl mx-auto font-light leading-relaxed drop-shadow-md mb-10">
            Unforgettable journeys, curated just for you.
        </p>

        <!-- Tour Search Form -->
        <form action="{{ route('site.tour.search') }}" method="GET"
            class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-6 md:p-8 max-w-5xl mx-auto text-left ring-1 ring-white/20">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div class="space-y-1.5 relative md:col-span-4 lg:col-span-1">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Search</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i
                                class="ri-search-line text-slate-400 group-focus-within:text-orange-500 transition-colors"></i>
                        </div>
                        <input name="q" value="{{ request('q') }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium text-slate-700"
                            placeholder="Where do you want to go?">
                    </div>
                </div>
                <div class="space-y-1.5 relative">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Category</label>
                    <select name="category"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium text-slate-700">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5 relative">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Destination</label>
                    <select name="destination"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium text-slate-700">
                        <option value="">All Destinations</option>
                        @foreach($destinations as $destination)
                            <option value="{{ $destination->id }}" {{ request('destination') == $destination->id ? 'selected' : '' }}>{{ $destination->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5 relative">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest pl-1">Duration</label>
                    <select name="duration"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all font-medium text-slate-700">
                        <option value="">Any Duration</option>
                        <option value="1-3" {{ request('duration') == '1-3' ? 'selected' : '' }}>1-3 Days</option>
                        <option value="4-7" {{ request('duration') == '4-7' ? 'selected' : '' }}>4-7 Days</option>
                        <option value="7+" {{ request('duration') == '7+' ? 'selected' : '' }}>7+ Days</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end pt-4 border-t border-slate-100/50">
                <button type="submit"
                    class="w-full md:w-auto bg-linear-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white py-3 px-8 rounded-xl font-bold shadow-lg shadow-orange-500/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                    <i class="ri-search-line"></i>
                    Search Tours
                </button>
            </div>
        </form>
    </div>
</div>