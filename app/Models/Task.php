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
}