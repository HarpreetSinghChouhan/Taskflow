<x-layouts.dashboard title="Task Details">

    @php
        $statusMeta = [
            'todo'        => ['Pending',     '#f1f5f9', '#475569'],
            'in_progress' => ['In Progress', '#eff6ff', '#2563eb'],
            'in_review'   => ['In Review',   '#fffbeb', '#b45309'],
            'done'        => ['Done',        '#ecfdf5', '#047857'],
        ];
        [$sLabel, $sBg, $sColor] = $statusMeta[$task->status] ?? $statusMeta['todo'];
        $hoursText = fn ($h) => rtrim(rtrim(number_format((float) $h, 2), '0'), '.') ?: '0';
    @endphp

    <div class="mb-4">
        <a href="{{ $backUrl }}" class="text-decoration-none text-muted fs-13 d-inline-flex align-items-center gap-1 mb-2">
            <i class="bi bi-arrow-left"></i> {{ $backLabel }}
        </a>
        <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">{{ $task->title }}</h3>
        <p class="text-muted fs-13 mb-0">Project: {{ $task->project?->name ?? '—' }}</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <x-card title="Progress Updates ({{ $task->logs->count() }})" icon="bi-clock-history">
                @forelse ($task->logs as $log)
                    <div class="d-flex gap-3 py-3 {{ ! $loop->last ? 'border-bottom' : '' }}">
                        <span class="badge bg-primary-subtle text-primary align-self-start px-2 py-2">{{ $hoursText($log->hours) }} h</span>
                        <div>
                            <div class="fs-12 text-muted">{{ $log->log_date->format('M d, Y') }} &bull; {{ $log->user?->name }}</div>
                            <div class="fs-13 text-navy" style="white-space: pre-line;">{{ $log->note }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted fs-13 mb-0">No progress updates yet.</p>
                @endforelse
            </x-card>
        </div>

        <div class="col-lg-4">
            <x-card title="Task Details" icon="bi-info-circle-fill">
                <ul class="list-group list-group-flush fs-13">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Status</span>
                        <span class="badge rounded-pill" style="background: {{ $sBg }}; color: {{ $sColor }};">{{ $sLabel }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Priority</span>
                        <span class="fw-semibold text-capitalize">{{ $task->priority }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Due date</span>
                        <span>{{ $task->due_date?->format('M d, Y') ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Team leader</span>
                        <span>{{ $task->assignee?->name ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Employee</span>
                        <span>{{ $task->employee?->name ?? 'Not assigned yet' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Project manager</span>
                        <span>{{ $task->project?->manager?->name ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Total hours</span>
                        <span class="fw-semibold">{{ $hoursText($totalHours) }} h</span>
                    </li>
                </ul>
                @if ($task->description)
                    <div class="mt-3 fs-13 text-secondary" style="white-space: pre-line;">{{ $task->description }}</div>
                @endif
            </x-card>
        </div>
    </div>

</x-layouts.dashboard>