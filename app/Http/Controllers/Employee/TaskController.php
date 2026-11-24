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

    public function show(Task $task): View
    {
        Gate::authorize('work', $task);

        $task->load([
            'project.manager',
            'assignee',
            'logs'         => fn ($q) => $q->with('user')->latest('log_date')->latest('id'),
            'reviewEvents' => fn ($q) => $q->with('user')->oldest('id'),
        ]);

        return view('employee.tasks.show', [
            'task'           => $task,
            'totalHours'     => $task->logs->sum('hours'),
            'approvedHours'  => $task->approvedHours(),
            'remainingHours' => $task->remainingHours(),
        ]);
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        Gate::authorize('work', $task);

        if (in_array($task->status, ['done', 'in_review'], true)) {
            return back()->with('error', 'This task is under review or done, so its status cannot be changed.');
        }

        $data = $request->validate([
            'status' => ['required', 'in:todo,in_progress'],
        ]);

        $task->update(['status' => $data['status']]);

        return back()->with('success', 'Status updated.');
    }

    public function storeLog(Request $request, Task $task): RedirectResponse
    {
        Gate::authorize('work', $task);

        if (in_array($task->status, ['done', 'in_review'], true)) {
            return back()->with('error', 'This task is under review or done. You cannot add updates.');
        }

        $data = $request->validate([
            'log_date' => ['required', 'date', 'before_or_equal:today'],
            'hours'    => ['required', 'numeric', 'min:0', 'max:24'],
            'note'     => ['required', 'string', 'max:2000'],
        ]);

        // Hours cannot go beyond what the admin approved
        $remaining = $task->remainingHours();
        if ((float) $data['hours'] > $remaining) {
            return back()->withInput()->withErrors([
                'hours' => $remaining > 0
                    ? "Only {$remaining} approved hours are left on this task."
                : 'No approved hours are left. Your project manager needs to request more hours.',
            ]);
        }

        $task->logs()->create($data + ['user_id' => Auth::id()]);

        return back()->with('success', 'Update added.');
    }

    /**
    * Employee sends the task for review with what is left and the next tasks.
    */
    public function submitReview(Request $request, Task $task): RedirectResponse
    {
        Gate::authorize('work', $task);

        if (in_array($task->status, ['done', 'in_review'], true)) {
            return back()->with('error', 'This task is already under review or done.');
        }

        $data = $request->validate([
            'summary'    => ['required', 'string', 'max:3000'],
            'next_tasks' => ['required', 'string', 'max:3000'],
        ], [
            'summary.required'    => 'Please write what you completed.',
            'next_tasks.required' => 'Please write what is left or your next tasks.',
        ]);

        $task->update(['status' => 'in_review', 'review_stage' => 'tl']);

        $task->reviewEvents()->create([
            'user_id' => Auth::id(),
            'type'    => 'submitted',
            'body'    => "Completed:\n{$data['summary']}\n\nNext tasks:\n{$data['next_tasks']}",
        ]);

        return back()->with('success', 'Submitted for review. Your team leader will check it first.');
    }
    /**
    * Employee gives the reason when the PM asks why the task is not complete.
    */
    public function replyReason(Request $request, Task $task): RedirectResponse
    {
        Gate::authorize('replyReason', $task);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:3000'],
        ], [
            'body.required' => 'Please write the reason.',
        ]);

        $task->update(['review_stage' => 'pm']);

        $task->reviewEvents()->create([
            'user_id' => Auth::id(),
            'type'    => 'employee_reason',
            'body'    => $data['body'],
        ]);

        return back()->with('success', 'Reason sent to the project manager.');
    }
}