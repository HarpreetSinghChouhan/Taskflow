<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $pm;
    private User $otherPm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->pm = User::factory()->create(['role' => 'project_manager', 'status' => 'active']);
        $this->otherPm = User::factory()->create(['role' => 'project_manager', 'status' => 'active']);
    }

    private function makeProject(User $pm, array $overrides = []): Project
    {
        return Project::create(array_merge([
            'name'               => 'Test Project',
            'project_manager_id' => $pm->id,
            'status'             => 'planned',
        ], $overrides));
    }

    // ---- Admin ----

    public function test_admin_can_create_project(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/projects', [
            'name'               => 'Client Portal',
            'project_manager_id' => $this->pm->id,
            'status'             => 'active',
            'start_date'         => '2026-11-01',
            'deadline'           => '2026-12-31',
        ]);

        $response->assertRedirect('/admin/projects');
        $this->assertDatabaseHas('projects', [
            'name'               => 'Client Portal',
            'project_manager_id' => $this->pm->id,
        ]);
    }

    public function test_project_manager_must_be_an_active_pm(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'status' => 'active']);

        $response = $this->actingAs($this->admin)->post('/admin/projects', [
            'name'               => 'Bad Manager',
            'project_manager_id' => $employee->id,
            'status'             => 'planned',
        ]);

        $response->assertSessionHasErrors('project_manager_id');
        $this->assertDatabaseMissing('projects', ['name' => 'Bad Manager']);
    }

    public function test_deadline_cannot_be_before_start_date(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/projects', [
            'name'               => 'Bad Dates',
            'project_manager_id' => $this->pm->id,
            'status'             => 'planned',
            'start_date'         => '2026-12-01',
            'deadline'           => '2026-11-01',
        ]);

        $response->assertSessionHasErrors('deadline');
    }

    public function test_admin_can_update_project(): void
    {
        $project = $this->makeProject($this->pm);

        $this->actingAs($this->admin)->put("/admin/projects/{$project->id}", [
            'name'               => 'Renamed',
            'project_manager_id' => $this->otherPm->id,
            'status'             => 'active',
        ])->assertRedirect('/admin/projects');

        $this->assertDatabaseHas('projects', [
            'id'                 => $project->id,
            'name'               => 'Renamed',
            'project_manager_id' => $this->otherPm->id,
        ]);
    }

    public function test_admin_can_delete_project(): void
    {
        $project = $this->makeProject($this->pm);

        $this->actingAs($this->admin)->delete("/admin/projects/{$project->id}");

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    // ---- Project Manager ----

    public function test_pm_sees_only_their_own_projects(): void
    {
        $this->makeProject($this->pm, ['name' => 'Alpha Mine']);
        $this->makeProject($this->otherPm, ['name' => 'Beta Theirs']);

        $response = $this->actingAs($this->pm)->get('/pm/projects');

        $response->assertStatus(200);
        $response->assertSee('Alpha Mine');
        $response->assertDontSee('Beta Theirs');
    }

    public function test_pm_can_update_own_project(): void
    {
        $project = $this->makeProject($this->pm);

        $this->actingAs($this->pm)->put("/pm/projects/{$project->id}", [
            'name'   => 'Updated By PM',
            'status' => 'active',
        ])->assertRedirect('/pm/projects');

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Updated By PM']);
    }

    public function test_pm_cannot_open_edit_page_of_other_pms_project(): void
    {
        $project = $this->makeProject($this->otherPm);

        $this->actingAs($this->pm)->get("/pm/projects/{$project->id}/edit")->assertStatus(403);
    }

    public function test_pm_cannot_update_other_pms_project(): void
    {
        $project = $this->makeProject($this->otherPm, ['name' => 'Original']);

        $this->actingAs($this->pm)->put("/pm/projects/{$project->id}", [
            'name'   => 'Hacked',
            'status' => 'active',
        ])->assertStatus(403);

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Original']);
    }

    public function test_pm_cannot_change_project_manager(): void
    {
        $project = $this->makeProject($this->pm);

        $this->actingAs($this->pm)->put("/pm/projects/{$project->id}", [
            'name'               => 'Same Project',
            'status'             => 'active',
            'project_manager_id' => $this->otherPm->id,
        ]);

        $this->assertDatabaseHas('projects', [
            'id'                 => $project->id,
            'project_manager_id' => $this->pm->id,
        ]);
    }

    // ---- Role walls ----

    public function test_pm_cannot_open_admin_projects(): void
    {
        $this->actingAs($this->pm)->get('/admin/projects')->assertRedirect('/pm/dashboard');
    }

    public function test_employee_cannot_open_pm_projects(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'status' => 'active']);

        $this->actingAs($employee)->get('/pm/projects')->assertRedirect('/employee/dashboard');
    }
}