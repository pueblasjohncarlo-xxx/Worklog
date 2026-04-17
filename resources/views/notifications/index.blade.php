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
    <div class="max-w-5xl mx-auto">
        <div class="bg-white/10 border border-white/10 rounded-xl overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-white/10 flex items-center justify-between gap-3">
                <div>
                    <div class="text-lg font-bold text-white">All notifications</div>
                    <div class="text-sm text-slate-300">Includes read and unread items.</div>
                </div>

                <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-sm font-semibold text-white border border-white/10">
                        Mark all read
                    </button>
                </form>
            </div>

            <div>
                @forelse($notifications as $notification)
                    @php
                        $data = (array) ($notification->data ?? []);
                        $title = $data['title'] ?? $data['subject'] ?? 'Notification';
                        $content = $data['content'] ?? '';
                        $isUnread = $notification->read_at === null;
                    @endphp

                    <a href="{{ route('notifications.read', $notification->id) }}" class="block px-4 sm:px-6 py-4 border-b border-white/10 hover:bg-white/5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    @if($isUnread)
                                        <span class="inline-flex h-2 w-2 rounded-full bg-red-500 flex-shrink-0" aria-hidden="true"></span>
                                    @endif
                                    <div class="text-sm sm:text-base font-semibold text-white truncate">{{ $title }}</div>
                                </div>

                                @if($content)
                                    <div class="mt-1 text-sm text-slate-200/90 line-clamp-2">{{ $content }}</div>
                                @endif

                                <div class="mt-1 text-xs text-slate-400">
                                    {{ $notification->created_at?->diffForHumans() }}
                                    @if(! $isUnread)
                                        <span class="ml-2">• Read</span>
                                    @endif
                                </div>
                            </div>

                            <div class="text-slate-300 flex-shrink-0">→</div>
                        </div>
                    </a>
                @empty
                    <div class="px-4 sm:px-6 py-10 text-center text-slate-300">
                        No notifications yet.
                    </div>
                @endforelse
            </div>

            @if(method_exists($notifications, 'links'))
                <div class="px-4 sm:px-6 py-4 border-t border-white/10">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-dynamic-component>
