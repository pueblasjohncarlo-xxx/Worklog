<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-slate-400 dark:border-gray-500 rounded-md font-bold text-xs text-slate-900 dark:text-gray-100 uppercase tracking-widest shadow-sm hover:bg-slate-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:bg-slate-100 disabled:text-slate-500 disabled:border-slate-300 transition ease-in-out duration-150 cursor-pointer']) }}>
    {{ $slot }}
</button>
