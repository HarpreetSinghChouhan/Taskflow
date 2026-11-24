<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PmHoursTest extends TestCase
{
    use RefreshDatabase;

    private User $pm;
    private User $otherPm;
    private User $tl;
    private User $emp;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pm = User::factory()->create(['role' => 'project_manager', 'status' => 'active']);
        $this->otherPm = User::factory()->create(['role' => 'project_manager', 'status' => 'active']);
        $this->tl = User::factory()->create(['role' => 'team_leader', 'status' => 'active', 'reports_to' => $this->pm->id]);
        $this->emp = User::factory()->create(['role' => 'employee', 'status' => 'active', 'reports_to' => $this->tl->id]);

        $this->project = Project::create([
            'name' => 'Alpha Project', 'project_manager_id' => $this->pm->id, 'status' => 'active',
        ]);
    }           

    private function makeTask(Project $project, array $overrides = []): Task
    {
        return Task::create(array_merge([
            'project_id'  => $project->id,
            'title'       => 'Sample Task',
            'assigned_to' => $this->tl->id,
            'employee_id' => $this->emp->id,
            'created_by'  => $project->project_manager_id,
            'status'      => 'todo',
            'priority'    => 'medium',
        ], $overrides));
    }

    private function addLog(Task $task, float $hours, ?string $date = null): TaskLog
    {
        return TaskLog::create([
            'task_id'  => $task->id,
            'user_id'  => $this->emp->id,
            'log_date' => $date ?? now()->toDateString(),
            'hours'    => $hours,
            'note'     => 'Work done',
        ]);
    }

    public function test_projects_list_shows_total_hours_of_own_projects_only(): void
    {
        $a = $this->makeTask($this->project, ['title' => 'A']);
        $b = $this->makeTask($this->project, ['title' => 'B']);
        $this->addLog($a, 2.5);
        $this->addLog($b, 1.5);

        $foreign = Project::create(['name' => 'Foreign', 'project_manager_id' => $this->otherPm->id, 'status' => 'active']);
        $this->addLog($this->makeTask($foreign), 9);

        $response = $this->actingAs($this->pm)->get('/pm/projects');

        $response->assertOk();
        $rows = $response->viewData('projects')->getCollection();
        $this->assertEquals(4.0, (float) $rows->firstWhere('id', $this->project->id)->total_hours);
        $this->assertNull($rows->firstWhere('id', $foreign->id));
    }

    public function test_deleted_task_hours_are_not_counted(): void
    {
        $a = $this->makeTask($this->project, ['title' => 'A']);
        $b = $this->makeTask($this->project, ['title' => 'B']);
        $this->addLog($a, 2);
        $this->addLog($b, 3);
        $b->delete();

        $rows = $this->actingAs($this->pm)->get('/pm/projects')->viewData('projects')->getCollection();

        $this->assertEquals(2.0, (float) $rows->firstWhere('id', $this->project->id)->total_hours);
    }

    public function test_project_tasks_page_shows_task_and_project_hours(): void
    {
        $a = $this->makeTask($this->project, ['title' => 'A']);
        $b = $this->makeTask($this->project, ['title' => 'B']);
        $this->addLog($a, 2.5);
        $this->addLog($b, 1.5);

        $response = $this->actingAs($this->pm)->get("/pm/projects/{$this->project->id}/tasks");

        $response->assertOk();
        $this->assertEquals(4.0, (float) $response->viewData('totalHours'));
        $row = $response->viewData('tasks')->getCollection()->firstWhere('id', $a->id);
        $this->assertEquals(2.5, (float) $row->logged_hours);
    }

    public function test_task_overview_shows_hours_per_task(): void
    {
        $a = $this->makeTask($this->project, ['title' => 'A']);
        $this->addLog($a, 3);
        $this->addLog($a, 1.25);

        $response = $this->actingAs($this->pm)->get('/pm/tasks');

        $response->assertOk();
        $row = $response->viewData('tasks')->getCollection()->firstWhere('id', $a->id);
        $this->assertEquals(4.25, (float) $row->logged_hours);
    }

    public function test_hours_summary_counts_total_week_and_progress(): void
    {
        $done = $this->makeTask($this->project, ['title' => 'Done Task', 'status' => 'done']);
        $this->makeTask($this->project, ['title' => 'Open Task']);
        $this->addLog($done, 2);
        $this->addLog($done, 3, now()->subDays(40)->toDateString());

        $summary = $this->pm->hoursSummary();

        $this->assertEquals(5.0, $summary['total_hours']);
        $this->assertEquals(2.0, $summary['week_hours']);
        $project = $summary['projects']->first();
        $this->assertSame(2, (int) $project->tasks_count);
        $this->assertSame(1, (int) $project->done_tasks_count);
    }

    public function test_hours_summary_ignores_other_pms_projects(): void
    {
        $foreign = Project::create(['name' => 'Foreign', 'project_manager_id' => $this->otherPm->id, 'status' => 'active']);
        $this->addLog($this->makeTask($foreign), 9);

        $summary = $this->pm->hoursSummary();

        $this->assertEquals(0.0, $summary['total_hours']);
        $this->assertEquals(0.0, $summary['week_hours']);
        $this->assertCount(1, $summary['projects']);
    }

    public function test_pm_dashboard_loads_with_hours_block(): void
    {
        $this->addLog($this->makeTask($this->project), 2);

        $this->actingAs($this->pm)->get('/pm/dashboard')->assertOk();
    }
    public function test_admin_can_open_project_progress_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $task = $this->makeTask($this->project, ['title' => 'Tracked Task']);
        $this->addLog($task, 2.5);

        $this->actingAs($admin)->get("/admin/projects/{$this->project->id}")
            ->assertOk()
            ->assertSee('Tracked Task')
            ->assertSee($this->emp->name);
    }

    public function test_pm_cannot_open_admin_project_page(): void
    {
        $this->actingAs($this->pm)->get("/admin/projects/{$this->project->id}")
            ->assertRedirect('/pm/dashboard');
    }
}