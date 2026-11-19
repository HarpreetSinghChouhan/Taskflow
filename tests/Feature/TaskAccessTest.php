<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $pm;
    private User $otherPm;
    private User $tl;
    private User $otherTl;
    private Project $project;
    private Project $otherProject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pm = User::factory()->create(['role' => 'project_manager', 'status' => 'active']);
        $this->otherPm = User::factory()->create(['role' => 'project_manager', 'status' => 'active']);

        $this->tl = User::factory()->create([
            'role' => 'team_leader', 'status' => 'active', 'reports_to' => $this->pm->id,
        ]);
        $this->otherTl = User::factory()->create([
            'role' => 'team_leader', 'status' => 'active', 'reports_to' => $this->otherPm->id,
        ]);

        $this->project = Project::create([
            'name' => 'My Project', 'project_manager_id' => $this->pm->id, 'status' => 'active',
        ]);
        $this->otherProject = Project::create([
            'name' => 'Other Project', 'project_manager_id' => $this->otherPm->id, 'status' => 'active',
        ]);
    }

    private function makeTask(Project $project, User $assignee, array $overrides = []): Task
    {
        return Task::create(array_merge([
            'project_id'  => $project->id,
            'title'       => 'Sample Task',
            'assigned_to' => $assignee->id,
            'created_by'  => $project->project_manager_id,
            'status'      => 'todo',
            'priority'    => 'medium',
        ], $overrides));
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title'       => 'Build login API',
            'assigned_to' => $this->tl->id,
            'status'      => 'todo',
            'priority'    => 'high',
            'due_date'    => '2026-12-31',
        ], $overrides);
    }

    public function test_pm_can_create_task_for_own_team_leader(): void
    {
        $response = $this->actingAs($this->pm)
            ->post("/pm/projects/{$this->project->id}/tasks", $this->payload());

        $response->assertRedirect("/pm/projects/{$this->project->id}/tasks");
        $this->assertDatabaseHas('tasks', [
            'title'       => 'Build login API',
            'project_id'  => $this->project->id,
            'assigned_to' => $this->tl->id,
            'created_by'  => $this->pm->id,
            'priority'    => 'high',
        ]);
    }

    public function test_pm_cannot_assign_task_to_other_pms_team_leader(): void
    {
        $response = $this->actingAs($this->pm)
            ->post("/pm/projects/{$this->project->id}/tasks", $this->payload(['assigned_to' => $this->otherTl->id]));

        $response->assertSessionHasErrors('assigned_to');
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_pm_cannot_assign_task_to_an_employee(): void
    {
        $employee = User::factory()->create([
            'role' => 'employee', 'status' => 'active', 'reports_to' => $this->tl->id,
        ]);

        $response = $this->actingAs($this->pm)
            ->post("/pm/projects/{$this->project->id}/tasks", $this->payload(['assigned_to' => $employee->id]));

        $response->assertSessionHasErrors('assigned_to');
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_task_requires_a_title(): void
    {
        $response = $this->actingAs($this->pm)
            ->post("/pm/projects/{$this->project->id}/tasks", $this->payload(['title' => '']));

        $response->assertSessionHasErrors('title');
    }

    public function test_task_list_shows_only_tasks_of_that_project(): void
    {
        $second = Project::create([
            'name' => 'Second Project', 'project_manager_id' => $this->pm->id, 'status' => 'active',
        ]);
        $this->makeTask($this->project, $this->tl, ['title' => 'Visible Task']);
        $this->makeTask($second, $this->tl, ['title' => 'Hidden Task']);

        $response = $this->actingAs($this->pm)->get("/pm/projects/{$this->project->id}/tasks");

        $response->assertStatus(200);
        $response->assertSee('Visible Task');
        $response->assertDontSee('Hidden Task');
    }

    public function test_pm_cannot_open_tasks_of_other_pms_project(): void
    {
        $this->actingAs($this->pm)
            ->get("/pm/projects/{$this->otherProject->id}/tasks")
            ->assertStatus(403);
    }

    public function test_pm_cannot_create_task_in_other_pms_project(): void
    {
        $this->actingAs($this->pm)
            ->post("/pm/projects/{$this->otherProject->id}/tasks", $this->payload())
            ->assertStatus(403);

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_pm_can_update_own_task(): void
    {
        $task = $this->makeTask($this->project, $this->tl);

        $this->actingAs($this->pm)
            ->put("/pm/tasks/{$task->id}", $this->payload(['title' => 'Renamed Task', 'status' => 'in_progress']))
            ->assertRedirect("/pm/projects/{$this->project->id}/tasks");

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id, 'title' => 'Renamed Task', 'status' => 'in_progress',
        ]);
    }

    public function test_pm_cannot_update_other_pms_task(): void
    {
        $task = $this->makeTask($this->otherProject, $this->otherTl, ['title' => 'Original']);

        $this->actingAs($this->pm)
            ->put("/pm/tasks/{$task->id}", $this->payload(['title' => 'Hacked']))
            ->assertStatus(403);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Original']);
    }

    public function test_pm_can_delete_own_task(): void
    {
        $task = $this->makeTask($this->project, $this->tl);

        $this->actingAs($this->pm)->delete("/pm/tasks/{$task->id}");

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    public function test_pm_cannot_delete_other_pms_task(): void
    {
        $task = $this->makeTask($this->otherProject, $this->otherTl);

        $this->actingAs($this->pm)->delete("/pm/tasks/{$task->id}")->assertStatus(403);

        $this->assertNotSoftDeleted('tasks', ['id' => $task->id]);
    }

    public function test_team_leader_cannot_open_pm_task_pages(): void
    {
        $this->actingAs($this->tl)
            ->get("/pm/projects/{$this->project->id}/tasks")
            ->assertRedirect('/tl/dashboard');
    }
    public function test_task_overview_lists_tasks_of_all_own_projects_only(): void
    {
        $second = Project::create([
            'name' => 'Second Project', 'project_manager_id' => $this->pm->id, 'status' => 'active',
        ]);
        $this->makeTask($this->project, $this->tl, ['title' => 'Task In First']);
        $this->makeTask($second, $this->tl, ['title' => 'Task In Second']);
        $this->makeTask($this->otherProject, $this->otherTl, ['title' => 'Foreign Task']);

        $response = $this->actingAs($this->pm)->get('/pm/tasks');

        $response->assertStatus(200);
        $response->assertSee('Task In First');
        $response->assertSee('Task In Second');
        $response->assertDontSee('Foreign Task');
    }

    public function test_task_overview_project_filter_cannot_leak_other_pms_tasks(): void
    {
        $this->makeTask($this->otherProject, $this->otherTl, ['title' => 'Foreign Task']);

        $response = $this->actingAs($this->pm)->get('/pm/tasks?project=' . $this->otherProject->id);

        $response->assertStatus(200);
        $response->assertDontSee('Foreign Task');
    }
}