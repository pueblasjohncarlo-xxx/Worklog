@php
    $langs = [
        'en' => 'English',
        'es' => 'Espanol',
        'fr' => 'Francais',
        'de' => 'Deutsch',
        'it' => 'Italiano',
        'pt' => 'Portugues',
        'pt_BR' => 'Portugues (BR)',
        'nl' => 'Nederlands',
        'pl' => 'Polski',
        'ru' => 'Russkiy',
        'ja' => 'Japanese',
        'ko' => 'Korean',
        'zh_CN' => 'Chinese (Simplified)',
        'zh_TW' => 'Chinese (Traditional)',
        'ar' => 'Arabic',
        'hi' => 'Hindi',
        'id' => 'Bahasa Indonesia',
        'ms' => 'Bahasa Melayu',
        'th' => 'Thai',
        'tr' => 'Turkish',
        'vi' => 'Vietnamese',
    ];
@endphp

<div x-data="{ open: false }" class="relative">
    <button
        @click="open = !open"
        :aria-expanded="open.toString()"
        aria-haspopup="menu"
        class="rounded-full border border-white/15 bg-white/5 p-2 text-gray-100 transition-colors hover:bg-white/12 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/80 focus:ring-offset-2 focus:ring-offset-slate-900"
    >
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M12 3a9 9 0 100 18 9 9 0 000-18z" stroke-width="2"></path>
            <path d="M3 12h18M12 3a15 15 0 010 18M12 3a15 15 0 000 18" stroke-width="2"></path>
        </svg>
    </button>

    <div
        x-show="open"
        x-transition.origin.top.right
        @click.away="open = false"
        class="absolute right-0 mt-2 w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-900 shadow-2xl ring-1 ring-slate-900/10 z-50"
        style="display: none;"
    >
        <div class="border-b border-slate-200 bg-slate-50 px-4 py-3">
            <div class="text-[11px] font-black uppercase tracking-[0.18em] text-slate-500">Language</div>
            <div class="mt-1 text-sm font-bold text-slate-900">{{ $langs[app()->getLocale()] ?? 'English' }}</div>
        </div>

        <form method="POST" action="{{ route('locale.set') }}" class="max-h-64 overflow-y-auto">
            @csrf
            @foreach($langs as $code => $label)
                <button
                    type="submit"
                    name="locale"
                    value="{{ $code }}"
                    class="flex w-full items-center justify-between px-4 py-3 text-left text-sm transition-colors hover:bg-slate-100 focus:bg-slate-100 focus:outline-none {{ app()->getLocale() === $code ? 'bg-indigo-50 font-bold text-indigo-900' : 'font-semibold text-slate-800' }}"
                >
                    <span>{{ $label }}</span>
                    @if(app()->getLocale() === $code)
                        <span class="wl-status-badge wl-status-info px-2 py-0.5 text-[10px]">
                            <span class="wl-status-badge-icon" aria-hidden="true">i</span>
                            <span>Active</span>
                        </span>
                    @endif
                </button>
            @endforeach
        </form>
    </div>
</div>
