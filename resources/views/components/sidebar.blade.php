@props(['user' => null])

@php
    $user = $user ?? Auth::user();
    $role = $user->role ?? 'employee';
    $roleLabels = [
        'admin'           => ['label' => 'Administrator',     'dot' => 'role-admin',    'icon' => 'bi-shield-shaded'],
        'project_manager' => ['label' => 'Project Manager',   'dot' => 'role-pm',       'icon' => 'bi-briefcase-fill'],
        'team_leader'     => ['label' => 'Team Leader',       'dot' => 'role-tl',       'icon' => 'bi-diagram-3-fill'],
        'employee'        => ['label' => 'Software Engineer', 'dot' => 'role-employee', 'icon' => 'bi-person-badge-fill'],
    ];
    $currentRoleMeta = $roleLabels[$role] ?? $roleLabels['employee'];
@endphp

<aside class="dash-sidebar" id="dashSidebar">
    {{-- Brand Header --}}
    <div class="sidebar-header">
        <a href="{{ route('home') }}" class="sidebar-brand">
            <span class="sidebar-brand-icon">
                <i class="bi bi-layers-half"></i>
            </span>
            <div class="sidebar-brand-text">
                TaskFlow
                <small>BASELINE IT WORKSPACE</small>
            </div>
        </a>
    </div>

    {{-- User Role Pill --}}
    <div class="sidebar-role-badge role-badge-{{ $role }}">
        <span class="role-badge-dot {{ $currentRoleMeta['dot'] }}"></span>
        <span class="fs-12 fw-semibold text-truncate">{{ $currentRoleMeta['label'] }}</span>
    </div>

    {{-- Role Specific Nav Items --}}
    <nav class="sidebar-nav">
        <div class="nav-section-title">Main</div>

        @if ($role === 'admin')
            {{--  Admin Nav --}}
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-speedometer2"></i>
                    <span>Admin Dashboard</span>
                </div>
            </a>

            <div class="nav-section-title">Team Management</div>

            <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.index') && !request()->query('role') ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-people-fill"></i>
                    <span>All Members</span>
                </div>
            </a>

            <a href="{{ route('admin.users.index', ['role' => 'project_manager']) }}" class="sidebar-link {{ request()->query('role') === 'project_manager' ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-briefcase-fill" style="color: #6366f1;"></i>
                    <span>Project Managers</span>
                </div>
            </a>

            <a href="{{ route('admin.users.index', ['role' => 'team_leader']) }}" class="sidebar-link {{ request()->query('role') === 'team_leader' ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-diagram-3-fill" style="color: #f59e0b;"></i>
                    <span>Team Leaders</span>
                </div>
            </a>

            <a href="{{ route('admin.users.index', ['role' => 'employee']) }}" class="sidebar-link {{ request()->query('role') === 'employee' ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-person-badge-fill" style="color: #10b981;"></i>
                    <span>Employees</span>
                </div>
            </a>

            <a href="{{ route('admin.users.create') }}" class="sidebar-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-person-plus-fill" style="color: #3b82f6;"></i>
                    <span class="fw-semibold">+ Create User</span>
                </div>
            </a>

            <div class="nav-section-title">Workspace &amp; Delivery</div>
            <a href="{{ route('admin.projects.index') }}" class="sidebar-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-folder-check"></i>
                    <span>All Projects</span>
                </div>
                <span class="sidebar-badge">{{ \App\Models\Project::count() }}</span>
            </a>
            <a href="{{ route('admin.hours.index') }}" class="sidebar-link {{ request()->routeIs('admin.hours.*') ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-hourglass-split"></i>
                    <span>Hour Requests</span>
                </div>
                <span class="sidebar-badge" style="background:#D97706;">{{ \App\Models\TaskHourAllotment::where('status', 'pending')->count() }}</span>
            </a>
            <a href="{{ route('admin.confirmations.index') }}" class="sidebar-link {{ request()->routeIs('admin.confirmations.*') ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-patch-check"></i>
                    <span>Completed Tasks</span>
                </div>
                <span class="sidebar-badge" style="background:#059669;">{{ \App\Models\Task::awaitingAdmin()->count() }}</span>
            </a>

            <a href="javascript:void(0)" class="sidebar-link">
                <div class="link-left">
                    <i class="bi bi-check2-square"></i>
                    <span>Master Task List</span>
                </div>
            </a>

            <a href="javascript:void(0)" class="sidebar-link">
                <div class="link-left">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <span>Reports &amp; Analytics</span>
                </div>
            </a>

            <div class="nav-section-title">System</div>

            <a href="javascript:void(0)" class="sidebar-link">
                <div class="link-left">
                    <i class="bi bi-sliders"></i>
                    <span>Workspace Settings</span>
                </div>
            </a>

        @elseif ($role === 'project_manager')
            {{--  PM Nav --}}
            <a href="{{ route('pm.dashboard') }}" class="sidebar-link {{ request()->routeIs('pm.dashboard') ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>PM Dashboard</span>
                </div>
            </a>

            <div class="nav-section-title">Projects &amp; Tasks</div>

            <a href="{{ route('pm.projects.index') }}" class="sidebar-link {{ request()->routeIs('pm.projects.*') ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-folder-symlink-fill"></i>
                    <span>My Projects</span>
                </div>
                <span class="sidebar-badge">{{ $user->projects()->where('status', 'active')->count() }} Active</span>
            </a>

            <a href="{{ route('pm.tasks.overview') }}" class="sidebar-link {{ request()->routeIs('pm.tasks.overview') && ! request()->query('review') ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-kanban"></i>
                    <span>Task Management</span>
                </div>
            </a>

            <a href="{{ route('pm.tasks.overview', ['review' => 'pm']) }}" class="sidebar-link {{ request()->query('review') === 'pm' ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-hourglass-split"></i>
                    <span>Pending Reviews</span>
                </div>
                <span class="sidebar-badge" style="background:#D97706;">{{ \App\Models\Task::whereHas('project', fn ($q) => $q->where('project_manager_id', $user->id))->where('status', 'in_review')->where('review_stage', 'pm')->count() }}</span>
            </a>

            <a href="{{ route('pm.team-leaders.index') }}" class="sidebar-link {{ request()->routeIs('pm.team-leaders.*') ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-people"></i>
                    <span>My Team Leaders</span>
                </div>
                <span class="sidebar-badge">{{ $user->reports()->where('role', 'team_leader')->count() }}</span>
            </a>

            <a href="javascript:void(0)" class="sidebar-link">
                <div class="link-left">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                    <span>Sprint Reports</span>
                </div>
            </a>

        @elseif ($role === 'team_leader')
            {{--  Team Leader Nav --}}
            <a href="{{ route('tl.dashboard') }}" class="sidebar-link {{ request()->routeIs('tl.dashboard') ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-pie-chart-fill"></i>
                    <span>TL Dashboard</span>
                </div>
            </a>

            <div class="nav-section-title">Sprint &amp; Team</div>

            <a href="{{ route('tl.team.index') }}" class="sidebar-link {{ request()->routeIs('tl.team.*') ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-diagram-3"></i>
                    <span>My Sub-Team</span>
                </div>
                <span class="sidebar-badge">{{ $user->reports()->where('role', 'employee')->count() }}</span>
            </a>

            <a href="{{ route('tl.tasks.index') }}" class="sidebar-link {{ request()->routeIs('tl.tasks.*') ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-list-task"></i>
                    <span>Team Tasks</span>
                </div>
                <span class="sidebar-badge">{{ $user->assignedTasks()->count() }}</span>
            </a>

            <a href="javascript:void(0)" class="sidebar-link">
                <div class="link-left">
                    <i class="bi bi-patch-check"></i>
                    <span>Code Review Board</span>
                </div>
                <span class="sidebar-badge" style="background:#0D9488;">3</span>
            </a>

            <a href="javascript:void(0)" class="sidebar-link">
                <div class="link-left">
                    <i class="bi bi-folder2-open"></i>
                    <span>Assigned Projects</span>
                </div>
            </a>

        @else
            {{--  Developer Nav --}}
            <a href="{{ route('employee.dashboard') }}" class="sidebar-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                <div class="link-left">
                    <i class="bi bi-laptop"></i>
                    <span>Developer Workspace</span>
                </div>
            </a>

            <div class="nav-section-title">My Tasks &amp; Delivery</div>

                <a href="{{ route('employee.tasks.index') }}" class="sidebar-link {{ request()->routeIs('employee.tasks.*') && ! request()->query('status') ? 'active' : '' }}">
                    <div class="link-left">
                        <i class="bi bi-check2-circle"></i>
                        <span>My Assigned Tasks</span>
                    </div>
                    <span class="sidebar-badge">{{ $user->employeeTasks()->count() }} Total</span>
                </a>

                <a href="{{ route('employee.tasks.index', ['status' => 'in_progress']) }}" class="sidebar-link {{ request()->query('status') === 'in_progress' ? 'active' : '' }}">
                    <div class="link-left">
                        <i class="bi bi-clock-history"></i>
                        <span>In Progress</span>
                    </div>
                    <span class="sidebar-badge" style="background:#2563EB;">{{ $user->employeeTasks()->where('status', 'in_progress')->count() }}</span>
                </a>

                <a href="{{ route('employee.tasks.index', ['status' => 'in_review']) }}" class="sidebar-link {{ request()->query('status') === 'in_review' ? 'active' : '' }}">
                    <div class="link-left">
                        <i class="bi bi-send-check"></i>
                        <span>Submitted for Review</span>
                    </div>
                </a>

                <a href="{{ route('employee.tasks.index', ['status' => 'done']) }}" class="sidebar-link {{ request()->query('status') === 'done' ? 'active' : '' }}">
                    <div class="link-left">
                        <i class="bi bi-flag-fill"></i>
                        <span>Completed Tasks</span>
                    </div>
                </a>
                <a href="{{ route('employee.projects') }}" class="sidebar-link {{ request()->routeIs('employee.projects') ? 'active' : '' }}">
                    <div class="link-left">
                        <i class="bi bi-folder2"></i>
                        <span>My Projects</span>
                    </div>
                </a>

                <a href="{{ route('employee.team') }}" class="sidebar-link {{ request()->routeIs('employee.team') ? 'active' : '' }}">
                    <div class="link-left">
                        <i class="bi bi-people"></i>
                        <span>My Team</span>
                    </div>
                </a>
            @endif

            <div class="nav-section-title">Account</div>
            <a href="{{ route('home') }}" class="sidebar-link">
            <div class="link-left">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Public Home Page</span>
            </div>
        </a>
    </nav>

    {{-- Footer --}}
    <div class="sidebar-footer">
        <div class="user-mini-profile">
            <div class="user-avatar-sm">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
            </div>
            <div class="d-flex flex-column lh-1 text-truncate" style="max-width: 140px;">
                <span class="fs-13 fw-semibold text-navy text-truncate">{{ $user->name ?? 'User' }}</span>
                <small class="text-muted fs-11 text-truncate mt-1">{{ $user->designation ?? 'Team Member' }}</small>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button type="submit" class="btn-sidebar-logout" title="Sign Out">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</aside>
