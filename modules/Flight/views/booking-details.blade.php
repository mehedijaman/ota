@extends('site-layout')

@section('meta-title', 'Booking Details | ' . config('app.name'))

@section('content')
    <section class="py-12 bg-slate-50 min-h-screen">
        <div class="container max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <a href="{{ route('flight.myBookings') }}"
                        class="text-blue-600 hover:text-blue-700 text-sm mb-2 inline-block">
                        <i class="ri-arrow-left-line"></i> Back to My Bookings
                    </a>
                    <h1 class="text-2xl font-bold text-slate-800">Booking {{ $booking->booking_number }}</h1>
                </div>
                <span class="text-sm px-3 py-1 rounded-full 
                        {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $booking->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                    {{ ucfirst($booking->status) }}
                </span>
            </div>

            <!-- Flight Details -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="font-bold text-lg text-slate-800 mb-4">Flight Details</h2>

                @foreach($booking->segments as $segment)
                    <div class="flex items-center justify-between py-4 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center">
                                <span class="font-bold text-slate-600">{{ $segment->airline?->iata_code }}</span>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">
                                    {{ $segment->departureAirport?->city }} ({{ $segment->departureAirport?->iata_code }}) →
                                    {{ $segment->arrivalAirport?->city }} ({{ $segment->arrivalAirport?->iata_code }})
                                </p>
                                <p class="text-sm text-slate-500">
                                    {{ \Carbon\Carbon::parse($segment->departure_datetime)->format('l, d M Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-slate-700">
                                {{ \Carbon\Carbon::parse($segment->departure_datetime)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($segment->arrival_datetime)->format('H:i') }}
                            </p>
                            <p class="text-sm text-slate-500">{{ $segment->airline?->iata_code }}{{ $segment->flight_number }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Passengers -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="font-bold text-lg text-slate-800 mb-4">Passengers</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="text-left py-2 text-slate-500 font-medium">Name</th>
                                <th class="text-left py-2 text-slate-500 font-medium">Type</th>
                                <th class="text-left py-2 text-slate-500 font-medium">Ticket No.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->passengers as $passenger)
                                <tr class="border-b border-slate-50">
                                    <td class="py-3 text-slate-800">{{ $passenger->title }} {{ $passenger->first_name }}
                                        {{ $passenger->last_name }}</td>
                                    <td class="py-3 text-slate-600">{{ ucfirst($passenger->passenger_type) }}</td>
                                    <td class="py-3">
                                        @if($passenger->ticket_number)
                                            <span
                                                class="font-mono bg-green-100 text-green-700 px-2 py-1 rounded text-xs">{{ $passenger->ticket_number }}</span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="font-bold text-lg text-slate-800 mb-4">Payment Summary</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Base Fare</span>
                        <span class="text-slate-800">৳{{ number_format($booking->base_fare_total) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Taxes & Fees</span>
                        <span class="text-slate-800">৳{{ number_format($booking->tax_total) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-slate-100 pt-2 mt-2 font-bold text-lg">
                        <span class="text-slate-800">Total</span>
                        <span class="text-orange-500">৳{{ number_format($booking->total_amount) }}</span>
                    </div>
                    <div class="flex justify-between text-green-600">
                        <span>Paid</span>
                        <span>৳{{ number_format($booking->amount_paid) }}</span>
                    </div>
                    @if($booking->amount_due > 0)
                        <div class="flex justify-between text-red-600">
                            <span>Due</span>
                            <span>৳{{ number_format($booking->amount_due) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment History -->
            @if($booking->payments->count())
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="font-bold text-lg text-slate-800 mb-4">Payment History</h2>
                    <div class="space-y-3">
                        @foreach($booking->payments as $payment)
                            <div class="flex items-center justify-between py-2 border-b border-slate-50">
                                <div>
                                    <p class="text-sm text-slate-800">{{ ucfirst($payment->method) }}</p>
                                    <p class="text-xs text-slate-500">{{ $payment->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                <span class="font-bold {{ $payment->type === 'refund' ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $payment->type === 'refund' ? '-' : '+' }}৳{{ number_format($payment->amount) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection