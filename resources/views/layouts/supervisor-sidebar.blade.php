<div x-cloak class="app-sidebar fixed inset-y-0 left-0 h-full w-64 bg-black text-gray-100 flex flex-col z-40 shadow-xl overflow-y-auto glow-border-right transform transition-transform duration-300 md:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    <!-- Header/Logo -->
    <div class="p-6 flex items-center justify-between border-b border-indigo-500/30 bg-black/20">
        <x-wl-sidebar-logo />
        <button @click="sidebarOpen = false" class="md:hidden text-gray-200 hover:text-white">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 mt-6 px-4 space-y-1" @click="if (window.innerWidth < 768) sidebarOpen = false">
        <!-- Dashboard -->
        <a href="{{ route('supervisor.dashboard') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('supervisor.dashboard') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-200' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2-0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2-0 012-2h2a2 2 0 012 2v2a2 2-0 01-2 2h-2a2 2-0 01-2-2V6zM4 16a2 2-0 012-2h2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2-0 012-2h2a2 2 0 01-2 2h-2a2 2-0 01-2-2v-2z" />
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <a href="{{ route('supervisor.accomplishment-reports') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('supervisor.accomplishment-reports') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-200' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="font-medium">Accomplishment Reports</span>
        </a>

        <!-- Team Overview -->
        <a href="{{ route('supervisor.team.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('supervisor.team.*') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-200' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="font-medium">Team Overview</span>
        </a>

        <!-- Task History & Tracking -->
        <a href="{{ route('supervisor.tasks.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('supervisor.tasks.*') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-200' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m-6 0a2 2 0 002 2h2a2 2 0 002-2m-6 0a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <span class="font-medium">Tasks</span>
        </a>

        <!-- Announcements -->
        <a href="{{ route('supervisor.announcements.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('supervisor.announcements.*') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-200' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
            </svg>
            <span class="font-medium">Announcements</span>
        </a>

        <!-- Performance Evaluations -->
        <a href="{{ route('supervisor.evaluations.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('supervisor.evaluations.*') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-200' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="font-medium">Performance Evaluation</span>
        </a>

        <!-- Settings -->
        <a href="{{ route('settings.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors hover:bg-gray-900 text-gray-200">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="font-medium">Settings</span>
        </a>
    </nav>

    <!-- Footer / Profile -->
    <div class="p-4 border-t border-indigo-900 text-gray-200 mt-auto">
        <a href="{{ route('logout.get') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-red-900/20 text-red-300 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="font-medium">Log Out</span>
        </a>
    </div>
</div>
