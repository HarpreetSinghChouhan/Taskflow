<x-layouts.dashboard title="Team Leader Dashboard">

    {{-- Header Banner --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">
                Engineering Team Lead Hub
            </h3>
            <p class="text-muted fs-13 mb-0">
                Lead Engineer: <strong>{{ Auth::user()->name }}</strong> &bull; Managing 8 Core Software Engineers
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 px-3 py-2 fs-13">
                <i class="bi bi-people me-1"></i> Standup Notes
            </button>
            <button type="button" class="btn btn-sm btn-primary rounded-3 px-3 py-2 fs-13" style="background: var(--dash-brand); border: none;">
                <i class="bi bi-plus-circle me-1"></i> Assign Task to Team
            </button>
        </div>
    </div>

    {{-- 4 TL KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Assigned Engineers" value="8" icon="bi-people-fill" color="teal" footer="100% active &bull; 0 on leave" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Sub-Team Active Tasks" value="18" icon="bi-list-task" color="primary" footer="Sprint 14 backlog in progress" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Code Reviews Awaiting TL" value="3" icon="bi-patch-check" color="warning" valueClass="text-warning" footer="PRs waiting &bull; merge readiness" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Sprint Tasks Completed" value="28" icon="bi-check2-circle" color="success" valueClass="text-success" footer="On target for Friday release" />
        </div>
    </div>

    {{-- Code Reviews & Pull Requests Waiting for Team Leader --}}
    <x-card title="Team Code Review &amp; Deliverables Board" icon="bi-code-slash" badge="Sprint 14 Queue" class="mb-4" noBodyPadding="true">
        <div class="table-responsive">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Module / Commit Task</th>
                        <th>Developer</th>
                        <th>Branch / PR</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="fw-semibold text-navy">MySQL Query Optimization &amp; Index Mapping</div>
                            <small class="text-muted">Baseline ERP &bull; 8 queries rewritten (3x speedup)</small>
                        </td>
                        <td>Rohan Verma</td>
                        <td><code>feature/db-indices</code></td>
                        <td><x-badge status="in_review" text="Ready for TL" /></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-success rounded-2 px-2 py-1 fs-12">
                                <i class="bi bi-check2-all"></i> Approve Code
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-2 px-2 py-1 fs-12 ms-1">
                                <i class="bi bi-chat-left-text"></i> Comment
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="fw-semibold text-navy">Export Task Reports to XLSX with Maatwebsite</div>
                            <small class="text-muted">TaskFlow Core &bull; Unit tests added</small>
                        </td>
                        <td>Kavita Patel</td>
                        <td><code>feature/excel-export</code></td>
                        <td><x-badge status="in_review" text="Ready for TL" /></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-success rounded-2 px-2 py-1 fs-12">
                                <i class="bi bi-check2-all"></i> Approve Code
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-2 px-2 py-1 fs-12 ms-1">
                                <i class="bi bi-chat-left-text"></i> Comment
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Sub-Team Developers Status Table --}}
    <x-card title="Sub-Team Live Sprints (8 Developers)" icon="bi-people" badge="Daily Sprints" class="mb-0" noBodyPadding="true">
        <div class="table-responsive">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Engineer</th>
                        <th>Current Task</th>
                        <th>Project</th>
                        <th>Priority</th>
                        <th>Today's Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="fw-semibold text-navy">Rohan Verma</div>
                            <small class="text-muted">Software Engineer (Backend)</small>
                        </td>
                        <td>MySQL Query Optimization</td>
                        <td>Baseline ERP Platform</td>
                        <td><span class="priority-high">High</span></td>
                        <td><x-badge status="in_review" text="Review Submitted" /></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="fw-semibold text-navy">Kavita Patel</div>
                            <small class="text-muted">Software Engineer (Full-Stack)</small>
                        </td>
                        <td>Export Task Reports to XLSX</td>
                        <td>TaskFlow Core</td>
                        <td><span class="priority-medium">Medium</span></td>
                        <td><x-badge status="in_progress" /></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="fw-semibold text-navy">Amitabh Roy</div>
                            <small class="text-muted">Frontend Engineer</small>
                        </td>
                        <td>Chart.js Live Analytics Widgets</td>
                        <td>Logistics GPS App</td>
                        <td><span class="priority-low">Normal</span></td>
                        <td><x-badge status="active" /></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>

</x-layouts.dashboard>
