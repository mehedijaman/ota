@extends('site-layout')

@section('meta-title', 'My Bookings | ' . config('app.name'))

@section('content')
    <section class="py-12 bg-slate-50 min-h-screen">
        <div class="container max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-slate-800 mb-8">My Flight Bookings</h1>

            @if($bookings->count())
                <div class="space-y-4">
                    @foreach($bookings as $booking)
                        <a href="{{ route('flight.viewBooking', $booking->uuid) }}"
                            class="block bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                            <div class="p-6">
                                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span
                                                class="font-mono text-sm bg-blue-100 text-blue-700 px-2 py-1 rounded">{{ $booking->booking_number }}</span>
                                            <span class="text-xs px-2 py-1 rounded-full 
                                                        {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-700' : '' }}
                                                        {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                        {{ $booking->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </div>
                                        @if($booking->segments->first())
                                            <p class="font-bold text-slate-800">
                                                {{ $booking->segments->first()->departureAirport?->city }} →
                                                {{ $booking->segments->last()->arrivalAirport?->city }}
                                            </p>
                                            <p class="text-sm text-slate-500">
                                                {{ \Carbon\Carbon::parse($booking->segments->first()->departure_datetime)->format('D, d M Y') }}
                                                • {{ $booking->total_passengers }} Passenger(s)
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-bold text-orange-500">৳{{ number_format($booking->total_amount) }}
                                        </p>
                                        @if($booking->ticketing_status === 'ticketed')
                                            <span class="text-xs text-green-600"><i class="ri-ticket-line"></i> E-Ticket Issued</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($bookings->hasPages())
                    <div class="mt-8">
                        {{ $bookings->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-16 bg-white rounded-2xl">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="ri-flight-takeoff-line text-4xl text-slate-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-700 mb-2">No bookings yet</h3>
                    <p class="text-slate-500 mb-8">Start planning your next trip!</p>
                    <a href="{{ route('flight.home') }}"
                        class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                        <i class="ri-search-line"></i> Search Flights
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection