<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@baselineitdevelopment.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'designation' => 'Principal Architect',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Sarah Jenkins',
            'email' => 'pm@baselineitdevelopment.com',
            'password' => Hash::make('password'),
            'role' => 'project_manager',
            'designation' => 'Senior Project Manager',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Vikram Malhotra',
            'email' => 'tl@baselineitdevelopment.com',
            'password' => Hash::make('password'),
            'role' => 'team_leader',
            'designation' => 'Lead Engineer',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Rohan Verma',
            'email' => 'employee@baselineitdevelopment.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'designation' => 'Software Engineer',
            'status' => 'active',
        ]);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Sign in to Workspace');
        $response->assertSee('Baseline IT');
    }

    public function test_admin_can_login_and_reach_dashboard(): void
    {
        $admin = User::where('email', 'admin@baselineitdevelopment.com')->first();

        $response = $this->post('/login', [
            'email' => 'admin@baselineitdevelopment.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect('/admin/dashboard');

        $dashResponse = $this->actingAs($admin)->get('/admin/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Executive Command Center');
        $dashResponse->assertSee('Total Employees');
    }

    public function test_pm_can_login_and_reach_dashboard(): void
    {
        $pm = User::where('email', 'pm@baselineitdevelopment.com')->first();

        $response = $this->post('/login', [
            'email' => 'pm@baselineitdevelopment.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($pm);
        $response->assertRedirect('/pm/dashboard');

        $dashResponse = $this->actingAs($pm)->get('/pm/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Sprint & Delivery Management');
        $dashResponse->assertSee('My Managed Projects');
    }

    public function test_tl_can_login_and_reach_dashboard(): void
    {
        $tl = User::where('email', 'tl@baselineitdevelopment.com')->first();

        $response = $this->post('/login', [
            'email' => 'tl@baselineitdevelopment.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($tl);
        $response->assertRedirect('/tl/dashboard');

        $dashResponse = $this->actingAs($tl)->get('/tl/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Engineering Team Lead Hub');
        $dashResponse->assertSee('Assigned Engineers');
    }

    public function test_employee_can_login_and_reach_dashboard(): void
    {
        $employee = User::where('email', 'employee@baselineitdevelopment.com')->first();

        $response = $this->post('/login', [
            'email' => 'employee@baselineitdevelopment.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($employee);
        $response->assertRedirect('/employee/dashboard');

        $dashResponse = $this->actingAs($employee)->get('/employee/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('My Developer Workspace');
        $dashResponse->assertSee('My Assigned Tasks');
    }

    public function test_team_leader_cannot_access_admin_dashboard_and_is_redirected(): void
    {
        $tl = User::where('email', 'tl@baselineitdevelopment.com')->first();

        $response = $this->actingAs($tl)->get('/admin/dashboard');

        // Should be blocked and redirected back to their own TL dashboard
        $response->assertRedirect('/tl/dashboard');
        $response->assertSessionHas('error');

        // Testing short URL /admin as well
        $shortResponse = $this->actingAs($tl)->get('/admin');
        $shortResponse->assertRedirect('/tl/dashboard');
        $shortResponse->assertSessionHas('error');
    }

    public function test_employee_cannot_access_admin_dashboard(): void
    {
        $employee = User::where('email', 'employee@baselineitdevelopment.com')->first();

        $response = $this->actingAs($employee)->get('/admin/dashboard');

        $response->assertRedirect('/employee/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@baselineitdevelopment.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::where('email', 'admin@baselineitdevelopment.com')->first();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
