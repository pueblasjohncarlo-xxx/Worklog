@php
    $languageOptions = [
        'en' => 'English',
        'es' => 'Español',
        'fr' => 'Français',
        'de' => 'Deutsch',
        'it' => 'Italiano',
        'pt' => 'Português',
        'pt_BR' => 'Português (BR)',
        'ja' => '日本語',
        'ko' => '한국어',
        'zh_CN' => '简体中文',
        'zh_TW' => '繁體中文',
        'ar' => 'العربية',
        'hi' => 'Hindi',
        'id' => 'Bahasa Indonesia',
        'ms' => 'Bahasa Melayu',
        'th' => 'ไทย',
        'tr' => 'Türkçe',
        'vi' => 'Tiếng Việt',
    ];
@endphp

@if (session('settings-status'))
    <div class="rounded-2xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-100">
        {{ session('settings-status') }}
    </div>
@endif

<section class="rounded-2xl border border-white/10 bg-white/5 p-5">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-white">Account Settings</h2>
            <p class="mt-1 text-sm text-slate-300">Your main account details update live across WorkLog as soon as they are saved.</p>
        </div>
        <div class="rounded-xl bg-slate-950/80 px-3 py-2 text-xs font-bold uppercase tracking-[0.25em] text-slate-300">Live Sync</div>
    </div>
</section>

<section class="rounded-2xl border border-white/10 bg-white/5 p-5">
    <div class="mb-4">
        <h2 class="text-lg font-bold text-white">Privacy Settings</h2>
        <p class="mt-1 text-sm text-slate-300">Control who sees your profile details and activity presence.</p>
    </div>

    <form method="POST" action="{{ route('settings.update') }}" class="space-y-4">
        @csrf
        @method('PATCH')
        <input type="hidden" name="section" value="privacy">

        <div>
            <label for="privacy_profile_visibility" class="text-xs font-bold uppercase tracking-[0.22em] text-slate-300">Profile Visibility</label>
            <select id="privacy_profile_visibility" name="privacy[profile_visibility]" class="mt-2 block w-full rounded-xl border border-white/10 bg-slate-950/80 px-4 py-3 text-sm text-white focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-400/30">
                <option value="role_only" @selected($settings['privacy']['profile_visibility'] === 'role_only')>Only people in related roles</option>
                <option value="organization" @selected($settings['privacy']['profile_visibility'] === 'organization')>Anyone in WorkLog</option>
                <option value="public" @selected($settings['privacy']['profile_visibility'] === 'public')>Public within shared lists</option>
            </select>
        </div>

        <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-slate-950/70 px-4 py-3">
            <input type="checkbox" name="privacy[show_email]" value="1" @checked($settings['privacy']['show_email']) class="mt-1 rounded border-slate-600 bg-slate-950 text-sky-500 focus:ring-sky-400">
            <span>
                <span class="block text-sm font-semibold text-white">Show email in profile cards</span>
                <span class="block text-sm text-slate-400">Keeps email visible in system profile summaries and user details.</span>
            </span>
        </label>

        <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-slate-950/70 px-4 py-3">
            <input type="checkbox" name="privacy[show_activity_status]" value="1" @checked($settings['privacy']['show_activity_status']) class="mt-1 rounded border-slate-600 bg-slate-950 text-sky-500 focus:ring-sky-400">
            <span>
                <span class="block text-sm font-semibold text-white">Show activity status</span>
                <span class="block text-sm text-slate-400">Allows recent presence indicators in compatible parts of the app.</span>
            </span>
        </label>

        <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-slate-950/70 px-4 py-3">
            <input type="checkbox" name="privacy[allow_profile_indexing]" value="1" @checked($settings['privacy']['allow_profile_indexing']) class="mt-1 rounded border-slate-600 bg-slate-950 text-sky-500 focus:ring-sky-400">
            <span>
                <span class="block text-sm font-semibold text-white">Allow profile indexing</span>
                <span class="block text-sm text-slate-400">Lets your profile appear in broader organization-wide searches where supported.</span>
            </span>
        </label>

        <button type="submit" class="inline-flex items-center rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-sky-400">Save Privacy</button>
    </form>
</section>

<section class="rounded-2xl border border-white/10 bg-white/5 p-5">
    <div class="mb-4">
        <h2 class="text-lg font-bold text-white">Security & Login</h2>
        <p class="mt-1 text-sm text-slate-300">Password management and recent access visibility stay in one place.</p>
    </div>

    <div class="grid gap-3 sm:grid-cols-2">
        <div class="rounded-xl border border-white/10 bg-slate-950/70 p-4">
            <div class="text-xs uppercase tracking-[0.22em] text-slate-400">Last Login</div>
            <div class="mt-2 text-sm font-semibold text-white">{{ $user->last_login_at?->format('M d, Y h:i A') ?? 'Not recorded yet' }}</div>
        </div>
        <div class="rounded-xl border border-white/10 bg-slate-950/70 p-4">
            <div class="text-xs uppercase tracking-[0.22em] text-slate-400">Email Verification</div>
            <div class="mt-2 text-sm font-semibold text-white">{{ $user->email_verified_at ? 'Verified' : 'Pending verification' }}</div>
        </div>
    </div>
