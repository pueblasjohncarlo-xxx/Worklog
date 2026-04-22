@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-gray-800 dark:text-gray-100']) }}>
    {{ $value ?? $slot }}
</label>
