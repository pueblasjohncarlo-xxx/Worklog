@props(['role'])

@php
    $role = strtolower($role ?? '');
    
    $colors = match ($role) {
        'student' => 'bg-blue-500/20 text-blue-300 border-blue-500/30 ring-blue-500/20',
        'supervisor' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30 ring-emerald-500/20',
        'coordinator' => 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30 ring-cyan-500/20',
        'admin' => 'bg-orange-500/20 text-orange-300 border-orange-500/30 ring-orange-500/20',
        default => 'bg-gray-500/20 text-gray-300 border-gray-500/30 ring-gray-500/20',
    };

    $label = match ($role) {
        'student' => 'OJT Student',
        'supervisor' => 'Supervisor',
        'coordinator' => 'Coordinator',
        'admin' => 'Administrator',
        'ojt_adviser' => 'OJT Adviser',
        default => ucfirst(str_replace('_', ' ', $role)),
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border ring-1 ring-inset uppercase tracking-wide {$colors}"]) }}>
    {{ $label }}
</span>