<x-layouts.dashboard title="My Tasks">

    @php
        $statusMeta = [
            'todo'        => ['Pending',     '#f1f5f9', '#475569'],
            'in_progress' => ['In Progress', '#eff6ff', '#2563eb'],
            'in_review'   => ['In Review',   '#fffbeb', '#b45309'],
            'done'        => ['Done',        '#ecfdf5', '#047857'],
        ];
        $priorityMeta = [
            'low'    => ['Low',    '#f1f5f9', '#475569'],
            'medium' => ['Medium', '#eff6ff', '#2563eb'],
            'high'   => ['High',   '#fff7ed', '#c2410c'],
            'urgent' => ['Urgent', '#fef2f2', '#b91c1c'],
        ];
    @endphp

    <div class="mb-4">
        <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">My Assigned Tasks</h3>
        <p class="text-muted fs-13 mb-0">Baseline IT Development &bull; Tasks your team leader gave you</p>
    </div>

    <div class="dash-card mb-4 p-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="nav nav-pills gap-1 flex-wrap">
                <a href="{{ route('employee.tasks.index') }}" class="btn btn-sm {{ empty($statusFilter) ? 'btn-primary' : 'btn-light text-muted' }} rounded-pill px-3 fs-13">
                    All <span class="badge bg-white {{ empty($statusFilter) ? 'text-primary' : 'text-dark' }} ms-1">{{ $counts['all'] }}</span>
                </a>
                @foreach ($statusMeta as $key => $meta)
                    <a href="{{ route('employee.tasks.index', ['status' => $key]) }}" class="btn btn-sm {{ $statusFilter === $key ? 'btn-primary' : 'btn-light text-muted' }} rounded-pill px-3 fs-13">
                        {{ $meta[0] }}
                        <span class="badge bg-white {{ $statusFilter === $key ? 'text-primary' : 'text-dark' }} ms-1">{{ $counts[$key] }}</span>
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('employee.tasks.index') }}" class="d-flex align-items-center gap-2">
                @if ($statusFilter)
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                @endif
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search task..." value="{{ $search }}">
                </div>
                <button type="submit" class="btn btn-sm btn-outline-primary">Search</button>
                @if ($search || $statusFilter)
                    <a href="{{ route('employee.tasks.index') }}" class="btn btn-sm btn-outline-secondary" title="Clear"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>
    </div>

    <x-card noBodyPadding="true">
        <div class="table-responsive">
            <table class="dash-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 28%;">Task</th>
                        <th>Project</th>
                        <th>Priority</th>
                        <th>Due Date</th>
                        <th>Hours</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        @php
                            [$sLabel, $sBg, $sColor] = $statusMeta[$task->status] ?? $statusMeta['todo'];
                            [$pLabel, $pBg, $pColor] = $priorityMeta[$task->priority] ?? $priorityMeta['medium'];
                            $overdue = $task->due_date && $task->due_date->isPast() && $task->status !== 'done';
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold text-navy text-truncate" style="font-size: 14px; max-width: 270px;">{{ $task->title }}</div>
                                @if ($task->description)
                                    <small class="text-muted d-block text-truncate fs-12" style="max-width: 270px;">{{ $task->description }}</small>
                                @endif
                            </td>
                            <td><span class="fs-13 text-secondary">{{ $task->project?->name ?? '—' }}</span></td>
                            <td>
                                <span class="badge rounded-pill px-2 py-1" style="background: {{ $pBg }}; color: {{ $pColor }}; font-size: 12px; border: 1px solid rgba(0,0,0,0.06);">{{ $pLabel }}</span>
                            </td>
                            <td>
                                <span class="fs-12 {{ $overdue ? 'text-danger fw-semibold' : 'text-muted' }}">
                                    {{ $task->due_date?->format('M d, Y') ?? '—' }}
                                    @if ($overdue)<i class="bi bi-exclamation-circle-fill ms-1" title="Overdue"></i>@endif
                                </span>
                            </td>
                            <td><span class="fs-13 text-secondary">{{ rtrim(rtrim(number_format((float) $task->logged_hours, 2), '0'), '.') ?: '0' }} h</span></td>
                            <td>
                                <span class="badge rounded-pill px-2 py-1" style="background: {{ $sBg }}; color: {{ $sColor }}; font-size: 12px; border: 1px solid rgba(0,0,0,0.06);">{{ $sLabel }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('employee.tasks.show', $task) }}" class="btn btn-sm btn-outline-primary px-3 py-1 fs-12 rounded-2">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-check2-square text-muted" style="font-size: 44px;"></i>
                                    <h6 class="fw-bold text-navy mt-3 mb-1">No Tasks Found</h6>
                                    <p class="text-muted fs-13 mb-0">Your team leader has not given you a task yet, or your filters match nothing.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tasks->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">{{ $tasks->links() }}</div>
        @endif
    </x-card>

</x-layouts.dashboard>