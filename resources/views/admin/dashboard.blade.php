<x-layouts.dashboard title="Admin Dashboard">

    {{-- Welcome Banner & Quick Creation Actions --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">
                Executive Command Center
            </h3>
            <p class="text-muted fs-13 mb-0">
                Baseline IT Development Pvt Ltd &bull; Full Organization &amp; Project Overview
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.users.create', ['role' => 'project_manager']) }}" class="btn btn-sm text-white rounded-3 px-3 py-2 fs-13 shadow-sm" style="background: #4f46e5;">
                <i class="bi bi-briefcase-fill me-1"></i> + Create PM
            </a>
            <a href="{{ route('admin.users.create', ['role' => 'team_leader']) }}" class="btn btn-sm text-white rounded-3 px-3 py-2 fs-13 shadow-sm" style="background: #d97706;">
                <i class="bi bi-diagram-3-fill me-1"></i> + Create TL
            </a>
            <a href="{{ route('admin.users.create', ['role' => 'employee']) }}" class="btn btn-sm text-white rounded-3 px-3 py-2 fs-13 shadow-sm" style="background: #059669;">
                <i class="bi bi-person-plus-fill me-1"></i> + Create Employee
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3 py-2 fs-13">
                <i class="bi bi-people-fill me-1"></i> Team Directory
            </a>
        </div>
    </div>

    {{-- Team Overview Quick Metrics (PM, TL, Employee, Total) --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="dash-card p-3 h-100 position-relative">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fs-12 text-muted fw-medium">Total Staff</span>
                    <span class="rounded-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #eff6ff; color: #2563eb;">
                        <i class="bi bi-people-fill"></i>
                    </span>
                </div>
                <div class="fs-24 fw-bold text-navy mb-1">{{ $totalUsers ?? 4 }}</div>
                <div class="d-flex align-items-center justify-content-between">
                    <small class="text-muted fs-11">Registered workspace users</small>
                    <a href="{{ route('admin.users.index') }}" class="fs-11 fw-semibold text-decoration-none">Manage &rarr;</a>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="dash-card p-3 h-100 position-relative" style="border-top: 3px solid #4f46e5;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fs-12 text-muted fw-medium">Project Managers (PM)</span>
                    <span class="rounded-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #eef2ff; color: #4f46e5;">
                        <i class="bi bi-briefcase-fill"></i>
                    </span>
                </div>
                <div class="fs-24 fw-bold mb-1" style="color: #4f46e5;">{{ $pmCount ?? 1 }}</div>
                <div class="d-flex align-items-center justify-content-between">
                    <small class="text-muted fs-11">Client Projects &amp; Sprints</small>
                    <a href="{{ route('admin.users.create', ['role' => 'project_manager']) }}" class="fs-11 fw-semibold text-decoration-none" style="color: #4f46e5;">+ Add PM</a>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="dash-card p-3 h-100 position-relative" style="border-top: 3px solid #d97706;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fs-12 text-muted fw-medium">Team Leaders (TL)</span>
                    <span class="rounded-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #fffbeb; color: #d97706;">
                        <i class="bi bi-diagram-3-fill"></i>
                    </span>
                </div>
                <div class="fs-24 fw-bold mb-1" style="color: #d97706;">{{ $tlCount ?? 1 }}</div>
                <div class="d-flex align-items-center justify-content-between">
                    <small class="text-muted fs-11">Technical Leads &amp; Reviews</small>
                    <a href="{{ route('admin.users.create', ['role' => 'team_leader']) }}" class="fs-11 fw-semibold text-decoration-none" style="color: #d97706;">+ Add TL</a>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="dash-card p-3 h-100 position-relative" style="border-top: 3px solid #059669;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fs-12 text-muted fw-medium">Employees / Developers</span>
                    <span class="rounded-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #ecfdf5; color: #059669;">
                        <i class="bi bi-person-badge-fill"></i>
                    </span>
                </div>
                <div class="fs-24 fw-bold mb-1" style="color: #059669;">{{ $employeeCount ?? 1 }}</div>
                <div class="d-flex align-items-center justify-content-between">
                    <small class="text-muted fs-11">Engineers &amp; Execution</small>
                    <a href="{{ route('admin.users.create', ['role' => 'employee']) }}" class="fs-11 fw-semibold text-decoration-none" style="color: #059669;">+ Add Emp</a>
                </div>
            </div>
        </div>
    </div>

    {{-- 8 Metric Cards Grid --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Total Employees" :value="$totalUsers ?? 45" icon="bi-people-fill" color="primary" trend="+4" footer="joined this quarter" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Active Projects" value="18" icon="bi-folder-check" color="teal" footer="6 in planning &bull; 2 on hold" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Total Sprinted Tasks" value="240" icon="bi-list-task" color="indigo" footer="Across 18 enterprise portals" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Completed Tasks" value="186" icon="bi-check-circle-fill" color="success" valueClass="text-success" trend="77.5%" footer="completion rate" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Awaiting QA / Review" value="14" icon="bi-hourglass-split" color="warning" valueClass="text-warning" footer="Awaiting PM/TL signoff" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Overdue Tasks" value="3" icon="bi-exclamation-triangle-fill" color="danger" valueClass="text-danger" footer="Immediate escalation" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="High Priority Tasks" value="12" icon="bi-fire" color="danger" footer="Due within 48 hours" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Sprint Velocity" value="94.2%" icon="bi-graph-up-arrow" color="sky" trend="High" footer="velocity efficiency" />
        </div>
    </div>

    {{-- Interactive Visual Analytics Row --}}
    <div class="row g-4 mb-4">
        {{-- Task Priority Breakdown Chart --}}
        <div class="col-lg-8">
            <x-card title="Sprint Task Distribution by Status" icon="bi-bar-chart-fill" badge="Live Sprints" class="h-100">
                <canvas id="taskStatusBarChart" height="110"></canvas>
            </x-card>
        </div>

        {{-- Project Portfolio Status Doughnut --}}
        <div class="col-lg-4">
            <x-card title="Project Portfolio" icon="bi-pie-chart-fill" badge="24 Total" class="h-100">
                <div class="d-flex flex-column align-items-center justify-content-center">
                    <div style="width: 190px; height: 190px;">
                        <canvas id="projectDoughnutChart"></canvas>
                    </div>
                    <div class="d-flex flex-wrap justify-content-center gap-3 mt-3 fs-12">
                        <span class="text-muted"><x-badge status="active" text="Active (18)" /></span>
                        <span class="text-muted"><x-badge status="completed" text="Completed (4)" /></span>
                        <span class="text-muted"><x-badge status="planning" text="Planning (2)" /></span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    {{-- Upcoming Milestones & Recent Audit Activities --}}
    <div class="row g-4 mb-4">
        {{-- Active Projects Table --}}
        <div class="col-lg-8">
            <x-card title="Active Client Deliveries &bull; Milestones" icon="bi-folder-fill" class="mb-0" noBodyPadding="true">
                <x-slot:action>
                    <a href="javascript:void(0)" class="fs-12 text-decoration-none fw-semibold">View All Projects &rarr;</a>
                </x-slot:action>

                <div class="table-responsive">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Project Name</th>
                                <th>Lead PM</th>
                                <th>Deadline</th>
                                <th>Progress</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="fw-semibold text-navy">Baseline ERP Cloud Platform</div>
                                    <small class="text-muted">Enterprise Software Solution</small>
                                </td>
                                <td>Sarah Jenkins</td>
                                <td>Dec 20, 2026</td>
                                <td style="width: 140px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 84%"></div>
                                        </div>
                                        <span class="fs-12 fw-semibold">84%</span>
                                    </div>
                                </td>
                                <td><x-badge status="active" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="fw-semibold text-navy">Fintech Payment Gateway API</div>
                                    <small class="text-muted">Banking &amp; Transactions</small>
                                </td>
                                <td>Sarah Jenkins</td>
                                <td>Jan 15, 2027</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 92%"></div>
                                        </div>
                                        <span class="fs-12 fw-semibold">92%</span>
                                    </div>
                                </td>
                                <td><x-badge status="in_review" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="fw-semibold text-navy">Logistics GPS Tracking Mobile App</div>
                                    <small class="text-muted">Flutter &amp; Go Backend</small>
                                </td>
                                <td>Vikram Malhotra</td>
                                <td>Nov 30, 2026</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: 58%"></div>
                                        </div>
                                        <span class="fs-12 fw-semibold">58%</span>
                                    </div>
                                </td>
                                <td><x-badge status="active" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="fw-semibold text-navy">Healthcare Telemedicine Portal</div>
                                    <small class="text-muted">HIPAA Compliant System</small>
                                </td>
                                <td>Sarah Jenkins</td>
                                <td>Dec 05, 2026</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: 32%"></div>
                                        </div>
                                        <span class="fs-12 fw-semibold">32%</span>
                                    </div>
                                </td>
                                <td><x-badge status="overdue" text="Delayed" /></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        {{-- Live Audit Activity Stream --}}
        <div class="col-lg-4">
            <x-card title="Real-Time Audit Log" icon="bi-clock-history" class="mb-0">
                <x-slot:action>
                    <x-badge status="active" text="Live" />
                </x-slot:action>

                <div class="d-flex flex-column gap-3 fs-13">
                    <div class="d-flex gap-2">
                        <span class="text-primary mt-1"><i class="bi bi-check2-circle fs-6"></i></span>
                        <div>
                            <span class="fw-semibold text-navy">Rohan Verma</span> completed task <em>OAuth2 Refresh Handler</em>
                            <small class="text-muted d-block fs-11">5 mins ago &bull; Baseline ERP</small>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <span class="text-warning mt-1"><i class="bi bi-hourglass-split fs-6"></i></span>
                        <div>
                            <span class="fw-semibold text-navy">Vikram Malhotra</span> submitted <em>Sprint 14 Review</em> for PM approval
                            <small class="text-muted d-block fs-11">24 mins ago &bull; Logistics Portal</small>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <span class="text-success mt-1"><i class="bi bi-person-plus-fill fs-6"></i></span>
                        <div>
                            <span class="fw-semibold text-navy">Sarah Jenkins</span> assigned 3 developers to <em>Fintech API</em>
                            <small class="text-muted d-block fs-11">1 hr ago &bull; Allocation</small>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <span class="text-danger mt-1"><i class="bi bi-flag-fill fs-6"></i></span>
                        <div>
                            <span class="fw-semibold text-navy">System Alert:</span> Deadline approaching for <em>Telemedicine Portal</em>
                            <small class="text-muted d-block fs-11">2 hrs ago &bull; Automated Alert</small>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    {{-- Workspace Team Roster (PM, TL, Employees) --}}
    @if (isset($recentUsers) && count($recentUsers) > 0)
        <div class="row g-4 mb-4">
            <div class="col-12">
                <x-card title="Workspace Team Roster &bull; Quick Access" icon="bi-people-fill" noBodyPadding="true">
                    <x-slot:action>
                        <a href="{{ route('admin.users.index') }}" class="fs-12 text-decoration-none fw-semibold">View All Staff &rarr;</a>
                    </x-slot:action>

                    <div class="table-responsive">
                        <table class="dash-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name &amp; Email</th>
                                    <th>Role</th>
                                    <th>Designation</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentUsers as $userItem)
                                    @php
                                        $roleBadge = match($userItem->role) {
                                            'admin'           => ['label' => 'Administrator',   'bg' => '#1e293b', 'color' => '#ffffff', 'icon' => 'bi-shield-shaded'],
                                            'project_manager' => ['label' => 'Project Manager', 'bg' => '#eef2ff', 'color' => '#4f46e5', 'icon' => 'bi-briefcase-fill'],
                                            'team_leader'     => ['label' => 'Team Leader',     'bg' => '#fffbeb', 'color' => '#b45309', 'icon' => 'bi-diagram-3-fill'],
                                            'employee'        => ['label' => 'Employee',        'bg' => '#ecfdf5', 'color' => '#047857', 'icon' => 'bi-person-badge-fill'],
                                            default           => ['label' => ucfirst($userItem->role), 'bg' => '#f1f5f9', 'color' => '#475569', 'icon' => 'bi-person'],
                                        };
                                        $initials = collect(explode(' ', $userItem->name))->map(fn($p) => strtoupper(substr($p, 0, 1)))->take(2)->join('');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0"
                                                     style="width: 32px; height: 32px; background: {{ $roleBadge['bg'] }}; color: {{ $roleBadge['color'] }}; font-size: 11px;">
                                                    {{ $initials }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-navy fs-13">{{ $userItem->name }}</div>
                                                    <small class="text-muted fs-11">{{ $userItem->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill px-2.5 py-1 fs-11"
                                                  style="background: {{ $roleBadge['bg'] }}; color: {{ $roleBadge['color'] }}; font-weight: 600;">
                                                <i class="bi {{ $roleBadge['icon'] }} me-1"></i> {{ $roleBadge['label'] }}
                                            </span>
                                        </td>
                                        <td class="fs-12 text-secondary">{{ $userItem->designation ?? '—' }}</td>
                                        <td class="fs-12 text-secondary">{{ $userItem->phone ?? '—' }}</td>
                                        <td>
                                            <span class="badge {{ $userItem->status === 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} fs-11">
                                                {{ ucfirst($userItem->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.users.edit', $userItem) }}" class="btn btn-sm btn-outline-secondary px-2 py-1 fs-11 rounded-2">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>
        </div>
    @endif

    <x-slot:scripts>
        <script>
        $(document).ready(function() {
            // Task Status Bar Chart
            const barCtx = document.getElementById('taskStatusBarChart');
            if (barCtx) {
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Completed', 'In Progress', 'In Review', 'To Do', 'Overdue'],
                        datasets: [{
                            label: 'Tasks',
                            data: [186, 32, 14, 5, 3],
                            backgroundColor: ['#16A34A', '#2563EB', '#D97706', '#64748B', '#DC2626'],
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#F1F5F9' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // Project Portfolio Doughnut Chart
            const doughnutCtx = document.getElementById('projectDoughnutChart');
            if (doughnutCtx) {
                new Chart(doughnutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Active', 'Completed', 'Planning', 'On Hold'],
                        datasets: [{
                            data: [18, 4, 2, 0],
                            backgroundColor: ['#2563EB', '#16A34A', '#4F46E5', '#CBD5E1'],
                            borderWidth: 2,
                            borderColor: '#FFFFFF'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: { legend: { display: false } }
                    }
                });
            }
        });
        </script>
    </x-slot:scripts>

</x-layouts.dashboard>
