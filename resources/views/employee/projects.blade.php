<x-layouts.dashboard title="My Projects">

    <div class="mb-4">
        <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">My Projects</h3>
        <p class="text-muted fs-13 mb-0">Baseline IT Development &bull; Projects you have tasks in</p>
    </div>

    <div class="row g-3">
        @forelse ($projects as $project)
            <div class="col-md-6 col-xl-4">
                <div class="dash-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div class="fw-bold text-navy">{{ $project->name }}</div>
                        <span class="badge bg-primary-subtle text-primary text-capitalize">{{ str_replace('_', ' ', $project->status) }}</span>
                    </div>
                    @if ($project->description)
                        <p class="text-muted fs-13 mb-3">{{ \Illuminate\Support\Str::limit($project->description, 140) }}</p>
                    @endif
                    <ul class="list-unstyled fs-13 mb-0">
                        <li class="d-flex justify-content-between py-1"><span class="text-muted">Project manager</span><span>{{ $project->manager?->name ?? '—' }}</span></li>
                        <li class="d-flex justify-content-between py-1"><span class="text-muted">Start</span><span>{{ $project->start_date?->format('M d, Y') ?? '—' }}</span></li>
                        <li class="d-flex justify-content-between py-1"><span class="text-muted">Deadline</span><span>{{ $project->deadline?->format('M d, Y') ?? '—' }}</span></li>
                        <li class="d-flex justify-content-between py-1"><span class="text-muted">My tasks</span><span class="fw-semibold">{{ $project->my_done_count }} / {{ $project->my_tasks_count }} done</span></li>
                    </ul>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="dash-card p-5 text-center">
                    <i class="bi bi-folder2-open text-muted" style="font-size: 44px;"></i>
                    <h6 class="fw-bold text-navy mt-3 mb-1">No Projects Yet</h6>
                    <p class="text-muted fs-13 mb-0">Projects appear here once your team leader gives you a task.</p>
                </div>
            </div>
        @endforelse
    </div>

</x-layouts.dashboard>