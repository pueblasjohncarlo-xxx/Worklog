<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WorkLog') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 500 500' fill='none'><path d='M150 150L200 350L250 250L300 350L350 150' stroke='%234F46E5' stroke-width='60' stroke-linecap='round' stroke-linejoin='round'/><path d='M350 150V350H450' stroke='%230D9488' stroke-width='60' stroke-linecap='round' stroke-linejoin='round'/><path d='M180 300L220 220L250 320L280 260L320 300' stroke='%23312E81' stroke-width='15' stroke-linecap='round' stroke-linejoin='round'/><path d='M320 120L360 160L440 80' stroke='%2384CC16' stroke-width='40' stroke-linecap='round' stroke-linejoin='round'/></svg>">
        
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
        
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-purple-900 via-indigo-950 to-black text-gray-100 min-h-screen bg-fixed overflow-x-hidden">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen flex flex-col md:flex-row">
            <!-- Sidebar -->
            @include('layouts.ojt-adviser-sidebar')

            <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/60 backdrop-blur-sm md:hidden"></div>

            <!-- Main Content -->
            <div class="flex-1 w-full md:ml-64 min-h-screen flex flex-col">
                <!-- Top Header -->
                <header class="bg-black/50 backdrop-blur-md border-b border-indigo-500/30 shadow-lg sticky top-0 z-30 w-full">
                    <div class="w-full px-3 sm:px-4 lg:px-6 py-3 sm:py-4 flex justify-between items-center gap-2 sm:gap-4">
                        <!-- Mobile Menu Button -->
                        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-400 hover:text-white p-1.5 sm:p-2 rounded-lg hover:bg-gray-900 flex-shrink-0">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <h2 class="font-semibold text-lg sm:text-xl text-white leading-tight drop-shadow-md truncate">
                            {{ $header ?? 'OJT Adviser Dashboard' }}
                        </h2>
                        
                        <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                            <x-notification-bell />
                            @include('layouts.partials.language-switcher-compact')
                            <div class="hidden sm:flex flex-col items-end">
                                <span class="text-xs text-indigo-300 uppercase font-bold tracking-wider">OJT Adviser</span>
                                <span class="text-sm font-semibold text-white">{{ Auth::user()->name }}</span>
                            </div>
                            <div class="relative flex-shrink-0">
                                <img src="{{ Auth::user()->profile_photo_url }}" data-avatar-user-id="{{ Auth::id() }}" alt="{{ Auth::user()->name }}" class="h-8 sm:h-10 w-8 sm:w-10 rounded-full object-cover border-2 border-indigo-500 shadow-md">
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 p-3 sm:p-4 lg:p-6 overflow-y-auto overflow-x-hidden">
                    <div class="max-w-7xl mx-auto w-full">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        @include('layouts.partials.avatar-sync')
    </body>
</html>
