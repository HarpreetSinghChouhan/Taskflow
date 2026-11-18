<x-layouts.dashboard title="Developer Workspace">

    {{-- Welcome Banner --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">
                My Developer Workspace
            </h3>
            <p class="text-muted fs-13 mb-0">
                Welcome back, <strong>{{ Auth::user()->name }}</strong> &bull; {{ Auth::user()->designation ?? 'Software Engineer' }}
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fs-12 d-flex align-items-center gap-2">
                <span class="role-badge-dot role-employee"></span> Sprint 14 &bull; Active
            </span>
        </div>
    </div>

    {{-- 4 Employee KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="My Assigned Tasks" value="9" icon="bi-list-check" color="primary" footer="Total tasks in current sprint" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="In Progress (Active)" value="3" icon="bi-clock-history" color="indigo" valueClass="text-primary" trend="Working today" footer="2 due this week" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Submitted for Review" value="2" icon="bi-send-check-fill" color="warning" valueClass="text-warning" footer="With TL Vikram Malhotra" />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-stat-card label="Completed Tasks" value="4" icon="bi-check-circle-fill" color="success" valueClass="text-success" trend="44.4%" footer="of assigned sprint done" />
        </div>
    </div>

    {{-- My Active Tasks List --}}
    <x-card title="My Sprint Tasks (Sprint 14)" icon="bi-kanban" badge="Assigned to Me" class="mb-4" noBodyPadding="true">
        <div class="table-responsive">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Task Title &amp; Deliverables</th>
                        <th>Project</th>
                        <th>Priority</th>
                        <th>Due Date</th>
                        <th>Current Status</th>
                        <th class="text-end">Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-muted">1</td>
                        <td>
                            <div class="fw-semibold text-navy">Implement Role-Based Access Control Middleware</div>
                            <small class="text-muted"><i class="bi bi-chat-left-text me-1"></i> 3 comments &bull; <i class="bi bi-paperclip me-1"></i> 1 attachment</small>
                        </td>
                        <td><span class="badge bg-light text-navy border">Baseline ERP</span></td>
                        <td><span class="priority-urgent">Urgent</span></td>
                        <td><span class="text-danger fw-semibold">Nov 20, 2026</span></td>
                        <td><x-badge status="in_progress" /></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-2 px-2 py-1 fs-12">
                                <i class="bi bi-send me-1"></i> Submit for Review
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">2</td>
                        <td>
                            <div class="fw-semibold text-navy">JWT Token Refresh &amp; Session Invalidation Handler</div>
                            <small class="text-muted"><i class="bi bi-chat-left-text me-1"></i> 4 comments &bull; PR #142 opened</small>
                        </td>
                        <td><span class="badge bg-light text-navy border">Fintech Gateway</span></td>
                        <td><span class="priority-high">High</span></td>
                        <td>Nov 22, 2026</td>
                        <td><x-badge status="in_review" /></td>
                        <td class="text-end">
                            <span class="badge bg-light text-muted border px-2 py-1 fs-11">Awaiting TL Signoff</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">3</td>
                        <td>
                            <div class="fw-semibold text-navy">Database Seeder for 4 User Roles &amp; Default Permissions</div>
                            <small class="text-muted"><i class="bi bi-check2-all text-success me-1"></i> Completed &amp; verified</small>
                        </td>
                        <td><span class="badge bg-light text-navy border">TaskFlow Core</span></td>
                        <td><span class="priority-medium">Medium</span></td>
                        <td>Nov 18, 2026</td>
                        <td><x-badge status="completed" /></td>
                        <td class="text-end">
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-11">Verified</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">4</td>
                        <td>
                            <div class="fw-semibold text-navy">Build DomPDF Invoice &amp; Report Generator Template</div>
                            <small class="text-muted">Backlog item ready to begin</small>
                        </td>
                        <td><span class="badge bg-light text-navy border">TaskFlow Core</span></td>
                        <td><span class="priority-low">Normal</span></td>
                        <td>Nov 28, 2026</td>
                        <td><x-badge status="todo" /></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-success rounded-2 px-2 py-1 fs-12">
                                <i class="bi bi-play-fill"></i> Start Task
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Today's Focus & Quick Notes --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <x-card title="Sprint 14 Milestone Progress" icon="bi-flag-fill" badge="44.4% Done" class="h-100 mb-0">
                <p class="text-muted fs-13 mb-3">
                    You have 4 days remaining before the sprint freeze on Friday, 6:00 PM. Prioritize the urgent RBAC middleware deliverable.
                </p>
                <div class="progress mb-2" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 44.4%"></div>
                </div>
                <div class="d-flex justify-content-between text-muted fs-11">
                    <span>4 Completed</span>
                    <span>3 In Progress</span>
                    <span>2 In Review</span>
                </div>
            </x-card>
        </div>

        <div class="col-lg-6">
            <x-card title="Recent Feedback &amp; Review Comments" icon="bi-chat-square-quote-fill" badge="Sprint 14" class="h-100 mb-0">
                <div class="d-flex flex-column gap-3 fs-13">
                    <div class="p-2 rounded-2 border bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-navy">Vikram Malhotra (TL)</strong>
                            <small class="text-muted fs-11">Today, 10:15 AM</small>
                        </div>
                        <span class="text-muted fs-12">"Great work on the database indexing. Please verify the softDeletes index before final PR merge."</span>
                    </div>

                    <div class="p-2 rounded-2 border bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-navy">Sarah Jenkins (PM)</strong>
                            <small class="text-muted fs-11">Yesterday, 04:30 PM</small>
                        </div>
                        <span class="text-muted fs-12">"Client approved the OAuth scope changes. Please proceed with token rotation implementation."</span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

</x-layouts.dashboard>
