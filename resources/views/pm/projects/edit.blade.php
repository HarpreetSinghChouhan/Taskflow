<x-layouts.dashboard title="Edit Project">

    <div class="mb-4">
        <a href="{{ route('pm.projects.index') }}" class="text-decoration-none text-muted fs-13 d-inline-flex align-items-center gap-1 mb-2">
            <i class="bi bi-arrow-left"></i> Back to My Projects
        </a>
        <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">
            Edit Project: {{ $project->name }}
        </h3>
        <p class="text-muted fs-13 mb-0">Update details, dates and status.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
            <ul class="mb-0 ps-3 fs-13">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <x-card>
                <form method="POST" action="{{ route('pm.projects.update', $project) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-navy fs-13">
                            Project Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                               value="{{ old('name', $project->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold text-navy fs-13">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                  rows="4">{{ old('description', $project->description) }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-navy fs-13">Project Manager</label>
                            <input type="text" class="form-control" value="{{ $project->manager?->name }}" disabled>
                            <small class="text-muted fs-12">Only an administrator can change the manager.</small>
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold text-navy fs-13">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                @foreach (['planned' => 'Planned', 'active' => 'Active', 'on_hold' => 'On Hold', 'completed' => 'Completed'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $project->status) === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label fw-semibold text-navy fs-13">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date"
                                   value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="deadline" class="form-label fw-semibold text-navy fs-13">Deadline</label>
                            <input type="date" class="form-control @error('deadline') is-invalid @enderror" id="deadline" name="deadline"
                                   value="{{ old('deadline', $project->deadline?->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3 pt-2">
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" style="background: var(--dash-brand); border: none;">
                            <i class="bi bi-check2 me-1"></i> Update Project
                        </button>
                        <a href="{{ route('pm.projects.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">Cancel</a>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

</x-layouts.dashboard>