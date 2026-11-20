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
        <a href="{{ route('employee.tasks.index') }}" class="text-decoration-none text-muted fs-13 d-inline-flex align-items-center gap-1 mb-2">
            <i class="bi bi-arrow-left"></i> Back to My Tasks
        </a>
        <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">{{ $task->title }}</h3>
        <p class="text-muted fs-13 mb-0">Project: {{ $task->project?->name ?? '—' }}</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
            <ul class="mb-0 ps-3 fs-13">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">

            {{-- Add update --}}
            <x-card title="Add Progress Update" icon="bi-pencil-square" class="mb-4">
                @if ($task->status === 'done')
                    <p class="text-muted fs-13 mb-0">This task is done, so no more updates can be added.</p>
                @else
                    <form method="POST" action="{{ route('employee.tasks.logs.store', $task) }}">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="log_date" class="form-label fw-semibold text-navy fs-13">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('log_date') is-invalid @enderror" id="log_date" name="log_date"
                                       value="{{ old('log_date', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="hours" class="form-label fw-semibold text-navy fs-13">Hours spent <span class="text-danger">*</span></label>
                                <input type="number" step="0.25" min="0" max="24" class="form-control @error('hours') is-invalid @enderror" id="hours" name="hours"
                                       placeholder="e.g. 2.5" value="{{ old('hours') }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="note" class="form-label fw-semibold text-navy fs-13">What did you work on? <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3" required>{{ old('note') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3" style="background: var(--dash-brand); border: none;">
                            <i class="bi bi-plus-lg me-1"></i> Add Update
                        </button>
                    </form>
                @endif
            </x-card>

            {{-- Updates list --}}
            <x-card title="Updates ({{ $task->logs->count() }})" icon="bi-clock-history">
                @forelse ($task->logs as $log)
                    <div class="d-flex gap-3 py-3 {{ ! $loop->last ? 'border-bottom' : '' }}">
                        <span class="badge bg-primary-subtle text-primary align-self-start px-2 py-2">{{ $hoursText($log->hours) }} h</span>
                        <div>
                            <div class="fs-12 text-muted">{{ $log->log_date->format('M d, Y') }} &bull; {{ $log->user?->name }}</div>
                            <div class="fs-13 text-navy" style="white-space: pre-line;">{{ $log->note }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted fs-13 mb-0">No updates yet.</p>
                @endforelse
            </x-card>
        </div>

        <div class="col-lg-4">
            <x-card title="Task Details" icon="bi-info-circle-fill">
                <ul class="list-group list-group-flush fs-13">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Status</span>
                        @if ($task->status === 'done')
                            <span class="badge rounded-pill" style="background: {{ $sBg }}; color: {{ $sColor }};">{{ $sLabel }}</span>
                        @else
                            <form method="POST" action="{{ route('employee.tasks.status', $task) }}">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    @foreach (['todo', 'in_progress', 'in_review'] as $s)
                                        <option value="{{ $s }}" {{ $task->status === $s ? 'selected' : '' }}>{{ $statusMeta[$s][0] }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @endif
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
                @if ($task->status === 'done')
                    <div class="alert alert-success border-0 fs-12 mt-3 mb-0">This task is done and locked.</div>
                @endif
            </x-card>
        </div>
    </div>

</x-layouts.dashboard>