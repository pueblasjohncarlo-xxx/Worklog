<div x-cloak class="app-sidebar fixed inset-y-0 left-0 h-full w-64 bg-black text-gray-100 flex flex-col z-40 shadow-xl overflow-y-auto glow-border-right transform transition-transform duration-300 md:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    <!-- Header/Logo -->
    <div class="p-6 flex items-center justify-between border-b border-indigo-500/30 bg-black/20">
        <x-wl-sidebar-logo />
        <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 mt-6 px-4 space-y-1" @click="if (window.innerWidth < 768) sidebarOpen = false">
        <!-- Dashboard -->
        <a href="{{ route('coordinator.dashboard') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('coordinator.dashboard') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <!-- Performance Evaluation -->
        <a href="{{ route('coordinator.evaluations.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('coordinator.evaluations.*') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="font-medium">Performance Evaluation</span>
        </a>

        <!-- Student Overview -->
        <a href="{{ route('coordinator.student-overview') }}" 
           class="flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('coordinator.student-overview') ? 'bg-indigo-900 text-white' : 'hover:bg-gray-900 text-gray-300' }}">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="font-medium">OJT Student Overview</span>
            </div>
            @php $studentCount = \App\Models\User::where('role', \App\Models\User::ROLE_STUDENT)->count(); @endphp
            <span class="bg-gray-800 text-gray-400 text-xs px-2 py-0.5 rounded-full">{{ $studentCount }}</span>
        </a>

        <!-- OJT Advisory -->
        <a href="{{ route('coordinator.adviser-overview') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('coordinator.adviser-overview') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="font-medium">OJT Advisory</span>
        </a>

        <!-- Registration Approvals -->
        <a href="{{ route('coordinator.registrations.pending') }}"
           class="flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('coordinator.registrations.*') ? 'bg-indigo-900 text-white' : 'hover:bg-gray-900 text-gray-300' }}">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">Registration Approvals</span>
            </div>
            @php
                $pendingQuery = \App\Models\User::whereIn('role', [\App\Models\User::ROLE_STUDENT, \App\Models\User::ROLE_SUPERVISOR, \App\Models\User::ROLE_OJT_ADVISER])
                    ->where('has_requested_account', true);

                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'status')) {
                    $pendingQuery->where('status', 'pending');
                } else {
                    $pendingQuery->where('is_approved', false);
                }

                $pendingRegistrations = $pendingQuery->count();
            @endphp
            @if($pendingRegistrations > 0)
                <span class="bg-orange-600 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingRegistrations }}</span>
            @endif
        </a>

        <!-- Supervisor Overview -->
        <a href="{{ route('coordinator.supervisor-overview') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('coordinator.supervisor-overview') ? 'bg-indigo-900 text-white' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="font-medium">Supervisor Overview</span>
        </a>

        <!-- Announcements (Replaces Assign Task) -->
        <a href="{{ route('coordinator.announcements.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('coordinator.announcements.*') ? 'bg-indigo-900 text-white' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
            </svg>
            <span class="font-medium">Announcements</span>
        </a>

        <!-- Daily Journals -->
        <a href="{{ route('coordinator.daily-journals') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('coordinator.daily-journals') ? 'bg-indigo-900 text-white' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
            </svg>
            <span class="font-medium">Daily Journals</span>
        </a>

        <!-- Accomplishment Reports -->
        <a href="{{ route('coordinator.accomplishment-reports') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('coordinator.accomplishment-reports') ? 'bg-indigo-900 text-white' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="font-medium">Accomplishment Reports</span>
        </a>

        <!-- Compliance Overview -->
        <a href="{{ route('coordinator.compliance-overview') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('coordinator.compliance-overview') ? 'bg-indigo-900 text-white' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span class="font-medium">Compliance Overview</span>
        </a>

        <!-- Deployment Management -->
        <a href="{{ route('coordinator.deployment.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('coordinator.deployment.index') ? 'bg-indigo-900 text-white' : 'hover:bg-gray-900 text-gray-300' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            <span class="font-medium">Deployment Management</span>
        </a>

        <!-- Company Directory -->
        <a href="{{ route('coordinator.companies.index') }}" 
           class="flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('coordinator.companies.index') ? 'bg-indigo-900 text-white' : 'hover:bg-gray-900 text-gray-300' }}">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="font-medium">Company Directory</span>
            </div>
            @php $companyCount = \App\Models\Company::count(); @endphp
            <span class="bg-gray-800 text-gray-400 text-xs px-2 py-0.5 rounded-full">{{ $companyCount }}</span>
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
            <span class="font-medium">Messages</span>
        </a>

    </nav>

    <!-- Footer / Profile -->
    <div class="p-4 border-t border-indigo-900 text-gray-300">
        <a href="{{ route('profile.edit') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-900 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="font-medium">My Profile</span>
        </a>
        <a href="{{ route('logout.get') }}" class="mt-1 w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-red-900/20 text-red-400 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="font-medium">Log Out</span>
        </a>
    </div>
</div>
