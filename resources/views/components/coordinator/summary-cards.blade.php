@props(['cards' => []])

@php
    $toneClasses = [
        'indigo' => ['card' => 'bg-slate-950/75 border-indigo-300/45 hover:border-indigo-200 hover:shadow-indigo-500/20 focus-visible:ring-indigo-300', 'label' => 'text-indigo-100', 'chip' => 'bg-indigo-100 text-indigo-950 border-indigo-300', 'icon' => 'ST'],
        'sky' => ['card' => 'bg-slate-950/75 border-sky-300/45 hover:border-sky-200 hover:shadow-sky-500/20 focus-visible:ring-sky-300', 'label' => 'text-sky-100', 'chip' => 'bg-sky-100 text-sky-950 border-sky-300', 'icon' => 'AO'],
        'emerald' => ['card' => 'bg-slate-950/75 border-emerald-300/45 hover:border-emerald-200 hover:shadow-emerald-500/20 focus-visible:ring-emerald-300', 'label' => 'text-emerald-100', 'chip' => 'bg-emerald-100 text-emerald-950 border-emerald-300', 'icon' => 'AD'],
        'cyan' => ['card' => 'bg-slate-950/75 border-cyan-300/45 hover:border-cyan-200 hover:shadow-cyan-500/20 focus-visible:ring-cyan-300', 'label' => 'text-cyan-100', 'chip' => 'bg-cyan-100 text-cyan-950 border-cyan-300', 'icon' => 'SP'],
        'amber' => ['card' => 'bg-slate-950/75 border-amber-300/45 hover:border-amber-200 hover:shadow-amber-500/20 focus-visible:ring-amber-300', 'label' => 'text-amber-100', 'chip' => 'bg-amber-100 text-amber-950 border-amber-300', 'icon' => 'IN'],
        'fuchsia' => ['card' => 'bg-slate-950/75 border-fuchsia-300/45 hover:border-fuchsia-200 hover:shadow-fuchsia-500/20 focus-visible:ring-fuchsia-300', 'label' => 'text-fuchsia-100', 'chip' => 'bg-fuchsia-100 text-fuchsia-950 border-fuchsia-300', 'icon' => 'PA'],
        'rose' => ['card' => 'bg-slate-950/75 border-rose-300/45 hover:border-rose-200 hover:shadow-rose-500/20 focus-visible:ring-rose-300', 'label' => 'text-rose-100', 'chip' => 'bg-rose-100 text-rose-950 border-rose-300', 'icon' => 'AR'],
        'orange' => ['card' => 'bg-slate-950/75 border-orange-300/45 hover:border-orange-200 hover:shadow-orange-500/20 focus-visible:ring-orange-300', 'label' => 'text-orange-100', 'chip' => 'bg-orange-100 text-orange-950 border-orange-300', 'icon' => 'AT'],
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
            class="block border rounded-xl shadow-lg overflow-hidden cursor-pointer hover:-translate-y-0.5 focus:outline-none focus-visible:ring-2 transition-all {{ $toneSet['card'] }}"
        >
            <div class="p-4 min-h-[7rem] flex flex-col justify-between">
                <div class="flex items-start justify-between gap-3">
                    <div class="text-xs font-black uppercase tracking-[0.18em] {{ $toneSet['label'] }}">{{ $card['label'] ?? 'Metric' }}</div>
                    <span class="inline-flex h-7 min-w-[2.25rem] items-center justify-center rounded-full border px-2 text-[11px] font-black tracking-[0.12em] {{ $toneSet['chip'] }}">
                        {{ $toneSet['icon'] }}
                    </span>
                </div>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div class="text-3xl font-black text-white leading-none">{{ $card['value'] ?? 0 }}</div>
                    <div class="text-[11px] font-bold uppercase tracking-[0.14em] text-slate-300">View</div>
                </div>
            </div>
        </a>
    @endforeach
</div>
