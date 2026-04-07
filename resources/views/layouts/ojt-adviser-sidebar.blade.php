<div class="fixed top-0 left-0 h-full w-64 bg-black text-gray-100 flex flex-col z-10 shadow-xl overflow-y-auto glow-border-right">
    <!-- Header/Logo -->
    <div class="p-6 flex items-center justify-center border-b border-indigo-500/30 bg-black/20">
        <x-wl-sidebar-logo />
    </div>

    <!-- Navigation -->
    <nav class="flex-1 mt-6 px-4 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('ojt_adviser.dashboard') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('ojt_adviser.dashboard') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <!-- Students Monitoring -->
        <a href="{{ route('ojt_adviser.students') }}" 
           class="flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('ojt_adviser.students*') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-300' }}">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="font-medium">OJT Students</span>
            </div>
            @php $studentCount = \App\Models\Assignment::where('ojt_adviser_id', auth()->id())->count(); @endphp
            <span class="bg-gray-800 text-gray-400 text-xs px-2 py-0.5 rounded-full">{{ $studentCount }}</span>
        </a>

        <a href="{{ route('ojt_adviser.accomplishment-reports') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('ojt_adviser.accomplishment-reports') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="font-medium">Accomplishment Reports</span>
        </a>

        <!-- Performance Evaluation -->
        <a href="{{ route('ojt_adviser.evaluations') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('ojt_adviser.evaluations*') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="font-medium">Evaluations</span>
        </a>

        <!-- Reports -->
        <a href="{{ route('ojt_adviser.reports') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('ojt_adviser.reports*') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="font-medium">Reports</span>
        </a>

        <!-- Messages -->
        <a href="{{ route('messages.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('messages.*') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <span class="font-medium">Messages</span>
        </a>
    </nav>

    <!-- Footer -->
    <div class="p-4 border-t border-indigo-500/30">
        <a href="{{ route('profile.edit') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors hover:bg-gray-900 text-gray-300 mb-1">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="font-medium">My Profile</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors hover:bg-red-900/30 text-red-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="font-medium">Log Out</span>
            </button>
        </form>
    </div>
</div>
