<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ConfirmationController extends Controller
{
    /**
     * Tasks completed by the PM, waiting for the admin, and recent confirmations.
     */
    public function index(): View
    {
        $events = fn ($q) => $q->with('user')->oldest('id');

        $pending = Task::awaitingAdmin()
            ->with(['project.manager', 'assignee', 'employee', 'reviewEvents' => $events])
            ->withSum('logs as logged_hours', 'hours')
            ->latest('updated_at')
            ->get();

        $confirmed = Task::whereHas('reviewEvents', fn ($q) => $q->where('type', 'admin_confirmed'))
            ->with(['project', 'employee', 'reviewEvents' => $events])
            ->withSum('logs as logged_hours', 'hours')
            ->latest('updated_at')
            ->limit(20)
            ->get();

        return view('admin.confirmations.index', compact('pending', 'confirmed'));
    }

    /**
     * Admin confirms a completed task.
     */
    public function confirm(Request $request, Task $task): RedirectResponse
    {
        if (! Task::awaitingAdmin()->whereKey($task->id)->exists()) {
            return back()->with('error', 'This task is not waiting for confirmation.');
        }

        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $task->reviewEvents()->create([
            'user_id' => Auth::id(),
            'type'    => 'admin_confirmed',
            'body'    => $data['note'] ?? null,
        ]);

        return back()->with('success', "Task '{$task->title}' confirmed.");
    }
}