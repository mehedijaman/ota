<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('meta-title', config('app.name', 'Modular'))</title>

    <meta name="description" content="@yield('meta-description', 'Laravel and Vue Development With Joy')" />

    <!-- Improves Lighthouse score (related to Best Practices)-->
    @if (app()->environment('production'))
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    @endif

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="/favicon.svg" />

    <!-- Remix Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    @vite(['resources-site/css/site.css', 'resources-site/js/index-app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @yield('headEndScripts')
</head>

<body>
    <div id="app">
        <x-header></x-header>

        @yield('content')
    </div>

    <div id="app-footer">
        <x-footer></x-footer>
    </div>

    @yield('bodyEndScripts')
    @stack('scripts')
</body>

</html>