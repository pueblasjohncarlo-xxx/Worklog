@props([
    'status' => null,
    'label' => null,
    'size' => 'default',
])

@php
    $rawStatus = strtolower(trim((string) ($status ?? 'unknown')));
    $normalized = str_replace([' ', '-'], '_', $rawStatus);

    $meta = match ($normalized) {
        'approved' => ['class' => 'wl-status-approved', 'icon' => '✓', 'label' => 'Approved'],
        'submitted' => ['class' => 'wl-status-submitted', 'icon' => '↑', 'label' => 'Submitted'],
        'pending' => ['class' => 'wl-status-pending', 'icon' => '!', 'label' => 'Pending'],
        'draft' => ['class' => 'wl-status-draft', 'icon' => '•', 'label' => 'Draft'],
        'rejected', 'declined' => ['class' => 'wl-status-rejected', 'icon' => '×', 'label' => 'Rejected'],
        'missing', 'overdue', 'missing_overdue' => ['class' => 'wl-status-missing', 'icon' => '!', 'label' => 'Missing / Overdue'],
        'active' => ['class' => 'wl-status-active', 'icon' => '✓', 'label' => 'Active'],
        'inactive' => ['class' => 'wl-status-inactive', 'icon' => '•', 'label' => 'Inactive'],
        'complete', 'completed' => ['class' => 'wl-status-complete', 'icon' => '✓', 'label' => 'Complete'],
        'incomplete' => ['class' => 'wl-status-incomplete', 'icon' => '!', 'label' => 'Incomplete'],
        'unassigned', 'not_assigned' => ['class' => 'wl-status-unassigned', 'icon' => '•', 'label' => 'Not Assigned'],
        'read' => ['class' => 'wl-status-read', 'icon' => '✓', 'label' => 'Read'],
        'unread' => ['class' => 'wl-status-info', 'icon' => '!', 'label' => 'Unread'],
        'on_track' => ['class' => 'wl-status-on_track', 'icon' => '✓', 'label' => 'On Track'],
        'review', 'under_review' => ['class' => 'wl-status-info', 'icon' => 'i', 'label' => 'Under Review'],
        default => ['class' => 'wl-status-info', 'icon' => 'i', 'label' => ucfirst(str_replace('_', ' ', $normalized ?: 'status'))],
    };

    $resolvedLabel = $label ?: $meta['label'];
    $sizeClass = $size === 'sm' ? 'px-2.5 py-1 text-[11px]' : ($size === 'lg' ? 'px-3.5 py-1.5 text-sm' : '');
@endphp

<span {{ $attributes->merge(['class' => trim("wl-status-badge {$meta['class']} {$sizeClass}")]) }}>
    <span class="wl-status-badge-icon" aria-hidden="true">{{ $meta['icon'] }}</span>
    <span>{{ $resolvedLabel }}</span>
</span>
