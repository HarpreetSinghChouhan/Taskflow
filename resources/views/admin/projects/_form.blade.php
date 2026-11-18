<div class="mb-3">
    <label for="name" class="form-label fw-semibold text-navy fs-13">
        Project Name <span class="text-danger">*</span>
    </label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
           placeholder="e.g. Client Portal Redesign" value="{{ old('name', $project->name ?? '') }}" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label fw-semibold text-navy fs-13">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
              rows="4" placeholder="What is this project about?">{{ old('description', $project->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="project_manager_id" class="form-label fw-semibold text-navy fs-13">
            Project Manager <span class="text-danger">*</span>
        </label>
        <select class="form-select @error('project_manager_id') is-invalid @enderror" id="project_manager_id" name="project_manager_id" required>
            <option value="">Select...</option>
            @foreach ($projectManagers as $pm)
                <option value="{{ $pm->id }}"
                    {{ (string) old('project_manager_id', $project->project_manager_id ?? '') === (string) $pm->id ? 'selected' : '' }}>
                    {{ $pm->name }}
                </option>
            @endforeach
        </select>
        @if ($projectManagers->isEmpty())
            <small class="text-danger fs-12">No active project manager found. Create one first.</small>
        @endif
        @error('project_manager_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="status" class="form-label fw-semibold text-navy fs-13">
            Status <span class="text-danger">*</span>
        </label>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            @foreach (['planned' => 'Planned', 'active' => 'Active', 'on_hold' => 'On Hold', 'completed' => 'Completed'] as $value => $label)
                <option value="{{ $value }}"
                    {{ old('status', $project->status ?? 'planned') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="start_date" class="form-label fw-semibold text-navy fs-13">Start Date</label>
        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date"
               value="{{ old('start_date', $project?->start_date?->format('Y-m-d')) }}">
        @error('start_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="deadline" class="form-label fw-semibold text-navy fs-13">Deadline</label>
        <input type="date" class="form-control @error('deadline') is-invalid @enderror" id="deadline" name="deadline"
               value="{{ old('deadline', $project?->deadline?->format('Y-m-d')) }}">
        @error('deadline')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>