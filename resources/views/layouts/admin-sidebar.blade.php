<div x-data="{ mobileOpen: false }" 
    @window:resize="mobileOpen = window.innerWidth < 768 ? false : mobileOpen" 
    class="fixed top-0 left-0 h-screen w-64 bg-black text-gray-100 flex flex-col z-40 shadow-xl overflow-y-auto glow-border-right transition-all duration-300"
    :class="{'-translate-x-full md:translate-x-0': !mobileOpen && window.innerWidth < 768, 'translate-x-0': mobileOpen || window.innerWidth >= 768}">
    <!-- Header/Logo -->
    <div class="p-4 sm:p-6 flex items-center justify-between border-b border-indigo-500/30 bg-black/20 flex-shrink-0">
        <x-wl-sidebar-logo />
        <button @click="mobileOpen = false" class="md:hidden text-gray-200 hover:text-white p-1 rounded transition">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 mt-4 sm:mt-6 px-3 sm:px-4 space-y-1 overflow-y-auto">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center gap-3 px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg transition-colors text-sm sm:text-base {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-200' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span class="font-medium hidden sm:inline">Admin Dashboard</span>
            <span class="font-medium sm:hidden text-xs">Dashboard</span>
        </a>

        <!-- User Management -->
        <a href="{{ route('admin.users.index') }}" 
           class="flex items-center justify-between px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg transition-colors text-sm sm:text-base {{ request()->routeIs('admin.users.*') ? 'bg-indigo-900 text-white' : 'hover:bg-gray-900 text-gray-200' }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="font-medium hidden sm:inline">User Management</span>
                <span class="font-medium sm:hidden text-xs">Users</span>
            </div>
        </a>

        <!-- Invitation Links -->
        <a href="{{ route('invitations.index') }}"
           class="flex items-center gap-3 px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg transition-colors text-sm sm:text-base {{ request()->routeIs('invitations.*') ? 'bg-indigo-900 text-white shadow-lg' : 'hover:bg-gray-900 text-gray-200' }}">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8m-8 4h5m5 7H6a2 2 0 01-2-2V7a2 2 0 012-2h2l2-2h4l2 2h2a2 2 0 012 2v12a2 2 0 01-2 2z" />
            </svg>
            <span class="font-medium hidden sm:inline">Invitation Links</span>
            <span class="font-medium sm:hidden text-xs">Invites</span>
        </a>

        <!-- Messages -->
        <a href="{{ route('messages.index') }}" 
           class="flex items-center gap-3 px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg transition-colors text-sm sm:text-base {{ request()->routeIs('messages.*') ? 'bg-indigo-900 text-white' : 'hover:bg-gray-900 text-gray-200' }}">
            <div class="relative flex-shrink-0">
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
            <span class="font-medium hidden sm:inline">Messages</span>
            <span class="font-medium sm:hidden text-xs">Messages</span>
        </a>
    </nav>

    <!-- Footer / Profile -->
    <div class="p-3 sm:p-4 border-t border-indigo-900 text-gray-200 flex-shrink-0">
        <a href="{{ route('profile.edit') }}" 
           class="flex items-center gap-3 px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg hover:bg-gray-900 transition-colors text-sm sm:text-base text-gray-200">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="font-medium hidden sm:inline">My Profile</span>
            <span class="font-medium sm:hidden text-xs">Profile</span>
        </a>
        <a href="{{ route('logout.get') }}" class="mt-2 w-full flex items-center gap-3 px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg hover:bg-red-900/20 text-red-300 transition-colors text-sm sm:text-base">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="font-medium hidden sm:inline">Log Out</span>
            <span class="font-medium sm:hidden text-xs">Exit</span>
        </a>
    </div>

    <!-- Mobile Overlay -->
    <div x-show="mobileOpen" class="fixed inset-0 bg-black/50 z-30 md:hidden" @click="mobileOpen = false"></div>
</div>
</div>
