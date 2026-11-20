<x-layouts.dashboard title="My Team">

    @php
        $statusMeta = [
            'todo'        => ['Pending',     '#f1f5f9', '#475569'],
            'in_progress' => ['In Progress', '#eff6ff', '#2563eb'],
            'in_review'   => ['In Review',   '#fffbeb', '#b45309'],
            'done'        => ['Done',        '#ecfdf5', '#047857'],
        ];
    @endphp

    <div class="mb-4">
        <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">My Team</h3>
        <p class="text-muted fs-13 mb-0">Baseline IT Development &bull; Your team leader, team members and team tasks</p>
    </div>

    @if (! $leader)
        <div class="alert alert-info border-0 shadow-sm rounded-3">You are not assigned to a team leader yet.</div>
    @else
        {{-- Team leader --}}
        <x-card title="Team Leader" icon="bi-diagram-3-fill" class="mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                     style="width: 48px; height: 48px; background: #fffbeb; color: #b45309;">
                    {{ strtoupper(substr($leader->name, 0, 1)) }}
                </div>
                <div>
                    <div class="fw-semibold text-navy">{{ $leader->name }}</div>
                    <small class="text-muted">{{ $leader->designation ?? 'Team Leader' }} &bull; {{ $leader->email }}</small>
                </div>
            </div>
        </x-card>

        {{-- Team members --}}
        <x-card title="Team Members ({{ $teammates->count() }})" icon="bi-people-fill" noBodyPadding="true" class="mb-4">
            <div class="table-responsive">
                <table class="dash-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Designation</th>
                            <th>Pending</th>
                            <th>In Progress</th>
                            <th>In Review</th>
                            <th>Done</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($teammates as $member)
                            <tr>
                                <td class="fw-semibold text-navy fs-13">
                                    {{ $member->name }}
                                    @if ($member->id === auth()->id())
                                        <span class="badge bg-secondary-subtle text-secondary fs-11 ms-1">You</span>
                                    @endif
                                </td>
                                <td class="fs-13 text-secondary">{{ $member->designation ?? '—' }}</td>
                                <td class="fs-13">{{ $member->pending_count }}</td>
                                <td class="fs-13">{{ $member->in_progress_count }}</td>
                                <td class="fs-13">{{ $member->in_review_count }}</td>
                                <td class="fs-13">{{ $member->done_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Team tasks (read-only) --}}
        <x-card title="Team Tasks" icon="bi-list-task" noBodyPadding="true">
            <div class="table-responsive">
                <table class="dash-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 32%;">Task</th>
                            <th>Project</th>
                            <th>Priority</th>
                            <th>Employee</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teamTasks as $task)
                            @php [$sLabel, $sBg, $sColor] = $statusMeta[$task->status] ?? $statusMeta['todo']; @endphp
                            <tr>
                                <td class="fw-semibold text-navy fs-13">{{ $task->title }}</td>
                                <td class="fs-13 text-secondary">{{ $task->project?->name ?? '—' }}</td>
                                <td class="fs-13 text-capitalize">{{ $task->priority }}</td>
                                <td class="fs-13 text-secondary">{{ $task->employee?->name ?? 'Not assigned' }}</td>
                                <td>
                                    <span class="badge rounded-pill px-2 py-1" style="background: {{ $sBg }}; color: {{ $sColor }}; font-size: 12px;">{{ $sLabel }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted fs-13 py-4">No team tasks yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($teamTasks->hasPages())
                <div class="p-3 border-top d-flex justify-content-center">{{ $teamTasks->links() }}</div>
            @endif
        </x-card>
    @endif

</x-layouts.dashboard>