@props([
    'label',
    'value',
    'icon' => 'bi-activity',
    'color' => 'primary',
    'trend' => null,
    'footer' => null,
    'valueClass' => null,
])

@php
    $colorMaps = [
        'primary' => ['bg' => 'rgba(37, 99, 235, 0.08)',  'text' => '#2563EB'],
        'success' => ['bg' => 'rgba(22, 163, 74, 0.08)',  'text' => '#16A34A'],
        'warning' => ['bg' => 'rgba(217, 119, 6, 0.08)',  'text' => '#D97706'],
        'danger'  => ['bg' => 'rgba(220, 38, 38, 0.08)',  'text' => '#DC2626'],
        'indigo'  => ['bg' => 'rgba(79, 70, 229, 0.08)',  'text' => '#4F46E5'],
        'teal'    => ['bg' => 'rgba(13, 148, 136, 0.08)', 'text' => '#0D9488'],
        'sky'     => ['bg' => 'rgba(2, 132, 199, 0.08)',  'text' => '#0284C7'],
    ];
    $theme = $colorMaps[$color] ?? $colorMaps['primary'];
@endphp

<div class="dash-stat-card">
    <div class="stat-header">
        <span class="stat-label">{{ $label }}</span>
        <span class="stat-icon" style="background: {{ $theme['bg'] }}; color: {{ $theme['text'] }};">
            <i class="bi {{ $icon }}"></i>
        </span>
    </div>
    <div class="stat-value {{ $valueClass }}">{{ $value }}</div>
    <div class="stat-footer">
        @if ($trend)
            <span class="text-success fw-semibold"><i class="bi bi-arrow-up-right me-1"></i>{{ $trend }}</span>
        @endif
        @if ($footer)
            <span>{{ $footer }}</span>
        @endif
    </div>
</div>
