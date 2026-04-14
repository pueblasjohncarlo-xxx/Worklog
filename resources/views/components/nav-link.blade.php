@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 dark:border-indigo-600 text-sm font-semibold leading-5 text-white dark:text-gray-100 focus:outline-none focus:border-indigo-300 transition duration-150 ease-in-out cursor-pointer'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-semibold leading-5 text-indigo-100 dark:text-gray-300 hover:text-white dark:hover:text-white hover:border-indigo-300 dark:hover:border-indigo-400 focus:outline-none focus:text-white dark:focus:text-white focus:border-indigo-300 dark:focus:border-indigo-400 transition duration-150 ease-in-out cursor-pointer';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
