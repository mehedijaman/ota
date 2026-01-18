@extends('site-layout')

@section('meta-title', 'Booking Confirmed | ' . config('app.name'))

@section('content')
    <section class="py-16 bg-slate-50 min-h-screen">
        <div class="container max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Success Message -->
            <div class="text-center mb-12">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="ri-check-line text-4xl text-green-600"></i>
                </div>
                <h1 class="text-3xl font-bold text-slate-800 mb-2">Booking Confirmed!</h1>
                <p class="text-slate-600">Your booking reference is <span
                        class="font-bold text-blue-600">{{ $booking->booking_number }}</span></p>
            </div>

            <!-- Booking Details -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="font-bold text-lg text-slate-800 mb-6 flex items-center gap-2">
                    <i class="ri-flight-takeoff-line text-blue-600"></i> Flight Details
                </h2>

                @foreach($booking->segments as $segment)
                    <div class="flex items-center justify-between py-4 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center">
                                <span class="font-bold text-slate-600">{{ $segment->airline?->iata_code }}</span>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">{{ $segment->departureAirport?->city }} →
                                    {{ $segment->arrivalAirport?->city }}</p>
                                <p class="text-sm text-slate-500">
                                    {{ \Carbon\Carbon::parse($segment->departure_datetime)->format('D, d M Y') }} •
                                    {{ \Carbon\Carbon::parse($segment->departure_datetime)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($segment->arrival_datetime)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-slate-700">
                                {{ $segment->airline?->iata_code }}{{ $segment->flight_number }}</p>
                            <p class="text-sm text-slate-500">{{ ucfirst($segment->cabin_class) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Passengers -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2">
                    <i class="ri-user-line text-blue-600"></i> Passengers
                </h2>
                <div class="space-y-3">
                    @foreach($booking->passengers as $passenger)
                        <div class="flex items-center justify-between py-2">
                            <p class="text-slate-700">
                                {{ $passenger->title }} {{ $passenger->first_name }} {{ $passenger->last_name }}
                                <span class="text-xs text-slate-500">({{ ucfirst($passenger->passenger_type) }})</span>
                            </p>
                            @if($passenger->ticket_number)
                                <span
                                    class="text-xs font-mono bg-green-100 text-green-700 px-2 py-1 rounded">{{ $passenger->ticket_number }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('flight.myBookings') }}"
                    class="flex-1 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-center transition-colors">
                    View My Bookings
                </a>
                <a href="{{ route('flight.home') }}"
                    class="flex-1 py-4 border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold rounded-lg text-center transition-colors">
                    Search More Flights
                </a>
            </div>
        </div>
    </section>
@endsection