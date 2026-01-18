@extends('site-layout')

@section('meta-title', 'Book Flight | ' . config('app.name'))

@section('content')
    <section class="py-8 bg-slate-50 min-h-screen">
        <div class="container max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-slate-800 mb-8">Complete Your Booking</h1>

            <form action="{{ route('flight.storeBooking') }}" method="POST">
                @csrf

                <!-- Hidden booking data fields -->
                <input type="hidden" name="trip_type" value="{{ $search['trip_type'] ?? 'one_way' }}">
                <input type="hidden" name="departure_date"
                    value="{{ $search['departure_date'] ?? now()->addDays(7)->toDateString() }}">
                <input type="hidden" name="return_date" value="{{ $search['return_date'] ?? '' }}">
                <input type="hidden" name="cabin_class" value="{{ $search['cabin_class'] ?? 'economy' }}">
                <input type="hidden" name="adult_count" value="{{ $search['adults'] ?? 1 }}">
                <input type="hidden" name="child_count" value="{{ $search['children'] ?? 0 }}">
                <input type="hidden" name="infant_count" value="{{ $search['infants'] ?? 0 }}">
                <input type="hidden" name="offer_id" value="{{ $offerId ?? '' }}">

                <!-- Flight Summary -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h2 class="font-bold text-lg text-slate-800 mb-4">Flight Summary</h2>
                    <div class="flex items-center justify-between py-4 border-b border-slate-100">
                        <div>
                            <p class="font-bold text-slate-800">{{ strtoupper($search['origin'] ?? 'DAC') }} →
                                {{ strtoupper($search['destination'] ?? 'DXB') }}</p>
                            <p class="text-sm text-slate-500">{{ $search['departure_date'] ?? 'Select Date' }} •
                                {{ ucfirst($search['cabin_class'] ?? 'Economy') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-slate-500">
                                {{ ($search['adults'] ?? 1) + ($search['children'] ?? 0) + ($search['infants'] ?? 0) }}
                                Passenger(s)</p>
                        </div>
                    </div>
                </div>

                <!-- Passenger Information -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h2 class="font-bold text-lg text-slate-800 mb-4">Passenger Information</h2>

                    <div class="space-y-6">
                        <!-- Passenger 1 -->
                        <div class="p-4 border border-slate-200 rounded-lg">
                            <h3 class="font-semibold text-slate-700 mb-4">Passenger 1 (Adult)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm text-slate-600 mb-1">Title</label>
                                    <select name="passengers[0][title]"
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                                        <option value="Mr">Mr</option>
                                        <option value="Mrs">Mrs</option>
                                        <option value="Ms">Ms</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm text-slate-600 mb-1">First Name</label>
                                    <input type="text" name="passengers[0][first_name]"
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg" required>
                                </div>
                                <div>
                                    <label class="block text-sm text-slate-600 mb-1">Last Name</label>
                                    <input type="text" name="passengers[0][last_name]"
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg" required>
                                </div>
                                <div>
                                    <label class="block text-sm text-slate-600 mb-1">Date of Birth</label>
                                    <input type="date" name="passengers[0][date_of_birth]"
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg" required>
                                </div>
                                <div>
                                    <label class="block text-sm text-slate-600 mb-1">Passport Number</label>
                                    <input type="text" name="passengers[0][passport_number]"
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm text-slate-600 mb-1">Passport Expiry</label>
                                    <input type="date" name="passengers[0][passport_expiry]"
                                        class="w-full px-4 py-2 border border-slate-200 rounded-lg">
                                </div>
                            </div>
                            <input type="hidden" name="passengers[0][passenger_type]" value="adult">
                            <input type="hidden" name="passengers[0][is_lead_passenger]" value="1">
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h2 class="font-bold text-lg text-slate-800 mb-4">Contact Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-slate-600 mb-1">Email</label>
                            <input type="email" name="contact_email"
                                class="w-full px-4 py-2 border border-slate-200 rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-sm text-slate-600 mb-1">Phone</label>
                            <input type="tel" name="contact_phone"
                                class="w-full px-4 py-2 border border-slate-200 rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-sm text-slate-600 mb-1">First Name</label>
                            <input type="text" name="contact_first_name"
                                class="w-full px-4 py-2 border border-slate-200 rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-sm text-slate-600 mb-1">Last Name</label>
                            <input type="text" name="contact_last_name"
                                class="w-full px-4 py-2 border border-slate-200 rounded-lg" required>
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-lg transition-colors shadow-lg">
                    Proceed to Payment
                </button>
            </form>
        </div>
    </section>
@endsection