<x-layouts.dashboard title="Task Board">

    {{-- ── Page Header ──────────────────────────────────────────────────── --}}
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
        <div>
            <a href="{{ route('pm.projects.index') }}" class="kb-back-link">
                <i class="bi bi-arrow-left"></i> Back to My Projects
            </a>
            <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">
                {{ $project->name }}
            </h3>
            <p class="text-muted fs-13 mb-0">
                <i class="bi bi-info-circle me-1 opacity-50"></i>
                Drag a card to another column to change its status
            </p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <div class="kb-view-switcher">
                <a href="{{ route('pm.tasks.index', $project) }}" class="kb-view-btn">
                    <i class="bi bi-list-ul"></i> List
                </a>
                <a href="{{ route('pm.tasks.board', $project) }}" class="kb-view-btn active">
                    <i class="bi bi-kanban"></i> Board
                </a>
            </div>
            <a href="{{ route('pm.tasks.create', $project) }}" class="kb-new-task-btn">
                <i class="bi bi-plus-lg"></i> New Task
            </a>
        </div>
    </div>

    @php
        $columnMeta = [
            'todo'        => ['label' => 'Pending',     'color' => '#64748b', 'accent' => '#f1f5f9', 'ring' => '#cbd5e1'],
            'in_progress' => ['label' => 'In Progress', 'color' => '#2563eb', 'accent' => '#eff6ff', 'ring' => '#bfdbfe'],
            'in_review'   => ['label' => 'In Review',   'color' => '#d97706', 'accent' => '#fffbeb', 'ring' => '#fde68a'],
            'done'        => ['label' => 'Done',        'color' => '#16a34a', 'accent' => '#f0fdf4', 'ring' => '#bbf7d0'],
        ];
        $priorityMeta = [
            'low'    => ['label' => 'Low',    'rank' => 4, 'color' => '#64748b', 'bg' => '#f1f5f9'],
            'medium' => ['label' => 'Medium', 'rank' => 3, 'color' => '#2563eb', 'bg' => '#eff6ff'],
            'high'   => ['label' => 'High',   'rank' => 2, 'color' => '#ea580c', 'bg' => '#fff7ed'],
            'urgent' => ['label' => 'Urgent', 'rank' => 1, 'color' => '#dc2626', 'bg' => '#fef2f2'],
        ];
        $avatarColors = ['#6366f1','#2563eb','#0891b2','#059669','#d97706','#dc2626','#7c3aed','#db2777'];
    @endphp

    {{-- ── Kanban Board ─────────────────────────────────────────────────── --}}
    <div class="kb-board" id="kbBoard" data-csrf="{{ csrf_token() }}">

        @foreach ($columnMeta as $status => $meta)
        <div class="kb-col">

            {{-- Column Header --}}
            <div class="kb-col-head" style="--col-color: {{ $meta['color'] }};">
                <div class="d-flex align-items-center gap-2">
                    <span class="kb-status-dot" style="background: {{ $meta['color'] }};"></span>
                    <span class="kb-col-label">{{ $meta['label'] }}</span>
                </div>
                <span class="kb-col-count"
                      style="background: {{ $meta['accent'] }}; color: {{ $meta['color'] }}; border-color: {{ $meta['ring'] }};">
                    {{ $columns[$status]->count() }}
                </span>
            </div>

            {{-- Drop Zone --}}
            <div class="kb-col-body" data-status="{{ $status }}">

                {{-- Always in DOM; JS shows/hides based on card count --}}
                <div class="kb-empty" style="{{ $columns[$status]->isNotEmpty() ? 'display:none;' : '' }}">
                    <i class="bi bi-inbox" style="font-size: 28px; color: {{ $meta['ring'] }};"></i>
                    <span>No tasks</span>
                </div>

                @foreach ($columns[$status] as $task)
                    @php
                        $p       = $priorityMeta[$task->priority] ?? $priorityMeta['medium'];
                        $overdue = $task->due_date && $task->due_date->isPast() && $task->status !== 'done';
                        $initials= strtoupper(substr($task->assignee?->name ?? 'U', 0, 1));
                        $avColor = $avatarColors[crc32($task->assignee?->name ?? 'U') % count($avatarColors)];
                    @endphp

                    <div class="kb-card"
                         style="--priority-color: {{ $p['color'] }};"
                         data-url="{{ route('pm.tasks.move', $task) }}"
                         data-rank="{{ $p['rank'] }}"
                         data-due="{{ $task->due_date?->timestamp ?? 9999999999 }}"
                         data-id="{{ $task->id }}">  

                        <div class="kb-card-stripe" style="background: {{ $p['color'] }};"></div>

                        <div class="kb-card-inner">

                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <span class="kb-card-title">{{ $task->title }}</span>
                                <a href="{{ route('pm.tasks.edit', $task) }}" class="kb-edit-icon" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>

                            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                                <span class="kb-priority-pill"
                                      style="color: {{ $p['color'] }}; background: {{ $p['bg'] }}; border-color: {{ $p['color'] }}2a;">
                                    {{ $p['label'] }}
                                </span>
                                @if ($task->due_date)
                                    <span class="kb-due {{ $overdue ? 'kb-due--overdue' : '' }}">
                                        <i class="bi bi-calendar3"></i>
                                        {{ $task->due_date->format('M d') }}
                                        @if ($overdue)<i class="bi bi-exclamation-circle-fill ms-1"></i>@endif
                                    </span>
                                @endif
                            </div>

                            <div class="kb-assignee">
                                <span class="kb-avatar" style="background: {{ $avColor }};">{{ $initials }}</span>
                                <span class="kb-assignee-name">{{ $task->assignee?->name ?? 'Unassigned' }}</span>
                            </div>

                        </div>
                    </div>
                @endforeach

            </div>{{-- /.kb-col-body --}}
        </div>{{-- /.kb-col --}}
        @endforeach

    </div>{{-- /.kb-board --}}


    {{-- ── Styles & Scripts ─────────────────────────────────────────────── --}}
    <x-slot:styles>
        <link href="{{ asset('assets/css/board.css') }}" rel="stylesheet">
    </x-slot:styles>

    <x-slot:scripts>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <script src="{{ asset('assets/js/board.js') }}"></script>
    </x-slot:scripts>

</x-layouts.dashboard>
