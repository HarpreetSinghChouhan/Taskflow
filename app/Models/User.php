<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'designation',
        'joining_date',
        'profile_image',
        'status',
        'reports_to',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'joining_date' => 'date',
            'password' => 'hashed',
        ];
    }

    /**
     * Role checking helpers
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isProjectManager(): bool
    {
        return $this->role === 'project_manager';
    }

    public function isTeamLeader(): bool
    {
        return $this->role === 'team_leader';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get dashboard URL based on user role
     */
    public function getDashboardRoute(): string
    {
        return match ($this->role) {
            'admin' => route('admin.dashboard'),
            'project_manager' => route('pm.dashboard'),
            'team_leader' => route('tl.dashboard'),
            'employee' => route('employee.dashboard'),
            default => route('home'),
        };
    }
    // Role that a given role reports to
    public static function parentRoleFor(string $role): ?string
    {
        return match ($role) {
            'team_leader' => 'project_manager',
            'employee' => 'team_leader',
            default => null,
        };
    }

// The person this user reports to
    public function manager()
    {
        return $this->belongsTo(User::class, 'reports_to')->withTrashed();
    }

// Users who report to this user
    public function reports()
    {
        return $this->hasMany(User::class, 'reports_to');
    }
    // Projects owned by this project manager
    public function projects()
    {
        return $this->hasMany(Project::class, 'project_manager_id');
    }
    // Tasks assigned to this user
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }
    // Tasks given to this employee by their team leader
    public function employeeTasks()
    {
        return $this->hasMany(Task::class, 'employee_id');
    }
    // Progress updates written by this user
    public function taskLogs()
    {
        return $this->hasMany(TaskLog::class);
    }
    // Hours summary shown on the project manager dashboard
    public function hoursSummary(): array
    {
        $projects = $this->projects()
            ->withCount([
                'tasks',
                'tasks as done_tasks_count' => fn ($q) => $q->where('status', 'done'),
            ])
            ->withSum('taskLogs as total_hours', 'hours')
            ->orderByDesc('total_hours')
            ->get();

        $weekHours = TaskLog::whereHas('task.project', fn ($q) => $q->where('project_manager_id', $this->id))
            ->whereBetween('log_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
            ->sum('hours');

        return [
            'projects'    => $projects,
            'total_hours' => (float) $projects->sum('total_hours'),
            'week_hours'  => (float) $weekHours,
        ];
    }
}