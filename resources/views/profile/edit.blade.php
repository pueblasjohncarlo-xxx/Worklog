@php
    $role = $user->role;
    $layout = match ($role) {
        \App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_STAFF => 'admin-layout',
        \App\Models\User::ROLE_COORDINATOR => 'coordinator-layout',
        \App\Models\User::ROLE_SUPERVISOR => 'supervisor-layout',
        \App\Models\User::ROLE_STUDENT => 'student-layout',
        \App\Models\User::ROLE_OJT_ADVISER => 'ojt-adviser-layout',
        default => 'app-layout',
    };

    $roleLabel = ucfirst(str_replace('_', ' ', $role));
@endphp

<x-dynamic-component :component="$layout" header="Settings">
    <div class="max-w-7xl mx-auto space-y-6">
        <section class="rounded-3xl border border-white/10 bg-slate-950/75 shadow-2xl shadow-black/30 overflow-hidden">
            <div class="border-b border-white/10 bg-gradient-to-r from-sky-500/15 via-indigo-500/10 to-emerald-500/15 px-6 py-6 sm:px-8">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center gap-4">
                        <img
                            src="{{ $user->profile_photo_url }}"
                            data-avatar-user-id="{{ $user->id }}"
                            alt="{{ $user->name }}"
                            class="h-20 w-20 rounded-2xl border-2 border-sky-400/60 object-cover shadow-lg shadow-sky-950/40"
                        >
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.3em] text-sky-200/80">WorkLog Settings</p>
                            <h1 class="mt-2 text-2xl font-black tracking-tight text-white" data-user-name-id="{{ $user->id }}">{{ $user->name }}</h1>
                            <p class="mt-1 text-sm text-slate-300">{{ $roleLabel }} account with profile sync, preferences, privacy, and security controls.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                            <div class="text-[11px] uppercase tracking-[0.24em] text-slate-400">Role</div>
                            <div class="mt-2 text-sm font-semibold text-white">{{ $roleLabel }}</div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                            <div class="text-[11px] uppercase tracking-[0.24em] text-slate-400">Notifications</div>
                            <div class="mt-2 text-sm font-semibold text-white">{{ number_format($unreadNotificationsCount) }} unread</div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                            <div class="text-[11px] uppercase tracking-[0.24em] text-slate-400">Email</div>
                            <div class="mt-2 truncate text-sm font-semibold text-white" data-user-email-id="{{ $user->id }}">{{ $user->email }}</div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                            <div class="text-[11px] uppercase tracking-[0.24em] text-slate-400">Last Login</div>
                            <div class="mt-2 text-sm font-semibold text-white">{{ $user->last_login_at?->diffForHumans() ?? 'Not recorded' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 px-6 py-6 sm:px-8 lg:grid-cols-[1.3fr_0.9fr]">
                <div class="space-y-6">
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 p-4">
                            <div class="text-xs font-bold uppercase tracking-[0.25em] text-emerald-200/80">Account Status</div>
                            <div class="mt-2 text-lg font-bold text-white">{{ $user->is_approved ? 'Approved' : 'Pending Review' }}</div>
                            <div class="mt-1 text-sm text-emerald-100/80">{{ $user->email_verified_at ? 'Email verified' : 'Email verification needed' }}</div>
                        </div>

                        <div class="rounded-2xl border border-indigo-400/20 bg-indigo-500/10 p-4">
                            <div class="text-xs font-bold uppercase tracking-[0.25em] text-indigo-200/80">Preferences</div>
                            <div class="mt-2 text-lg font-bold text-white">{{ ucfirst($settings['preferences']['theme']) }}</div>
                            <div class="mt-1 text-sm text-indigo-100/80">{{ $settings['preferences']['compact_mode'] ? 'Compact mode enabled' : 'Standard spacing' }}</div>
                        </div>

                        <div class="rounded-2xl border border-amber-400/20 bg-amber-500/10 p-4">
                            <div class="text-xs font-bold uppercase tracking-[0.25em] text-amber-200/80">Privacy</div>
                            <div class="mt-2 text-lg font-bold text-white">{{ ucfirst(str_replace('_', ' ', $settings['privacy']['profile_visibility'])) }}</div>
                            <div class="mt-1 text-sm text-amber-100/80">{{ $settings['privacy']['show_activity_status'] ? 'Activity visible' : 'Activity hidden' }}</div>
                        </div>
                    </div>

                    @if($user->role === \App\Models\User::ROLE_STUDENT && $currentAssignment)
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h2 class="text-lg font-bold text-white">Deployment Snapshot</h2>
                                    <p class="text-sm text-slate-300">Your active assignment and hour progress remain visible here while you manage account settings.</p>
                                </div>
                                @php
                                    $progress = $requiredHours > 0 ? min(100, ($approvedHours / $requiredHours) * 100) : 0;
                                @endphp
                                <div class="rounded-xl bg-slate-900/80 px-4 py-2 text-sm font-semibold text-sky-200">{{ number_format($progress, 1) }}% complete</div>
                            </div>

                            <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                <div class="rounded-2xl border border-white/10 bg-slate-950/70 p-4">
                                    <div class="text-xs uppercase tracking-[0.22em] text-slate-400">Company</div>
                                    <div class="mt-2 text-sm font-semibold text-white">{{ $currentAssignment->company?->name ?? 'Not assigned' }}</div>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-slate-950/70 p-4">
                                    <div class="text-xs uppercase tracking-[0.22em] text-slate-400">Supervisor</div>
                                    <div class="mt-2 text-sm font-semibold text-white">{{ $currentAssignment->supervisor?->name ?? 'Not assigned' }}</div>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-slate-950/70 p-4">
                                    <div class="text-xs uppercase tracking-[0.22em] text-slate-400">Approved Hours</div>
                                    <div class="mt-2 text-sm font-semibold text-white">{{ number_format($approvedHours, 1) }} hrs</div>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-slate-950/70 p-4">
                                    <div class="text-xs uppercase tracking-[0.22em] text-slate-400">Required Hours</div>
                                    <div class="mt-2 text-sm font-semibold text-white">{{ number_format($requiredHours, 1) }} hrs</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <div class="grid gap-6 xl:grid-cols-2">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                            @include('profile.partials.update-password-form')
                        </div>
                        <div class="rounded-2xl border border-rose-400/20 bg-rose-500/10 p-5">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    @include('profile.partials.settings-panels')
                </div>
            </div>
        </section>
    </div>
</x-dynamic-component>

@push('scripts')
@if (session('status') === 'profile-updated')
<script>
    window.addEventListener('DOMContentLoaded', function () {
        if (window.WorkLogProfileSync && typeof window.WorkLogProfileSync.broadcast === 'function') {
            window.WorkLogProfileSync.broadcast({
                id: {{ (int) $user->id }},
                name: @json($user->name),
                email: @json($user->email),
                avatar_url: @json($user->profile_photo_url),
                updated_at: @json(optional($user->updated_at)->toIso8601String()),
            });
        } else if (window.WorkLogAvatarSync && typeof window.WorkLogAvatarSync.broadcast === 'function') {
            window.WorkLogAvatarSync.broadcast();
        }
    });
</script>
@endif
@endpush
