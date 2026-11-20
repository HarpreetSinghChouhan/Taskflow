<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Tasks given to the logged-in employee.
     */
    public function index(Request $request): View
    {
        $me = Auth::id();
        $statusFilter = $request->query('status');
        $search = $request->query('search');

        $base = fn () => Task::where('employee_id', $me);

        $query = $base()->with('project')->withSum('logs as logged_hours', 'hours');

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($statusFilter && in_array($statusFilter, ['todo', 'in_progress', 'in_review', 'done'])) {
            $query->where('status', $statusFilter);
        }

        $tasks = $query
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

        return view('employee.tasks.index', compact('tasks', 'counts', 'statusFilter', 'search'));
    }

    /**
     * One task with its progress updates.
     */
    public function show(Task $task): View
    {
        Gate::authorize('work', $task);

        $task->load([
            'project.manager',
            'assignee',
            'logs' => fn ($q) => $q->with('user')->latest('log_date')->latest('id'),
        ]);

        $totalHours = $task->logs->sum('hours');

        return view('employee.tasks.show', compact('task', 'totalHours'));
    }

    /**
     * Employee moves the task between pending, in progress and in review.
     */
    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        Gate::authorize('work', $task);

        if ($task->status === 'done') {
            return back()->with('error', 'This task is already done and cannot be changed.');
        }

        $data = $request->validate([
            'status' => ['required', 'in:todo,in_progress,in_review'],
        ]);

        $task->update(['status' => $data['status']]);

        return back()->with('success', 'Status updated.');
    }

    /**
     * Add a progress update with the hours spent.
     */
    public function storeLog(Request $request, Task $task): RedirectResponse
    {
        Gate::authorize('work', $task);

        if ($task->status === 'done') {
            return back()->with('error', 'This task is already done. You cannot add updates.');
        }

        $data = $request->validate([
            'log_date' => ['required', 'date', 'before_or_equal:today'],
            'hours'    => ['required', 'numeric', 'min:0', 'max:24'],
            'note'     => ['required', 'string', 'max:2000'],
        ]);

        $task->logs()->create($data + ['user_id' => Auth::id()]);

        return back()->with('success', 'Update added.');
    }
}