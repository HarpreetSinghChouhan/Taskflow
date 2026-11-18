@props([
    'status' => 'active',
    'text' => null,
])

@php
    $statusMap = [
        'active'      => ['class' => 'status-active',      'default' => 'Active'],
        'completed'   => ['class' => 'status-completed',   'default' => 'Completed'],
        'in_progress' => ['class' => 'status-in-progress', 'default' => 'In Progress'],
        'in_review'   => ['class' => 'status-in-review',   'default' => 'In Review'],
        'todo'        => ['class' => 'status-todo',        'default' => 'To Do'],
        'overdue'     => ['class' => 'status-overdue',     'default' => 'Overdue'],
        'planning'    => ['class' => 'status-planning',    'default' => 'Planning'],
    ];

    $meta = $statusMap[$status] ?? ['class' => 'status-todo', 'default' => ucfirst($status)];
    $displayText = $text ?? $meta['default'];
@endphp

<span class="badge-status-pill {{ $meta['class'] }}">
    {{ $displayText }}
</span>
