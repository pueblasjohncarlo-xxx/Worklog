@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:border-indigo-500 dark:focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-500 rounded-md shadow-sm disabled:bg-gray-100 disabled:text-gray-600 disabled:border-gray-300 dark:disabled:bg-gray-800 dark:disabled:text-gray-300 dark:disabled:border-gray-700 disabled:opacity-100']) }}>
