<x-layouts.dashboard title="Team & Role Management">

    {{-- Page Header & Direct Create Actions --}}
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">
                Team &amp; Role Management
            </h3>
            <p class="text-muted fs-13 mb-0">
                Baseline IT Development &bull; Create and oversee Project Managers, Team Leaders, and Employees
            </p>
        </div>

        {{-- Direct Buttons for PM, TL, Employee Creation --}}
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.users.create', ['role' => 'project_manager']) }}" class="btn btn-sm text-white px-3 py-2 rounded-3 shadow-sm" style="background: #4f46e5; font-size: 13px;">
                <i class="bi bi-briefcase-fill me-1"></i> + Create PM
            </a>
            <a href="{{ route('admin.users.create', ['role' => 'team_leader']) }}" class="btn btn-sm text-white px-3 py-2 rounded-3 shadow-sm" style="background: #d97706; font-size: 13px;">
                <i class="bi bi-diagram-3-fill me-1"></i> + Create TL
            </a>
            <a href="{{ route('admin.users.create', ['role' => 'employee']) }}" class="btn btn-sm text-white px-3 py-2 rounded-3 shadow-sm" style="background: #059669; font-size: 13px;">
                <i class="bi bi-person-plus-fill me-1"></i> + Create Employee
            </a>
        </div>
    </div>

    {{-- Quick Metric Counter Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="dash-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #eff6ff; color: #2563eb; font-size: 20px;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="fs-12 text-muted fw-medium">Total Staff</div>
                    <div class="fs-20 fw-bold text-navy">{{ $counts['all'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="dash-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #eef2ff; color: #4f46e5; font-size: 20px;">
                    <i class="bi bi-briefcase-fill"></i>
                </div>
                <div>
                    <div class="fs-12 text-muted fw-medium">Project Managers</div>
                    <div class="fs-20 fw-bold" style="color: #4f46e5;">{{ $counts['project_manager'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="dash-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #fffbeb; color: #d97706; font-size: 20px;">
                    <i class="bi bi-diagram-3-fill"></i>
                </div>
                <div>
                    <div class="fs-12 text-muted fw-medium">Team Leaders</div>
                    <div class="fs-20 fw-bold" style="color: #d97706;">{{ $counts['team_leader'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="dash-card p-3 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #ecfdf5; color: #059669; font-size: 20px;">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                    <div class="fs-12 text-muted fw-medium">Employees</div>
                    <div class="fs-20 fw-bold" style="color: #059669;">{{ $counts['employee'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Tabs & Search Controls --}}
    <div class="dash-card mb-4 p-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            {{-- Role Filter Tabs --}}
            <div class="nav nav-pills gap-1 flex-wrap">
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm {{ empty($roleFilter) ? 'btn-primary' : 'btn-light text-muted' }} rounded-pill px-3 fs-13">
                    All <span class="badge bg-white {{ empty($roleFilter) ? 'text-primary' : 'text-dark' }} ms-1">{{ $counts['all'] }}</span>
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'project_manager']) }}" class="btn btn-sm {{ $roleFilter === 'project_manager' ? 'btn-primary' : 'btn-light text-muted' }} rounded-pill px-3 fs-13">
                    <i class="bi bi-briefcase-fill me-1 text-indigo"></i> PMs
                    <span class="badge bg-white {{ $roleFilter === 'project_manager' ? 'text-primary' : 'text-dark' }} ms-1">{{ $counts['project_manager'] }}</span>
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'team_leader']) }}" class="btn btn-sm {{ $roleFilter === 'team_leader' ? 'btn-primary' : 'btn-light text-muted' }} rounded-pill px-3 fs-13">
                    <i class="bi bi-diagram-3-fill me-1 text-warning"></i> TLs
                    <span class="badge bg-white {{ $roleFilter === 'team_leader' ? 'text-primary' : 'text-dark' }} ms-1">{{ $counts['team_leader'] }}</span>
                </a>
                <a href="{{ route('admin.users.index', ['role' => 'employee']) }}" class="btn btn-sm {{ $roleFilter === 'employee' ? 'btn-primary' : 'btn-light text-muted' }} rounded-pill px-3 fs-13">
                    <i class="bi bi-person-badge-fill me-1 text-success"></i> Employees
                    <span class="badge bg-white {{ $roleFilter === 'employee' ? 'text-primary' : 'text-dark' }} ms-1">{{ $counts['employee'] }}</span>
                </a>
            </div>

            {{-- Search & Status Filter Form --}}
            <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0">
                @if ($roleFilter)
                    <input type="hidden" name="role" value="{{ $roleFilter }}">
                @endif
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email..." value="{{ $search }}">
                </div>
                <select name="status" class="form-select form-select-sm" style="max-width: 120px;" onchange="this.form.submit()">
                    <option value="">Status</option>
                    <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
                @if ($search || $statusFilter || $roleFilter)
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary" title="Clear Filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Users Table --}}
    <x-card noBodyPadding="true">
        <div class="table-responsive">
            <table class="dash-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 28%;">Member</th>
                        <th>Role</th>
                        <th>Designation</th>
                        <th>Reports To</th>
                        <th>Contact</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 15%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $member)
                        @php
                            $roleMeta = match($member->role) {
                                'admin' => ['label' => 'Administrator', 'bg' => '#1e293b', 'color' => '#ffffff', 'icon' => 'bi-shield-shaded'],
                                'project_manager' => ['label' => 'Project Manager', 'bg' => '#eef2ff', 'color' => '#4f46e5', 'icon' => 'bi-briefcase-fill'],
                                'team_leader' => ['label' => 'Team Leader', 'bg' => '#fffbeb', 'color' => '#b45309', 'icon' => 'bi-diagram-3-fill'],
                                'employee' => ['label' => 'Employee', 'bg' => '#ecfdf5', 'color' => '#047857', 'icon' => 'bi-person-badge-fill'],
                                default => ['label' => ucfirst($member->role), 'bg' => '#f1f5f9', 'color' => '#475569', 'icon' => 'bi-person'],
                            };
                            $initials = collect(explode(' ', $member->name))->map(fn($part) => strtoupper(substr($part, 0, 1)))->take(2)->join('');
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0"
                                         style="width: 40px; height: 40px; background: {{ $roleMeta['bg'] }}; color: {{ $roleMeta['color'] }}; font-size: 13px; border: 1px solid rgba(0,0,0,0.06);">
                                        {{ $initials }}
                                    </div>
                                    <div class="text-truncate">
                                        <div class="fw-semibold text-navy text-truncate" style="font-size: 14px;">
                                            {{ $member->name }}
                                            @if ($member->id === Auth::id())
                                                <span class="badge bg-secondary-subtle text-secondary fs-11 ms-1">You</span>
                                            @endif
                                        </div>
                                        <small class="text-muted text-truncate d-block fs-12">{{ $member->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1"
                                      style="background: {{ $roleMeta['bg'] }}; color: {{ $roleMeta['color'] }}; font-weight: 600; font-size: 12px; border: 1px solid rgba(0,0,0,0.06);">
                                    <i class="bi {{ $roleMeta['icon'] }}"></i>
                                    {{ $roleMeta['label'] }}
                                </span>
                            </td>
                            <td>
                                <span class="fs-13 text-secondary">{{ $member->designation ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="fs-13 text-secondary">{{ $member->manager?->name ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="fs-13 text-secondary">
                                    @if ($member->phone)
                                        <i class="bi bi-telephone text-muted me-1"></i>{{ $member->phone }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <span class="fs-12 text-muted">
                                    {{ $member->joining_date ? $member->joining_date->format('M d, Y') : ($member->created_at ? $member->created_at->format('M d, Y') : '—') }}
                                </span>
                            </td>
                            <td>
                                @if ($member->status === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-12 fw-medium rounded-pill">
                                        <i class="bi bi-check-circle-fill me-1"></i> Active
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-12 fw-medium rounded-pill">
                                        <i class="bi bi-x-circle-fill me-1"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    {{-- Edit Link --}}
                                    <a href="{{ route('admin.users.edit', $member) }}" class="btn btn-sm btn-outline-secondary px-2 py-1 fs-12 rounded-2" title="Edit Member">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    {{-- Status Toggle --}}
                                    @if ($member->id !== Auth::id())
                                        <form method="POST" action="{{ route('admin.users.toggle-status', $member) }}" class="d-inline" onsubmit="return confirm('Toggle status for {{ addslashes($member->name) }}?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $member->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }} px-2 py-1 fs-12 rounded-2" title="{{ $member->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                                <i class="bi {{ $member->status === 'active' ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                            </button>
                                        </form>

                                        {{-- Delete Link --}}
                                        <form method="POST" action="{{ route('admin.users.destroy', $member) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete {{ addslashes($member->name) }}? This user can be restored later.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1 fs-12 rounded-2" title="Delete User">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-people text-muted" style="font-size: 44px;"></i>
                                    <h6 class="fw-bold text-navy mt-3 mb-1">No Team Members Found</h6>
                                    <p class="text-muted fs-13 mb-3">Try adjusting your filters or create a new PM, TL, or Employee.</p>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.users.create', ['role' => 'project_manager']) }}" class="btn btn-sm text-white px-3" style="background: #4f46e5;">+ Create PM</a>
                                        <a href="{{ route('admin.users.create', ['role' => 'team_leader']) }}" class="btn btn-sm text-white px-3" style="background: #d97706;">+ Create TL</a>
                                        <a href="{{ route('admin.users.create', ['role' => 'employee']) }}" class="btn btn-sm text-white px-3" style="background: #059669;">+ Create Employee</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $users->links() }}
            </div>
        @endif
    </x-card>

</x-layouts.dashboard>
