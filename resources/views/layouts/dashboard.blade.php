<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'TaskFlow Workspace &bull; Baseline IT Development')</title>

    {{-- Google Poppins Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Dashboard Dedicated Stylesheet --}}
    <link href="{{ asset('assets/css/dashboard.css') }}?v={{ time() }}" rel="stylesheet">

    @yield('styles')
</head>
<body class="dashboard-body">

    {{-- Mobile Sidebar Backdrop --}}
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="dashboard-wrapper">

        {{-- ── Sidebar ── --}}
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

            {{-- User Role Pill Indicator --}}
            @php
                $user = Auth::user();
                $role = $user->role ?? 'employee';
                $roleLabels = [
                    'admin' => ['label' => 'Administrator', 'dot' => 'role-admin', 'icon' => 'bi-shield-shaded'],
                    'project_manager' => ['label' => 'Project Manager', 'dot' => 'role-pm', 'icon' => 'bi-briefcase-fill'],
                    'team_leader' => ['label' => 'Team Leader', 'dot' => 'role-tl', 'icon' => 'bi-diagram-3-fill'],
                    'employee' => ['label' => 'Software Engineer', 'dot' => 'role-employee', 'icon' => 'bi-person-badge-fill'],
                ];
                $currentRoleMeta = $roleLabels[$role] ?? $roleLabels['employee'];
            @endphp

            <div class="sidebar-role-badge">
                <span class="role-badge-dot {{ $currentRoleMeta['dot'] }}"></span>
                <span class="fs-12 fw-semibold text-truncate">{{ $currentRoleMeta['label'] }}</span>
            </div>

            {{-- Role-Specific Navigation Links (from TASKFLOW-MASTER-PLAN.md) --}}
            <nav class="sidebar-nav">
                <div class="nav-section-title">Main</div>

                @if ($role === 'admin')
                    {{-- 👑 Admin Navigation --}}
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <div class="link-left">
                            <i class="bi bi-speedometer2"></i>
                            <span>Admin Dashboard</span>
                        </div>
                    </a>

                    <div class="nav-section-title">Workspace &amp; Delivery</div>

                    <a href="javascript:void(0)" class="sidebar-link">
                        <div class="link-left">
                            <i class="bi bi-people-fill"></i>
                            <span>Employee Directory</span>
                        </div>
                        <span class="sidebar-badge">45</span>
                    </a>

                    <!-- <a href="javascript:void(0)" class="sidebar-link">
                        <div class="link-left">
                            <i class="bi bi-folder-check"></i>
                            <span>All Projects</span>
                        </div>
                        <span class="sidebar-badge">24</span>
                    </a> -->
                    <a href="{{ route('admin.projects.index') }}" class="sidebar-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
                        <div class="link-left">
                            <i class="bi bi-folder-check"></i>
                            <span>All Projects</span>
                        </div>
                        <span class="sidebar-badge">{{ \App\Models\Project::count() }}</span>
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
                    {{-- 📋 Project Manager Navigation --}}
                    <a href="{{ route('pm.dashboard') }}" class="sidebar-link {{ request()->routeIs('pm.dashboard') ? 'active' : '' }}">
                        <div class="link-left">
                            <i class="bi bi-grid-1x2-fill"></i>
                            <span>PM Dashboard</span>
                        </div>
                    </a>

                    <div class="nav-section-title">Projects &amp; Tasks</div>

                    <a href="javascript:void(0)" class="sidebar-link">
                        <div class="link-left">
                            <i class="bi bi-folder-symlink-fill"></i>
                            <span>My Projects</span>
                        </div>
                        <span class="sidebar-badge">6 Active</span>
                    </a>

                    <a href="javascript:void(0)" class="sidebar-link">
                        <div class="link-left">
                            <i class="bi bi-kanban"></i>
                            <span>Task Management</span>
                        </div>
                    </a>

                    <a href="javascript:void(0)" class="sidebar-link">
                        <div class="link-left">
                            <i class="bi bi-hourglass-split"></i>
                            <span>Pending Reviews</span>
                        </div>
                        <span class="sidebar-badge" style="background:#D97706;">5</span>
                    </a>

                    <a href="javascript:void(0)" class="sidebar-link">
                        <div class="link-left">
                            <i class="bi bi-people"></i>
                            <span>Assigned Teams</span>
                        </div>
                    </a>

                    <a href="javascript:void(0)" class="sidebar-link">
                        <div class="link-left">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Sprint Reports</span>
                        </div>
                    </a>

                @elseif ($role === 'team_leader')
                    {{-- 👥 Team Leader Navigation --}}
                    <a href="{{ route('tl.dashboard') }}" class="sidebar-link {{ request()->routeIs('tl.dashboard') ? 'active' : '' }}">
                        <div class="link-left">
                            <i class="bi bi-pie-chart-fill"></i>
                            <span>TL Dashboard</span>
                        </div>
                    </a>

                    <div class="nav-section-title">Sprint &amp; Team</div>

                    <a href="javascript:void(0)" class="sidebar-link">
                        <div class="link-left">
                            <i class="bi bi-diagram-3"></i>
                            <span>My Sub-Team</span>
                        </div>
                        <span class="sidebar-badge">8 Devs</span>
                    </a>

                    <a href="javascript:void(0)" class="sidebar-link">
                        <div class="link-left">
                            <i class="bi bi-list-task"></i>
                            <span>Team Tasks</span>
                        </div>
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
                    {{-- 👤 Employee / Developer Navigation --}}
                    <a href="{{ route('employee.dashboard') }}" class="sidebar-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                        <div class="link-left">
                            <i class="bi bi-laptop"></i>
                            <span>Developer Workspace</span>
                        </div>
                    </a>

                    <div class="nav-section-title">My Tasks &amp; Delivery</div>

                    <a href="javascript:void(0)" class="sidebar-link">
                        <div class="link-left">
                            <i class="bi bi-check2-circle"></i>
                            <span>My Assigned Tasks</span>
                        </div>
                        <span class="sidebar-badge">9 Total</span>
                    </a>

                    <a href="javascript:void(0)" class="sidebar-link">
                        <div class="link-left">
                            <i class="bi bi-clock-history"></i>
                            <span>In Progress</span>
                        </div>
                        <span class="sidebar-badge" style="background:#2563EB;">3</span>
                    </a>

                    <a href="javascript:void(0)" class="sidebar-link">
                        <div class="link-left">
                            <i class="bi bi-send-check"></i>
                            <span>Submitted for Review</span>
                        </div>
                    </a>

                    <a href="javascript:void(0)" class="sidebar-link">
                        <div class="link-left">
                            <i class="bi bi-flag-fill"></i>
                            <span>Completed Tasks</span>
                        </div>
                    </a>

                    <a href="javascript:void(0)" class="sidebar-link">
                        <div class="link-left">
                            <i class="bi bi-folder2"></i>
                            <span>My Projects</span>
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

            {{-- Sidebar Footer with Current User Profile --}}
            <div class="sidebar-footer">
                <div class="user-mini-profile">
                    <div class="user-avatar-sm">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="d-flex flex-column lh-1 text-truncate" style="max-width: 140px;">
                        <span class="fs-13 fw-semibold text-white text-truncate">{{ $user->name ?? 'User' }}</span>
                        <small class="text-white-50 fs-11 text-truncate">{{ $user->designation ?? 'Team Member' }}</small>
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

        {{-- ── Main Area (Topnav + Body) ── --}}
        <div class="dash-main-area">

            {{-- Top Navigation Bar --}}
            <header class="dash-topnav">
                <div class="topnav-left">
                    <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="Toggle Sidebar">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="topnav-breadcrumbs d-none d-md-flex">
                        <a href="{{ route('home') }}">TaskFlow</a>
                        <i class="bi bi-chevron-right fs-11"></i>
                        <span class="fw-medium text-navy">@yield('page_title', 'Dashboard')</span>
                    </div>
                </div>

                <div class="topnav-right">
                    {{-- Today's Date / Live Clock --}}
                    <div class="d-none d-sm-flex align-items-center gap-2 text-muted fs-13 border-end pe-3">
                        <i class="bi bi-calendar3"></i>
                        <span>{{ now()->format('D, M d, Y') }}</span>
                        <span class="badge bg-light text-navy border ms-1" id="liveClock">--:--</span>
                    </div>

                    {{-- Notifications Bell --}}
                    <button type="button" class="topnav-icon-btn" title="Notifications">
                        <i class="bi bi-bell"></i>
                        <span class="topnav-badge-dot"></span>
                    </button>

                    {{-- User Profile Pill Dropdown --}}
                    <div class="dropdown">
                        <div class="topnav-user-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar-sm" style="background: var(--dash-brand);">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="d-none d-md-flex flex-column lh-1 text-start">
                                <span class="fs-13 fw-semibold text-navy">{{ $user->name }}</span>
                                <small class="text-muted fs-11">{{ $currentRoleMeta['label'] }}</small>
                            </div>
                            <i class="bi bi-chevron-down fs-11 text-muted ms-1"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-2 mt-2" style="min-width: 200px;">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-semibold text-navy fs-13">{{ $user->name }}</div>
                                <small class="text-muted fs-11">{{ $user->email }}</small>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 fs-13 py-2" href="{{ route('home') }}">
                                    <i class="bi bi-house me-2 text-muted"></i> Return to Home
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item rounded-2 fs-13 py-2 text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Log Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            {{-- Body View Content --}}
            <main class="dash-content-body">
                {{-- Flash Notifications --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4 rounded-3" role="alert">
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <div class="fw-medium fs-14">{{ session('success') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>

        </div>
    </div>

    {{-- jQuery 3.7.1 --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Bootstrap 5 Bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Chart.js (for analytics) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    {{-- Dashboard Dedicated Script --}}
    <script src="{{ asset('assets/js/dashboard.js') }}?v={{ time() }}"></script>

    @yield('scripts')
</body>
</html>
