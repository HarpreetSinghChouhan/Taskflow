<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskHourAllotment;
use App\Models\TaskLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
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

        $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->pm = User::factory()->create(['role' => 'project_manager', 'status' => 'active']);
        $this->otherPm = User::factory()->create(['role' => 'project_manager', 'status' => 'active']);
        $this->tl = User::factory()->create(['role' => 'team_leader', 'status' => 'active', 'reports_to' => $this->pm->id]);
        $this->otherTl = User::factory()->create(['role' => 'team_leader', 'status' => 'active', 'reports_to' => $this->otherPm->id]);
        $this->emp = User::factory()->create(['role' => 'employee', 'status' => 'active', 'reports_to' => $this->tl->id]);
        $this->otherEmp = User::factory()->create(['role' => 'employee', 'status' => 'active', 'reports_to' => $this->otherTl->id]);

        $this->project = Project::create([
            'name' => 'Alpha Project', 'project_manager_id' => $this->pm->id, 'status' => 'active',
        ]);
        $this->task = $this->makeTask();
    }

    private function makeTask(array $overrides = []): Task
    {
        return Task::create(array_merge([
            'project_id'  => $this->project->id,
            'title'       => 'Main Task',
            'assigned_to' => $this->tl->id,
            'employee_id' => $this->emp->id,
            'created_by'  => $this->pm->id,
            'status'      => 'in_progress',
            'priority'    => 'medium',
        ], $overrides));
    }

    private function allot(Task $task, float $hours, string $status = 'approved'): TaskHourAllotment
    {
        return TaskHourAllotment::create([
            'task_id'         => $task->id,
            'requested_by'    => $this->pm->id,
            'requested_hours' => $hours,
            'approved_hours'  => $status === 'approved' ? $hours : null,
            'status'          => $status,
            'note'            => 'Needed',
            'decided_at'      => $status === 'pending' ? null : now(),
        ]);
    }

    private function logHours(Task $task, float $hours): TaskLog
    {
        return TaskLog::create([
            'task_id'  => $task->id,
            'user_id'  => $this->emp->id,
            'log_date' => now()->toDateString(),
            'hours'    => $hours,
            'note'     => 'Work done',
        ]);
    }

    private function logPayload(float $hours): array
    {
        return ['log_date' => now()->toDateString(), 'hours' => $hours, 'note' => 'Worked on it'];
    }

    private function inReview(string $stage): void
    {
        $this->task->update(['status' => 'in_review', 'review_stage' => $stage]);
    }

    private function event(string $type): array
    {
        return ['task_id' => $this->task->id, 'type' => $type];
    }

    // ---- PM asks for hours ----

    public function test_pm_can_request_hours_for_own_task(): void
    {
        $this->actingAs($this->pm)
            ->post("/pm/tasks/{$this->task->id}/hours", ['requested_hours' => 30, 'note' => 'Backend work'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('task_hour_allotments', [
            'task_id' => $this->task->id, 'requested_by' => $this->pm->id,
            'requested_hours' => 30, 'status' => 'pending',
        ]);
    }

    public function test_pm_cannot_request_more_than_40_hours(): void
    {
        $this->actingAs($this->pm)
            ->post("/pm/tasks/{$this->task->id}/hours", ['requested_hours' => 41, 'note' => 'Too much'])
            ->assertSessionHasErrors('requested_hours');

        $this->assertDatabaseCount('task_hour_allotments', 0);
    }

    public function test_pm_cannot_request_while_one_is_pending(): void
    {
        $this->allot($this->task, 10, 'pending');

        $this->actingAs($this->pm)
            ->post("/pm/tasks/{$this->task->id}/hours", ['requested_hours' => 5, 'note' => 'Again'])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('task_hour_allotments', 1);
    }

    public function test_pm_can_ask_more_hours_only_after_approved_hours_are_used(): void
    {
        $this->allot($this->task, 10);
        $this->logHours($this->task, 4);

        $this->actingAs($this->pm)
            ->post("/pm/tasks/{$this->task->id}/hours", ['requested_hours' => 20, 'note' => 'More'])
            ->assertSessionHas('error');
        $this->assertDatabaseCount('task_hour_allotments', 1);

        $this->logHours($this->task, 6);

        $this->actingAs($this->pm)
            ->post("/pm/tasks/{$this->task->id}/hours", ['requested_hours' => 20, 'note' => 'More'])
            ->assertSessionHas('success');
        $this->assertDatabaseCount('task_hour_allotments', 2);
    }

    public function test_pm_cannot_request_hours_for_other_pms_task(): void
    {
        $foreignProject = Project::create([
            'name' => 'Foreign', 'project_manager_id' => $this->otherPm->id, 'status' => 'active',
        ]);
        $foreignTask = $this->makeTask(['project_id' => $foreignProject->id, 'assigned_to' => $this->otherTl->id, 'employee_id' => null]);

        $this->actingAs($this->pm)
            ->post("/pm/tasks/{$foreignTask->id}/hours", ['requested_hours' => 10, 'note' => 'Nope'])
            ->assertStatus(403);
    }

    // ---- Admin decides ----

    public function test_admin_can_approve_up_to_40_hours(): void
    {
        $a = $this->allot($this->task, 30, 'pending');

        $this->actingAs($this->admin)
            ->patch("/admin/hour-requests/{$a->id}", ['decision' => 'approve', 'approved_hours' => 30])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('task_hour_allotments', [
            'id' => $a->id, 'status' => 'approved', 'approved_hours' => 30, 'decided_by' => $this->admin->id,
        ]);
    }

    public function test_admin_cannot_approve_more_than_40_hours(): void
    {
        $a = $this->allot($this->task, 40, 'pending');

        $this->actingAs($this->admin)
            ->patch("/admin/hour-requests/{$a->id}", ['decision' => 'approve', 'approved_hours' => 45])
            ->assertSessionHasErrors('approved_hours');

        $this->assertDatabaseHas('task_hour_allotments', ['id' => $a->id, 'status' => 'pending']);
    }

    public function test_admin_must_write_a_reason_to_reject(): void
    {
        $a = $this->allot($this->task, 10, 'pending');

        $this->actingAs($this->admin)
            ->patch("/admin/hour-requests/{$a->id}", ['decision' => 'reject'])
            ->assertSessionHasErrors('admin_note');
        $this->assertDatabaseHas('task_hour_allotments', ['id' => $a->id, 'status' => 'pending']);

        $this->actingAs($this->admin)
            ->patch("/admin/hour-requests/{$a->id}", ['decision' => 'reject', 'admin_note' => 'Not needed'])
            ->assertSessionHas('success');
        $this->assertDatabaseHas('task_hour_allotments', ['id' => $a->id, 'status' => 'rejected']);
    }

    public function test_pm_cannot_decide_hour_requests(): void
    {
        $a = $this->allot($this->task, 10, 'pending');

        $this->actingAs($this->pm)
            ->patch("/admin/hour-requests/{$a->id}", ['decision' => 'approve', 'approved_hours' => 10])
            ->assertRedirect('/pm/dashboard');

        $this->assertDatabaseHas('task_hour_allotments', ['id' => $a->id, 'status' => 'pending']);
    }

    // ---- Employee works inside approved hours ----

    public function test_employee_cannot_log_without_approved_hours(): void
    {
        $this->actingAs($this->emp)
            ->post("/employee/tasks/{$this->task->id}/logs", $this->logPayload(2))
            ->assertSessionHasErrors('hours');

        $this->assertDatabaseCount('task_logs', 0);
    }

    public function test_employee_cannot_log_more_than_remaining_hours(): void
    {
        $this->allot($this->task, 5);
        $this->logHours($this->task, 3);

        $this->actingAs($this->emp)
            ->post("/employee/tasks/{$this->task->id}/logs", $this->logPayload(3))
            ->assertSessionHasErrors('hours');
        $this->assertDatabaseCount('task_logs', 1);

        $this->actingAs($this->emp)
            ->post("/employee/tasks/{$this->task->id}/logs", $this->logPayload(2))
            ->assertSessionHas('success');
        $this->assertDatabaseCount('task_logs', 2);
    }

    // ---- Employee submits for review ----

    public function test_submit_for_review_needs_both_fields(): void
    {
        $this->actingAs($this->emp)
            ->post("/employee/tasks/{$this->task->id}/review", ['summary' => 'All done'])
            ->assertSessionHasErrors('next_tasks');

        $this->assertDatabaseHas('tasks', ['id' => $this->task->id, 'status' => 'in_progress']);
    }

    public function test_employee_can_submit_for_review_before_hours_run_out(): void
    {
        $this->allot($this->task, 20);
        $this->logHours($this->task, 2);

        $this->actingAs($this->emp)
            ->post("/employee/tasks/{$this->task->id}/review", ['summary' => 'Finished early', 'next_tasks' => 'Nothing left'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', ['id' => $this->task->id, 'status' => 'in_review', 'review_stage' => 'tl']);
        $this->assertDatabaseHas('task_review_events', $this->event('submitted') + ['user_id' => $this->emp->id]);
    }

    public function test_employee_is_locked_while_task_is_in_review(): void
    {
        $this->allot($this->task, 10);
        $this->inReview('tl');

        $this->actingAs($this->emp)
            ->post("/employee/tasks/{$this->task->id}/logs", $this->logPayload(1))
            ->assertSessionHas('error');
        $this->actingAs($this->emp)
            ->patch("/employee/tasks/{$this->task->id}/status", ['status' => 'in_progress'])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('task_logs', 0);
        $this->assertDatabaseHas('tasks', ['id' => $this->task->id, 'status' => 'in_review']);
    }

    // ---- Team leader checks ----

    public function test_tl_can_approve_and_send_to_pm(): void
    {
        $this->inReview('tl');

        $this->actingAs($this->tl)
            ->post("/tl/tasks/{$this->task->id}/review", ['decision' => 'approve'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', ['id' => $this->task->id, 'status' => 'in_review', 'review_stage' => 'pm']);
        $this->assertDatabaseHas('task_review_events', $this->event('tl_approved'));
    }

    public function test_tl_return_needs_a_comment_and_reopens_the_task(): void
    {
        $this->inReview('tl');

        $this->actingAs($this->tl)
            ->post("/tl/tasks/{$this->task->id}/review", ['decision' => 'return'])
            ->assertSessionHasErrors('body');

        $this->actingAs($this->tl)
            ->post("/tl/tasks/{$this->task->id}/review", ['decision' => 'return', 'body' => 'Fix the layout'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', ['id' => $this->task->id, 'status' => 'in_progress', 'review_stage' => null]);
        $this->assertDatabaseHas('task_review_events', $this->event('tl_returned') + ['body' => 'Fix the layout']);
    }

    public function test_other_tl_cannot_review(): void
    {
        $this->inReview('tl');

        $this->actingAs($this->otherTl)
            ->post("/tl/tasks/{$this->task->id}/review", ['decision' => 'approve'])
            ->assertStatus(403);
    }

    public function test_tl_cannot_review_when_it_is_not_their_turn(): void
    {
        $this->inReview('pm');

        $this->actingAs($this->tl)
            ->post("/tl/tasks/{$this->task->id}/review", ['decision' => 'approve'])
            ->assertStatus(403);
    }

    // ---- Project manager verifies ----

    public function test_pm_can_complete_the_task_after_tl_approval(): void
    {
        $this->inReview('pm');

        $this->actingAs($this->pm)
            ->post("/pm/tasks/{$this->task->id}/review", ['decision' => 'complete'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', ['id' => $this->task->id, 'status' => 'done', 'review_stage' => null]);
        $this->assertDatabaseHas('task_review_events', $this->event('pm_completed'));
    }

    public function test_pm_asks_why_and_employee_gives_a_reason(): void
    {
        $this->inReview('pm');

        $this->actingAs($this->pm)
            ->post("/pm/tasks/{$this->task->id}/review", ['decision' => 'question'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', ['id' => $this->task->id, 'review_stage' => 'employee']);
        $this->assertDatabaseHas('task_review_events', $this->event('pm_question') + ['body' => 'Why is this task not complete?']);

        $this->actingAs($this->emp)
            ->post("/employee/tasks/{$this->task->id}/reason", ['body' => 'The client changed the design'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', ['id' => $this->task->id, 'status' => 'in_review', 'review_stage' => 'pm']);
        $this->assertDatabaseHas('task_review_events', $this->event('employee_reason') + ['body' => 'The client changed the design']);
    }

    public function test_pm_return_needs_a_comment(): void
    {
        $this->inReview('pm');

        $this->actingAs($this->pm)
            ->post("/pm/tasks/{$this->task->id}/review", ['decision' => 'return'])
            ->assertSessionHasErrors('body');

        $this->actingAs($this->pm)
            ->post("/pm/tasks/{$this->task->id}/review", ['decision' => 'return', 'body' => 'Tests are missing'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', ['id' => $this->task->id, 'status' => 'in_progress', 'review_stage' => null]);
    }

    public function test_pm_cannot_verify_before_tl_approves(): void
    {
        $this->inReview('tl');

        $this->actingAs($this->pm)
            ->post("/pm/tasks/{$this->task->id}/review", ['decision' => 'complete'])
            ->assertStatus(403);

        $this->assertDatabaseHas('tasks', ['id' => $this->task->id, 'status' => 'in_review']);
    }

    public function test_other_pm_cannot_verify(): void
    {
        $this->inReview('pm');

        $this->actingAs($this->otherPm)
            ->post("/pm/tasks/{$this->task->id}/review", ['decision' => 'complete'])
            ->assertStatus(403);
    }

    public function test_employee_cannot_reply_unless_pm_asked(): void
    {
        $this->inReview('pm');

        $this->actingAs($this->emp)
            ->post("/employee/tasks/{$this->task->id}/reason", ['body' => 'Hello'])
            ->assertStatus(403);
    }

    public function test_other_employee_cannot_reply(): void
    {
        $this->inReview('employee');

        $this->actingAs($this->otherEmp)
            ->post("/employee/tasks/{$this->task->id}/reason", ['body' => 'Hello'])
            ->assertStatus(403);
    }

    // ---- Admin confirms ----

    public function test_admin_confirms_a_task_completed_by_the_pm(): void
    {
        $this->task->update(['status' => 'done']);
        $this->task->reviewEvents()->create(['user_id' => $this->pm->id, 'type' => 'pm_completed']);

        $this->actingAs($this->admin)->get('/admin/confirmations')
            ->assertOk()
            ->assertSee('Main Task');

        $this->actingAs($this->admin)
            ->post("/admin/confirmations/{$this->task->id}", ['note' => 'Good work'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('task_review_events', $this->event('admin_confirmed') + ['body' => 'Good work']);
        $this->assertSame(0, Task::awaitingAdmin()->count());

        $this->actingAs($this->admin)
            ->post("/admin/confirmations/{$this->task->id}")
            ->assertSessionHas('error');
    }

    public function test_admin_cannot_confirm_a_task_the_pm_has_not_completed(): void
    {
        $this->actingAs($this->admin)
            ->post("/admin/confirmations/{$this->task->id}")
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('task_review_events', $this->event('admin_confirmed'));
    }

    public function test_admin_can_read_the_task_detail(): void
    {
        $this->actingAs($this->admin)->get("/admin/tasks/{$this->task->id}")
            ->assertOk()
            ->assertSee('Main Task');
    }

    public function test_pm_cannot_open_admin_confirmations(): void
    {
        $this->actingAs($this->pm)->get('/admin/confirmations')->assertRedirect('/pm/dashboard');
    }

    // ---- The whole flow ----

    public function test_full_flow_from_hours_request_to_admin_confirmation(): void
    {
        $this->actingAs($this->pm)
            ->post("/pm/tasks/{$this->task->id}/hours", ['requested_hours' => 10, 'note' => 'First week']);
        $allotment = TaskHourAllotment::firstOrFail();

        $this->actingAs($this->admin)
            ->patch("/admin/hour-requests/{$allotment->id}", ['decision' => 'approve', 'approved_hours' => 10]);

        $this->actingAs($this->emp)->post("/employee/tasks/{$this->task->id}/logs", $this->logPayload(4));
        $this->actingAs($this->emp)->post("/employee/tasks/{$this->task->id}/review", [
            'summary' => 'Everything is built', 'next_tasks' => 'Nothing left',
        ]);

        $this->actingAs($this->tl)->post("/tl/tasks/{$this->task->id}/review", ['decision' => 'approve']);
        $this->actingAs($this->pm)->post("/pm/tasks/{$this->task->id}/review", ['decision' => 'complete']);
        $this->actingAs($this->admin)->post("/admin/confirmations/{$this->task->id}");

        $this->assertDatabaseHas('tasks', ['id' => $this->task->id, 'status' => 'done', 'review_stage' => null]);
        $this->assertSame(
            ['submitted', 'tl_approved', 'pm_completed', 'admin_confirmed'],
            $this->task->reviewEvents()->orderBy('id')->pluck('type')->all()
        );
        $this->assertEquals(4.0, $this->task->loggedHours());
    }
}