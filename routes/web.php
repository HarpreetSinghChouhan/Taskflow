<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Pm\ProjectController as PmProjectController;
use App\Http\Controllers\Pm\TaskController as PmTaskController;
use App\Http\Controllers\Pm\TeamLeaderController as PmTeamLeaderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 👑 Admin Portal (Only accessible by Admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    // Team & Role Management (PM, TL, Employee)
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('users', UserController::class);
    Route::resource('projects', ProjectController::class)->except('show');
});

// 📋 Project Manager Portal (Only accessible by Project Manager)
Route::middleware(['auth', 'role:project_manager'])->group(function () {
    Route::get('/pm', fn() => redirect()->route('pm.dashboard'));
    Route::get('/pm/dashboard', [DashboardController::class, 'pm'])->name('pm.dashboard');
    Route::get('/pm/projects', [PmProjectController::class, 'index'])->name('pm.projects.index');
    Route::get('/pm/projects/{project}/edit', [PmProjectController::class, 'edit'])->name('pm.projects.edit');
    Route::put('/pm/projects/{project}', [PmProjectController::class, 'update'])->name('pm.projects.update');
    Route::get('/pm/tasks', [PmTaskController::class, 'overview'])->name('pm.tasks.overview');
    Route::get('/pm/projects/{project}/board', [PmTaskController::class, 'board'])->name('pm.tasks.board');
    Route::patch('/pm/tasks/{task}/move', [PmTaskController::class, 'move'])->name('pm.tasks.move');
    Route::get('/pm/projects/{project}/tasks', [PmTaskController::class, 'index'])->name('pm.tasks.index');
    Route::get('/pm/projects/{project}/tasks/create', [PmTaskController::class, 'create'])->name('pm.tasks.create');
    Route::post('/pm/projects/{project}/tasks', [PmTaskController::class, 'store'])->name('pm.tasks.store');
    Route::get('/pm/tasks/{task}/edit', [PmTaskController::class, 'edit'])->name('pm.tasks.edit');
    Route::put('/pm/tasks/{task}', [PmTaskController::class, 'update'])->name('pm.tasks.update');
    Route::delete('/pm/tasks/{task}', [PmTaskController::class, 'destroy'])->name('pm.tasks.destroy');
    Route::get('/pm/team-leaders', [PmTeamLeaderController::class, 'index'])->name('pm.team-leaders.index');
});
  
// 👥 Team Leader Portal (Only accessible by Team Leader)
Route::middleware(['auth', 'role:team_leader'])->group(function () {
    Route::get('/tl', fn() => redirect()->route('tl.dashboard'));
    Route::get('/tl/dashboard', [DashboardController::class, 'tl'])->name('tl.dashboard');
});

// 👤 Developer Portal (Only accessible by Employee)
Route::middleware(['auth', 'role:employee'])->group(function () {
    Route::get('/employee', fn() => redirect()->route('employee.dashboard'));
    Route::get('/employee/dashboard', [DashboardController::class, 'employee'])->name('employee.dashboard');
});
Route::get('/account/verify/{user}', [AuthController::class, 'verify'])
    ->middleware('signed')
    ->name('account.verify');