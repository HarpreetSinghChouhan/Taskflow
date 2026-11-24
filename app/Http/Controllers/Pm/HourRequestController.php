<?php

namespace App\Http\Controllers\Pm;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class HourRequestController extends Controller
{
    /**
     * PM asks the admin for hours on a task.
     */
    public function store(Request $request, Task $task): RedirectResponse
    {
        Gate::authorize('requestHours', $task);

        if ($task->status === 'done') {
            return back()->with('error', 'This task is already done.');
        }

        if (! $task->canRequestHours()) {
            return back()->with('error', 'You can ask for more hours only when no request is pending and the earlier hours are fully used.');
        }

        $data = $request->validate([
            'requested_hours' => ['required', 'numeric', 'min:0.25', 'max:40'],
            'note'            => ['required', 'string', 'max:1000'],
        ], [
            'requested_hours.max' => 'You can ask for at most 40 hours (one week) at a time.',
        ]);

        $task->hourAllotments()->create($data + ['requested_by' => Auth::id()]);

        return back()->with('success', 'Hours request sent to the admin.');
    }
}