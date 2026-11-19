<x-layouts.dashboard title="My Projects">

    {{-- Header --}}
    <div class="mb-4">
        <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">
            My Projects
        </h3>
        <p class="text-muted fs-13 mb-0">
            Baseline IT Development &bull; Projects assigned to you
        </p>
    </div>

    {{-- Filters --}}
    <div class="dash-card mb-4 p-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="nav nav-pills gap-1 flex-wrap">
                <a href="{{ route('pm.projects.index') }}" class="btn btn-sm {{ empty($statusFilter) ? 'btn-primary' : 'btn-light text-muted' }} rounded-pill px-3 fs-13">
                    All <span class="badge bg-white {{ empty($statusFilter) ? 'text-primary' : 'text-dark' }} ms-1">{{ $counts['all'] }}</span>
                </a>
                @foreach (['planned' => 'Planned', 'active' => 'Active', 'on_hold' => 'On Hold', 'completed' => 'Completed'] as $key => $label)
                    <a href="{{ route('pm.projects.index', ['status' => $key]) }}" class="btn btn-sm {{ $statusFilter === $key ? 'btn-primary' : 'btn-light text-muted' }} rounded-pill px-3 fs-13">
                        {{ $label }}
                        <span class="badge bg-white {{ $statusFilter === $key ? 'text-primary' : 'text-dark' }} ms-1">{{ $counts[$key] }}</span>
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('pm.projects.index') }}" class="d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0">
                @if ($statusFilter)
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                @endif
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by project name..." value="{{ $search }}">
                </div>
                <button type="submit" class="btn btn-sm btn-outline-primary">Search</button>
                @if ($search || $statusFilter)
                    <a href="{{ route('pm.projects.index') }}" class="btn btn-sm btn-outline-secondary" title="Clear Filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Projects Table --}}
    <x-card noBodyPadding="true">
        <div class="table-responsive">
            <table class="dash-table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 36%;">Project</th>
                        <th>Start</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 18%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                        @php
                            $statusMeta = match($project->status) {
                                'planned'   => ['label' => 'Planned',   'bg' => '#eff6ff', 'color' => '#2563eb'],
                                'active'    => ['label' => 'Active',    'bg' => '#ecfdf5', 'color' => '#047857'],
                                'on_hold'   => ['label' => 'On Hold',   'bg' => '#fffbeb', 'color' => '#b45309'],
                                'completed' => ['label' => 'Completed', 'bg' => '#f1f5f9', 'color' => '#475569'],
                                default     => ['label' => ucfirst($project->status), 'bg' => '#f1f5f9', 'color' => '#475569'],
                            };
                            $overdue = $project->deadline
                                && $project->deadline->isPast()
                                && $project->status !== 'completed';
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold text-navy text-truncate" style="font-size: 14px; max-width: 320px;">
                                    {{ $project->name }}
                                </div>
                                @if ($project->description)
                                    <small class="text-muted d-block text-truncate fs-12" style="max-width: 320px;">
                                        {{ $project->description }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="fs-12 text-muted">{{ $project->start_date?->format('M d, Y') ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="fs-12 {{ $overdue ? 'text-danger fw-semibold' : 'text-muted' }}">
                                    {{ $project->deadline?->format('M d, Y') ?? '—' }}
                                    @if ($overdue)
                                        <i class="bi bi-exclamation-circle-fill ms-1" title="Overdue"></i>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2 py-1"
                                      style="background: {{ $statusMeta['bg'] }}; color: {{ $statusMeta['color'] }}; font-weight: 600; font-size: 12px; border: 1px solid rgba(0,0,0,0.06);">
                                    {{ $statusMeta['label'] }}
                                </span>
                            </td>
                            
                            <td class="text-end">
                                <a href="{{ route('pm.tasks.index', $project) }}" class="btn btn-sm btn-outline-primary px-2 py-1 fs-12 rounded-2" title="View Tasks">
                                    <i class="bi bi-list-task me-1"></i>Tasks
                                    <span class="badge bg-primary-subtle text-primary ms-1">{{ $project->tasks_count }}</span>
                                </a>
                                <a href="{{ route('pm.projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary px-2 py-1 fs-12 rounded-2" title="Edit Project">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-folder2-open text-muted" style="font-size: 44px;"></i>
                                    <h6 class="fw-bold text-navy mt-3 mb-1">No Projects Found</h6>
                                    <p class="text-muted fs-13 mb-0">No projects are assigned to you yet, or your filters match nothing.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($projects->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $projects->links() }}
            </div>
        @endif
    </x-card>

</x-layouts.dashboard>