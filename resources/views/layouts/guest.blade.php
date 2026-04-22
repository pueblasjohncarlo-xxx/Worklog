<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WorkLog') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 500 500' fill='none'><path d='M150 150L200 350L250 250L300 350L350 150' stroke='%234F46E5' stroke-width='60' stroke-linecap='round' stroke-linejoin='round'/><path d='M350 150V350H450' stroke='%230D9488' stroke-width='60' stroke-linecap='round' stroke-linejoin='round'/><path d='M180 300L220 220L250 320L280 260L320 300' stroke='%23312E81' stroke-width='15' stroke-linecap='round' stroke-linejoin='round'/><path d='M320 120L360 160L440 80' stroke='%2384CC16' stroke-width='40' stroke-linecap='round' stroke-linejoin='round'/></svg>">
        <link rel="shortcut icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 500 500' fill='none'><path d='M150 150L200 350L250 250L300 350L350 150' stroke='%234F46E5' stroke-width='60' stroke-linecap='round' stroke-linejoin='round'/><path d='M350 150V350H450' stroke='%230D9488' stroke-width='60' stroke-linecap='round' stroke-linejoin='round'/><path d='M180 300L220 220L250 320L280 260L320 300' stroke='%23312E81' stroke-width='15' stroke-linecap='round' stroke-linejoin='round'/><path d='M320 120L360 160L440 80' stroke='%2384CC16' stroke-width='40' stroke-linecap='round' stroke-linejoin='round'/></svg>">

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
        <style>
            body.worklog-ui-hardening .guest-auth-card {
                background: rgba(15, 23, 42, 0.72);
                border: 1px solid rgba(129, 140, 248, 0.22);
                box-shadow: 0 18px 40px rgba(15, 23, 42, 0.35);
                backdrop-filter: blur(16px);
                color: #f8fafc;
            }

            body.worklog-ui-hardening .guest-auth-title {
                color: #ffffff;
                font-weight: 800;
                letter-spacing: -0.01em;
            }

            body.worklog-ui-hardening .guest-auth-body,
            body.worklog-ui-hardening .guest-auth-card .text-gray-600,
            body.worklog-ui-hardening .guest-auth-card .text-gray-500,
            body.worklog-ui-hardening .guest-auth-card .text-gray-400 {
                color: #dbe4ff !important;
                opacity: 1 !important;
            }

            body.worklog-ui-hardening .guest-auth-card label,
            body.worklog-ui-hardening .guest-auth-card .text-sm,
            body.worklog-ui-hardening .guest-auth-card .text-xs {
                color: #e5e7eb;
            }

            body.worklog-ui-hardening .guest-auth-card input,
            body.worklog-ui-hardening .guest-auth-card select,
            body.worklog-ui-hardening .guest-auth-card textarea {
                background: rgba(2, 6, 23, 0.58) !important;
                border: 1px solid rgba(148, 163, 184, 0.28) !important;
                color: #f8fafc !important;
            }

            body.worklog-ui-hardening .guest-auth-card input::placeholder,
            body.worklog-ui-hardening .guest-auth-card textarea::placeholder {
                color: #cbd5e1 !important;
                opacity: 1 !important;
            }

            body.worklog-ui-hardening .guest-auth-card a {
                color: #c7d2fe;
            }

            body.worklog-ui-hardening .guest-auth-card a:hover {
                color: #ffffff !important;
            }

            body.worklog-ui-hardening .guest-auth-card .text-red-300,
            body.worklog-ui-hardening .guest-auth-card .text-rose-600 {
                color: #fda4af !important;
            }

            body.worklog-ui-hardening .guest-auth-card .text-emerald-300,
            body.worklog-ui-hardening .guest-auth-card .text-emerald-600,
            body.worklog-ui-hardening .guest-auth-card .text-green-600,
            body.worklog-ui-hardening .guest-auth-card .text-green-400 {
                color: #86efac !important;
            }
        </style>

    </head>
    <body class="worklog-ui-hardening font-sans antialiased bg-gradient-to-br from-purple-900 via-indigo-900 to-black text-gray-100">
        <div class="min-h-screen flex flex-col items-center justify-center px-4">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </div>
        <x-loading-screen />
    </body>
</html>
