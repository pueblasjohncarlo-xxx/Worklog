@props(['role'])

@php
    $role = strtolower($role ?? '');
    
    $colors = match ($role) {
        'student' => 'bg-blue-600/20 text-blue-100 border-blue-400/40 ring-blue-400/20',
        'supervisor' => 'bg-emerald-600/20 text-emerald-100 border-emerald-400/40 ring-emerald-400/20',
        'coordinator' => 'bg-cyan-600/20 text-cyan-100 border-cyan-400/40 ring-cyan-400/20',
        'admin' => 'bg-orange-600/20 text-orange-100 border-orange-400/40 ring-orange-400/20',
        default => 'bg-gray-600/20 text-gray-100 border-gray-400/40 ring-gray-400/20',
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