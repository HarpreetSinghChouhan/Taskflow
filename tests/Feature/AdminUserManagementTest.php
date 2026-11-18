<?php

namespace Tests\Feature;

use App\Mail\WelcomeUserMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@baselineitdevelopment.com',
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_view_user_directory(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('Team &amp; Role Management', false);
        $response->assertSee('+ Create PM');
        $response->assertSee('+ Create TL');
        $response->assertSee('+ Create Employee');
    }

    public function test_admin_can_view_create_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users/create?role=project_manager');

        $response->assertStatus(200);
        $response->assertSee('Create New Team Member');
        $response->assertSee('Project Manager');
    }

    public function test_admin_can_create_project_manager_and_pm_can_login(): void
    {
        $userData = [
            'name' => 'New PM User',
            'email' => 'newpm@baseline.com',
            'phone' => '+91 9988776655',
            'role' => 'project_manager',
            'designation' => 'Technical PM',
            'joining_date' => '2026-11-17',
            'password' => 'Secret@123',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/users', $userData);

        $response->assertRedirect('/admin/users?role=project_manager');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'newpm@baseline.com',
            'role' => 'project_manager',
            'status' => 'active',
        ]);

        // Verify newly created PM can log in and reach PM dashboard
        $this->post('/logout');

        $loginResponse = $this->post('/login', [
            'email' => 'newpm@baseline.com',
            'password' => 'Secret@123',
        ]);

        $loginResponse->assertRedirect('/pm/dashboard');
        $this->assertAuthenticated();

        $dashResponse = $this->get('/pm/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('New PM User');
    }

    public function test_admin_can_create_team_leader_and_tl_can_login(): void
    {
        $pm = User::factory()->create(['role' => 'project_manager', 'status' => 'active']);
        $userData = [
            'name' => 'New TL User',
            'email' => 'newtl@baseline.com',
            'phone' => '+91 9988776644',
            'role' => 'team_leader',
            'designation' => 'Lead Architect',
            'joining_date' => '2026-11-17',
            'password' => 'Secret@123',
            'status' => 'active',
            'reports_to' => $pm->id,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/users', $userData);

        $response->assertRedirect('/admin/users?role=team_leader');
        $this->assertDatabaseHas('users', [
            'email' => 'newtl@baseline.com',
            'role' => 'team_leader',
            'reports_to' => $pm->id,
        ]);

        $this->post('/logout');

        $loginResponse = $this->post('/login', [
            'email' => 'newtl@baseline.com',
            'password' => 'Secret@123',
        ]);

        $loginResponse->assertRedirect('/tl/dashboard');
    }

    public function test_admin_can_create_employee_and_employee_can_login(): void
    {
        $tl = User::factory()->create(['role' => 'team_leader', 'status' => 'active']);
        $userData = [
            'name' => 'New Dev User',
            'email' => 'newdev@baseline.com',
            'phone' => '+91 9988776633',
            'role' => 'employee',
            'designation' => 'Frontend Engineer',
            'joining_date' => '2026-11-17',
            'password' => 'Secret@123',
            'status' => 'active',
            'reports_to' => $tl->id,
        ];

        $response = $this->actingAs($this->admin)->post('/admin/users', $userData);

        $response->assertRedirect('/admin/users?role=employee');
        $this->assertDatabaseHas('users', [
            'email' => 'newdev@baseline.com',   
            'role' => 'employee',
        ]);

        $this->post('/logout');

        $loginResponse = $this->post('/login', [
            'email' => 'newdev@baseline.com',
            'password' => 'Secret@123',
        ]);

        $loginResponse->assertRedirect('/employee/dashboard');
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);

        $response = $this->actingAs($employee)->get('/admin/users');
        $response->assertRedirect('/employee/dashboard');
    }
    public function test_team_leader_requires_a_project_manager(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'TL No Boss', 'email' => 'tl2@baseline.com', 'role' => 'team_leader',
            'password' => 'Secret@123', 'status' => 'active',
        ]);

        $response->assertSessionHasErrors('reports_to');
    }

    public function test_employee_cannot_report_to_a_project_manager(): void
    {
        $pm = User::factory()->create(['role' => 'project_manager', 'status' => 'active']);

        $response = $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Dev', 'email' => 'dev2@baseline.com', 'role' => 'employee',
            'password' => 'Secret@123', 'status' => 'active', 'reports_to' => $pm->id,
        ]);

        $response->assertSessionHasErrors('reports_to');
    }

    public function test_welcome_email_is_sent_on_create(): void
    {
        Mail::fake();

        $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Mail PM', 'email' => 'mailpm@baseline.com', 'role' => 'project_manager',
            'password' => 'Secret@123', 'status' => 'active',
        ]);

        Mail::assertSent(WelcomeUserMail::class, fn ($mail) => $mail->hasTo('mailpm@baseline.com'));
    }

    public function test_cannot_delete_manager_who_has_reports(): void
    {
        $tl = User::factory()->create(['role' => 'team_leader', 'status' => 'active']);
        User::factory()->create(['role' => 'employee', 'status' => 'active', 'reports_to' => $tl->id]);

        $response = $this->actingAs($this->admin)->delete("/admin/users/{$tl->id}");

        $response->assertSessionHas('error');
    $this->assertNotSoftDeleted('users', ['id' => $tl->id]);
    }

    public function test_signed_verify_link_marks_email_verified(): void
    {
        $user = User::factory()->create(['role' => 'employee', 'email_verified_at' => null]);

        $url = URL::temporarySignedRoute('account.verify', now()->addDay(), ['user' => $user->id]);

        $this->get($url)->assertRedirect('/login');
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_unsigned_verify_link_is_rejected(): void
    {
        $user = User::factory()->create(['role' => 'employee', 'email_verified_at' => null]);

        $this->get("/account/verify/{$user->id}")->assertStatus(403);
        $this->assertNull($user->fresh()->email_verified_at);
    }
}
