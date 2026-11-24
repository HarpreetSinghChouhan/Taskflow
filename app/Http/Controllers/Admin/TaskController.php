<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Read-only task page with progress updates and review timeline.
     */
    public function show(Task $task): View
    {
        Gate::authorize('view', $task);

        $task->load([
            'project.manager',
            'assignee',
            'employee',
            'logs'         => fn ($q) => $q->with('user')->latest('log_date')->latest('id'),
            'reviewEvents' => fn ($q) => $q->with('user')->oldest('id'),
        ]);

        return view('tasks.show', [
            'task'       => $task,
            'totalHours' => $task->logs->sum('hours'),
            'backUrl'    => route('admin.confirmations.index'),
            'backLabel'  => 'Back to Completed Tasks',
        ]);
    }
}