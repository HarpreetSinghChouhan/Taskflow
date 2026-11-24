<x-layouts.dashboard title="Project Progress">

    @php
        $statusMeta = [
            'todo'        => ['Pending',     '#f1f5f9', '#475569'],
            'in_progress' => ['In Progress', '#eff6ff', '#2563eb'],
            'in_review'   => ['In Review',   '#fffbeb', '#b45309'],
            'done'        => ['Done',        '#ecfdf5', '#047857'],
        ];
        $cards = [
            ['Progress',        $percent . '%',        'bi-graph-up-arrow',    '#ecfdf5', '#059669'],
            ['Tasks Done',      $counts['done'] . ' / ' . $total, 'bi-check2-square', '#eff6ff', '#2563eb'],
            ['Total Hours',     $totalHours . ' h',    'bi-clock-history',     '#eef2ff', '#4f46e5'],
            ['Overdue Tasks',   $overdue,              'bi-exclamation-circle', '#fef2f2', '#dc2626'],
        ];
    @endphp


    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3 mb-4">
        <div>
            <a href="{{ route('admin.projects.index') }}" class="text-decoration-none text-muted fs-13 d-inline-flex align-items-center gap-1 mb-2">
                <i class="bi bi-arrow-left"></i> Back to Projects
            </a>
            <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">{{ $project->name }}</h3>
            <p class="text-muted fs-13 mb-0">
                Manager: {{ $project->manager?->name ?? '—' }} &bull;
                {{ $project->start_date?->format('M d, Y') ?? '—' }} to {{ $project->deadline?->format('M d, Y') ?? '—' }} &bull;
                <span class="text-capitalize">{{ str_replace('_', ' ', $project->status) }}</span>
            </p>
        </div>
        <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary px-3">
            <i class="bi bi-pencil-square me-1"></i> Edit Project
        </a>
    </div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">
        @foreach ($cards as [$label, $value, $icon, $bg, $color])
            <div class="col-6 col-xl-3">
                <div class="dash-card p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: {{ $bg }}; color: {{ $color }}; font-size: 20px;">
                        <i class="bi {{ $icon }}"></i>
                    </div>
                    <div>
                        <div class="fs-12 text-muted fw-medium">{{ $label }}</div>
                        <div class="fs-20 fw-bold" style="color: {{ $color }};">{{ $value }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Status breakdown --}}
    <div class="dash-card p-3 mb-4">
        <div class="progress mb-2" style="height: 10px;">
            @foreach ($statusMeta as $key => [$label, $bg, $color])
                @if ($total && $counts[$key])
                    <div class="progress-bar" style="width: {{ $counts[$key] / $total * 100 }}%; background: {{ $color }};" title="{{ $label }}"></div>
                @endif
            @endforeach
        </div>
        <div class="d-flex flex-wrap gap-3 fs-12">
            @foreach ($statusMeta as $key => [$label, $bg, $color])
                <span><i class="bi bi-circle-fill me-1" style="color: {{ $color }}; font-size: 8px;"></i>{{ $label }}: <strong>{{ $counts[$key] }}</strong></span>
            @endforeach
        </div>
    </div>

    {{-- Tasks --}}
    <x-card title="Tasks" icon="bi-list-task" noBodyPadding="true" class="mb-4">
        <div class="table-responsive">
            <table class="dash-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 28%;">Task</th>
                        <th>Team Leader</th>
                        <th>Employee</th>
                        <th>Priority</th>
                        <th>Due</th>
                        <th>Status</th>
                        <th class="text-end">Hours</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        @php
                            [$sLabel, $sBg, $sColor] = $statusMeta[$task->status] ?? $statusMeta['todo'];
                            $late = $task->due_date && $task->due_date->isPast() && $task->status !== 'done';
                        @endphp
                        <tr>
                            <td class="fw-semibold text-navy fs-13">{{ $task->title }}</td>
                            <td class="fs-13 text-secondary">{{ $task->assignee?->name ?? '—' }}</td>
                            <td class="fs-13 text-secondary">{{ $task->employee?->name ?? 'Not assigned' }}</td>
                            <td class="fs-13 text-capitalize">{{ $task->priority }}</td>
                            <td class="fs-12 {{ $late ? 'text-danger fw-semibold' : 'text-muted' }}">{{ $task->due_date?->format('M d, Y') ?? '—' }}</td>
                            <td><span class="badge rounded-pill px-2 py-1" style="background: {{ $sBg }}; color: {{ $sColor }}; font-size: 12px;">{{ $sLabel }}</span></td>
                            <td class="text-end fs-13 fw-semibold">{{ (float) $task->logged_hours }} h</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted fs-13 py-4">No tasks in this project yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="row g-4">
        {{-- Hours per person --}}
        <div class="col-lg-6">
            <x-card title="Hours by Person" icon="bi-people-fill" noBodyPadding="true">
                <div class="table-responsive">
                    <table class="dash-table align-middle mb-0">
                        <thead>
                            <tr><th>Person</th><th>Updates</th><th>Last update</th><th class="text-end">Hours</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($people as $row)
                                <tr>
                                    <td class="fs-13">
                                        <span class="fw-semibold text-navy">{{ $row->user?->name ?? '—' }}</span>
                                        <small class="text-muted text-capitalize d-block fs-12">{{ str_replace('_', ' ', $row->user?->role ?? '') }}</small>
                                    </td>
                                    <td class="fs-13">{{ $row->entries }}</td>
                                    <td class="fs-12 text-muted">{{ \Illuminate\Support\Carbon::parse($row->last_date)->format('M d, Y') }}</td>
                                    <td class="text-end fs-13 fw-semibold">{{ (float) $row->hours }} h</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted fs-13 py-4">No hours logged yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        {{-- Recent updates --}}
        <div class="col-lg-6">
            <x-card title="Recent Updates" icon="bi-clock-history">
                @forelse ($recentLogs as $log)
                    <div class="d-flex gap-3 py-2 {{ ! $loop->last ? 'border-bottom' : '' }}">
                        <span class="badge bg-primary-subtle text-primary align-self-start px-2 py-2">{{ (float) $log->hours }} h</span>
                        <div>
                            <div class="fs-12 text-muted">{{ $log->log_date->format('M d, Y') }} &bull; {{ $log->user?->name }} &bull; {{ $log->task?->title }}</div>
                            <div class="fs-13 text-navy" style="white-space: pre-line;">{{ $log->note }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted fs-13 mb-0">No updates yet.</p>
                @endforelse
            </x-card>
        </div>
    </div>

</x-layouts.dashboard>