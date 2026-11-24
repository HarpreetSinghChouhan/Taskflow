<x-layouts.dashboard title="Edit Task">

    <div class="mb-4">
        <a href="{{ route('pm.tasks.index', $project) }}" class="text-decoration-none text-muted fs-13 d-inline-flex align-items-center gap-1 mb-2">
            <i class="bi bi-arrow-left"></i> Back to Tasks
        </a>
        <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">
            Edit Task: {{ $task->title }}
        </h3>
        <p class="text-muted fs-13 mb-0">Project: {{ $project->name }}</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <x-card>
                <form method="POST" action="{{ route('pm.tasks.update', $task) }}">
                    @csrf
                    @method('PUT')

                    @include('pm.tasks._form')

                    <div class="d-flex align-items-center gap-3 pt-2">
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" style="background: var(--dash-brand); border: none;">
                            <i class="bi bi-check2 me-1"></i> Update Task
                        </button>
                        <a href="{{ route('pm.tasks.index', $project) }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">Cancel</a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

</x-layouts.dashboard>