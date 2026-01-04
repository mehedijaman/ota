@extends('site-layout')

@section('meta-title', 'Terms and Conditions | TriptonicBD')

@section('meta-description', 'Read our terms and conditions regarding bookings, payments, cancellations, and user responsibilities.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- Legal Hero -->
    <div class="bg-slate-50 border-b border-slate-200">
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-12">
            <h1 class="text-3xl md:text-5xl font-bold text-slate-900 mb-4">Terms and Conditions</h1>
            <p class="text-slate-500 text-lg">Last Updated: January 01, 2026</p>
        </div>
    </div>

    <!-- Content -->
    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <!-- Sidebar -->
            <div class="lg:col-span-3 hidden lg:block">
                <div class="sticky top-32 space-y-1">
                    <a href="#introduction" class="block py-2 px-4 text-blue-600 font-bold bg-blue-50 rounded-lg">1. Introduction</a>
                    <a href="#bookings" class="block py-2 px-4 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition-colors">2. Bookings & Reservations</a>
                    <a href="#payments" class="block py-2 px-4 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition-colors">3. Payments</a>
                    <a href="#cancellations" class="block py-2 px-4 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition-colors">4. Cancellations</a>
                    <a href="#liability" class="block py-2 px-4 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition-colors">5. Liability</a>
                </div>
            </div>

            <!-- Main Text -->
            <div class="lg:col-span-9 prose prose-slate max-w-none prose-headings:font-bold prose-headings:text-slate-800 prose-p:text-slate-600 prose-li:text-slate-600">
                <div id="introduction" class="scroll-mt-32 mb-12">
                    <h2>1. Introduction</h2>
                    <p>Welcome to TriptonicBD. These Terms and Conditions govern your use of our website and services. By accessing or using our platform, you agree to be bound by these terms.</p>
                </div>

                <div id="bookings" class="scroll-mt-32 mb-12">
                    <h2>2. Bookings & Reservations</h2>
                    <p>All bookings made through our platform are subject to availability and acceptance by the respective service providers (airlines, hotels, tour operators). We act as an intermediary and are not responsible for the acts or omissions of these third-party providers.</p>
                    <ul>
                        <li>You must provide accurate information during the booking process.</li>
                        <li>It is your responsibility to check your booking details and travel documents.</li>
                    </ul>
                </div>

                <div id="payments" class="scroll-mt-32 mb-12">
                    <h2>3. Payments</h2>
                    <p>Prices are displayed in BDT (Bangladeshi Taka) unless otherwise stated. Full payment is required at the time of booking unless a deposit option is available.</p>
                    <p>We accept major credit cards, debit cards, and mobile financial services (Bkash, Nagad).</p>
                </div>

                <div id="cancellations" class="scroll-mt-32 mb-12">
                    <h2>4. Cancellations & Refunds</h2>
                    <p>Cancellation policies vary by service provider. Please review the specific terms associated with your booking before confirming.</p>
                    <p>TriptonicBD service fees are generally non-refundable. Refunds, if applicable, will be processed back to the original payment method within 7-14 business days.</p>
                </div>

                 <div id="liability" class="scroll-mt-32 mb-12">
                    <h2>5. Limitation of Liability</h2>
                    <p>TriptonicBD shall not be liable for any indirect, incidental, special, or consequential damages arising out of or in connection with your use of our services.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
