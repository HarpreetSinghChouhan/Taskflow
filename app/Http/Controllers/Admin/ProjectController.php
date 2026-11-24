<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Models\TaskLog;

class ProjectController extends Controller
{
    /**
     * List all projects (admin sees every project).
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');
        $search = $request->query('search');

        $query = Project::query()
            ->with('manager')
            ->withCount(['tasks', 'tasks as done_tasks_count' => fn ($q) => $q->where('status', 'done')])
            ->withSum('taskLogs as total_hours', 'hours');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($statusFilter && in_array($statusFilter, ['planned', 'active', 'on_hold', 'completed'])) {
            $query->where('status', $statusFilter);
        }

        $projects = $query->latest('id')->paginate(10)->withQueryString();

        $counts = [
            'all'       => Project::count(),
            'planned'   => Project::where('status', 'planned')->count(),
            'active'    => Project::where('status', 'active')->count(),
            'on_hold'   => Project::where('status', 'on_hold')->count(),
            'completed' => Project::where('status', 'completed')->count(),
        ];

        return view('admin.projects.index', compact('projects', 'counts', 'statusFilter', 'search'));
    }
    /**
    * Detailed progress and time tracking of one project.
    */
    public function show(Project $project): View
    {
        $project->load('manager');

        $tasks = $project->tasks()
            ->with(['assignee', 'employee'])
            ->withSum('logs as logged_hours', 'hours')
            ->orderByRaw("CASE status WHEN 'in_progress' THEN 1 WHEN 'in_review' THEN 2 WHEN 'todo' THEN 3 ELSE 4 END")
            ->orderBy('id')
            ->get();

        $byStatus = $tasks->countBy('status');
        $counts = [
            'todo'        => $byStatus['todo'] ?? 0,
            'in_progress' => $byStatus['in_progress'] ?? 0,
            'in_review'   => $byStatus['in_review'] ?? 0,
            'done'        => $byStatus['done'] ?? 0,
        ];

        $total = $tasks->count();
        $percent = $total ? round($counts['done'] / $total * 100) : 0;
        $overdue = $tasks->filter(fn ($t) => $t->due_date && $t->due_date->isPast() && $t->status !== 'done')->count();
        $totalHours = (float) $tasks->sum('logged_hours');

        $taskIds = $tasks->pluck('id');

        // Hours per person
        $people = TaskLog::selectRaw('user_id, SUM(hours) as hours, COUNT(*) as entries, MAX(log_date) as last_date')
            ->whereIn('task_id', $taskIds)
            ->groupBy('user_id')
            ->orderByDesc('hours')
            ->with('user')
            ->get();

        $recentLogs = TaskLog::with(['user', 'task'])
            ->whereIn('task_id', $taskIds)
            ->latest('log_date')->latest('id')
            ->limit(10)
            ->get();

        return view('admin.projects.show', compact(
            'project', 'tasks', 'counts', 'total', 'percent', 'overdue', 'totalHours', 'people', 'recentLogs'
        ));
    }

    public function create(): View
    {
        return view('admin.projects.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $project = Project::create($this->validated($request));

        return redirect()
            ->route('admin.projects.index')
            ->with('success', "Project '{$project->name}' created successfully.");
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.edit', array_merge(compact('project'), $this->formData()));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $project->update($this->validated($request));

        return redirect()
            ->route('admin.projects.index')
            ->with('success', "Project '{$project->name}' updated successfully.");
    }

    public function destroy(Project $project): RedirectResponse
    {
        $name = $project->name;
        $project->delete();

        return back()->with('success', "Project '{$name}' has been deleted.");
    }

    // Active project managers for the dropdown
    private function formData(): array
    {
        return [
            'projectManagers' => User::where('role', 'project_manager')
                ->where('status', 'active')->orderBy('name')->get(['id', 'name']),
        ];
    }

    // Shared validation for create and update
    private function validated(Request $request): array
    {
        return $request->validate([
            'name'               => ['required', 'string', 'max:150'],
            'description'        => ['nullable', 'string', 'max:5000'],
            'project_manager_id' => [
                'required',
                Rule::exists('users', 'id')->where(
                    fn ($q) => $q->where('role', 'project_manager')
                        ->where('status', 'active')
                        ->whereNull('deleted_at')
                ),
            ],
            'start_date'         => ['nullable', 'date'],
            'deadline'           => ['nullable', 'date', 'after_or_equal:start_date'],
            'status'             => ['required', 'in:planned,active,on_hold,completed'],
        ], [
            'project_manager_id.required' => 'Please assign a project manager.',
            'project_manager_id.exists'   => 'Selected project manager is invalid or inactive.',
            'deadline.after_or_equal'     => 'Deadline cannot be before the start date.',
        ]);
    }
}