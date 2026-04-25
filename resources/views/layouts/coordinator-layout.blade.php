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

        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            /* Select2 Dark Mode Customization */
            .select2-container--default .select2-selection--multiple {
                background-color: #1f2937;
                border-color: #374151;
                border-radius: 0.375rem;
                min-height: 2.5rem;
            }
            .select2-container--default .select2-selection--multiple .select2-selection__choice {
                background-color: #4f46e5;
                border: none;
                color: white;
                border-radius: 0.25rem;
                padding: 2px 8px;
                margin-top: 6px;
            }
            .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
                color: white;
                margin-right: 5px;
                border-right: 1px solid rgba(255,255,255,0.3);
            }
            .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
                background-color: #4338ca;
                color: white;
            }
            .select2-dropdown {
                background-color: #1f2937;
                border-color: #374151;
                color: #e5e7eb;
            }
            .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
                background-color: #4f46e5;
                color: white;
            }
            .select2-container--default .select2-results__option--selectable {
                color: #e5e7eb;
            }
            .select2-container--default .select2-search--inline .select2-search__field {
                color: #e5e7eb;
                font-family: inherit;
            }
            .select2-container--default .select2-results__group {
                color: #9ca3af;
                font-weight: bold;
                padding: 6px 6px;
                background-color: #111827;
            }

            .top-header-title-scope,
            .top-header-title-scope * {
                color: #ffffff !important;
            }

            .top-header-title-link {
                display: block;
                border-radius: 0.5rem;
                padding: 0.125rem 0.375rem;
                transition: background-color 150ms ease, color 150ms ease;
            }

            .top-header-title-link:hover,
            .top-header-title-link:focus-visible {
                background-color: rgba(255, 255, 255, 0.08);
                color: #c7d2fe !important;
                outline: none;
            }
        </style>
        @include('layouts.partials.ui-visibility-hardening')
    </head>
    <body class="worklog-ui-hardening font-sans antialiased bg-gradient-to-br from-purple-900 via-indigo-950 to-black text-gray-100 min-h-screen bg-fixed shimmer-bg overflow-x-hidden">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen flex flex-col md:flex-row">
            @include('layouts.coordinator-sidebar')

            <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/60 backdrop-blur-sm md:hidden"></div>

            <div class="flex-1 w-full md:ml-64 min-h-screen flex flex-col">
                <header class="bg-black/50 backdrop-blur-md border-b border-indigo-500/30 shadow-lg sticky top-0 z-30 w-full">
                    <div class="w-full px-3 sm:px-4 lg:px-6 py-3 sm:py-4 flex justify-between items-center gap-2 sm:gap-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-400 hover:text-white p-1.5 sm:p-2 rounded-lg hover:bg-gray-900 flex-shrink-0">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <div class="flex-1 min-w-0">
                            <a href="{{ url()->current() }}" class="top-header-title-link top-header-title-scope font-semibold text-lg sm:text-xl leading-tight drop-shadow-md truncate" title="Refresh this page">
                                {{ $header ?? 'Coordinator Dashboard' }}
                            </a>
                        </div>

                        <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                            <x-notification-bell />
                            @include('layouts.partials.language-switcher-compact')
                            <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-sm font-semibold text-white border border-white/10">
                                    Logout
                                </button>
                            </form>
                            <div class="hidden sm:flex flex-col items-end">
                                <span class="text-xs text-indigo-300 uppercase font-bold tracking-wider">Coordinator</span>
                                <span class="text-sm font-semibold text-white" data-user-name-id="{{ Auth::id() }}">{{ Auth::user()->name }}</span>
                            </div>
                            <a href="{{ route('settings.index') }}" class="relative flex-shrink-0 rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400" title="Open settings">
                                <img src="{{ Auth::user()->profile_photo_url }}" data-avatar-user-id="{{ Auth::id() }}" alt="{{ Auth::user()->name }}" class="h-8 sm:h-10 w-8 sm:w-10 rounded-full object-cover border-2 border-indigo-500 shadow-md">
                                <div class="absolute bottom-0 right-0 h-2 sm:h-3 w-2 sm:w-3 rounded-full bg-green-500 border-2 border-black"></div>
                            </a>
                        </div>
                    </div>
                </header>

                <main class="flex-1 p-3 sm:p-4 lg:p-6 overflow-y-auto overflow-x-hidden">
                    <div class="max-w-7xl mx-auto w-full">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        <!-- Select2 JS -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        @include('layouts.partials.avatar-sync')
        @stack('scripts')
    </body>
</html>
