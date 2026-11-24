<?php

namespace App\Http\Controllers\Pm;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{   
    /**
 * List tasks across all projects of the logged-in PM.
 */
    public function overview(Request $request): View
    {
        $pmId = Auth::id();
        $statusFilter = $request->query('status');
        $priorityFilter = $request->query('priority');
        $projectFilter = $request->query('project');
        $search = $request->query('search');

        // Only tasks that belong to this PM's projects
        $base = fn () => Task::whereHas('project', fn ($q) => $q->where('project_manager_id', $pmId));

        $query = $base()->with(['project', 'assignee', 'employee'])->withSum('logs as logged_hours', 'hours');
        if ($request->query('review') === 'pm') {
            $query->where('status', 'in_review')->where('review_stage', 'pm');
        }   
        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($projectFilter) {
            $query->where('project_id', (int) $projectFilter);
        }

        if ($statusFilter && in_array($statusFilter, ['todo', 'in_progress', 'in_review', 'done'])) {
            $query->where('status', $statusFilter);
        }

        if ($priorityFilter && in_array($priorityFilter, ['low', 'medium', 'high', 'urgent'])) {
            $query->where('priority', $priorityFilter);
        }

        $tasks = $query->latest('id')->paginate(10)->withQueryString();

        $byStatus = $base()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'all'         => $byStatus->sum(),
            'todo'        => $byStatus['todo'] ?? 0,
            'in_progress' => $byStatus['in_progress'] ?? 0,
            'in_review'   => $byStatus['in_review'] ?? 0,
            'done'        => $byStatus['done'] ?? 0,
        ];

        $projects = Project::where('project_manager_id', $pmId)->orderBy('name')->get(['id', 'name']);

