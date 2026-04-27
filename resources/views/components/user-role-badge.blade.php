@props(['role'])

@php
    $role = strtolower($role ?? '');
    
    $colors = match ($role) {
        'student' => 'wl-status-badge wl-status-submitted',
        'supervisor' => 'wl-status-badge wl-status-active',
        'coordinator' => 'wl-status-badge wl-status-info',
        'admin' => 'wl-status-badge wl-status-missing',
        'ojt_adviser' => 'wl-status-badge wl-status-pending',
        default => 'wl-status-badge wl-status-draft',
    };

    $meta = match ($role) {
        'student' => ['label' => 'OJT Student', 'icon' => 'S'],
        'supervisor' => ['label' => 'Supervisor', 'icon' => 'SP'],
        'coordinator' => ['label' => 'Coordinator', 'icon' => 'CO'],
        'admin' => ['label' => 'Administrator', 'icon' => 'AD'],
        'ojt_adviser' => ['label' => 'OJT Adviser', 'icon' => 'OA'],
        default => ucfirst(str_replace('_', ' ', $role)),
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 {$colors}"]) }}>
    <span class="wl-status-badge-icon" aria-hidden="true">{{ is_array($meta) ? $meta['icon'] : 'i' }}</span>
    {{ is_array($meta) ? $meta['label'] : $meta }}
</span>
