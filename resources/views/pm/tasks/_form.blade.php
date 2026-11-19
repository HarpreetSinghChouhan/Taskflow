<div class="mb-3">
    <label for="title" class="form-label fw-semibold text-navy fs-13">
        Task Name <span class="text-danger">*</span>
    </label>
    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
           placeholder="e.g. Build login API" value="{{ old('title', $task->title ?? '') }}" required>
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label fw-semibold text-navy fs-13">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
              rows="4" placeholder="What needs to be done?">{{ old('description', $task->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="assigned_to" class="form-label fw-semibold text-navy fs-13">
            Assign To (Team Leader) <span class="text-danger">*</span>
        </label>
        <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to" required>
            <option value="">Select...</option>
            @foreach ($teamLeaders as $tl)
                <option value="{{ $tl->id }}"
                    {{ (string) old('assigned_to', $task->assigned_to ?? '') === (string) $tl->id ? 'selected' : '' }}>
                    {{ $tl->name }}@if ($tl->designation) ({{ $tl->designation }})@endif
                </option>
            @endforeach
        </select>
        @if ($teamLeaders->isEmpty())
            <small class="text-danger fs-12">No active team leader in your team yet.</small>
        @endif
        @error('assigned_to')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="due_date" class="form-label fw-semibold text-navy fs-13">Due Date</label>
        <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date"
               value="{{ old('due_date', $task?->due_date?->format('Y-m-d')) }}">
        @error('due_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="priority" class="form-label fw-semibold text-navy fs-13">
            Priority <span class="text-danger">*</span>
        </label>
        <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
            @foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'] as $value => $label)
                <option value="{{ $value }}"
                    {{ old('priority', $task->priority ?? 'medium') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('priority')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="status" class="form-label fw-semibold text-navy fs-13">
            Status <span class="text-danger">*</span>
        </label>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            @foreach (['todo' => 'Pending', 'in_progress' => 'In Progress', 'in_review' => 'In Review', 'done' => 'Done'] as $value => $label)
                <option value="{{ $value }}"
                    {{ old('status', $task->status ?? 'todo') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>