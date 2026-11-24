<?php

namespace App\Http\Controllers\Tl;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeamController extends Controller
{
    /**
     * Each employee of the team leader with task progress and hours.
     */
    public function index(): View
    {
        $tl = Auth::user();

        // Only count tasks that were given to this team leader
        $mine = fn ($status = null) => fn ($q) => $q
            ->where('assigned_to', $tl->id)
            ->when($status, fn ($q2) => $q2->where('status', $status));

        $employees = $tl->reports()
            ->where('role', 'employee')
            ->withCount([
                'employeeTasks as total_count'       => $mine(),
                'employeeTasks as pending_count'     => $mine('todo'),
                'employeeTasks as in_progress_count' => $mine('in_progress'),
                'employeeTasks as in_review_count'   => $mine('in_review'),
                'employeeTasks as done_count'        => $mine('done'),
            ])
            ->withSum(
                ['taskLogs as hours_logged' => fn ($q) => $q->whereHas('task', fn ($t) => $t->where('assigned_to', $tl->id))],
                'hours'
            )
            ->orderBy('name')
            ->get();

        $summary = [
            'employees'  => $employees->count(),
            'unassigned' => Task::where('assigned_to', $tl->id)->whereNull('employee_id')->count(),
            'in_review'  => $employees->sum('in_review_count'),
            'hours'      => $employees->sum('hours_logged'),
        ];

        return view('tl.team', compact('employees', 'summary'));
    }
}