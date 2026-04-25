@php
    $user = auth()->user();
    $role = $user?->role;

    $layout = match ($role) {
        \App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_STAFF => 'admin-layout',
        \App\Models\User::ROLE_COORDINATOR => 'coordinator-layout',
        \App\Models\User::ROLE_SUPERVISOR => 'supervisor-layout',
        \App\Models\User::ROLE_STUDENT => 'student-layout',
        \App\Models\User::ROLE_OJT_ADVISER => 'ojt-adviser-layout',
        default => 'app-layout',
    };
@endphp

<x-dynamic-component :component="$layout" header="Notifications">
    <div class="max-w-5xl mx-auto space-y-5">
        @if (session('status'))
            <div class="rounded-2xl border border-amber-400/30 bg-amber-500/10 px-4 py-3 text-sm font-medium text-amber-100">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-3xl border border-white/10 bg-slate-950/80 shadow-2xl shadow-black/30 overflow-hidden">
            <div class="border-b border-white/10 bg-gradient-to-r from-sky-500/15 via-indigo-500/10 to-slate-950 px-5 py-5 sm:px-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-xl font-black tracking-tight text-white">All notifications</h1>
                        <p class="mt-1 text-sm leading-6 text-slate-300">Readable, high-contrast updates for titles, messages, timestamps, and actions.</p>
                    </div>

                    <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="flex-shrink-0">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-xl border border-sky-300/30 bg-sky-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-sky-300">
                            Mark all as read
                        </button>
                    </form>
                </div>
            </div>

            <div class="divide-y divide-white/10">
                @forelse($notifications as $notification)
                    @php
                        $data = (array) ($notification->data ?? []);
                        $title = $data['title'] ?? $data['subject'] ?? 'Notification';
                        $content = $data['content'] ?? '';
                        $isUnread = $notification->read_at === null;
                    @endphp

                    <a href="{{ route('notifications.read', $notification->id) }}" class="block px-5 py-5 transition hover:bg-white/5 sm:px-6">
                        <div class="flex items-start gap-4">
                            <div class="mt-1 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-2xl {{ $isUnread ? 'bg-sky-400 text-slate-950' : 'bg-slate-800 text-slate-300' }}">
                                @if($isUnread)
                                    <span class="h-2.5 w-2.5 rounded-full bg-slate-950"></span>
                                @else
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="text-base font-bold leading-6 text-white">{{ $title }}</div>
                                        @if($content)
                                            <div class="mt-1 text-sm leading-6 text-slate-200">{{ $content }}</div>
                                        @endif
                                    </div>

                                    <div class="flex-shrink-0 text-xs font-medium uppercase tracking-[0.2em] text-slate-400">
                                        {{ $notification->created_at?->format('M d, Y h:i A') }}
                                    </div>
                                </div>

                                <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
                                    <span class="rounded-full px-3 py-1 font-semibold {{ $isUnread ? 'bg-sky-400/15 text-sky-200' : 'bg-slate-800 text-slate-300' }}">
                                        {{ $isUnread ? 'Unread' : 'Read' }}
                                    </span>
                                    <span class="text-slate-400">{{ $notification->created_at?->diffForHumans() }}</span>
                                    <span class="font-semibold text-sky-200">Open notification</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-5 py-12 text-center sm:px-6">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-800 text-slate-300">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <h2 class="mt-4 text-lg font-bold text-white">No notifications yet</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-300">New approvals, reminders, and account updates will appear here when they arrive.</p>
                    </div>
                @endforelse
            </div>

            @if(method_exists($notifications, 'links'))
                <div class="border-t border-white/10 px-5 py-4 sm:px-6">
                    {{ $notifications->links() }}
                </div>
            @endif
        </section>
    </div>
</x-dynamic-component>
