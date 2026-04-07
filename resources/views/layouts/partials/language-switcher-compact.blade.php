<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="p-2 rounded-full text-white hover:text-indigo-200 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M12 3a9 9 0 100 18 9 9 0 000-18z" stroke-width="2"></path>
            <path d="M3 12h18M12 3a15 15 0 010 18M12 3a15 15 0 000 18" stroke-width="2"></path>
        </svg>
    </button>
    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-56 bg-white text-gray-900 rounded-xl shadow-xl ring-1 ring-black/5 overflow-hidden z-50" style="display: none;">
        <div class="px-4 py-3 border-b border-gray-100">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Language</div>
            <div class="text-sm font-bold text-gray-900">{{ $langs[app()->getLocale()] ?? 'English' }}</div>
        </div>
        <form method="POST" action="{{ route('locale.set') }}" class="max-h-64 overflow-y-auto">
            @csrf
            @php
                $langs = [
                    'en' => 'English','es' => 'Español','fr' => 'Français','de' => 'Deutsch','it' => 'Italiano',
                    'pt' => 'Português','pt_BR' => 'Português (BR)','nl' => 'Nederlands','pl' => 'Polski','ru' => 'Русский',
                    'ja' => '日本語','ko' => '한국어','zh_CN' => '简体中文','zh_TW' => '繁體中文',
                    'ar' => 'العربية','hi' => 'हिन्दी','id' => 'Bahasa Indonesia','ms' => 'Bahasa Melayu','th' => 'ไทย',
                    'tr' => 'Türkçe','vi' => 'Tiếng Việt'
                ];
            @endphp
            @foreach($langs as $code => $label)
                <button type="submit" name="locale" value="{{ $code }}"
                    class="w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 {{ app()->getLocale() === $code ? 'bg-indigo-100 font-semibold text-indigo-700' : 'text-gray-800' }}">
                    {{ $label }}
                </button>
            @endforeach
        </form>
    </div>
</div>
