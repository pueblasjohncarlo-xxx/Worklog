<div class="fixed top-0 left-0 h-full w-64 bg-black text-gray-100 flex flex-col z-10 shadow-xl overflow-y-auto glow-border-right">
    <!-- Header/Logo -->
    <div class="p-6 flex items-center justify-center border-b border-indigo-500/30 bg-black/20">
        <x-wl-sidebar-logo />
    </div>

    <!-- Navigation -->
    <nav class="flex-1 mt-6 px-4 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('student.dashboard') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('student.dashboard') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2-0 012-2h2a2 2-0 012 2v2a2 2-0 01-2 2H6a2 2-0 01-2-2V6zM14 6a2 2-0 012-2h2a2 2-0 012 2v2a2 2-0 01-2 2h-2a2 2-0 01-2-2V6zM14 6a2 2-0 012-2h2a2 2-0 012 2v2a2 2-0 01-2 2h-2a2 2-0 01-2-2V6zM4 16a2 2-0 012-2h2a2 2-0 01-2 2H6a2 2-0 01-2-2v-2zM14 16a2 2-0 012-2h2a2 2-0 01-2 2h-2a2 2-0 01-2-2v-2z" />
            </svg>
            <span class="font-medium text-lg">Dashboard</span>
        </a>

        <!-- Leave Request -->
        <a href="{{ route('student.leaves.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('student.leaves.*') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="font-medium text-lg">Leave Request</span>
        </a>

        <!-- My Tasks -->
        <a href="{{ route('student.tasks.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('student.tasks.*') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
            <span class="font-medium text-lg">My Tasks</span>
        </a>

        <!-- Accomplishment Report -->
        <a href="{{ route('student.journal.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('student.journal.*') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2-0 01-2-2V5a2 2-0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="font-medium text-lg">Accomplishment Report</span>
        </a>

        <!-- Hours Log -->
        <a href="{{ route('student.reports.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->url() == route('student.reports.index') && !request()->has('view') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-medium text-lg">Hours Log</span>
        </a>

        <!-- Announcements -->
        <a href="{{ route('student.announcements.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('student.announcements.*') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
            </svg>
            <span class="font-medium text-lg">Announcements</span>
        </a>

        <!-- Reports -->
        <a href="{{ route('student.reports.index', ['view' => 'reports']) }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->get('view') == 'reports' ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="font-medium text-lg">Reports</span>
        </a>

        <!-- Messages -->
        <a href="{{ route('messages.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('messages.*') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-300' }}">
            <div class="relative">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                @php
                    $unreadMessages = Auth::user()->unreadNotifications->where('type', 'App\Notifications\NewMessageNotification')->count();
                @endphp
                @if($unreadMessages > 0)
                    <span class="absolute -top-2 -right-2 flex items-center justify-center min-w-[1.25rem] h-5 px-1 text-[10px] font-bold text-white bg-red-600 rounded-full border-2 border-black">
                        {{ $unreadMessages > 99 ? '99+' : $unreadMessages }}
                    </span>
                @endif
            </div>
            <span class="font-medium text-lg">Messages</span>
        </a>
    </nav>

    <!-- Footer / Profile -->
    <div class="p-4 border-t border-indigo-900 text-gray-300 bg-black/40">
        <div class="px-4 py-3 mb-2">
            <p class="text-[12px] uppercase tracking-[0.2em] text-gray-500 font-black">Signed in as</p>
            <p class="text-base font-black text-indigo-400 truncate mt-1">{{ Auth::user()->name }}</p>
        </div>
        <a href="{{ route('profile.edit') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('profile.edit') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="text-base font-bold">My Profile</span>
        </a>
        <a href="{{ route('logout.get') }}" class="mt-2 w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-red-900/20 text-red-400 transition-colors text-left group">
                <svg class="h-5 w-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="text-base font-black uppercase tracking-wider">Log Out</span>
        </a>
    </div>
</div>
