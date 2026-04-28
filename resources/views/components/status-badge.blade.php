@props([
    'status' => null,
    'label' => null,
    'size' => 'default',
])

@php
    $rawStatus = strtolower(trim((string) ($status ?? 'unknown')));
    $normalized = str_replace([' ', '-'], '_', $rawStatus);

    $meta = match ($normalized) {
        'approved' => ['class' => 'wl-status-approved', 'label' => 'Approved'],
        'submitted' => ['class' => 'wl-status-submitted', 'label' => 'Submitted'],
        'pending' => ['class' => 'wl-status-pending', 'label' => 'Pending'],
        'draft' => ['class' => 'wl-status-draft', 'label' => 'Draft'],
        'rejected', 'declined' => ['class' => 'wl-status-rejected', 'label' => 'Rejected'],
        'missing', 'overdue', 'missing_overdue' => ['class' => 'wl-status-missing', 'label' => 'Overdue'],
        'active' => ['class' => 'wl-status-active', 'label' => 'Active'],
        'inactive' => ['class' => 'wl-status-inactive', 'label' => 'Inactive'],
        'complete', 'completed' => ['class' => 'wl-status-complete', 'label' => 'Done'],
        'in_progress' => ['class' => 'wl-status-info', 'label' => 'In Progress'],
        'incomplete' => ['class' => 'wl-status-incomplete', 'label' => 'Incomplete'],
        'unassigned', 'not_assigned' => ['class' => 'wl-status-unassigned', 'label' => 'Not Assigned'],
        'read' => ['class' => 'wl-status-read', 'label' => 'Read'],
        'unread' => ['class' => 'wl-status-info', 'label' => 'Unread'],
        'on_track' => ['class' => 'wl-status-on_track', 'label' => 'On Track'],
        'review', 'under_review' => ['class' => 'wl-status-info', 'label' => 'Under Review'],
        default => ['class' => 'wl-status-info', 'label' => ucfirst(str_replace('_', ' ', $normalized ?: 'status'))],
    };

    $resolvedLabel = $label ?: $meta['label'];
    $sizeClass = $size === 'sm' ? 'px-2.5 py-1 text-[11px]' : ($size === 'lg' ? 'px-3.5 py-1.5 text-sm' : '');
@endphp

<span {{ $attributes->merge(['class' => trim("wl-status-badge {$meta['class']} {$sizeClass}")]) }}>
    <span>{{ $resolvedLabel }}</span>
</span>
