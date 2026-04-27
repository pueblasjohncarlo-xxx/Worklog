<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-700 border border-red-800 rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-red-800 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:bg-slate-400 disabled:border-slate-400 disabled:text-slate-950 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
