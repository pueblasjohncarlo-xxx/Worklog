<div class="fixed left-4 bottom-4 z-50">
    <form method="POST" action="{{ route('locale.set') }}" class="flex items-center gap-2 bg-black/60 backdrop-blur-md text-white rounded-full px-3 py-2 border border-white/20 shadow">
        @csrf
        <div class="flex items-center gap-2">
            <svg class="h-4 w-4 text-white/90" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M12 3a9 9 0 100 18 9 9 0 000-18z" stroke-width="2"></path>
                <path d="M3 12h18M12 3a15 15 0 010 18M12 3a15 15 0 000 18" stroke-width="2"></path>
            </svg>
            <span class="text-xs font-semibold uppercase tracking-wide">Language</span>
        </div>
        <select name="locale" class="bg-transparent text-sm font-semibold pl-1 pr-2 py-1 focus:outline-none">
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
                <option value="{{ $code }}" {{ app()->getLocale() === $code ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="text-xs font-bold bg-indigo-600 hover:bg-indigo-700 px-2 py-1 rounded-full">Apply</button>
    </form>
</div>
