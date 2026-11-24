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
        $allotments = $task->hourAllotments()->with('decider')->latest('id')->get();
        $approvedHours = $task->approvedHours();
        $remainingHours = $task->remainingHours();
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
            <x-card title="Allotted Hours" icon="bi-hourglass-split" class="mb-4">
                <div class="d-flex flex-wrap gap-4 mb-3 fs-13">
                    <div><span class="text-muted">Approved:</span> <strong>{{ $approvedHours }} h</strong></div>
                    <div><span class="text-muted">Logged:</span> <strong>{{ (float) $totalHours }} h</strong></div>
                    <div><span class="text-muted">Remaining:</span> <strong>{{ $remainingHours }} h</strong></div>
                </div>
                @foreach ($allotments as $a)
                    @php
                        $badge = ['pending' => 'bg-warning-subtle text-warning', 'approved' => 'bg-success-subtle text-success', 'rejected' => 'bg-danger-subtle text-danger'][$a->status];
                    @endphp
                    <div class="border rounded-3 p-2 mb-2 fs-13">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>
                                Requested <strong>{{ (float) $a->requested_hours }} h</strong>
                                @if ($a->status === 'approved') &bull; Approved <strong>{{ (float) $a->approved_hours }} h</strong>@endif
                            </span>
                            <span class="badge {{ $badge }} text-capitalize">{{ $a->status }}</span>
                        </div>
                        <div class="text-muted fs-12">PM note: {{ $a->note }}</div>
                        @if ($a->admin_note)
                            <div class="text-muted fs-12">Admin note: {{ $a->admin_note }}</div>
                        @endif
                    </div>
                @endforeach

                @can('requestHours', $task)
                    @if ($task->status !== 'done' && $task->canRequestHours())
                        <form method="POST" action="{{ route('pm.tasks.hours.store', $task) }}" class="row g-2 mt-2">
                        @csrf
                        <div class="col-md-3">
                            <input type="number" name="requested_hours" step="0.25" min="0.25" max="40"
                                    class="form-control form-control-sm @error('requested_hours') is-invalid @enderror"
                                    placeholder="Hours (max 40)" value="{{ old('requested_hours') }}" required>
                                 @error('requested_hours') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="note" class="form-control form-control-sm @error('note') is-invalid @enderror"
                                placeholder="Why are these hours needed?" value="{{ old('note') }}" required>
                                @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-sm btn-primary w-100">Request Hours</button>
                        </div>
                    </form>
                @elseif ($task->status !== 'done')
                    <p class="text-muted fs-12 mb-0 mt-2">You can ask for more hours once no request is pending and the approved hours are fully used.</p>
                @endif
            @endcan
            </x-card>
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
            {{-- Review timeline --}}
            @can('reviewAsLeader', $task)
                <x-card title="Your Review" icon="bi-clipboard-check" class="mb-4">
                    <p class="text-muted fs-12">Check the employee's work. If it looks right, send it to the project manager. Otherwise return it with a comment.</p>
                    <form method="POST" action="{{ route('tl.tasks.review', $task) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="body" class="form-label fw-semibold text-navy fs-13">
                                Comment <small class="text-muted fw-normal">(required when returning)</small>
                            </label>
                            <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="3">{{ old('body') }}</textarea>
                            @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" name="decision" value="approve" class="btn btn-success px-4">
                                <i class="bi bi-check2-circle me-1"></i> Approve and send to PM
                            </button>
                            <button type="submit" name="decision" value="return" class="btn btn-outline-danger px-4">
                                <i class="bi bi-arrow-return-left me-1"></i> Return to employee
                            </button>
                        </div>
                    </form>
                </x-card>
            @endcan
            @can('reviewAsManager', $task)
                <x-card title="Verify Completion" icon="bi-patch-check" class="mb-4">
                    <p class="text-muted fs-12">The team leader has approved this work. Check whether the task is really complete.</p>
                    <form method="POST" action="{{ route('pm.tasks.review', $task) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="pm_body" class="form-label fw-semibold text-navy fs-13">
                                Comment <small class="text-muted fw-normal">(required only when returning)</small>
                            </label>
                            <textarea class="form-control @error('body') is-invalid @enderror" id="pm_body" name="body" rows="3">{{ old('body') }}</textarea>
                            @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" name="decision" value="complete" class="btn btn-success px-4"
                                onclick="return confirm('Mark this task complete and confirm to the admin?')">
                                <i class="bi bi-patch-check me-1"></i> Complete and confirm to admin
                            </button>
                            <button type="submit" name="decision" value="question" class="btn btn-outline-warning px-4">
                                <i class="bi bi-question-circle me-1"></i> Ask why it is not complete
                            </button>
                            <button type="submit" name="decision" value="return" class="btn btn-outline-danger px-4">
                                <i class="bi bi-arrow-return-left me-1"></i> Return to employee
                            </button>
                        </div>
                    </form>
                </x-card>
            @endcan
            @include('tasks._review-timeline', ['events' => $task->reviewEvents])
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