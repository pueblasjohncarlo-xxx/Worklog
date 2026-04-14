@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-indigo-400 dark:border-indigo-600 text-start text-base font-semibold text-white dark:text-indigo-200 bg-indigo-900/40 dark:bg-indigo-900/50 focus:outline-none focus:text-white dark:focus:text-white focus:bg-indigo-900/60 dark:focus:bg-indigo-900 focus:border-indigo-300 dark:focus:border-indigo-300 transition duration-150 ease-in-out cursor-pointer'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-semibold text-indigo-100 dark:text-gray-300 hover:text-white dark:hover:text-white hover:bg-indigo-900/30 dark:hover:bg-indigo-800/40 hover:border-indigo-300 dark:hover:border-indigo-400 focus:outline-none focus:text-white dark:focus:text-white focus:bg-indigo-900/30 dark:focus:bg-indigo-800/40 focus:border-indigo-300 dark:focus:border-indigo-400 transition duration-150 ease-in-out cursor-pointer';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
