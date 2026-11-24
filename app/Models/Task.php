<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'assigned_to',
        'employee_id',
        'created_by',
        'status',
        'review_stage',
        'priority',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // The team leader this task is assigned to
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to')->withTrashed();
    }
    // The employee the team leader gave this task to
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id')->withTrashed();
    }

    // Progress updates written on this task
    public function logs()
    {
        return $this->hasMany(TaskLog::class);
    }

    // The project manager who created the task
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }
    // Hour requests and approvals for this task
    public function hourAllotments()
    {
        return $this->hasMany(TaskHourAllotment::class);
    }

    // Review timeline: submit, TL check, PM question, employee reason, completion
    public function reviewEvents()
    {
        return $this->hasMany(TaskReviewEvent::class);
    }
    // Total hours the admin has approved for this task
    public function approvedHours(): float
    {
        return (float) $this->hourAllotments()->where('status', 'approved')->sum('approved_hours');
    }

    // Total hours the employee has logged on this task
    public function loggedHours(): float
    {
        return (float) $this->logs()->sum('hours');
    }

    public function remainingHours(): float
    {
        return max(0, round($this->approvedHours() - $this->loggedHours(), 2));
    }

    // A new request is allowed only if nothing is pending and earlier hours are used up
    public function canRequestHours(): bool
    {
        if ($this->hourAllotments()->where('status', 'pending')->exists()) {
            return false;
        }

        $approved = round($this->approvedHours(), 2);

        return $approved == 0 || round($this->loggedHours(), 2) >= $approved;
    }
    // Done tasks the PM confirmed but the admin has not yet confirmed
    public function scopeAwaitingAdmin($query)
    {
        return $query->where('status', 'done')
            ->whereHas('reviewEvents', fn ($q) => $q->where('type', 'pm_completed'))
            ->whereDoesntHave('reviewEvents', fn ($q) => $q->where('type', 'admin_confirmed'));
    }
}