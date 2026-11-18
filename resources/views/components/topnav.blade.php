@props(['title' => 'Dashboard', 'user' => null])

@php
    $user = $user ?? Auth::user();
    $role = $user->role ?? 'employee';
    $roleLabels = [
        'admin'           => 'Administrator',
        'project_manager' => 'Project Manager',
        'team_leader'     => 'Team Leader',
        'employee'        => 'Software Engineer',
    ];
    $roleTitle = $roleLabels[$role] ?? 'Team Member';
@endphp

<header class="dash-topnav">
    <div class="topnav-left">
        <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="Toggle Sidebar">
            <i class="bi bi-list"></i>
        </button>

        <div class="topnav-breadcrumbs d-none d-md-flex">
            <a href="{{ route('home') }}">TaskFlow</a>
            <i class="bi bi-chevron-right fs-11"></i>
            <span class="fw-medium text-navy">{{ $title }}</span>
        </div>
    </div>

    <div class="topnav-right">
        {{-- Today's Date / Live Clock --}}
        <div class="d-none d-sm-flex align-items-center gap-2 text-muted fs-13 border-end pe-3">
            <i class="bi bi-calendar3"></i>
            <span>{{ now()->format('D, M d, Y') }}</span>
            <span class="badge px-2 py-1 fs-12 ms-1 rounded-pill" style="background: #F1F5F9; color: #1E293B; border: 1px solid #CBD5E1; font-weight: 600;" id="liveClock">--:--</span>
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
                    <small class="text-muted fs-11">{{ $roleTitle }}</small>
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
