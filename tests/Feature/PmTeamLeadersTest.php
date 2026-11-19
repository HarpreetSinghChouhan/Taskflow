<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PmTeamLeadersTest extends TestCase
{
    use RefreshDatabase;

    private User $pm;
    private User $otherPm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pm = User::factory()->create(['role' => 'project_manager', 'status' => 'active']);
        $this->otherPm = User::factory()->create(['role' => 'project_manager', 'status' => 'active']);
    }

    public function test_pm_sees_only_own_team_leaders(): void
    {
        User::factory()->create([
            'name' => 'Mine Leader', 'role' => 'team_leader', 'status' => 'active', 'reports_to' => $this->pm->id,
        ]);
        User::factory()->create([
            'name' => 'Theirs Leader', 'role' => 'team_leader', 'status' => 'active', 'reports_to' => $this->otherPm->id,
        ]);

        $response = $this->actingAs($this->pm)->get('/pm/team-leaders');

        $response->assertStatus(200);
        $response->assertSee('Mine Leader');
        $response->assertDontSee('Theirs Leader');
    }

    public function test_counts_are_scoped_to_this_pm(): void
    {
        $tl = User::factory()->create([
            'role' => 'team_leader', 'status' => 'active', 'reports_to' => $this->pm->id,
        ]);
        User::factory()->count(2)->create([
            'role' => 'employee', 'status' => 'active', 'reports_to' => $tl->id,
        ]);

        $myProject = Project::create([
            'name' => 'Mine', 'project_manager_id' => $this->pm->id, 'status' => 'active',
        ]);
        $otherProject = Project::create([
            'name' => 'Theirs', 'project_manager_id' => $this->otherPm->id, 'status' => 'active',
        ]);

        foreach ([$myProject, $myProject, $otherProject] as $project) {
            Task::create([
                'project_id'  => $project->id,
                'title'       => 'Task',
                'assigned_to' => $tl->id,
                'created_by'  => $project->project_manager_id,
                'status'      => 'todo',
                'priority'    => 'low',
            ]);
        }

        $response = $this->actingAs($this->pm)->get('/pm/team-leaders');

        $leader = $response->viewData('teamLeaders')->first();
        $this->assertSame(2, $leader->employees_count);
        // Only the 2 tasks from this PM's own project are counted
        $this->assertSame(2, $leader->tasks_count);
    }

    public function test_team_leader_cannot_open_pm_team_leaders_page(): void
    {
        $tl = User::factory()->create([
            'role' => 'team_leader', 'status' => 'active', 'reports_to' => $this->pm->id,
        ]);

        $this->actingAs($tl)->get('/pm/team-leaders')->assertRedirect('/tl/dashboard');
    }
}