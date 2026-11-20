<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $pm;
    private User $otherPm;
    private User $tl;
    private User $otherTl;
    private User $emp;
    private User $otherEmp;
    private Project $project;
    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pm = User::factory()->create(['role' => 'project_manager', 'status' => 'active']);
        $this->otherPm = User::factory()->create(['role' => 'project_manager', 'status' => 'active']);

        $this->tl = User::factory()->create(['role' => 'team_leader', 'status' => 'active', 'reports_to' => $this->pm->id]);
        $this->otherTl = User::factory()->create(['role' => 'team_leader', 'status' => 'active', 'reports_to' => $this->pm->id]);

        $this->emp = User::factory()->create(['role' => 'employee', 'status' => 'active', 'reports_to' => $this->tl->id]);
        $this->otherEmp = User::factory()->create(['role' => 'employee', 'status' => 'active', 'reports_to' => $this->otherTl->id]);

        $this->project = Project::create([
            'name' => 'Alpha Project', 'project_manager_id' => $this->pm->id, 'status' => 'active',
        ]);

        $this->task = $this->makeTask(['title' => 'Main Task', 'employee_id' => $this->emp->id]);
    }

    private function makeTask(array $overrides = []): Task
    {
        return Task::create(array_merge([
            'project_id'  => $this->project->id,
            'title'       => 'Sample Task',
            'assigned_to' => $this->tl->id,
            'created_by'  => $this->pm->id,
            'status'      => 'todo',
            'priority'    => 'medium',
        ], $overrides));
    }

    private function logPayload(array $overrides = []): array
    {
        return array_merge([
            'log_date' => now()->toDateString(),
            'hours'    => 2.5,
            'note'     => 'Built the login form',
        ], $overrides);
    }

    // ---- Team leader ----

    public function test_tl_sees_only_tasks_given_to_them(): void
    {
        $this->makeTask(['title' => 'Other TL Task', 'assigned_to' => $this->otherTl->id]);

        $this->actingAs($this->tl)->get('/tl/tasks')
            ->assertStatus(200)
            ->assertSee('Main Task')
            ->assertDontSee('Other TL Task');
    }

    public function test_tl_can_assign_task_to_own_employee(): void
    {
        $task = $this->makeTask(['title' => 'Fresh Task']);

        $this->actingAs($this->tl)
            ->patch("/tl/tasks/{$task->id}/employee", ['employee_id' => $this->emp->id])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'employee_id' => $this->emp->id]);
    }

    public function test_tl_cannot_assign_task_to_other_tls_employee(): void
    {
        $task = $this->makeTask(['title' => 'Fresh Task']);

        $this->actingAs($this->tl)
            ->patch("/tl/tasks/{$task->id}/employee", ['employee_id' => $this->otherEmp->id])
            ->assertSessionHasErrors('employee_id');

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'employee_id' => null]);
    }

    public function test_tl_cannot_assign_task_that_belongs_to_other_tl(): void
    {
        $task = $this->makeTask(['assigned_to' => $this->otherTl->id]);

        $this->actingAs($this->tl)
            ->patch("/tl/tasks/{$task->id}/employee", ['employee_id' => $this->emp->id])
            ->assertStatus(403);
    }

    public function test_tl_can_clear_the_assigned_employee(): void
    {
        $this->actingAs($this->tl)
            ->patch("/tl/tasks/{$this->task->id}/employee", ['employee_id' => ''])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', ['id' => $this->task->id, 'employee_id' => null]);
    }

    public function test_tl_team_page_shows_progress_and_hours(): void
    {
        $done = $this->makeTask(['title' => 'Done Task', 'employee_id' => $this->emp->id, 'status' => 'done']);
        TaskLog::create(['task_id' => $this->task->id, 'user_id' => $this->emp->id, 'log_date' => now()->toDateString(), 'hours' => 1.5, 'note' => 'a']);
        TaskLog::create(['task_id' => $done->id, 'user_id' => $this->emp->id, 'log_date' => now()->toDateString(), 'hours' => 2, 'note' => 'b']);

        $response = $this->actingAs($this->tl)->get('/tl/team');

        $response->assertStatus(200);
        $row = $response->viewData('employees')->firstWhere('id', $this->emp->id);
        $this->assertSame(2, $row->total_count);
        $this->assertSame(1, $row->done_count);
        $this->assertEquals(3.5, (float) $row->hours_logged);
        $response->assertDontSee($this->otherEmp->name);
    }

    // ---- Employee ----

    public function test_employee_sees_only_own_tasks(): void
    {
        $this->makeTask(['title' => 'Someone Elses Task', 'employee_id' => $this->otherEmp->id]);

        $this->actingAs($this->emp)->get('/employee/tasks')
            ->assertStatus(200)
            ->assertSee('Main Task')
            ->assertDontSee('Someone Elses Task');
    }

    public function test_employee_can_add_progress_update_with_hours(): void
    {
        $this->actingAs($this->emp)
            ->post("/employee/tasks/{$this->task->id}/logs", $this->logPayload())
            ->assertSessionHas('success');

        $this->assertDatabaseHas('task_logs', [
            'task_id' => $this->task->id,
            'user_id' => $this->emp->id,
            'hours'   => 2.5,
            'note'    => 'Built the login form',
        ]);
    }

    public function test_progress_update_rejects_future_date_and_too_many_hours(): void
    {
        $this->actingAs($this->emp)
            ->post("/employee/tasks/{$this->task->id}/logs", $this->logPayload(['log_date' => now()->addDay()->toDateString()]))
            ->assertSessionHasErrors('log_date');

        $this->actingAs($this->emp)
            ->post("/employee/tasks/{$this->task->id}/logs", $this->logPayload(['hours' => 25]))
            ->assertSessionHasErrors('hours');

        $this->assertDatabaseCount('task_logs', 0);
    }

    public function test_employee_cannot_log_on_a_done_task(): void
    {
        $this->task->update(['status' => 'done']);

        $this->actingAs($this->emp)
            ->post("/employee/tasks/{$this->task->id}/logs", $this->logPayload())
            ->assertSessionHas('error');

        $this->assertDatabaseCount('task_logs', 0);
    }

    public function test_employee_can_change_status_but_not_to_done(): void
    {
        $this->actingAs($this->emp)
            ->patch("/employee/tasks/{$this->task->id}/status", ['status' => 'in_review'])
            ->assertSessionHas('success');
        $this->assertDatabaseHas('tasks', ['id' => $this->task->id, 'status' => 'in_review']);

        $this->actingAs($this->emp)
            ->patch("/employee/tasks/{$this->task->id}/status", ['status' => 'done'])
            ->assertSessionHasErrors('status');
        $this->assertDatabaseHas('tasks', ['id' => $this->task->id, 'status' => 'in_review']);
    }

    public function test_employee_cannot_work_on_another_employees_task(): void
    {
        $task = $this->makeTask(['employee_id' => $this->otherEmp->id]);

        $this->actingAs($this->emp)->get("/employee/tasks/{$task->id}")->assertStatus(403);
        $this->actingAs($this->emp)->post("/employee/tasks/{$task->id}/logs", $this->logPayload())->assertStatus(403);
        $this->actingAs($this->emp)->patch("/employee/tasks/{$task->id}/status", ['status' => 'in_progress'])->assertStatus(403);
    }

    public function test_employee_team_page_shows_leader_and_teammates(): void
    {
        $mate = User::factory()->create(['name' => 'Teammate Zed', 'role' => 'employee', 'status' => 'active', 'reports_to' => $this->tl->id]);

        $this->actingAs($this->emp)->get('/employee/team')
            ->assertStatus(200)
            ->assertSee($this->tl->name)
            ->assertSee($mate->name)
            ->assertDontSee($this->otherEmp->name);
    }

    public function test_employee_projects_page_lists_only_projects_with_their_tasks(): void
    {
        $other = Project::create(['name' => 'Hidden Project', 'project_manager_id' => $this->pm->id, 'status' => 'active']);
        $this->makeTask(['project_id' => $other->id, 'employee_id' => $this->otherEmp->id]);

        $this->actingAs($this->emp)->get('/employee/projects')
            ->assertStatus(200)
            ->assertSee('Alpha Project')
            ->assertDontSee('Hidden Project');
    }

    // ---- PM and TL task detail page ----

    public function test_task_detail_page_access(): void
    {
        $this->actingAs($this->pm)->get("/pm/tasks/{$this->task->id}")->assertStatus(200)->assertSee('Main Task');
        $this->actingAs($this->tl)->get("/tl/tasks/{$this->task->id}")->assertStatus(200)->assertSee('Main Task');

        $this->actingAs($this->otherPm)->get("/pm/tasks/{$this->task->id}")->assertStatus(403);
        $this->actingAs($this->otherTl)->get("/tl/tasks/{$this->task->id}")->assertStatus(403);
        $this->actingAs($this->emp)->get("/pm/tasks/{$this->task->id}")->assertRedirect('/employee/dashboard');
    }

    // ---- Reassigning the team leader ----

    public function test_pm_changing_team_leader_clears_the_employee(): void
    {
        $this->actingAs($this->pm)->put("/pm/tasks/{$this->task->id}", [
            'title'       => 'Main Task',
            'assigned_to' => $this->otherTl->id,
            'status'      => 'todo',
            'priority'    => 'medium',
        ])->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'id'          => $this->task->id,
            'assigned_to' => $this->otherTl->id,
            'employee_id' => null,
        ]);
    }
}