        return view('pm.tasks.overview', compact(
            'tasks', 'counts', 'projects', 'statusFilter', 'priorityFilter', 'projectFilter', 'search'
        ));
    }
    /**
    * Read-only task page with progress updates.
    */
    public function show(Task $task): View
    {
        Gate::authorize('view', $task);

        $task->load([
            'project.manager',
            'assignee',
            'employee',
            'logs' => fn ($q) => $q->with('user')->latest('log_date')->latest('id'),
            'reviewEvents' => fn ($q) => $q->with('user')->oldest('id'),
        ]);

        return view('tasks.show', [
            'task'       => $task,
            'totalHours' => $task->logs->sum('hours'),
            'backUrl'    => route('pm.tasks.index', $task->project_id),
            'backLabel'  => 'Back to Project Tasks',
            ]);
        }
        /**
    * PM completes the task, asks why it is not complete, or returns it.
    */
    public function review(Request $request, Task $task): RedirectResponse
    {
        Gate::authorize('reviewAsManager', $task);

        $data = $request->validate([
            'decision' => ['required', 'in:complete,question,return'],
            'body'     => ['required_if:decision,return', 'nullable', 'string', 'max:2000'],
        ], [
        'body.required_if' => 'Please write a comment when returning the task.',
        ]);

        $body = $data['body'] ?? null;

        if ($data['decision'] === 'complete') {
            $task->update(['status' => 'done', 'review_stage' => null]);
            $type = 'pm_completed';
            $message = 'Task marked complete. The admin can see your confirmation.';
        } elseif ($data['decision'] === 'question') {
            $task->update(['review_stage' => 'employee']);
            $type = 'pm_question';
            $body = $body ?: 'Why is this task not complete?';
            $message = 'Question sent. The employee will give a reason.';
        } else {
            $task->update(['status' => 'in_progress', 'review_stage' => null]);
            $type = 'pm_returned';
            $message = 'Task returned to the employee.';
        }

        $task->reviewEvents()->create([
            'user_id' => Auth::id(),
            'type'    => $type,
            'body'    => $body,
        ]);

        return back()->with('success', $message);
    }
        /**
        * Kanban board of one project (columns are statuses).
        */
        public function board(Project $project): View
        {
            Gate::authorize('viewAny', [Task::class, $project]);

            // Urgent first, then earliest due date (no date last), then oldest
            $tasks = $project->tasks()
                ->with('assignee','employee')
                ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
                ->orderByRaw('due_date IS NULL')
                ->orderBy('due_date')
                ->orderBy('id')
                ->get();

            $grouped = $tasks->groupBy('status');

            $columns = [
                'todo'        => $grouped->get('todo', collect()),
                'in_progress' => $grouped->get('in_progress', collect()),
                'in_review'   => $grouped->get('in_review', collect()),
                'done'        => $grouped->get('done', collect()),
            ];

            return view('pm.tasks.board', compact('project', 'columns'));
        }

        /**
        * Change a task's status after a drag and drop (AJAX).
        */
        public function move(Request $request, Task $task): JsonResponse
        {
            Gate::authorize('update', $task);

            $data = $request->validate([
                'status' => ['required', 'in:todo,in_progress,in_review,done'],
            ]);

            $task->update([
                'status'       => $data['status'],
                'review_stage' => $data['status'] === 'in_review' ? ($task->review_stage ?? 'pm') : null,
            ]);
            return response()->json(['ok' => true, 'status' => $task->status]);
        }
        /**
        * List the tasks of one project.
        */
        public function index(Request $request, Project $project): View
        {
            Gate::authorize('viewAny', [Task::class, $project]);

            $statusFilter = $request->query('status');
            $priorityFilter = $request->query('priority');
            $search = $request->query('search');

            $query = $project->tasks()->with(['assignee', 'employee'])->withSum('logs as logged_hours', 'hours');
            
            if ($search) {
                $query->where('title', 'like', "%{$search}%");
            }

            if ($statusFilter && in_array($statusFilter, ['todo', 'in_progress', 'in_review', 'done'])) {
                $query->where('status', $statusFilter);
            }

            if ($priorityFilter && in_array($priorityFilter, ['low', 'medium', 'high', 'urgent'])) {
                $query->where('priority', $priorityFilter);
            }

            $tasks = $query->latest('id')->paginate(10)->withQueryString();
            $totalHours = $project->taskLogs()->sum('hours');
            // One query for all status counts
            $byStatus = $project->tasks()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $counts = [
                'all'         => $byStatus->sum(),
                'todo'        => $byStatus['todo'] ?? 0,
                'in_progress' => $byStatus['in_progress'] ?? 0,
                'in_review'   => $byStatus['in_review'] ?? 0,
                'done'        => $byStatus['done'] ?? 0,
            ];

            return view('pm.tasks.index', compact(
                'project', 'tasks', 'counts', 'statusFilter', 'priorityFilter', 'search','totalHours'
            ));
        }

    public function create(Project $project): View
    {
        Gate::authorize('create', [Task::class, $project]);

        return view('pm.tasks.create', array_merge(compact('project'), $this->formData()));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('create', [Task::class, $project]);

        $data = $this->validated($request);
        $data['project_id'] = $project->id;
        $data['created_by'] = Auth::id();

        $task = Task::create($data);

        return redirect()
            ->route('pm.tasks.index', $project)
            ->with('success', "Task '{$task->title}' created successfully.");
    }

    public function edit(Task $task): View
    {
        Gate::authorize('update', $task);

        $project = $task->project;

        return view('pm.tasks.edit', array_merge(compact('task', 'project'), $this->formData()));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        Gate::authorize('update', $task);

        $data = $this->validated($request);
        $data['review_stage'] = $data['status'] === 'in_review' ? ($task->review_stage ?? 'pm') : null;

        // A new team leader has a different team, so clear the old employee
        if ((int) $data['assigned_to'] !== (int) $task->assigned_to) {
            $data['employee_id'] = null;
        }

        $task->update($data);

        return redirect()
            ->route('pm.tasks.index', $task->project_id)
            ->with('success', "Task '{$task->title}' updated successfully.");
    }

    public function destroy(Task $task): RedirectResponse
    {
        Gate::authorize('delete', $task);

        $title = $task->title;
        $task->delete();

        return back()->with('success', "Task '{$title}' has been deleted.");
    }

    // Active team leaders of the logged-in PM for the dropdown
    private function formData(): array
    {
        return [
            'teamLeaders' => Auth::user()->reports()
                ->where('role', 'team_leader')
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name', 'designation']),
        ];
    }

    // Shared validation for create and update
    private function validated(Request $request): array
    {
        return $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'assigned_to' => [
                'required',
                Rule::exists('users', 'id')->where(
                    fn ($q) => $q->where('role', 'team_leader')
                        ->where('reports_to', Auth::id())
                        ->where('status', 'active')
                        ->whereNull('deleted_at')
                ),
            ],
            'status'      => ['required', 'in:todo,in_progress,in_review,done'],
            'priority'    => ['required', 'in:low,medium,high,urgent'],
            'due_date'    => ['nullable', 'date'],
        ], [
            'assigned_to.required' => 'Please assign this task to a team leader.',
            'assigned_to.exists'   => 'Selected team leader is invalid, inactive, or not in your team.',
        ]);
    }
}