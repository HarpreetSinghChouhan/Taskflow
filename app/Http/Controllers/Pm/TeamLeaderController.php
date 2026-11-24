<?php

namespace App\Http\Controllers\Pm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeamLeaderController extends Controller
{
    /**
     * List the team leaders who report to the logged-in PM.
     */
    public function index(Request $request): View
    {
        $pm = Auth::user();
        $search = $request->query('search');

        $query = $pm->reports()
            ->where('role', 'team_leader')
            ->with(['reports' => fn ($q) => $q->where('role', 'employee')->orderBy('name')])
            ->withCount([
                'reports as employees_count' => fn ($q) => $q->where('role', 'employee'),
                // Only tasks from this PM's own projects
                'assignedTasks as tasks_count' => fn ($q) => $q->whereHas(
                    'project',
                    fn ($p) => $p->where('project_manager_id', $pm->id)
                ),
            ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        $teamLeaders = $query->orderBy('name')->get();

        $totals = [
            'leaders'   => $teamLeaders->count(),
            'employees' => $teamLeaders->sum('employees_count'),
            'tasks'     => $teamLeaders->sum('tasks_count'),
        ];

        return view('pm.team-leaders.index', compact('teamLeaders', 'totals', 'search'));
    }
}