@props(['cards' => []])

@php
    $toneClasses = [
        'indigo' => ['card' => 'bg-indigo-600/20 border-indigo-500/30 hover:shadow-indigo-500/20 focus-visible:ring-indigo-400', 'label' => 'text-indigo-200'],
        'sky' => ['card' => 'bg-sky-600/20 border-sky-500/30 hover:shadow-sky-500/20 focus-visible:ring-sky-400', 'label' => 'text-sky-200'],
        'emerald' => ['card' => 'bg-emerald-600/20 border-emerald-500/30 hover:shadow-emerald-500/20 focus-visible:ring-emerald-400', 'label' => 'text-emerald-200'],
        'cyan' => ['card' => 'bg-cyan-600/20 border-cyan-500/30 hover:shadow-cyan-500/20 focus-visible:ring-cyan-400', 'label' => 'text-cyan-200'],
        'amber' => ['card' => 'bg-amber-600/20 border-amber-500/30 hover:shadow-amber-500/20 focus-visible:ring-amber-400', 'label' => 'text-amber-200'],
        'fuchsia' => ['card' => 'bg-fuchsia-600/20 border-fuchsia-500/30 hover:shadow-fuchsia-500/20 focus-visible:ring-fuchsia-400', 'label' => 'text-fuchsia-200'],
        'rose' => ['card' => 'bg-rose-600/20 border-rose-500/30 hover:shadow-rose-500/20 focus-visible:ring-rose-400', 'label' => 'text-rose-200'],
        'orange' => ['card' => 'bg-orange-600/20 border-orange-500/30 hover:shadow-orange-500/20 focus-visible:ring-orange-400', 'label' => 'text-orange-200'],
    ];
@endphp

<div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-8">
    @foreach ($cards as $card)
        @php
            $tone = $card['tone'] ?? 'indigo';
            $toneSet = $toneClasses[$tone] ?? $toneClasses['indigo'];
        @endphp
        <a
            href="{{ $card['href'] ?? '#' }}"
            class="block backdrop-blur-md border rounded-lg shadow-lg overflow-hidden cursor-pointer hover:-translate-y-0.5 focus:outline-none focus-visible:ring-2 transition-all {{ $toneSet['card'] }}"
        >
            <div class="p-4 min-h-[5.25rem] flex flex-col items-center justify-center text-center">
                <div class="text-xs font-bold uppercase tracking-widest {{ $toneSet['label'] }}">{{ $card['label'] ?? 'Metric' }}</div>
                <div class="mt-2 text-3xl font-black text-white leading-none">{{ $card['value'] ?? 0 }}</div>
            </div>
        </a>
    @endforeach
</div>
