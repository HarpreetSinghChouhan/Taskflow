<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    // Admin and PM can open the project list
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isProjectManager();
    }

    // Admin sees all, PM only their own
    public function view(User $user, Project $project): bool
    {
        return $user->isAdmin()
            || ($user->isProjectManager() && $project->project_manager_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Project $project): bool
    {
        return $this->view($user, $project);
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->isAdmin();
    }
}