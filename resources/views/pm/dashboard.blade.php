<x-layouts.dashboard title="Project Manager Workspace">

    {{-- Header Banner --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">
                Sprint &amp; Delivery Management
            </h3>
            <p class="text-muted fs-13 mb-0">
                Welcome, <strong>{{ Auth::user()->name }}</strong> &bull; Tracking 6 Assigned Projects &amp; Sprints
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 px-3 py-2 fs-13">
                <i class="bi bi-clock-history me-1"></i> Sprint Timeline
            </button>
            <button type="button" class="btn btn-sm btn-primary rounded-3 px-3 py-2 fs-13" style="background: var(--dash-brand); border: none;">
                <i class="bi bi-plus-circle me-1"></i> New Task Assignment
            </button>
        </div>
    </div>
    @php $hours = auth()->user()->hoursSummary(); @endphp

{{-- Hours overview --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="dash-card p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #eef2ff; color: #4f46e5; font-size: 20px;">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <div class="fs-12 text-muted fw-medium">Total Hours (all projects)</div>
                <div class="fs-20 fw-bold" style="color: #4f46e5;">{{ $hours['total_hours'] }} h</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="dash-card p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #ecfdf5; color: #059669; font-size: 20px;">
                <i class="bi bi-calendar-week"></i>
            </div>
            <div>
                <div class="fs-12 text-muted fw-medium">Hours This Week</div>
                <div class="fs-20 fw-bold" style="color: #059669;">{{ $hours['week_hours'] }} h</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="dash-card p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #fffbeb; color: #d97706; font-size: 20px;">
                <i class="bi bi-folder-check"></i>
            </div>
            <div>
                <div class="fs-12 text-muted fw-medium">Projects</div>
                <div class="fs-20 fw-bold" style="color: #d97706;">{{ $hours['projects']->count() }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Hours by project --}}
<x-card title="Hours by Project" icon="bi-bar-chart-line-fill" noBodyPadding="true" class="mb-4">
    <div class="table-responsive">
        <table class="dash-table align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 36%;">Project</th>
                    <th>Tasks</th>
                    <th>Progress</th>
                    <th class="text-end">Hours</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($hours['projects'] as $p)
                    @php $percent = $p->tasks_count ? round($p->done_tasks_count / $p->tasks_count * 100) : 0; @endphp
                    <tr>
                        <td>
                            <a href="{{ route('pm.tasks.index', $p) }}" class="fw-semibold text-navy text-decoration-none fs-13">{{ $p->name }}</a>
                        </td>
                        <td class="fs-13">{{ $p->done_tasks_count }} / {{ $p->tasks_count }} done</td>
                        <td style="min-width: 130px;">
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: {{ $percent }}%;"></div>
                            </div>
                            <small class="text-muted fs-12">{{ $percent }}%</small>
                        </td>
                        <td class="text-end fs-13 fw-semibold">{{ (float) $p->total_hours }} h</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted fs-13 py-4">No projects yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-card>

    {{-- 4 PM KPI Metrics --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="My Managed Projects" value="6" icon="bi-briefcase-fill" color="primary" footer="5 on schedule &bull; 1 review" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Active Sprint Tasks" value="42" icon="bi-list-task" color="indigo" footer="Assigned to 14 developers" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Pending PM Reviews" value="5" icon="bi-patch-check-fill" color="warning" valueClass="text-warning" footer="Action required &bull; code deliverables" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Sprint Velocity" value="96.5%" icon="bi-lightning-charge-fill" color="success" valueClass="text-success" trend="+4.2%" footer="vs last sprint" />
        </div>
    </div>

    {{-- Pending Reviews Approval Queue --}}
    <x-card title="Deliverables Waiting for PM Approval" icon="bi-hourglass-top" badge="5 Pending Reviews" class="mb-4" noBodyPadding="true">
        <div class="table-responsive">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Task / Deliverable</th>
                        <th>Project</th>
                        <th>Submitted By</th>
                        <th>Priority</th>
                        <th>Submitted Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="fw-semibold text-navy">JWT Token Refresh &amp; Rotation Logic</div>
                            <small class="text-muted"><i class="bi bi-paperclip me-1"></i> 2 files attached (PR #142)</small>
                        </td>
                        <td>Baseline ERP Platform</td>
                        <td>Rohan Verma (Dev)</td>
                        <td><span class="priority-high">High</span></td>
                        <td>Today, 09:40 AM</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-success rounded-2 px-2 py-1 fs-12">
                                <i class="bi bi-check2"></i> Approve
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1 fs-12 ms-1">
                                <i class="bi bi-arrow-counterclockwise"></i> Request Changes
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="fw-semibold text-navy">Stripe Webhook Event Listener &amp; DB Ledger</div>
                            <small class="text-muted"><i class="bi bi-paperclip me-1"></i> Unit tests passing (100%)</small>
                        </td>
                        <td>Fintech Payment API</td>
                        <td>Vikram Malhotra (TL)</td>
                        <td><span class="priority-urgent">Urgent</span></td>
                        <td>Yesterday, 05:15 PM</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-success rounded-2 px-2 py-1 fs-12">
                                <i class="bi bi-check2"></i> Approve
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1 fs-12 ms-1">
                                <i class="bi bi-arrow-counterclockwise"></i> Request Changes
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Managed Projects Grid --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <x-card title="Current Sprint S-14 Milestones" icon="bi-diagram-3-fill" badge="Ends Nov 30" class="h-100 mb-0">
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-semibold text-navy fs-14">Baseline ERP Platform (Phase 2)</span>
                        <x-badge status="active" text="84% Sprinted" />
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 84%"></div>
                    </div>
                    <small class="text-muted fs-12 d-block mt-1">21 of 25 tasks completed &bull; 4 in progress</small>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-semibold text-navy fs-14">Fintech Payment Gateway Integration</span>
                        <x-badge status="in_review" text="92% Ready" />
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 92%"></div>
                    </div>
                    <small class="text-muted fs-12 d-block mt-1">12 of 13 tasks completed &bull; 1 final PM review</small>
                </div>
            </x-card>
        </div>

        <div class="col-lg-6">
            <x-card title="Team Resource Workload" icon="bi-people-fill" badge="14 Members" class="h-100 mb-0">
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="user-avatar-sm" style="background: rgba(37, 99, 235, 0.1); color: #2563EB;">VM</div>
                        <div>
                            <div class="fw-semibold text-navy fs-13">Vikram Malhotra</div>
                            <small class="text-muted fs-11">Team Leader &bull; 4 active tasks</small>
                        </div>
                    </div>
                    <span class="badge bg-light text-navy border">Optimal (80%)</span>
                </div>

                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="user-avatar-sm" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">RV</div>
                        <div>
                            <div class="fw-semibold text-navy fs-13">Rohan Verma</div>
                            <small class="text-muted fs-11">Software Engineer &bull; 3 active tasks</small>
                        </div>
                    </div>
                    <span class="badge bg-light text-navy border">Optimal (75%)</span>
                </div>

                <div class="d-flex justify-content-between align-items-center py-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="user-avatar-sm" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">AK</div>
                        <div>
                            <div class="fw-semibold text-navy fs-13">Ananya Kulkarni</div>
                            <small class="text-muted fs-11">UI/UX Designer &bull; 5 active tasks</small>
                        </div>
                    </div>
                    <span class="badge bg-warning-subtle text-warning border">Heavy (95%)</span>
                </div>
            </x-card>
        </div>
    </div>

</x-layouts.dashboard>
