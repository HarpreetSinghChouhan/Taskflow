<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    // True only for the PM who owns the project
    private function ownsProject(User $user, ?Project $project): bool
    {
        return $project !== null
            && $user->isProjectManager()
            && $project->project_manager_id === $user->id;
    }

    // View the task list of a project
    public function viewAny(User $user, Project $project): bool
    {
        return $this->ownsProject($user, $project);
    }

    // Create a task inside a project
    public function create(User $user, Project $project): bool
    {
        return $this->ownsProject($user, $project);
    }

    public function update(User $user, Task $task): bool
    {
        return $this->ownsProject($user, $task->project);
    }

    public function delete(User $user, Task $task): bool
    {
        return $this->ownsProject($user, $task->project);
    }
    // Team leader hands the task to one of their employees
    public function assignEmployee(User $user, Task $task): bool
    {
        return $user->isTeamLeader() && $task->assigned_to === $user->id;
    }
    // Employee works only on tasks given to them
    public function work(User $user, Task $task): bool
    {
        return $user->isEmployee() && $task->employee_id === $user->id;
    }
    // PM sees tasks of own projects, TL sees tasks given to them
    public function view(User $user, Task $task): bool
    {
        return $this->ownsProject($user, $task->project)
            || ($user->isTeamLeader() && $task->assigned_to === $user->id);
    }
}