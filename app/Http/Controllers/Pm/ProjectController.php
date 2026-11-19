<?php

namespace App\Http\Controllers\Pm;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * List only the projects owned by the logged-in PM.
     */
    public function index(Request $request): View
    {
        $pmId = Auth::id();
        $statusFilter = $request->query('status');
        $search = $request->query('search');

        $query = Project::where('project_manager_id', $pmId)->withCount('tasks');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($statusFilter && in_array($statusFilter, ['planned', 'active', 'on_hold', 'completed'])) {
            $query->where('status', $statusFilter);
        }

        $projects = $query->latest('id')->paginate(10)->withQueryString();

        $own = fn () => Project::where('project_manager_id', $pmId);
        $counts = [
            'all'       => $own()->count(),
            'planned'   => $own()->where('status', 'planned')->count(),
            'active'    => $own()->where('status', 'active')->count(),
            'on_hold'   => $own()->where('status', 'on_hold')->count(),
            'completed' => $own()->where('status', 'completed')->count(),
        ];

        return view('pm.projects.index', compact('projects', 'counts', 'statusFilter', 'search'));
    }

    public function edit(Project $project): View
    {
        Gate::authorize('update', $project);

        return view('pm.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        // PM cannot change the manager, only project details
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'start_date'  => ['nullable', 'date'],
            'deadline'    => ['nullable', 'date', 'after_or_equal:start_date'],
            'status'      => ['required', 'in:planned,active,on_hold,completed'],
        ], [
            'deadline.after_or_equal' => 'Deadline cannot be before the start date.',
        ]);

        $project->update($data);

        return redirect()
            ->route('pm.projects.index')
            ->with('success', "Project '{$project->name}' updated successfully.");
    }
}