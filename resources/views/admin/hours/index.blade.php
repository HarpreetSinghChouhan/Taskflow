<x-layouts.dashboard title="Hour Requests">

    <div class="mb-4">
        <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">Hour Requests</h3>
        <p class="text-muted fs-13 mb-0">Baseline IT Development &bull; Project managers ask for task hours here. Maximum 40 hours at a time.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 fs-13">{{ $errors->first() }}</div>
    @endif

    <x-card title="Pending Requests ({{ $pending->count() }})" icon="bi-hourglass-split" class="mb-4">
        @forelse ($pending as $a)
            <div class="border rounded-3 p-3 mb-3">
                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <div>
                        <div class="fw-semibold text-navy">{{ $a->task->title }}</div>
                        <div class="fs-12 text-muted">
                            Project: {{ $a->task->project?->name }} &bull; PM: {{ $a->requester?->name }}
                            &bull; Employee: {{ $a->task->employee?->name ?? 'Not assigned' }}
                        </div>
                    </div>
                    <span class="badge bg-warning-subtle text-warning align-self-start px-3 py-2">{{ (float) $a->requested_hours }} h requested</span>
                </div>
                <div class="fs-13 mt-2">Reason: {{ $a->note }}</div>
                <div class="fs-12 text-muted mt-1">
                    Already approved: {{ $a->task->approvedHours() }} h &bull; Logged so far: {{ $a->task->loggedHours() }} h
                </div>

                <form method="POST" action="{{ route('admin.hours.decide', $a) }}" class="row g-2 mt-2">
                    @csrf
                    @method('PATCH')
                    <div class="col-md-3">
                        <input type="number" name="approved_hours" step="0.25" min="0.25" max="40"
                               class="form-control form-control-sm" value="{{ min((float) $a->requested_hours, 40) }}">
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="admin_note" class="form-control form-control-sm" placeholder="Note (required when rejecting)">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" name="decision" value="approve" class="btn btn-sm btn-success flex-fill">Approve</button>
                        <button type="submit" name="decision" value="reject" class="btn btn-sm btn-outline-danger flex-fill">Reject</button>
                    </div>
                </form>
            </div>
        @empty
            <p class="text-muted fs-13 mb-0">No pending requests.</p>
        @endforelse
    </x-card>

    <x-card title="Recent Decisions" icon="bi-clock-history" noBodyPadding="true">
        <div class="table-responsive">
            <table class="dash-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Project</th>
                        <th>PM</th>
                        <th>Requested</th>
                        <th>Approved</th>
                        <th>Status</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($history as $a)
                        <tr>
                            <td class="fs-13 fw-semibold text-navy"><a href="{{ route('admin.tasks.show', $a->task) }}" class="fw-semibold text-navy text-decoration-none">{{ $a->task->title }}</a></td>
                            <td class="fs-13 text-secondary">{{ $a->task?->project?->name }}</td>
                            <td class="fs-13 text-secondary">{{ $a->requester?->name }}</td>
                            <td class="fs-13">{{ (float) $a->requested_hours }} h</td>
                            <td class="fs-13">{{ $a->approved_hours !== null ? (float) $a->approved_hours . ' h' : '—' }}</td>
                            <td>
                                <span class="badge {{ $a->status === 'approved' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} text-capitalize">{{ $a->status }}</span>
                            </td>
                            <td class="fs-12 text-muted">{{ $a->admin_note ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted fs-13 py-4">No decisions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

</x-layouts.dashboard>