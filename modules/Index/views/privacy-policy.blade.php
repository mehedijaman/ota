@extends('site-layout')

@section('meta-title', 'Privacy Policy')

@section('meta-description', 'Privacy Policy')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@extends('site-layout')

@section('meta-title', 'Privacy Policy | Travelly')

@section('meta-description', 'Learn how Travelly collects, uses, and protects your personal data.')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <!-- Legal Hero -->
    <div class="bg-slate-50 border-b border-slate-200">
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-12">
            <h1 class="text-3xl md:text-5xl font-bold text-slate-900 mb-4">Privacy Policy</h1>
            <p class="text-slate-500 text-lg">Last Updated: January 01, 2026</p>
        </div>
    </div>

    <!-- Content -->
    <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <!-- Sidebar -->
            <div class="lg:col-span-3 hidden lg:block">
                <div class="sticky top-32 space-y-1">
                    <a href="#info-collection" class="block py-2 px-4 text-blue-600 font-bold bg-blue-50 rounded-lg">1. Information Collection</a>
                    <a href="#use-data" class="block py-2 px-4 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition-colors">2. Use of Data</a>
                    <a href="#data-sharing" class="block py-2 px-4 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition-colors">3. Data Sharing</a>
                    <a href="#security" class="block py-2 px-4 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition-colors">4. Security</a>
                    <a href="#cookies" class="block py-2 px-4 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition-colors">5. Cookies</a>
                </div>
            </div>

            <!-- Main Text -->
            <div class="lg:col-span-9 prose prose-slate max-w-none prose-headings:font-bold prose-headings:text-slate-800 prose-p:text-slate-600 prose-li:text-slate-600">
                <div id="info-collection" class="scroll-mt-32 mb-12">
                    <h2>1. Information We Collect</h2>
                    <p>We collect information that you provide directly to us when you create an account, make a booking, or contact customer support.</p>
                    <ul>
                        <li>Personal Identification: Name, Email, Phone Number, Passport details.</li>
                        <li>Payment Information: Credit Card numbers (processed securely by third-party gateways).</li>
                    </ul>
                </div>

                <div id="use-data" class="scroll-mt-32 mb-12">
                    <h2>2. How We Use Your Data</h2>
                    <p>We use the collected data to process your bookings, communicate with you about your trip, send booking confirmations, and improve our services.</p>
                </div>

                <div id="data-sharing" class="scroll-mt-32 mb-12">
                    <h2>3. Data Sharing</h2>
                    <p>We share your information with airlines, hotels, and tour operators necessary to fulfill your booking. We do not sell your personal data to third parties for marketing purposes.</p>
                </div>

                <div id="security" class="scroll-mt-32 mb-12">
                    <h2>4. Security</h2>
                    <p>We implement robust security measures, including SSL encryption, to protect your personal information from unauthorized access, alteration, or disclosure.</p>
                </div>

                 <div id="cookies" class="scroll-mt-32 mb-12">
                    <h2>5. Cookies & Tracking</h2>
                    <p>We use cookies to enhance your browsing experience, analyze site traffic, and personalize content. You can manage your cookie preferences through your browser settings.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
