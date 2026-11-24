@php
    $typeMeta = [
        'submitted'       => ['Submitted for review',            'bi-send-check',        '#2563eb'],
        'tl_approved'     => ['Team leader approved',            'bi-check2-circle',     '#047857'],
        'tl_returned'     => ['Returned by team leader',         'bi-arrow-return-left', '#b45309'],
        'pm_question'     => ['PM asked why it is not complete', 'bi-question-circle',   '#b45309'],
        'employee_reason' => ['Employee reason',                 'bi-chat-left-text',    '#4f46e5'],
        'pm_completed'    => ['Completed by project manager',    'bi-patch-check',       '#047857'],
        'pm_returned'     => ['Returned by project manager',     'bi-arrow-return-left', '#dc2626'],
        'admin_confirmed' => ['Confirmed by admin',                'bi-shield-check',      '#047857'],
    ];
    $stageText = [
        'tl'       => 'Waiting for the team leader to check',
        'pm'       => 'Waiting for the project manager to verify',
        'employee' => 'Waiting for the employee to give a reason',
    ];
@endphp

<x-card title="Review Timeline" icon="bi-diagram-3" class="mb-4">
    @if ($task->status === 'in_review' && $task->review_stage)
        <div class="alert alert-info border-0 fs-13 py-2">{{ $stageText[$task->review_stage] ?? '' }}</div>
    @endif

    @forelse ($events as $event)
        @php [$label, $icon, $color] = $typeMeta[$event->type] ?? [$event->type, 'bi-dot', '#475569']; @endphp
        <div class="d-flex gap-3 py-3 {{ ! $loop->last ? 'border-bottom' : '' }}">
            <i class="bi {{ $icon }} fs-5" style="color: {{ $color }};"></i>
            <div>
                <div class="fs-13 fw-semibold" style="color: {{ $color }};">{{ $label }}</div>
                <div class="fs-12 text-muted">{{ $event->user?->name }} &bull; {{ $event->created_at->format('M d, Y h:i A') }}</div>
                @if ($event->body)
                    <div class="fs-13 text-navy mt-1" style="white-space: pre-line;">{{ $event->body }}</div>
                @endif
            </div>
        </div>
    @empty
        <p class="text-muted fs-13 mb-0">Nothing has been sent for review yet.</p>
    @endforelse
</x-card>