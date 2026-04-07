@props(['value' => 0])

<div class="w-24 bg-gray-200 rounded-full h-2.5">
    <div class="bg-indigo-600 h-2.5 rounded-full" @style(["width: {$value}%"])></div>
</div>