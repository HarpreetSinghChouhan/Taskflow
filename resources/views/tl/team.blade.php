<x-layouts.dashboard title="My Sub-Team">

    @php
        $hoursText = fn ($h) => rtrim(rtrim(number_format((float) $h, 2), '0'), '.') ?: '0';
        $cards = [
            ['Employees',            $summary['employees'],  'bi-person-badge-fill', '#ecfdf5', '#059669'],
            ['Unassigned Tasks',     $summary['unassigned'], 'bi-inbox-fill',         '#fffbeb', '#d97706'],
            ['Waiting for Review',   $summary['in_review'],  'bi-send-check',         '#eff6ff', '#2563eb'],
            ['Hours Logged',         $hoursText($summary['hours']) . ' h', 'bi-clock-history', '#eef2ff', '#4f46e5'],
        ];
    @endphp

    <div class="mb-4">
        <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">My Sub-Team</h3>
        <p class="text-muted fs-13 mb-0">Baseline IT Development &bull; Progress of the employees reporting to you</p>
    </div>

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

    <x-card noBodyPadding="true">
        <div class="table-responsive">
            <table class="dash-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 28%;">Employee</th>
                        <th>Pending</th>
                        <th>In Progress</th>
                        <th>In Review</th>
                        <th>Done</th>
                        <th>Progress</th>
                        <th>Hours</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $emp)
                        @php $percent = $emp->total_count ? round($emp->done_count / $emp->total_count * 100) : 0; @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold text-navy fs-13">
                                    {{ $emp->name }}
                                    @if ($emp->status !== 'active')
                                        <span class="badge bg-danger-subtle text-danger fs-11 ms-1">Inactive</span>
                                    @endif
                                </div>
                                <small class="text-muted fs-12">{{ $emp->designation ?? '—' }}</small>
                            </td>
                            <td class="fs-13">{{ $emp->pending_count }}</td>
                            <td class="fs-13">{{ $emp->in_progress_count }}</td>
                            <td class="fs-13">{{ $emp->in_review_count }}</td>
                            <td class="fs-13">{{ $emp->done_count }}</td>
                            <td style="min-width: 130px;">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: {{ $percent }}%;"></div>
                                </div>
                                <small class="text-muted fs-12">{{ $percent }}% ({{ $emp->done_count }}/{{ $emp->total_count }})</small>
                            </td>
                            <td class="fs-13 fw-semibold">{{ $hoursText($emp->hours_logged) }} h</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-people text-muted" style="font-size: 44px;"></i>
                                <h6 class="fw-bold text-navy mt-3 mb-1">No Team Members</h6>
                                <p class="text-muted fs-13 mb-0">No employee reports to you yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

</x-layouts.dashboard>