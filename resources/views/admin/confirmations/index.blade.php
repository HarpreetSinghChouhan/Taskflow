<x-layouts.dashboard title="Completed Tasks">

    @php $hoursText = fn ($h) => rtrim(rtrim(number_format((float) $h, 2), '0'), '.') ?: '0'; @endphp

    <div class="mb-4">
        <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">Completed Tasks</h3>
        <p class="text-muted fs-13 mb-0">Baseline IT Development &bull; Tasks the project manager marked complete. Confirm them here.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 fs-13">{{ $errors->first() }}</div>
    @endif

    <x-card title="Waiting for Your Confirmation ({{ $pending->count() }})" icon="bi-patch-check" class="mb-4">
        @forelse ($pending as $task)
            @php
                $submitted = $task->reviewEvents->where('type', 'submitted')->last();
                $completed = $task->reviewEvents->where('type', 'pm_completed')->last();
            @endphp
            <div class="border rounded-3 p-3 mb-3">
                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <div>
                        <a href="{{ route('admin.tasks.show', $task) }}" class="fw-semibold text-navy text-decoration-none">{{ $task->title }}</a>
                        <div class="fs-12 text-muted">
                            Project: {{ $task->project?->name }} &bull; PM: {{ $task->project?->manager?->name }}
                            &bull; Team leader: {{ $task->assignee?->name }} &bull; Employee: {{ $task->employee?->name ?? '—' }}
                        </div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary align-self-start px-3 py-2">
                        {{ $hoursText($task->logged_hours) }} h logged / {{ $hoursText($task->approvedHours()) }} h approved
                    </span>
                </div>

                @if ($submitted)
                    <div class="fs-12 text-muted mt-3">Employee's report</div>
                    <div class="fs-13 text-navy" style="white-space: pre-line;">{{ $submitted->body }}</div>
                @endif
                @if ($completed?->body)
                    <div class="fs-12 text-muted mt-2">PM comment</div>
                    <div class="fs-13 text-navy" style="white-space: pre-line;">{{ $completed->body }}</div>
                @endif

                <form method="POST" action="{{ route('admin.confirmations.confirm', $task) }}" class="row g-2 mt-2">
                    @csrf
                    <div class="col-md-8">
                        <input type="text" name="note" class="form-control form-control-sm" placeholder="Note (optional)">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-sm btn-success w-100">
                            <i class="bi bi-shield-check me-1"></i> Confirm
                        </button>
                    </div>
                </form>
            </div>
        @empty
            <p class="text-muted fs-13 mb-0">Nothing is waiting for confirmation.</p>
        @endforelse
    </x-card>

    <x-card title="Recently Confirmed" icon="bi-clock-history" noBodyPadding="true">
        <div class="table-responsive">
            <table class="dash-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Project</th>
                        <th>Employee</th>
                        <th>Hours</th>
                        <th>Confirmed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($confirmed as $task)
                        @php $c = $task->reviewEvents->where('type', 'admin_confirmed')->last(); @endphp
                        <tr>
                            <td class="fs-13">
                                <a href="{{ route('admin.tasks.show', $task) }}" class="fw-semibold text-navy text-decoration-none">{{ $task->title }}</a>
                            </td>
                            <td class="fs-13 text-secondary">{{ $task->project?->name }}</td>
                            <td class="fs-13 text-secondary">{{ $task->employee?->name ?? '—' }}</td>
                            <td class="fs-13">{{ $hoursText($task->logged_hours) }} h</td>
                            <td class="fs-12 text-muted">{{ $c?->created_at->format('M d, Y') }} &bull; {{ $c?->user?->name }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted fs-13 py-4">No confirmations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

</x-layouts.dashboard>