<section class="hero position-relative overflow-hidden" id="hero">
    {{-- Subtle Background Shapes --}}
    <div class="hero-shape hero-shape-1"></div>
    <div class="hero-shape hero-shape-2"></div>

    <div class="container position-relative">
        <div class="row align-items-center g-5 py-4">

            {{-- Left Content --}}
            <div class="col-lg-6 text-center text-lg-start">
                <div class="badge-tag mb-3 d-inline-flex align-items-center gap-2 reveal">
                    <span class="badge-dot"></span>
                    <span>Baseline IT Development Pvt Ltd &bull; Project Portal</span>
                </div>

                <h1 class="hero-title reveal" style="transition-delay:.08s">
                    Manage Projects.<br>
                    <span class="text-gradient">Empower Your Team.</span><br>
                    Achieve More.
                </h1>

                <p class="hero-text reveal" style="transition-delay:.16s">
                    Bring your team, projects and tasks together in one place. Plan smarter, collaborate better and achieve your delivery goals faster with Baseline IT Development.
                </p>

                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start mb-4 reveal" style="transition-delay:.24s">
                    <a href="#cta" class="btn btn-brand btn-lg-custom">
                        <i class="bi bi-rocket-takeoff-fill"></i> Get Started
                    </a>
                    <a href="#features" class="btn btn-outline-brand btn-lg-custom">
                        <i class="bi bi-compass"></i> Explore Features
                    </a>
                </div>

                {{-- Quick Trust Highlights --}}
                <div class="hero-stats-row d-flex flex-wrap gap-4 pt-3 border-top justify-content-center justify-content-lg-start reveal" style="transition-delay:.32s">
                    <div class="stat-pill">
                        <i class="bi bi-shield-check text-brand"></i>
                        <span>Role-Based Control</span>
                    </div>
                    <div class="stat-pill">
                        <i class="bi bi-lightning-charge text-accent"></i>
                        <span>Fast &amp; Responsive</span>
                    </div>
                    <div class="stat-pill">
                        <i class="bi bi-pie-chart text-brand"></i>
                        <span>Live Analytics</span>
                    </div>
                </div>
            </div>

            {{-- Right: Dashboard Preview --}}
            <div class="col-lg-6 reveal-right" style="transition-delay:.2s">
                <div class="dashboard-preview-window">
                    {{-- Window Header Bar --}}
                    <div class="window-bar d-flex align-items-center justify-content-between">
                        <div class="window-controls d-flex gap-2">
                            <span class="dot red"></span>
                            <span class="dot yellow"></span>
                            <span class="dot green"></span>
                        </div>
                        <div class="window-title text-truncate">
                            <i class="bi bi-shield-lock me-1 text-muted"></i> Baseline IT &bull; Project Workspace
                        </div>
                        <div class="window-badge">
                            <span class="status-live">Live</span>
                        </div>
                    </div>

                    {{-- Window Content --}}
                    <div class="window-body">
                        {{-- Mini Stats Row --}}
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="mini-stat-card">
                                    <span class="mini-label">Active Projects</span>
                                    <div class="mini-val text-brand">24 <small class="text-success fs-7">+4</small></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mini-stat-card">
                                    <span class="mini-label">Tasks Done</span>
                                    <div class="mini-val text-success">186 <small class="text-muted fs-7">/ 210</small></div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mini-stat-card">
                                    <span class="mini-label">Team Members</span>
                                    <div class="mini-val text-navy">45 <small class="text-brand fs-7">Active</small></div>
                                </div>
                            </div>
                        </div>

                        {{-- Active Sprint Progress --}}
                        <div class="preview-card mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="icon-circle bg-primary-subtle text-primary">
                                        <i class="bi bi-folder-check"></i>
                                    </span>
                                    <div>
                                        <div class="fw-semibold text-navy fs-14">Enterprise Web Portal</div>
                                        <small class="text-muted">Baseline IT Development &bull; Client Delivery</small>
                                    </div>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 fs-12">92% Completed</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 3px;">
                                <div class="progress-bar" role="progressbar" style="width: 92%; background: var(--brand); border-radius: 3px;"></div>
                            </div>
                        </div>

                        {{-- Task Stream --}}
                        <div class="preview-task-list">
                            <div class="task-item-preview d-flex align-items-center justify-content-between p-2 rounded mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="check-box checked"><i class="bi bi-check"></i></span>
                                    <div>
                                        <div class="task-title fs-13">Role-Based Access Control Architecture</div>
                                        <small class="text-muted fs-11"><i class="bi bi-person me-1"></i> Admin &bull; Backend Module</small>
                                    </div>
                                </div>
                                <span class="badge-status done">Done</span>
                            </div>

                            <div class="task-item-preview d-flex align-items-center justify-content-between p-2 rounded mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="check-box in-progress"><i class="bi bi-clock-history"></i></span>
                                    <div>
                                        <div class="task-title fs-13">Agile Sprint &amp; Task Workflow</div>
                                        <small class="text-muted fs-11"><i class="bi bi-person me-1"></i> Project Manager &bull; Frontend</small>
                                    </div>
                                </div>
                                <span class="badge-status in-progress">In Review</span>
                            </div>

                            <div class="task-item-preview d-flex align-items-center justify-content-between p-2 rounded">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="check-box pending"></span>
                                    <div>
                                        <div class="task-title fs-13">Automated Reports &amp; Analytics Export</div>
                                        <small class="text-muted fs-11"><i class="bi bi-person me-1"></i> Team Leader &bull; Core API</small>
                                    </div>
                                </div>
                                <span class="badge-status pending">To Do</span>
                            </div>
                        </div>

                        {{-- Floating Highlight Card --}}
                        <div class="floating-badge-card d-none d-sm-flex align-items-center gap-3">
                            <div class="floating-icon">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-13 text-navy">+42% Team Velocity</div>
                                <small class="text-muted fs-11">Baseline IT software sprint efficiency</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
