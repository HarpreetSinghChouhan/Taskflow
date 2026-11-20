<x-layouts.dashboard title="Team Tasks">

    <div class="mb-4">
        <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">Team Tasks</h3>
        <p class="text-muted fs-13 mb-0">Baseline IT Development &bull; Tasks assigned to you. Give each one to a team member.</p>
    </div>

    {{-- Filters --}}
    <div class="dash-card mb-4 p-3">
        <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
            <div class="nav nav-pills gap-1 flex-wrap">
                <a href="{{ route('tl.tasks.index', request()->except('status', 'page')) }}" class="btn btn-sm {{ empty($statusFilter) ? 'btn-primary' : 'btn-light text-muted' }} rounded-pill px-3 fs-13">
                    All <span class="badge bg-white {{ empty($statusFilter) ? 'text-primary' : 'text-dark' }} ms-1">{{ $counts['all'] }}</span>
                </a>
                @foreach (['todo' => 'Pending', 'in_progress' => 'In Progress', 'in_review' => 'In Review', 'done' => 'Done'] as $key => $label)
                    <a href="{{ route('tl.tasks.index', array_merge(request()->except('status', 'page'), ['status' => $key])) }}" class="btn btn-sm {{ $statusFilter === $key ? 'btn-primary' : 'btn-light text-muted' }} rounded-pill px-3 fs-13">
                        {{ $label }}
                        <span class="badge bg-white {{ $statusFilter === $key ? 'text-primary' : 'text-dark' }} ms-1">{{ $counts[$key] }}</span>
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('tl.tasks.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                @if ($statusFilter)
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                @endif
                <div class="input-group input-group-sm" style="max-width: 200px;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search task..." value="{{ $search }}">
                </div>
                <select name="priority" class="form-select form-select-sm" style="max-width: 130px;" onchange="this.form.submit()">
                    <option value="">Priority</option>
                    @foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'] as $key => $label)
                        <option value="{{ $key }}" {{ $priorityFilter === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
                @if ($search || $statusFilter || $priorityFilter)
                    <a href="{{ route('tl.tasks.index') }}" class="btn btn-sm btn-outline-secondary" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
                @endif
            </form>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 fs-13">{{ $errors->first() }}</div>
    @endif

    {{-- Tasks Table --}}
    <x-card noBodyPadding="true">
        <div class="table-responsive">
            <table class="dash-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 26%;">Task</th>
                        <th>Project</th>
                        <th>Priority</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th style="width: 20%;">Assigned Employee</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        @php
                            $statusMeta = match($task->status) {
                                'todo'        => ['label' => 'Pending',     'bg' => '#f1f5f9', 'color' => '#475569'],
                                'in_progress' => ['label' => 'In Progress', 'bg' => '#eff6ff', 'color' => '#2563eb'],
                                'in_review'   => ['label' => 'In Review',   'bg' => '#fffbeb', 'color' => '#b45309'],
                                'done'        => ['label' => 'Done',        'bg' => '#ecfdf5', 'color' => '#047857'],
                                default       => ['label' => ucfirst($task->status), 'bg' => '#f1f5f9', 'color' => '#475569'],
                            };
                            $priorityMeta = match($task->priority) {
                                'low'    => ['label' => 'Low',    'bg' => '#f1f5f9', 'color' => '#475569'],
                                'medium' => ['label' => 'Medium', 'bg' => '#eff6ff', 'color' => '#2563eb'],
                                'high'   => ['label' => 'High',   'bg' => '#fff7ed', 'color' => '#c2410c'],
                                'urgent' => ['label' => 'Urgent', 'bg' => '#fef2f2', 'color' => '#b91c1c'],
                                default  => ['label' => ucfirst($task->priority), 'bg' => '#f1f5f9', 'color' => '#475569'],
                            };
                            $overdue = $task->due_date && $task->due_date->isPast() && $task->status !== 'done';
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('tl.tasks.show', $task) }}" class="fw-semibold text-navy text-truncate d-block text-decoration-none" style="font-size: 14px; max-width: 260px;">{{ $task->title }}</a>
                                @if ($task->description)
                                    <small class="text-muted d-block text-truncate fs-12" style="max-width: 260px;">{{ $task->description }}</small>
                                @endif
                            </td>
                            <td><span class="fs-13 text-secondary">{{ $task->project?->name ?? '—' }}</span></td>
                            <td>
                                <span class="badge rounded-pill px-2 py-1" style="background: {{ $priorityMeta['bg'] }}; color: {{ $priorityMeta['color'] }}; font-weight: 600; font-size: 12px; border: 1px solid rgba(0,0,0,0.06);">
                                    {{ $priorityMeta['label'] }}
                                </span>
                            </td>
                            <td>
                                <span class="fs-12 {{ $overdue ? 'text-danger fw-semibold' : 'text-muted' }}">
                                    {{ $task->due_date?->format('M d, Y') ?? '—' }}
                                    @if ($overdue)<i class="bi bi-exclamation-circle-fill ms-1" title="Overdue"></i>@endif
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2 py-1" style="background: {{ $statusMeta['bg'] }}; color: {{ $statusMeta['color'] }}; font-weight: 600; font-size: 12px; border: 1px solid rgba(0,0,0,0.06);">
                                    {{ $statusMeta['label'] }}
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('tl.tasks.assign', $task) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="employee_id" class="form-select form-select-sm" onchange="this.form.submit()" {{ $employees->isEmpty() ? 'disabled' : '' }}>
                                        <option value="">Unassigned</option>
                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->id }}" {{ (int) $task->employee_id === $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-list-task text-muted" style="font-size: 44px;"></i>
                                    <h6 class="fw-bold text-navy mt-3 mb-1">No Tasks Found</h6>
                                    <p class="text-muted fs-13 mb-0">No task is assigned to you yet, or your filters match nothing.</p>
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