</section>

<section class="rounded-2xl border border-white/10 bg-white/5 p-5">
    <div class="mb-4">
        <h2 class="text-lg font-bold text-white">Notifications</h2>
        <p class="mt-1 text-sm text-slate-300">Choose which updates should stay prominent for your role.</p>
    </div>

    <form method="POST" action="{{ route('settings.update') }}" class="space-y-4">
        @csrf
        @method('PATCH')
        <input type="hidden" name="section" value="notifications">

        @foreach ([
            'email_updates' => ['Email updates', 'Send important account and workflow notices by email.'],
            'browser_alerts' => ['Browser alerts', 'Keep on-screen alerts active while WorkLog is open.'],
            'deadline_alerts' => ['Deadline alerts', 'Highlight pending requirements and due dates sooner.'],
            'approval_alerts' => ['Approval alerts', 'Show approval-related activity for supervisory workflows.'],
        ] as $key => [$label, $description])
            <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-slate-950/70 px-4 py-3">
                <input type="checkbox" name="notifications[{{ $key }}]" value="1" @checked($settings['notifications'][$key]) class="mt-1 rounded border-slate-600 bg-slate-950 text-sky-500 focus:ring-sky-400">
                <span>
                    <span class="block text-sm font-semibold text-white">{{ $label }}</span>
                    <span class="block text-sm text-slate-400">{{ $description }}</span>
                </span>
            </label>
        @endforeach

        <div>
            <label for="notifications_digest_frequency" class="text-xs font-bold uppercase tracking-[0.22em] text-slate-300">Digest Frequency</label>
            <select id="notifications_digest_frequency" name="notifications[digest_frequency]" class="mt-2 block w-full rounded-xl border border-white/10 bg-slate-950/80 px-4 py-3 text-sm text-white focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-400/30">
                <option value="instant" @selected($settings['notifications']['digest_frequency'] === 'instant')>Instant</option>
                <option value="daily" @selected($settings['notifications']['digest_frequency'] === 'daily')>Daily summary</option>
                <option value="weekly" @selected($settings['notifications']['digest_frequency'] === 'weekly')>Weekly summary</option>
            </select>
        </div>

        <button type="submit" class="inline-flex items-center rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-sky-400">Save Notifications</button>
    </form>
</section>

<section class="rounded-2xl border border-white/10 bg-white/5 p-5">
    <div class="mb-4">
        <h2 class="text-lg font-bold text-white">Profile Settings</h2>
        <p class="mt-1 text-sm text-slate-300">Your display name, photo, role-specific details, and profile data are managed in the account form above.</p>
    </div>
    <div class="rounded-xl border border-white/10 bg-slate-950/70 p-4 text-sm text-slate-300">
        Changes to your profile picture and basic account information now update smoothly across avatars, names, and email labels without needing repeated page refreshes.
    </div>
</section>

<section class="rounded-2xl border border-white/10 bg-white/5 p-5">
    <div class="mb-4">
        <h2 class="text-lg font-bold text-white">Preferences</h2>
        <p class="mt-1 text-sm text-slate-300">Theme, density, and landing behavior for your WorkLog workspace.</p>
    </div>

    <form method="POST" action="{{ route('settings.update') }}" class="space-y-4">
        @csrf
        @method('PATCH')
        <input type="hidden" name="section" value="preferences">

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="preferences_theme" class="text-xs font-bold uppercase tracking-[0.22em] text-slate-300">Theme</label>
                <select id="preferences_theme" name="preferences[theme]" class="mt-2 block w-full rounded-xl border border-white/10 bg-slate-950/80 px-4 py-3 text-sm text-white focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-400/30">
                    <option value="system" @selected($settings['preferences']['theme'] === 'system')>System</option>
                    <option value="light" @selected($settings['preferences']['theme'] === 'light')>Light</option>
                    <option value="dark" @selected($settings['preferences']['theme'] === 'dark')>Dark</option>
                </select>
                <p class="mt-2 text-xs text-slate-400">Applies across WorkLog pages, cards, forms, tables, and dropdowns after you save.</p>
            </div>
            <div>
                <label for="preferences_start_page" class="text-xs font-bold uppercase tracking-[0.22em] text-slate-300">Start Page</label>
                <input id="preferences_start_page" name="preferences[start_page]" type="text" value="{{ $settings['preferences']['start_page'] }}" class="mt-2 block w-full rounded-xl border border-white/10 bg-slate-950/80 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-400/30">
            </div>
        </div>

        <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-slate-950/70 px-4 py-3">
            <input type="checkbox" name="preferences[compact_mode]" value="1" @checked($settings['preferences']['compact_mode']) class="mt-1 rounded border-slate-600 bg-slate-950 text-sky-500 focus:ring-sky-400">
            <span>
                <span class="block text-sm font-semibold text-white">Compact mode</span>
                <span class="block text-sm text-slate-400">Prefer denser tables and cards where the UI supports compact layouts.</span>
            </span>
        </label>

        <button type="submit" class="inline-flex items-center rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-sky-400">Save Preferences</button>
    </form>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const themeSelect = document.getElementById('preferences_theme');

        if (!themeSelect) {
            return;
        }

        themeSelect.addEventListener('change', function () {
            if (window.WorkLogTheme && typeof window.WorkLogTheme.apply === 'function') {
                window.WorkLogTheme.apply(themeSelect.value);
            }
        });
    });
