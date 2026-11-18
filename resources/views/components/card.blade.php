@props([
    'title' => null,
    'icon' => null,
    'badge' => null,
    'action' => null,
    'noBodyPadding' => false,
])

<div {{ $attributes->merge(['class' => 'dash-card']) }}>
    @if ($title || $action || $badge)
        <div class="dash-card-header">
            @if ($title)
                <h5 class="dash-card-title">
                    @if ($icon)
                        <i class="bi {{ $icon }} text-primary"></i>
                    @endif
                    <span>{{ $title }}</span>
                </h5>
            @endif

            @if ($badge)
                <span class="badge px-2.5 py-1 fs-12 rounded-pill" style="background: #F1F5F9; color: #1E293B; border: 1px solid #CBD5E1; font-weight: 600;">{{ $badge }}</span>
            @endif

            @if ($action)
                <div>{{ $action }}</div>
            @endif
        </div>
    @endif

    <div class="{{ $noBodyPadding ? 'p-0' : 'dash-card-body' }}">
        {{ $slot }}
    </div>
</div>
