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
    <button @click="open = !open" :aria-expanded="open.toString()" aria-haspopup="menu" class="relative rounded-full border border-white/15 bg-white/5 p-2 text-gray-100 transition-colors hover:bg-white/12 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/80 focus:ring-offset-2 focus:ring-offset-slate-900">
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
         x-transition.origin.top.right
         @click.away="open = false"
         class="origin-top-right absolute right-0 mt-2 w-96 overflow-hidden rounded-2xl border border-slate-700 bg-slate-950 shadow-2xl ring-1 ring-black/40 focus:outline-none z-50"
         role="menu" 
         aria-orientation="vertical" 
         aria-labelledby="user-menu-button" 
         tabindex="-1"
         style="display: none;">
        
        <div class="flex items-center justify-between gap-3 border-b border-slate-800 bg-slate-900 px-4 py-3.5">
            <div>
                <span class="text-sm font-black uppercase tracking-[0.18em] text-white">Notifications</span>
                <p class="mt-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-300">
                    <span x-text="unreadCount"></span> unread item<span x-text="unreadCount === 1 ? '' : 's'"></span>
                </p>
            </div>
            <form x-show="unreadCount > 0" action="{{ route('notifications.mark-all-read') }}" method="POST" @if($initialUnreadCount === 0) style="display: none;" @endif>
                @csrf
                <button type="submit" class="rounded-lg border border-sky-400/30 bg-sky-500/15 px-3 py-1.5 text-xs font-black uppercase tracking-[0.14em] text-sky-100 transition-colors hover:bg-sky-500/25 hover:text-white focus:bg-sky-500/25 focus:outline-none">Mark all read</button>
            </form>
        </div>

        <div class="max-h-80 overflow-y-auto bg-slate-950">
            <div x-show="!jsReady">
                @forelse($initialItems as $item)
                    <a href="{{ $item['read_url'] }}" class="group block border-b border-slate-800 bg-slate-950 px-4 py-4 transition-colors hover:bg-slate-900 focus:bg-slate-900 focus:outline-none last:border-0">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-sky-400/20 bg-sky-500/10 text-sky-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="text-sm font-black leading-5 text-white group-hover:text-sky-100">{{ $item['title'] ?? 'Notification' }}</p>
                                    <span class="inline-flex shrink-0 items-center rounded-full border border-sky-400/30 bg-sky-500/15 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.14em] text-sky-100">
                                        <span class="mr-1.5 inline-block h-2 w-2 rounded-full bg-sky-300"></span>
                                        Unread
                                    </span>
                                </div>
                                <p class="mt-1.5 text-xs font-semibold leading-5 text-slate-200">{{ $item['content'] ?? '' }}</p>
                                <div class="mt-3 flex items-center justify-between gap-3">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400">{{ $item['created_at_human'] ?? '' }}</p>
                                    <span class="text-[11px] font-black uppercase tracking-[0.12em] text-sky-200">Open</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-8 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl border border-slate-700 bg-slate-900 text-slate-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <p class="mt-4 text-sm font-black text-white">No new notifications</p>
                        <p class="mt-1 text-xs font-semibold text-slate-300">You are all caught up right now.</p>
                    </div>
                @endforelse
            </div>

            <div x-show="jsReady" style="display: none;">
                <template x-if="items && items.length">
                    <div>
                        <template x-for="item in items" :key="item.id">
                            <a :href="item.read_url" class="group block border-b border-slate-800 bg-slate-950 px-4 py-4 transition-colors hover:bg-slate-900 focus:bg-slate-900 focus:outline-none last:border-0">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-sky-400/20 bg-sky-500/10 text-sky-200">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-3">
                                            <p class="text-sm font-black leading-5 text-white group-hover:text-sky-100" x-text="item.title || 'Notification'"></p>
                                            <span class="inline-flex shrink-0 items-center rounded-full border border-sky-400/30 bg-sky-500/15 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.14em] text-sky-100">
                                                <span class="mr-1.5 inline-block h-2 w-2 rounded-full bg-sky-300"></span>
                                                Unread
                                            </span>
                                        </div>
                                        <p class="mt-1.5 text-xs font-semibold leading-5 text-slate-200" x-text="item.content || ''"></p>
                                        <div class="mt-3 flex items-center justify-between gap-3">
                                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-400" x-text="item.created_at_human || ''"></p>
                                            <span class="text-[11px] font-black uppercase tracking-[0.12em] text-sky-200">Open</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>
                </template>
                <template x-if="!items || !items.length">
                    <div class="px-6 py-8 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl border border-slate-700 bg-slate-900 text-slate-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <p class="mt-4 text-sm font-black text-white">No new notifications</p>
                        <p class="mt-1 text-xs font-semibold text-slate-300">You are all caught up right now.</p>
                    </div>
                </template>
            </div>
        </div>

        <div class="border-t border-slate-800 bg-slate-900 px-4 py-3">
            <a href="{{ route('notifications.index') }}" class="block rounded-xl border border-slate-700 bg-slate-800 px-3 py-3 text-center text-xs font-black uppercase tracking-[0.16em] text-white transition-colors hover:bg-slate-700 hover:text-sky-100 focus:bg-slate-700 focus:outline-none active:bg-slate-700">
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
