<div x-data="{ open: false }" class="relative mr-4">
    <button @click="open = !open" class="relative p-1 rounded-full text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
        <span class="sr-only">View notifications</span>
        <div class="relative">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            @php
                $unreadCount = auth()->user()->unreadNotifications->count();
            @endphp
            @if($unreadCount > 0)
                <span class="absolute -top-1.5 -right-1.5 flex items-center justify-center min-w-[1.25rem] h-5 px-1 text-[10px] font-bold text-white bg-red-600 rounded-full border-2 border-black">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @endif
        </div>
    </button>

    <div x-show="open" 
         @click.away="open = false"
         class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
         role="menu" 
         aria-orientation="vertical" 
         aria-labelledby="user-menu-button" 
         tabindex="-1"
         style="display: none;">
        
        <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
            <span class="text-sm font-semibold text-gray-700">Notifications</span>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800">Mark all read</button>
                </form>
            @endif
        </div>

        <div class="max-h-64 overflow-y-auto">
            @forelse(auth()->user()->unreadNotifications as $notification)
                <a href="{{ route('notifications.read', $notification->id) }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0">
                    <p class="text-sm font-medium text-gray-900">{{ $notification->data['title'] ?? $notification->data['subject'] ?? 'Notification' }}</p>
                    <p class="text-xs text-gray-500 mt-1 truncate">{{ $notification->data['content'] ?? '' }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                </a>
            @empty
                <div class="px-4 py-3 text-center text-sm text-gray-500">
                    No new notifications
                </div>
            @endforelse
        </div>
    </div>
</div>