</script>
@endpush

<section class="rounded-2xl border border-white/10 bg-white/5 p-5">
    <div class="mb-4">
        <h2 class="text-lg font-bold text-white">Blocking Settings</h2>
        <p class="mt-1 text-sm text-slate-300">Maintain a personal mute list for keywords or names you want deprioritized in compatible alerts.</p>
    </div>

    <form method="POST" action="{{ route('settings.update') }}" class="space-y-4">
        @csrf
        @method('PATCH')
        <input type="hidden" name="section" value="blocking">

        <div>
            <label for="blocking_muted_keywords" class="text-xs font-bold uppercase tracking-[0.22em] text-slate-300">Muted Keywords</label>
            <textarea id="blocking_muted_keywords" name="blocking[muted_keywords]" rows="3" class="mt-2 block w-full rounded-xl border border-white/10 bg-slate-950/80 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-400/30" placeholder="Example: reminder, sample">{{ $settings['blocking']['muted_keywords'] }}</textarea>
        </div>

        <div>
            <label for="blocking_hidden_people" class="text-xs font-bold uppercase tracking-[0.22em] text-slate-300">Hidden People</label>
            <textarea id="blocking_hidden_people" name="blocking[hidden_people]" rows="3" class="mt-2 block w-full rounded-xl border border-white/10 bg-slate-950/80 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-400/30" placeholder="One name or email per line">{{ $settings['blocking']['hidden_people'] }}</textarea>
        </div>

        <button type="submit" class="inline-flex items-center rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-sky-400">Save Blocking</button>
    </form>
</section>

<section class="rounded-2xl border border-white/10 bg-white/5 p-5">
    <div class="mb-4">
        <h2 class="text-lg font-bold text-white">Activity Log</h2>
        <p class="mt-1 text-sm text-slate-300">Recent audit activity tied to your account.</p>
    </div>

    <div class="space-y-3">
        @forelse ($activityLogs as $log)
            <div class="rounded-xl border border-white/10 bg-slate-950/70 px-4 py-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold text-white">{{ \Illuminate\Support\Str::headline((string) $log->action) }}</div>
                        <div class="mt-1 text-sm text-slate-400">{{ class_basename((string) $log->auditable_type) ?: 'System' }}</div>
                    </div>
                    <div class="text-xs text-slate-500">{{ $log->created_at?->diffForHumans() }}</div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-white/10 bg-slate-950/60 px-4 py-5 text-sm text-slate-400">
                No audit activity has been recorded for this account yet.
            </div>
        @endforelse
    </div>
</section>

<section class="rounded-2xl border border-white/10 bg-white/5 p-5">
    <div class="mb-4">
        <h2 class="text-lg font-bold text-white">Recent Notifications</h2>
        <p class="mt-1 text-sm text-slate-300">Readable, high-contrast snapshot of the latest account notifications.</p>
    </div>

    <div class="space-y-3">
        @forelse ($recentNotifications as $notification)
            @php
                $data = (array) ($notification->data ?? []);
                $title = $data['title'] ?? $data['subject'] ?? 'Notification';
                $content = $data['content'] ?? '';
            @endphp
            <div class="rounded-xl border border-white/10 bg-slate-950/70 px-4 py-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-white">{{ $title }}</div>
                        @if($content)
                            <div class="mt-1 text-sm leading-6 text-slate-300">{{ $content }}</div>
                        @endif
                    </div>
                    <div class="whitespace-nowrap text-xs text-slate-500">{{ $notification->created_at?->diffForHumans() }}</div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-white/10 bg-slate-950/60 px-4 py-5 text-sm text-slate-400">
                You do not have recent notifications right now.
            </div>
        @endforelse
    </div>
</section>
