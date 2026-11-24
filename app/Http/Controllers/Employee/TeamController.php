<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeamController extends Controller
{
    /**
     * Team leader, team members and the team's tasks (read-only).
     */
    public function team(): View
    {
        $me = Auth::user();
        $leader = $me->manager;

        $teammates = collect();
        $teamTasks = null;

        if ($leader) {
            $teammates = User::where('reports_to', $leader->id)
                ->where('role', 'employee')
                ->where('status', 'active')
                ->withCount([
                    'employeeTasks as pending_count'     => fn ($q) => $q->where('status', 'todo'),
                    'employeeTasks as in_progress_count' => fn ($q) => $q->where('status', 'in_progress'),
                    'employeeTasks as in_review_count'   => fn ($q) => $q->where('status', 'in_review'),
                    'employeeTasks as done_count'        => fn ($q) => $q->where('status', 'done'),
                ])
                ->orderBy('name')
                ->get();

            $teamTasks = Task::where('assigned_to', $leader->id)
                ->with(['project', 'employee'])
                ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
                ->orderBy('id')
                ->paginate(10);
        }

        return view('employee.team', compact('leader', 'teammates', 'teamTasks'));
    }

    /**
     * Projects in which the employee has tasks.
     */
    public function projects(): View
    {
        $me = Auth::id();

        $projects = Project::whereHas('tasks', fn ($q) => $q->where('employee_id', $me))
            ->with('manager')
            ->withCount([
                'tasks as my_tasks_count' => fn ($q) => $q->where('employee_id', $me),
                'tasks as my_done_count'  => fn ($q) => $q->where('employee_id', $me)->where('status', 'done'),
            ])
            ->orderBy('name')
            ->get();

        return view('employee.projects', compact('projects'));
    }
}