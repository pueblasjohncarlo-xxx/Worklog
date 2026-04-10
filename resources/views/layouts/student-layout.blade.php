<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WorkLog') }} - Student</title>

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
    <body class="font-sans antialiased bg-gradient-to-br from-purple-900 via-indigo-950 to-black text-gray-100 min-h-screen bg-fixed shimmer-bg">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen flex">
            <!-- Sidebar -->
            @include('layouts.student-sidebar')

            <!-- Main Content -->
            <div class="flex-1 ml-0 md:ml-64 min-h-screen flex flex-col">
                <!-- Top Header -->
                <header class="bg-black/50 backdrop-blur-md border-b border-indigo-500/30 shadow-lg sticky top-0 z-30">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center gap-4">
                        <!-- Mobile Menu Button -->
                        <button @click="$dispatch('toggle-sidebar')" class="md:hidden text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-900 flex-shrink-0">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <h2 class="font-black text-xl sm:text-2xl text-white leading-tight drop-shadow-md tracking-tight flex-1 sm:flex-none">
                            {{ $header ?? 'Student Dashboard' }}
                        </h2>
                        
                        <div class="flex items-center gap-2 sm:gap-4">
                            <x-notification-bell />
                            @include('layouts.partials.language-switcher-compact')
                            <div class="hidden sm:flex flex-col items-end">
                                <span class="text-[10px] text-indigo-300 uppercase font-black tracking-[0.2em]">Student</span>
                                <span class="text-sm sm:text-base font-black text-white">{{ Auth::user()->name }}</span>
                            </div>
                            <div class="relative">
                                @if (Auth::user()->profile_photo_path)
                                    <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}" class="h-10 w-10 rounded-full object-cover border-2 border-indigo-500 shadow-md">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-sm border-2 border-indigo-400 shadow-md">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-green-500 border-2 border-black"></div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="p-4 sm:p-6 flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>
        <script>
            // Handle sidebar toggle for mobile
            const studentSidebar = document.querySelector('[x-data*="mobileOpen"]');
            document.addEventListener('toggle-sidebar', () => {
                if (studentSidebar && studentSidebar.__x) {
                    studentSidebar.__x.unobservedData.mobileOpen = !studentSidebar.__x.unobservedData.mobileOpen;
                }
            });
        </script>
    </body>
</html>
