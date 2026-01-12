<div class="bg-slate-900 pt-28 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-white mb-6">Search Tours</h1>
        <!-- Filters -->
        <form action="{{ route('site.tour.search') }}" method="GET" class="bg-white rounded-xl p-6 shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1 block">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Tour name or keyword..."
                        class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                </div>
                <div>
                    <label
                        class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1 block">Category</label>
                    <select name="category"
                        class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label
                        class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1 block">Destination</label>
                    <select name="destination"
                        class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                        <option value="">All Destinations</option>
                        @foreach($destinations as $destination)
                            <option value="{{ $destination->id }}" {{ request('destination') == $destination->id ? 'selected' : '' }}>{{ $destination->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label
                        class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1 block">Duration</label>
                    <select name="duration"
                        class="w-full border border-slate-200 rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500">
                        <option value="">Any Duration</option>
                        <option value="1-3" {{ request('duration') == '1-3' ? 'selected' : '' }}>1-3 Days</option>
                        <option value="4-7" {{ request('duration') == '4-7' ? 'selected' : '' }}>4-7 Days</option>
                        <option value="7+" {{ request('duration') == '7+' ? 'selected' : '' }}>7+ Days</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end mt-4 pt-4 border-t border-slate-100">
                <button type="submit"
                    class="bg-orange-600 hover:bg-orange-700 text-white py-2 px-6 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <i class="ri-search-line"></i>
                    Search
                </button>
            </div>
        </form>
    </div>
</div>