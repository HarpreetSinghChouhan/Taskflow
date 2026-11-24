<?php

namespace App\Http\Controllers\Tl;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Tasks the project managers assigned to the logged-in team leader.
     */
    public function index(Request $request): View
    {
        $tl = Auth::user();
        $statusFilter = $request->query('status');
        $priorityFilter = $request->query('priority');
        $search = $request->query('search');

        $base = fn () => Task::where('assigned_to', $tl->id);

        $query = $base()->with(['project', 'employee']);

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }   

        if ($statusFilter && in_array($statusFilter, ['todo', 'in_progress', 'in_review', 'done'])) {
            $query->where('status', $statusFilter);
        }

        if ($priorityFilter && in_array($priorityFilter, ['low', 'medium', 'high', 'urgent'])) {
            $query->where('priority', $priorityFilter);
        }

        $tasks = $query
            ->orderByRaw("CASE WHEN status = 'in_review' AND review_stage = 'tl' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->orderByRaw('due_date IS NULL')
            ->orderBy('due_date')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $byStatus = $base()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        $counts = [
            'all'         => $byStatus->sum(),
            'todo'        => $byStatus['todo'] ?? 0,
            'in_progress' => $byStatus['in_progress'] ?? 0,
            'in_review'   => $byStatus['in_review'] ?? 0,
            'done'        => $byStatus['done'] ?? 0,
        ];

        // Active employees of this team leader
        $employees = $tl->reports()
            ->where('role', 'employee')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'designation']);

        return view('tl.tasks.index', compact(
            'tasks', 'counts', 'employees', 'statusFilter', 'priorityFilter', 'search'
        ));
    }

    /**
     * Give the task to an employee of this team (or clear it).
     */
    public function assignEmployee(Request $request, Task $task): RedirectResponse
    {
        Gate::authorize('assignEmployee', $task);

        $data = $request->validate([
            'employee_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(
                    fn ($q) => $q->where('role', 'employee')
                        ->where('reports_to', Auth::id())
                        ->where('status', 'active')
                        ->whereNull('deleted_at')
                ),
            ],
        ], [
            'employee_id.exists' => 'Selected employee is invalid, inactive, or not in your team.',
        ]);

        $task->update(['employee_id' => $data['employee_id'] ?? null]);
        $task->load('employee');

        $message = $task->employee
            ? "Task '{$task->title}' assigned to {$task->employee->name}."
            : "Task '{$task->title}' is now unassigned.";

        return back()->with('success', $message);
    }
    /**
    * Team leader approves the work (goes to PM) or returns it to the employee.
    */
    public function review(Request $request, Task $task): RedirectResponse
    {
        Gate::authorize('reviewAsLeader', $task);

        $data = $request->validate([
            'decision' => ['required', 'in:approve,return'],
            'body'     => ['required_if:decision,return', 'nullable', 'string', 'max:2000'],
        ], [
            'body.required_if' => 'Please write a comment when returning the task.',
        ]);

        if ($data['decision'] === 'approve') {
            $task->update(['review_stage' => 'pm']);
            $type = 'tl_approved';
            $message = 'Approved. The task now goes to the project manager.';
        } else {
            $task->update(['status' => 'in_progress', 'review_stage' => null]);
            $type = 'tl_returned';
            $message = 'Task returned to the employee.';
        }

        $task->reviewEvents()->create([
            'user_id' => Auth::id(),
            'type'    => $type,
            'body'    => $data['body'] ?? null,
        ]);

        return back()->with('success', $message);
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
            'backUrl'    => route('tl.tasks.index'),
            'backLabel'  => 'Back to Team Tasks',
        ]);
    }
}