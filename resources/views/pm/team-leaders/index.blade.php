<x-layouts.dashboard title="Assigned Team Leaders">

    {{-- Header --}}
    <div class="mb-4">
        <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">
            Assigned Team Leaders
        </h3>
        <p class="text-muted fs-13 mb-0">
            Baseline IT Development &bull; Team leaders reporting to you
        </p>
    </div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="dash-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #fffbeb; color: #d97706; font-size: 20px;">
                    <i class="bi bi-diagram-3-fill"></i>
                </div>
                <div>
                    <div class="fs-12 text-muted fw-medium">Team Leaders</div>
                    <div class="fs-20 fw-bold" style="color: #d97706;">{{ $totals['leaders'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dash-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #ecfdf5; color: #059669; font-size: 20px;">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                    <div class="fs-12 text-muted fw-medium">Employees in Teams</div>
                    <div class="fs-20 fw-bold" style="color: #059669;">{{ $totals['employees'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dash-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #eef2ff; color: #4f46e5; font-size: 20px;">
                    <i class="bi bi-list-task"></i>
                </div>
                <div>
                    <div class="fs-12 text-muted fw-medium">Tasks Assigned</div>
                    <div class="fs-20 fw-bold" style="color: #4f46e5;">{{ $totals['tasks'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="dash-card mb-4 p-3">
        <form method="GET" action="{{ route('pm.team-leaders.index') }}" class="d-flex align-items-center gap-2">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email, designation..." value="{{ $search }}">
            </div>
            <button type="submit" class="btn btn-sm btn-outline-primary">Search</button>
            @if ($search)
                <a href="{{ route('pm.team-leaders.index') }}" class="btn btn-sm btn-outline-secondary" title="Clear">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- Team leaders table --}}
    <x-card noBodyPadding="true">
        <div class="table-responsive">
            <table class="dash-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 32%;">Team Leader</th>
                        <th>Designation</th>
                        <th>Team</th>
                        <th>Tasks</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teamLeaders as $tl)
                        @php
                            $initials = collect(explode(' ', $tl->name))->map(fn($part) => strtoupper(substr($part, 0, 1)))->take(2)->join('');
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0"
                                         style="width: 40px; height: 40px; background: #fffbeb; color: #b45309; font-size: 13px; border: 1px solid rgba(0,0,0,0.06);">
                                        {{ $initials }}
                                    </div>
                                    <div class="text-truncate">
                                        <div class="fw-semibold text-navy text-truncate" style="font-size: 14px;">{{ $tl->name }}</div>
                                        <small class="text-muted text-truncate d-block fs-12">{{ $tl->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="fs-13 text-secondary">{{ $tl->designation ?? '—' }}</span></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-light fs-12 rounded-pill px-3"
                                        data-bs-toggle="collapse" data-bs-target="#team-{{ $tl->id }}"
                                        {{ $tl->employees_count === 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-people me-1"></i>
                                    {{ $tl->employees_count }} {{ \Illuminate\Support\Str::plural('employee', $tl->employees_count) }}
                                </button>
                            </td>
                            <td><span class="fs-13 text-secondary">{{ $tl->tasks_count }}</span></td>
                            <td>
                                @if ($tl->status === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-12 fw-medium rounded-pill">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-12 fw-medium rounded-pill">Inactive</span>
                                @endif
                            </td>
                        </tr>

                        {{-- Employees under this team leader --}}
                        <tr class="collapse" id="team-{{ $tl->id }}">
                            <td colspan="5" style="background: #f8fafc;">
                                <div class="py-2 px-3">
                                    <div class="fs-12 text-muted fw-semibold mb-2">Employees under {{ $tl->name }}</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($tl->reports as $emp)
                                            <span class="badge rounded-pill px-3 py-2 fw-medium {{ $emp->status === 'active' ? 'bg-white text-dark border' : 'bg-danger-subtle text-danger' }}"
                                                  style="font-size: 12px;">
                                                {{ $emp->name }}@if ($emp->designation) &middot; {{ $emp->designation }}@endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-diagram-3 text-muted" style="font-size: 44px;"></i>
                                    <h6 class="fw-bold text-navy mt-3 mb-1">No Team Leaders Found</h6>
                                    <p class="text-muted fs-13 mb-0">No team leader reports to you yet, or your search matches nothing.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

</x-layouts.dashboard>