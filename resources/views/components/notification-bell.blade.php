@php
    $user = auth()->user();
    $initialUnreadCount = $user->unreadNotifications()->count();
    $initialItems = $user->unreadNotifications()
        ->latest()
        ->take(10)
        ->get()
        ->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->data['type'] ?? null,
                'title' => $notification->data['title'] ?? $notification->data['subject'] ?? 'Notification',
                'content' => $notification->data['content'] ?? '',
                'url' => $notification->data['url'] ?? null,
                'read_url' => route('notifications.read', $notification->id),
                'created_at_human' => $notification->created_at?->diffForHumans(),
            ];
        })
        ->values();
@endphp

<div x-data="notificationBell({ initialUnreadCount: {{ $initialUnreadCount }}, initialItems: @js($initialItems), userId: {{ auth()->id() }} })" x-init="init()" class="relative mr-4">
    <button @click="open = !open" :aria-expanded="open.toString()" aria-haspopup="menu" class="relative rounded-full border border-white/15 bg-white/5 p-2 text-gray-200 hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
        <span class="sr-only">View notifications</span>
        <div class="relative">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <template x-if="unreadCount > 0">
                <span class="absolute -top-1.5 -right-1.5 flex items-center justify-center min-w-[1.4rem] h-5 px-1 text-[10px] font-black text-white bg-red-700 rounded-full border-2 border-black">
                    <span x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                </span>
            </template>
        </div>
    </button>

    <div x-show="open" 
         @click.away="open = false"
         class="origin-top-right absolute right-0 mt-2 w-80 rounded-xl border border-slate-200 bg-white shadow-2xl ring-1 ring-slate-900/10 focus:outline-none z-50"
         role="menu" 
         aria-orientation="vertical" 
         aria-labelledby="user-menu-button" 
         tabindex="-1"
         style="display: none;">
        
        <div class="px-4 py-3 border-b border-slate-200 flex justify-between items-center">
            <span class="text-sm font-bold text-slate-900">Notifications</span>
            <form x-show="unreadCount > 0" action="{{ route('notifications.mark-all-read') }}" method="POST" @if($initialUnreadCount === 0) style="display: none;" @endif>
                @csrf
                <button type="submit" class="text-xs font-bold text-indigo-700 hover:text-indigo-900">Mark all read</button>
            </form>
        </div>

        <div class="max-h-64 overflow-y-auto">
            <div x-show="!jsReady">
                @forelse($initialItems as $item)
                    <a href="{{ $item['read_url'] }}" class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-100 last:border-0">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-900">{{ $item['title'] ?? 'Notification' }}</p>
                            <x-status-badge status="unread" size="sm" />
                        </div>
                        <p class="text-xs text-slate-600 mt-1 truncate">{{ $item['content'] ?? '' }}</p>
                        <p class="text-xs text-slate-500 mt-2">{{ $item['created_at_human'] ?? '' }}</p>
                    </a>
                @empty
                    <div class="px-4 py-4 text-center text-sm font-medium text-slate-600">
                        No new notifications
                    </div>
                @endforelse
            </div>

            <div x-show="jsReady" style="display: none;">
                <template x-if="items && items.length">
                    <div>
                        <template x-for="item in items" :key="item.id">
                            <a :href="item.read_url" class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-100 last:border-0">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-semibold text-slate-900" x-text="item.title || 'Notification'"></p>
                                    <span class="wl-status-badge wl-status-info px-2.5 py-1 text-[11px]">
                                        <span class="wl-status-badge-icon" aria-hidden="true">!</span>
                                        <span>Unread</span>
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 truncate" x-text="item.content || ''"></p>
                                <p class="text-xs text-slate-500 mt-2" x-text="item.created_at_human || ''"></p>
                            </a>
                        </template>
                    </div>
                </template>
                <template x-if="!items || !items.length">
                    <div class="px-4 py-4 text-center text-sm font-medium text-slate-600">
                        No new notifications
                    </div>
                </template>
            </div>
        </div>

        <div class="px-4 py-3 border-t border-slate-200">
            <a href="{{ route('notifications.index') }}" class="block text-center text-xs font-bold text-indigo-700 hover:text-indigo-900">
                View all notifications
            </a>
        </div>
    </div>
</div>

<script>
function notificationBell({ initialUnreadCount, initialItems, userId }) {
    return {
        open: false,
        unreadCount: initialUnreadCount,
        items: initialItems || [],
        jsReady: false,
        pollTimer: null,
        lastNotifiedNotificationId: null,

        init() {
            this.jsReady = true;
            const storageKey = `worklog:last-notification-notified:${userId}`;
            const fromStorage = window.localStorage.getItem(storageKey);
            this.lastNotifiedNotificationId = fromStorage || null;

            this.fetchNotificationSummary();
            this.pollTimer = setInterval(() => this.fetchNotificationSummary(), 5000);

            window.addEventListener('beforeunload', () => {
                if (this.pollTimer) {
                    clearInterval(this.pollTimer);
                }
            });
        },

        async fetchNotificationSummary() {
            try {
                const response = await fetch('/api/notifications/summary?limit=10', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    cache: 'no-store',
                });

                if (!response.ok) {
                    return;
                }

                const data = await response.json();
                if (!data.success) {
                    return;
                }

                this.unreadCount = data.unread_count || 0;

                if (Array.isArray(data.items)) {
                    this.items = data.items;
                }

                if (!data.latest_unread || !data.latest_unread.id) {
                    return;
                }

                const latestId = String(data.latest_unread.id);
                if (!latestId) {
                    return;
                }

                if (this.lastNotifiedNotificationId === null) {
                    this.lastNotifiedNotificationId = latestId;
                    window.localStorage.setItem(`worklog:last-notification-notified:${userId}`, latestId);
                    return;
                }

                if (latestId !== this.lastNotifiedNotificationId) {
                    this.lastNotifiedNotificationId = latestId;
                    window.localStorage.setItem(`worklog:last-notification-notified:${userId}`, latestId);
                    this.showBrowserNotification(data.latest_unread);
                }
            } catch (error) {
                console.error('Notification polling error:', error);
            }
        },

        showBrowserNotification(notification) {
            if (!('Notification' in window)) {
                return;
            }

            const title = notification.title || 'Notification';
            const text = notification.content || '';

            const notify = () => {
                const n = new Notification(title, {
                    body: text,
                    tag: `worklog-notification-${notification.id}`,
                });

                const url = notification.url || notification.read_url;
                if (url) {
                    n.onclick = () => {
                        try { window.location.href = url; } catch (e) {}
                    };
                }
            };

            if (Notification.permission === 'granted') {
                notify();
                return;
            }

            if (Notification.permission !== 'denied') {
                Notification.requestPermission().then((permission) => {
                    if (permission === 'granted') {
                        notify();
                    }
                });
            }
        },
    };
}
</script>
