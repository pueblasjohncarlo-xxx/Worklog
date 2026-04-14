<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WorkLog') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23a855f7'/><text x='50' y='65' font-family='Arial' font-size='60' font-weight='bold' fill='white' text-anchor='middle'>W</text></svg>">
        <link rel="shortcut icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23a855f7'/><text x='50' y='65' font-family='Arial' font-size='60' font-weight='bold' fill='white' text-anchor='middle'>W</text></svg>">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
        @endif
        @include('layouts.partials.ui-visibility-hardening')
        
    </head>
    <body class="worklog-ui-hardening font-sans antialiased bg-gradient-to-br from-purple-900 via-indigo-950 to-black text-gray-100 min-h-screen">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-black/50 backdrop-blur-md border-b border-indigo-500/30 shadow-lg">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <a href="{{ url()->current() }}" class="top-header-title-link top-header-title-scope" title="Refresh this page">
                            {{ $header }}
                        </a>
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>
        <x-loading-screen />
        @stack('scripts')
    </body>
</html>
